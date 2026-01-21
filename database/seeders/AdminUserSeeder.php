<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
   public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@msi-ecs.com.ph'],
            [
                'name'     => 'System Admin',
                'password' => Hash::make('iSupportUAT2025!'),
                'role'     => '1',
            ]
        );
    }
}
