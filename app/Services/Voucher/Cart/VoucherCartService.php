<?php

namespace App\Services\Voucher\Cart;

use App\Models\Voucher;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;

class VoucherCartService
{
    private const SESSION_KEY = 'voucher.cart.lines';

    public function add(Voucher $voucher, array $data): string
    {
        $this->assertPurchasable($voucher);

        $quantity = $this->normalizeQuantity($voucher, (int) ($data['quantity'] ?? 1));
        $key = (string) Str::uuid();

        $lines = $this->rawLines();
        $lines[$key] = [
            'key' => $key,
            'voucher_id' => $voucher->id,
            'quantity' => $quantity,
            'purchase_for' => $data['purchase_for'] ?? 'gift',
            'recipient_name' => trim((string) $data['recipient_name']),
            'recipient_email' => strtolower(trim((string) $data['recipient_email'])),
            'personal_message' => trim((string) ($data['personal_message'] ?? '')),
            'gift_from' => trim((string) ($data['gift_from'] ?? '')),
            'delivery_method' => $data['delivery_method'] ?? 'email',
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

        $lines[$key] = array_merge($lines[$key], [
            'quantity' => $this->normalizeQuantity($voucher, (int) ($data['quantity'] ?? $lines[$key]['quantity'])),
            'purchase_for' => $data['purchase_for'] ?? $lines[$key]['purchase_for'] ?? 'gift',
            'recipient_name' => trim((string) ($data['recipient_name'] ?? $lines[$key]['recipient_name'])),
            'recipient_email' => strtolower(trim((string) ($data['recipient_email'] ?? $lines[$key]['recipient_email']))),
            'personal_message' => trim((string) ($data['personal_message'] ?? $lines[$key]['personal_message'] ?? '')),
            'gift_from' => trim((string) ($data['gift_from'] ?? $lines[$key]['gift_from'] ?? '')),
            'delivery_method' => $data['delivery_method'] ?? $lines[$key]['delivery_method'],
            'delivery_date' => $data['delivery_date'] ?? $lines[$key]['delivery_date'],
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

        foreach ($this->rawLines() as $key => $line) {
            $voucher = Voucher::query()->with('category')->find($line['voucher_id']);

            if (! $voucher || ! $voucher->purchasable) {
                continue;
            }

            $quantity = $this->normalizeQuantity($voucher, (int) $line['quantity']);
            $unitPrice = $voucher->discounted_price;
            $originalUnitPrice = (int) $voucher->selling_price;

            $lines[$key] = array_merge($line, [
                'quantity' => $quantity,
                'voucher' => $voucher,
                'unit_price' => $unitPrice,
                'line_total' => $unitPrice * $quantity,
                'original_line_total' => $originalUnitPrice * $quantity,
                'currency' => $voucher->currency ?: 'IDR',
                'price_type' => $voucher->price_type,
                'unit_type' => $voucher->unit_type,
            ]);
        }

        session()->put(self::SESSION_KEY, collect($lines)->map(fn(array $line): array => collect($line)->except('voucher')->all())->all());

        $collection = collect($lines);
        $subtotal = (int) $collection->sum('original_line_total');
        $total = (int) $collection->sum('line_total');

        return [
            'lines' => $collection,
            'subtotal' => $subtotal,
            'discount' => $subtotal - $total,
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
