<?php

namespace App\Filament\Resources\SyncedWebhotelierBookings\Pages;

use App\Filament\Resources\SyncedWebhotelierBookings\SyncedWebhotelierBookingResource;
use Filament\Resources\Pages\ListRecords;

class ListSyncedWebhotelierBookings extends ListRecords
{
    protected static string $resource = SyncedWebhotelierBookingResource::class;
}
