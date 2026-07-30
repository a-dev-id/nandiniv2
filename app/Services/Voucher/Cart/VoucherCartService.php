<?php

namespace App\Services\Voucher\Cart;

use App\Models\Voucher;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;

class VoucherCartService
{
    public const PRINT_DELIVERY_FEE = 100000;

    public const SERVICE_CHARGE_PERCENTAGE = 10;

    public const TAX_PERCENTAGE = 11;

    public const CART_DISCOUNT_PERCENTAGE = 10;

    private const SESSION_KEY = 'voucher.cart.lines';

    public function add(Voucher $voucher, array $data): string
    {
        $this->assertPurchasable($voucher);

        $quantity = $this->normalizeQuantity($voucher, (int) ($data['quantity'] ?? 1));
        $priceOption = $voucher->resolvePriceOption($data['price_option'] ?? null);
        $key = (string) Str::uuid();
        $purchaseFor = $data['purchase_for'] ?? 'gift';
        $deliveryMethod = $purchaseFor === 'self' ? 'email' : ($data['delivery_method'] ?? 'email');

        $lines = $this->rawLines();
        $lines[$key] = [
            'key' => $key,
            'voucher_id' => $voucher->id,
            'quantity' => $quantity,
            'price_option_key' => $priceOption['key'] ?? null,
            'purchase_for' => $purchaseFor,
            'recipient_name' => trim((string) ($data['recipient_name'] ?? '')),
            'recipient_email' => $purchaseFor === 'gift' && $deliveryMethod === 'print_at_resort'
                ? ''
                : strtolower(trim((string) ($data['recipient_email'] ?? ''))),
            'personal_message' => trim((string) ($data['personal_message'] ?? '')),
            'gift_from' => trim((string) ($data['gift_from'] ?? '')),
            'delivery_method' => $deliveryMethod,
            'hotel_note' => $purchaseFor === 'gift' && $deliveryMethod === 'print_at_resort'
                ? trim((string) ($data['hotel_note'] ?? ''))
                : '',
            'delivery_date' => $data['delivery_date'] ?? null,
        ];

        session()->put(self::SESSION_KEY, $lines);

        return $key;
    }

    public function update(string $key, array $data): void
    {
        $lines = $this->rawLines();

        if (! isset($lines[$key])) {
            throw new InvalidArgumentException('Cart line not found.');
        }

        $voucher = Voucher::query()->findOrFail($lines[$key]['voucher_id']);
        $this->assertPurchasable($voucher);
        $purchaseFor = $data['purchase_for'] ?? $lines[$key]['purchase_for'] ?? 'gift';
        $deliveryMethod = $purchaseFor === 'self'
            ? 'email'
            : ($data['delivery_method'] ?? $lines[$key]['delivery_method'] ?? 'email');
        $priceOption = $voucher->resolvePriceOption(
            array_key_exists('price_option', $data)
                ? $data['price_option']
                : ($lines[$key]['price_option_key'] ?? null)
        );

        $lines[$key] = array_merge($lines[$key], [
            'quantity' => $this->normalizeQuantity($voucher, (int) ($data['quantity'] ?? $lines[$key]['quantity'])),
            'price_option_key' => $priceOption['key'] ?? null,
            'purchase_for' => $purchaseFor,
            'recipient_name' => trim((string) ($data['recipient_name'] ?? $lines[$key]['recipient_name'])),
            'recipient_email' => $purchaseFor === 'gift' && $deliveryMethod === 'print_at_resort'
                ? ''
                : strtolower(trim((string) ($data['recipient_email'] ?? $lines[$key]['recipient_email']))),
            'personal_message' => trim((string) ($data['personal_message'] ?? $lines[$key]['personal_message'] ?? '')),
            'gift_from' => trim((string) ($data['gift_from'] ?? $lines[$key]['gift_from'] ?? '')),
            'delivery_method' => $deliveryMethod,
            'hotel_note' => $purchaseFor === 'gift' && $deliveryMethod === 'print_at_resort'
                ? trim((string) ($data['hotel_note'] ?? $lines[$key]['hotel_note'] ?? ''))
                : '',
            'delivery_date' => $data['delivery_date'] ?? $lines[$key]['delivery_date'] ?? null,
        ]);

        session()->put(self::SESSION_KEY, $lines);
    }

