<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class WebsiteContentEditorSeeder extends Seeder
{
    public function run(): void
    {
        $password = null;

        DB::transaction(function () use (&$password): void {
            $user = User::query()->firstOrNew(['email' => 'visaible@nandinibali.com']);
            if ($user->exists && $user->hasRole(Role::ADMINISTRATOR)) {
                throw new RuntimeException('This email belongs to an Administrator; no changes were made.');
            }

            $permission = Permission::query()->firstOrCreate(['name' => Permission::WEBSITE_CONTENT_MANAGE]);
            $role = Role::query()->updateOrCreate(['slug' => Role::WEBSITE_CONTENT_EDITOR], ['name' => 'Website Content Editor']);
            $role->permissions()->sync([$permission->id]);
            $user->name = 'Website Content Editor';
            if (! $user->exists) {
                $user->password = $password = Str::password(32);
            }

            $user->save();
            $user->roles()->sync([$role->id]);
        });

        if ($password !== null) {
            $this->command?->info('Generated password: '.$password);
        } else {
            $this->command?->info('Existing account updated; password preserved.');
        }
    }
}
