<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MembershipTier;

class MembershipTierSeeder extends Seeder
{
    public function run()
    {
        $tiers = [
            [
                'name' => 'Bronze',
                'slug' => 'bronze',
                'level' => 1,
                'daily_task_limit' => 5,
                'commission_multiplier' => 1.00,
                'upgrade_cost' => 0,
                'description' => 'Starter membership',
            ],
            [
                'name' => 'Silver',
                'slug' => 'silver',
                'level' => 2,
                'daily_task_limit' => 10,
                'commission_multiplier' => 1.20,
                'upgrade_cost' => 500,
                'description' => 'Intermediate membership with 20% bonus',
            ],
            [
                'name' => 'Gold',
                'slug' => 'gold',
                'level' => 3,
                'daily_task_limit' => 20,
                'commission_multiplier' => 1.50,
                'upgrade_cost' => 1500,
                'description' => 'Advanced membership with 50% bonus',
            ],
            [
                'name' => 'Platinum',
                'slug' => 'platinum',
                'level' => 4,
                'daily_task_limit' => 35,
                'commission_multiplier' => 2.00,
                'upgrade_cost' => 3000,
                'description' => 'Premium membership with 100% bonus',
            ],
            [
                'name' => 'Diamond',
                'slug' => 'diamond',
                'level' => 5,
                'daily_task_limit' => 50,
                'commission_multiplier' => 3.00,
                'upgrade_cost' => 6000,
                'description' => 'Elite membership with 200% bonus',
            ],
        ];

        foreach ($tiers as $tier) {
            MembershipTier::create($tier);
        }
    }
}