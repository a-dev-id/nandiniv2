<?php

namespace Tests\Feature;

use App\Filament\Resources\Vouchers\Pages\EditVoucher;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class VoucherAdminFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_room_price_options_can_be_reordered(): void
    {
        $this->actingAs(User::factory()->create());

        $voucher = Voucher::factory()->create([
            'price_options' => [
                ['key' => 'jungle-view', 'label' => 'Jungle View Villa', 'additional_price' => 0],
                ['key' => 'sunrise-view', 'label' => 'Sunrise View Villa', 'additional_price' => 330579],
                ['key' => 'panoramic-view', 'label' => 'Panoramic Jungle View Villa', 'additional_price' => 661157],
            ],
        ]);

        $component = Livewire::test(EditVoucher::class, ['record' => $voucher->getRouteKey()])
            ->assertOk();

        $priceOptions = $component->get('data.price_options');
        $itemKeys = array_keys($priceOptions);

        $this->assertCount(3, $itemKeys);
        $this->assertTrue(collect($itemKeys)->every(fn (string $key): bool => Str::isUuid($key)));

        $component
            ->callFormComponentAction(
                'price_options',
                'reorder',
                arguments: ['items' => array_reverse($itemKeys)],
            )
            ->assertHasNoErrors();

        $reorderedPriceOptions = $component->get('data.price_options');

        $this->assertSame(array_reverse($itemKeys), array_keys($reorderedPriceOptions));
        $this->assertSame(
            ['Panoramic Jungle View Villa', 'Sunrise View Villa', 'Jungle View Villa'],
            array_column(array_values($reorderedPriceOptions), 'label'),
        );
    }
}
