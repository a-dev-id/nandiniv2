<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateProgramSetting extends Model
{
    public const SINGLETON_ID = 1;

    protected $fillable = [
        'program_name',
        'affiliate_commission_percentage',
        'guest_discount_percentage',
        'payment_cycle',
        'preferred_payment_method',
        'alternative_payment_method',
        'minimum_payout_amount',
        'currency',
        'review_time_message',
        'booking_engine_base_url',
        'affiliate_domain',
        'short_link_domain',
        'payout_release_days',
        'commission_validation_start_day',
        'commission_validation_end_day',
        'preferred_payment_method_requires_finance_confirmation',
        'minimum_payout_requires_finance_confirmation',
        'click_unique_window',
        'click_event_retention_days',
        'review_time_expectation_hours',
        'registration_confirmation_enabled',
        'internal_invitation_enabled',
        'approval_notification_enabled',
        'rejection_notification_enabled',
        'payment_details_needed_notification_enabled',
        'payout_paid_notification_enabled',
    ];

    protected $casts = [
        'affiliate_commission_percentage' => 'decimal:2',
        'guest_discount_percentage' => 'decimal:2',
        'minimum_payout_amount' => 'decimal:2',
        'payout_release_days' => 'integer',
        'commission_validation_start_day' => 'integer',
        'commission_validation_end_day' => 'integer',
        'preferred_payment_method_requires_finance_confirmation' => 'boolean',
        'minimum_payout_requires_finance_confirmation' => 'boolean',
        'click_event_retention_days' => 'integer',
        'review_time_expectation_hours' => 'integer',
        'registration_confirmation_enabled' => 'boolean',
        'internal_invitation_enabled' => 'boolean',
        'approval_notification_enabled' => 'boolean',
        'rejection_notification_enabled' => 'boolean',
        'payment_details_needed_notification_enabled' => 'boolean',
        'payout_paid_notification_enabled' => 'boolean',
    ];

    public static function current(): self
    {
        return self::unguarded(
            fn (): self => self::query()->firstOrCreate(
                ['id' => self::SINGLETON_ID],
                self::defaults(),
            ),
        );
    }

    public static function defaults(): array
    {
        return [
            'program_name' => 'Nandini Partner Circle',
            'affiliate_commission_percentage' => 10.00,
            'guest_discount_percentage' => 3.00,
            'payment_cycle' => 'monthly',
            'preferred_payment_method' => 'wise',
            'alternative_payment_method' => 'bank_transfer',
            'minimum_payout_amount' => 500000.00,
            'currency' => 'IDR',
            'review_time_message' => 'Your account is currently under review. The review process may take up to 48 hours.',
            'booking_engine_base_url' => 'https://nandinijunglebyhanginggardens.reserve-online.net/',
            'affiliate_domain' => config('domains.affiliate'),
            'short_link_domain' => config('domains.short_link'),
            'payout_release_days' => 30,
            'commission_validation_start_day' => 1,
            'commission_validation_end_day' => 7,
            'preferred_payment_method_requires_finance_confirmation' => true,
            'minimum_payout_requires_finance_confirmation' => true,
            'click_unique_window' => 'daily',
            'click_event_retention_days' => (int) config('affiliate-clicks.retention_days', 395),
            'review_time_expectation_hours' => 48,
            'registration_confirmation_enabled' => true,
            'internal_invitation_enabled' => true,
            'approval_notification_enabled' => true,
            'rejection_notification_enabled' => true,
            'payment_details_needed_notification_enabled' => true,
            'payout_paid_notification_enabled' => true,
        ];
    }
}
