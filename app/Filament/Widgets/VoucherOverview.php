<?php

namespace App\Filament\Widgets;

use App\Models\VoucherOrderItem;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class VoucherOverview extends TableWidget
{
    protected static ?int $sort = 30;

    protected string $view = 'filament.widgets.collapsible-table';

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '60s';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => VoucherOrderItem::query()
                ->select('voucher_order_items.voucher_title')
                ->selectRaw('MIN(voucher_order_items.id) as id')
                ->selectRaw('SUM(voucher_order_items.quantity) as total_purchased')
                ->selectRaw('COUNT(DISTINCT voucher_redemptions.issued_voucher_id) as total_redeemed')
                ->leftJoin('issued_vouchers', 'issued_vouchers.voucher_order_item_id', '=', 'voucher_order_items.id')
                ->leftJoin('voucher_redemptions', 'voucher_redemptions.issued_voucher_id', '=', 'issued_vouchers.id')
                ->whereHas('order', fn(Builder $query) => $query->paid())
                ->groupBy('voucher_order_items.voucher_title'))
            ->columns([
                Tables\Columns\TextColumn::make('voucher_title')
                    ->label('Voucher Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_purchased')
                    ->label('Total Purchased')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_redeemed')
                    ->label('Redeemed')
                    ->numeric()
                    ->sortable(),
            ])
            ->defaultSort('total_purchased', 'desc')
            ->defaultKeySort(false)
            ->paginated([10, 25, 50]);
    }
}
