<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // password_changed_at is intentionally null so the admin is forced
        // to set a new password on their very first login.
        Admin::firstOrCreate(
            ['email' => 'admin@murtuza.dev'],
            [
                'name'                => 'Super Admin',
                'password'            => Hash::make('password'),
                'is_super_admin'      => true,
                'is_active'           => true,
                'password_changed_at' => null,
            ]
        );
    }
}
