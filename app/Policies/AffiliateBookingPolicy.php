<?php

namespace App\Policies;

use App\Models\AffiliateBooking;
use App\Models\Permission;
use App\Models\User;

class AffiliateBookingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(Permission::AFFILIATE_BOOKING_VIEW);
    }

    public function view(User $user, AffiliateBooking $booking): bool
    {
        return $user->hasPermissionTo(Permission::AFFILIATE_BOOKING_VIEW);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, AffiliateBooking $booking): bool
    {
        return $user->hasPermissionTo(Permission::AFFILIATE_BOOKING_MANAGE);
    }

    public function delete(User $user, AffiliateBooking $booking): bool
    {
        return false;
    }

    public function restore(User $user, AffiliateBooking $booking): bool
    {
        return false;
    }

    public function forceDelete(User $user, AffiliateBooking $booking): bool
    {
        return false;
    }
}
