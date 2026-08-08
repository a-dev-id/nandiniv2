<?php

use App\Models\Affiliate;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliate_password_reset_tokens', function (Blueprint $table): void {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::table('affiliates', function (Blueprint $table): void {
            $table->timestamp('email_verified_at')->nullable()->after('email');
            $table->string('password')->nullable()->after('email_verified_at');
            $table->timestamp('last_login_at')->nullable()->after('status')->index();
            $table->rememberToken();
        });

        Schema::table('affiliate_audit_events', function (Blueprint $table): void {
            $table->foreignId('actor_affiliate_id')->nullable()->after('actor_user_id')->constrained('affiliates')->nullOnDelete();
        });

        $affiliateRoleId = DB::table('roles')->where('slug', Role::AFFILIATE)->value('id');
        $legacyUserIds = [];

        DB::table('affiliates')->orderBy('id')->get()->each(function (object $affiliate) use ($affiliateRoleId, &$legacyUserIds): void {
            $user = DB::table('users')->where('id', $affiliate->user_id)->first()
                ?? DB::table('users')->where('email', $affiliate->email)->first();
            $legacyUserId = $user?->id;

            DB::table('affiliates')->where('id', $affiliate->id)->update([
                'email_verified_at' => $user?->email_verified_at,
                'password' => $user?->password ?? Hash::make(Str::random(64)),
                'remember_token' => $user?->remember_token,
            ]);

            if ($affiliateRoleId) {
                DB::table('model_has_roles')->insertOrIgnore([
                    'role_id' => $affiliateRoleId,
                    'model_type' => Affiliate::class,
                    'model_id' => $affiliate->id,
                ]);
            }

            DB::table('affiliate_audit_events')
                ->where('affiliate_id', $affiliate->id)
                ->where('actor_user_id', $legacyUserId)
                ->whereIn('event', ['affiliate_payment_profile.created', 'affiliate_payment_profile.updated'])
                ->update([
                    'actor_user_id' => null,
                    'actor_affiliate_id' => $affiliate->id,
                ]);

            if ($legacyUserId) {
                $legacyUserIds[] = (int) $legacyUserId;
            }
        });

        Schema::table('affiliates', function (Blueprint $table): void {
            $table->dropUnique(['user_id']);
            $table->dropConstrainedForeignId('user_id');
        });

        if ($affiliateRoleId && $legacyUserIds !== []) {
            DB::table('model_has_roles')
                ->where('role_id', $affiliateRoleId)
                ->where('model_type', 'App\\Models\\User')
                ->whereIn('model_id', $legacyUserIds)
                ->delete();

            $affiliateOnlyUserIds = collect($legacyUserIds)
                ->unique()
                ->filter(fn (int $userId): bool => ! DB::table('model_has_roles')
                    ->where('model_type', 'App\\Models\\User')
                    ->where('model_id', $userId)
                    ->exists())
                ->values()
                ->all();

            if ($affiliateOnlyUserIds !== []) {
                DB::table('sessions')->whereIn('user_id', $affiliateOnlyUserIds)->delete();
                DB::table('users')->whereIn('id', $affiliateOnlyUserIds)->delete();
            }
        }
    }

    public function down(): void
    {
        Schema::table('affiliates', function (Blueprint $table): void {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->restrictOnDelete();
        });

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

            DB::table('affiliates')->where('id', $affiliate->id)->update(['user_id' => $userId]);

            if ($affiliateRoleId) {
                DB::table('model_has_roles')->insertOrIgnore([
                    'role_id' => $affiliateRoleId,
                    'model_type' => 'App\\Models\\User',
                    'model_id' => $userId,
                ]);
            }
        });

        DB::table('model_has_roles')->where('model_type', Affiliate::class)->delete();

        Schema::table('affiliates', function (Blueprint $table): void {
            $table->unique('user_id');
        });

        Schema::table('affiliate_audit_events', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('actor_affiliate_id');
        });

        Schema::table('affiliates', function (Blueprint $table): void {
            $table->dropIndex(['last_login_at']);
            $table->dropColumn(['email_verified_at', 'password', 'last_login_at', 'remember_token']);
        });

        Schema::dropIfExists('affiliate_password_reset_tokens');
    }
};
