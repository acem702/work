<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'phone' => '08098765432',
            'password' => Hash::make('password'),
            'withdrawal_password' => Hash::make('password'),
            'role' => 'admin',
            'membership_tier_id' => 1,
            'point_balance' => 0,
            'status' => 'active',]);

        // Create sample agent
        User::create([
            'name' => 'AgentDemo',
            'phone' => '09098765432',
            'password' => Hash::make('password'),
            'withdrawal_password' => Hash::make('password'),
            'role' => 'agent',
            'membership_tier_id' => 1,
            'point_balance' => 1000,
            'status' => 'active',
        ]);

        // Create sample user
        User::create([
            'name' => 'UserDemo',
            'phone' => '08109876543',
            'password' => Hash::make('password'),
            'withdrawal_password' => Hash::make('password'),
            'role' => 'user',
            'membership_tier_id' => 1,
            'point_balance' => 500,
            'status' => 'active',
        ]);
    }
}