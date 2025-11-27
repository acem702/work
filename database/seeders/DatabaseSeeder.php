<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            MembershipTierSeeder::class,
            ReferralSettingSeeder::class,
            AdminUserSeeder::class,
            ProductSeeder::class,
        ]);
    }
}