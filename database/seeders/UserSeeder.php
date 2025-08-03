<?php


namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Ne crée l'utilisateur que s'il n'existe pas déjà
        User::firstOrCreate(
            ['email' => 'balele@gmail.com'], // Condition de recherche
            [
                'name' => 'Test User',
                'password' => Hash::make('password123'),
                'user_phone' => '0700000000',
                'device_id' => 'dummyDeviceId123456',
                'user_image' => 'N/A',
                'user_city' => 1,
                'user_area' => 1,
                'status' => 1,
                'wallet' => 0,
                'rewards' => 0,
                'is_verified' => 1,
                'block' => 0,
                'reg_date' => now()->toDateString(),
                'app_update' => 1,
                'referral_code' => 'TESTCODE',
                'membership' => 0,

            ]
        );
    }
}
