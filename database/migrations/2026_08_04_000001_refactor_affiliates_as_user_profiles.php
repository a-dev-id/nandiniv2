<?php

use App\Enums\AffiliateRegistrationSource;
use App\Enums\AffiliateStatus;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('affiliate_program_settings', function (Blueprint $table): void {
            $table->renameColumn('minimum_payout', 'minimum_payout_amount');
            $table->text('review_time_message')->nullable()->after('currency');
            $table->string('booking_engine_base_url')->nullable()->after('review_time_message');
            $table->string('affiliate_domain')->nullable()->after('booking_engine_base_url');
            $table->string('short_link_domain')->nullable()->after('affiliate_domain');
        });

        Schema::table('affiliates', function (Blueprint $table): void {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->restrictOnDelete();
            $table->string('phone_whatsapp')->nullable()->after('email')->index();
            $table->string('instagram')->nullable()->after('phone_whatsapp');
            $table->string('facebook')->nullable()->after('instagram');
            $table->string('tiktok')->nullable()->after('facebook');
            $table->string('x')->nullable()->after('tiktok');
            $table->string('threads')->nullable()->after('x');
            $table->string('registration_source')->default(AffiliateRegistrationSource::CreatedByNandini->value)->after('status')->index();
            $table->foreignId('created_by')->nullable()->after('registration_source')->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by')->index();
            $table->foreignId('rejected_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable()->after('rejected_by')->index();
            $table->foreignId('suspended_by')->nullable()->after('rejected_at')->constrained('users')->nullOnDelete();
            $table->timestamp('suspended_at')->nullable()->after('suspended_by')->index();
            $table->text('status_note')->nullable()->after('suspended_at');
            $table->string('affiliate_code')->nullable()->after('status_note')->unique();
            $table->timestamp('affiliate_code_generated_at')->nullable()->after('affiliate_code');
            $table->string('short_link_slug')->nullable()->after('affiliate_code_generated_at')->unique();
            $table->timestamp('short_link_activated_at')->nullable()->after('short_link_slug');
        });

        DB::transaction(function (): void {
            $affiliateRoleId = DB::table('roles')->where('slug', Role::AFFILIATE)->value('id');

            DB::table('affiliates')->orderBy('id')->get()->each(function (object $affiliate) use ($affiliateRoleId): void {
                $userId = DB::table('users')->where('email', $affiliate->email)->value('id');

                if (! $userId) {
                    $userId = DB::table('users')->insertGetId([
                        'name' => $affiliate->name,
                        'email' => $affiliate->email,
                        'email_verified_at' => $affiliate->email_verified_at,
                        'password' => $affiliate->password,
                        'remember_token' => $affiliate->remember_token,
                        'created_at' => $affiliate->created_at,
                        'updated_at' => $affiliate->updated_at,
                    ]);
                }

                $status = $affiliate->status === 'active'
                    ? AffiliateStatus::Approved->value
                    : $affiliate->status;

                DB::table('affiliates')->where('id', $affiliate->id)->update([
                    'user_id' => $userId,
                    'status' => $status,
                    'registration_source' => AffiliateRegistrationSource::CreatedByNandini->value,
                ]);

                DB::table('model_has_roles')
                    ->where('model_type', 'App\\Models\\Affiliate')
                    ->where('model_id', $affiliate->id)
                    ->get()
                    ->each(function (object $assignment) use ($userId): void {
                        DB::table('model_has_roles')->insertOrIgnore([
                            'role_id' => $assignment->role_id,
                            'model_type' => 'App\\Models\\User',
                            'model_id' => $userId,
                        ]);
                    });

                if ($affiliateRoleId) {
                    DB::table('model_has_roles')->insertOrIgnore([
                        'role_id' => $affiliateRoleId,
                        'model_type' => 'App\\Models\\User',
                        'model_id' => $userId,
                    ]);
                }
            });

            DB::table('model_has_roles')->where('model_type', 'App\\Models\\Affiliate')->delete();
        });

        Schema::table('affiliates', function (Blueprint $table): void {
            $table->foreignId('user_id')->nullable(false)->change();
            $table->unique('user_id');
            $table->dropIndex(['last_login_at']);
            $table->dropColumn(['email_verified_at', 'password', 'last_login_at', 'remember_token']);
        });
    }

    public function down(): void
    {
        Schema::table('affiliates', function (Blueprint $table): void {
            $table->timestamp('email_verified_at')->nullable()->after('email');
            $table->string('password')->nullable()->after('email_verified_at');
            $table->timestamp('last_login_at')->nullable()->after('status')->index();
            $table->rememberToken();
        });

        DB::table('affiliates')->orderBy('id')->get()->each(function (object $affiliate): void {
            $user = DB::table('users')->where('id', $affiliate->user_id)->first();

            DB::table('affiliates')->where('id', $affiliate->id)->update([
                'email_verified_at' => $user?->email_verified_at,
                'password' => $user?->password,
                'status' => $affiliate->status === AffiliateStatus::Approved->value ? 'active' : $affiliate->status,
            ]);
        });

        Schema::table('affiliates', function (Blueprint $table): void {
            $table->string('password')->nullable(false)->change();
            $table->dropUnique(['user_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['rejected_by']);
            $table->dropForeign(['suspended_by']);
            $table->dropColumn([
                'user_id',
                'phone_whatsapp',
                'instagram',
                'facebook',
                'tiktok',
                'x',
                'threads',
                'registration_source',
                'created_by',
                'approved_by',
                'approved_at',
                'rejected_by',
                'rejected_at',
                'suspended_by',
                'suspended_at',
                'status_note',
                'affiliate_code',
                'affiliate_code_generated_at',
                'short_link_slug',
                'short_link_activated_at',
            ]);
        });

        Schema::table('affiliate_program_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'review_time_message',
                'booking_engine_base_url',
                'affiliate_domain',
                'short_link_domain',
            ]);
            $table->renameColumn('minimum_payout_amount', 'minimum_payout');
        });
    }
};