    public function remove(string $key): void
    {
        $lines = $this->rawLines();
        unset($lines[$key]);
        session()->put(self::SESSION_KEY, $lines);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function refresh(): array
    {
        $lines = [];
        $globalDiscountActive = (int) collect($this->rawLines())->sum('quantity') >= 2;

        foreach ($this->rawLines() as $key => $line) {
            $voucher = Voucher::query()->with('category')->find($line['voucher_id']);

            if (! $voucher || ! $voucher->purchasable) {
                continue;
            }

            try {
                $priceOption = $voucher->resolvePriceOption($line['price_option_key'] ?? null);
            } catch (InvalidArgumentException) {
                continue;
            }

            $quantity = $this->normalizeQuantity($voucher, (int) $line['quantity']);
            $priceBeforeCartDiscount = $voucher->discountedPriceForOption($priceOption);
            $cartDiscountApplies = $globalDiscountActive;
            $unitPrice = $priceBeforeCartDiscount;
            $baseVoucherUnitPrice = $voucher->discountedPriceForOption();
            $roomUpgradeUnitPrice = max(0, $unitPrice - $baseVoucherUnitPrice);
            $originalUnitPrice = $voucher->originalPriceForOption($priceOption);
            $voucherSubtotal = $baseVoucherUnitPrice * $quantity;
            $roomUpgradeTotal = $roomUpgradeUnitPrice * $quantity;
            $baseLineTotal = $unitPrice * $quantity;
            $deliveryFee = ($line['delivery_method'] ?? 'email') === 'print_at_resort'
                ? self::PRINT_DELIVERY_FEE
                : 0;
            $preTaxLineTotal = $baseLineTotal + $deliveryFee;
            $lineServiceCharge = (int) round($preTaxLineTotal * self::SERVICE_CHARGE_PERCENTAGE / 100);
            $lineTax = (int) round($preTaxLineTotal * self::TAX_PERCENTAGE / 100);
            $lineTotalBeforeCartDiscount = $preTaxLineTotal + $lineServiceCharge + $lineTax;
            $lineCartDiscount = $cartDiscountApplies
                ? (int) round($lineTotalBeforeCartDiscount * self::CART_DISCOUNT_PERCENTAGE / 100)
                : 0;

            $lines[$key] = array_merge($line, [
                'quantity' => $quantity,
                'voucher' => $voucher,
                'price_option_key' => $priceOption['key'] ?? null,
                'price_option' => $priceOption,
                'price_before_cart_discount' => $priceBeforeCartDiscount,
                'cart_discount_percentage' => $cartDiscountApplies ? self::CART_DISCOUNT_PERCENTAGE : 0,
                'cart_discount' => $lineCartDiscount,
                'unit_price' => $unitPrice,
                'base_unit_price' => $baseVoucherUnitPrice,
                'room_upgrade_unit_price' => $roomUpgradeUnitPrice,
                'voucher_subtotal' => $voucherSubtotal,
                'room_upgrade_total' => $roomUpgradeTotal,
                'base_line_total' => $baseLineTotal,
                'delivery_fee' => $deliveryFee,
                'pre_tax_line_total' => $preTaxLineTotal,
                'service_charge' => $lineServiceCharge,
                'tax' => $lineTax,
                'line_total' => $lineTotalBeforeCartDiscount,
                'line_total_after_discount' => $lineTotalBeforeCartDiscount - $lineCartDiscount,
                'original_line_total' => ($originalUnitPrice * $quantity) + $deliveryFee,
                'currency' => $voucher->currency ?: 'IDR',
                'price_type' => $voucher->price_type,
                'unit_type' => $voucher->unit_type,
            ]);
        }

        session()->put(self::SESSION_KEY, collect($lines)->map(fn (array $line): array => collect($line)->except('voucher')->all())->all());

        $collection = collect($lines);
        $subtotal = (int) $collection->sum('line_total');
        $preTaxTotal = (int) $collection->sum('pre_tax_line_total');
        $cartDiscount = (int) $collection->sum('cart_discount');
        $serviceCharge = (int) $collection->sum('service_charge');
        $tax = (int) $collection->sum('tax');
        $total = $subtotal - $cartDiscount;

        return [
            'lines' => $collection,
            'subtotal' => $subtotal,
            'discount' => $cartDiscount,
            'cart_discount' => $cartDiscount,
            'cart_discount_percentage' => self::CART_DISCOUNT_PERCENTAGE,
            'global_discount_active' => $globalDiscountActive,
            'pre_tax_total' => $preTaxTotal,
            'service_charge' => $serviceCharge,
            'service_charge_percentage' => self::SERVICE_CHARGE_PERCENTAGE,
            'tax' => $tax,
            'tax_percentage' => self::TAX_PERCENTAGE,
            'total' => $total,
            'currency' => $collection->first()['currency'] ?? config('services.flywire.billing_currency', 'IDR'),
            'distinct_lines' => $collection->count(),
            'total_units' => (int) $collection->sum('quantity'),
        ];
    }

    public function lines(): Collection
    {
        return $this->refresh()['lines'];
    }

    public function isEmpty(): bool
    {
        return $this->lines()->isEmpty();
    }

    public function countUnits(): int
    {
        return $this->refresh()['total_units'];
    }

    private function rawLines(): array
    {
        return session()->get(self::SESSION_KEY, []);
    }

    private function assertPurchasable(Voucher $voucher): void
    {
        if (! $voucher->purchasable) {
            throw new InvalidArgumentException('This voucher is not available for purchase.');
        }
    }

    private function normalizeQuantity(Voucher $voucher, int $quantity): int
    {
        $min = max(1, (int) ($voucher->minimum_quantity ?: 1));
        $max = (int) ($voucher->purchase_limit_per_order ?: $voucher->maximum_quantity ?: 99);

        if ($quantity < $min || $quantity > $max) {
            throw new InvalidArgumentException("Quantity must be between {$min} and {$max}.");
        }

        return $quantity;
    }
}
