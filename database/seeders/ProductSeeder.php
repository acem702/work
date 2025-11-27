<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            // Bronze level products
            [
                'name' => 'Basic Task 1',
                'slug' => 'basic-task-1',
                'description' => 'Simple task for beginners',
                'base_points' => 50,
                'base_commission' => 10,
                'min_membership_tier_id' => 1,
            ],
            [
                'name' => 'Basic Task 2',
                'slug' => 'basic-task-2',
                'description' => 'Another simple task',
                'base_points' => 75,
                'base_commission' => 15,
                'min_membership_tier_id' => 1,
            ],
            
            // Silver level products
            [
                'name' => 'Intermediate Task 1',
                'slug' => 'intermediate-task-1',
                'description' => 'Task for silver members',
                'base_points' => 150,
                'base_commission' => 30,
                'min_membership_tier_id' => 2,
            ],
            [
                'name' => 'Intermediate Task 2',
                'slug' => 'intermediate-task-2',
                'description' => 'Another silver level task',
                'base_points' => 200,
                'base_commission' => 45,
                'min_membership_tier_id' => 2,
            ],
            
            // Gold level products
            [
                'name' => 'Advanced Task 1',
                'slug' => 'advanced-task-1',
                'description' => 'Task for gold members',
                'base_points' => 300,
                'base_commission' => 75,
                'min_membership_tier_id' => 3,
            ],
            [
                'name' => 'Advanced Task 2',
                'slug' => 'advanced-task-2',
                'description' => 'High value gold task',
                'base_points' => 400,
                'base_commission' => 100,
                'min_membership_tier_id' => 3,
            ],
            
            // Platinum level products
            [
                'name' => 'Premium Task 1',
                'slug' => 'premium-task-1',
                'description' => 'Platinum member exclusive',
                'base_points' => 600,
                'base_commission' => 150,
                'min_membership_tier_id' => 4,
            ],
            [
                'name' => 'Premium Task 2',
                'slug' => 'premium-task-2',
                'description' => 'High value platinum task',
                'base_points' => 800,
                'base_commission' => 200,
                'min_membership_tier_id' => 4,
            ],
            
            // Diamond level products
            [
                'name' => 'Elite Task 1',
                'slug' => 'elite-task-1',
                'description' => 'Diamond member exclusive',
                'base_points' => 1000,
                'base_commission' => 300,
                'min_membership_tier_id' => 5,
            ],
            [
                'name' => 'Elite Task 2',
                'slug' => 'elite-task-2',
                'description' => 'Top tier diamond task',
                'base_points' => 1500,
                'base_commission' => 500,
                'min_membership_tier_id' => 5,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}