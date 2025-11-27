<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReferralSetting;

class ReferralSettingSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            ['level' => 1, 'commission_percentage' => 10.00], // 10% from direct referrals
            ['level' => 2, 'commission_percentage' => 5.00],  // 5% from 2nd level
            ['level' => 3, 'commission_percentage' => 2.00],  // 2% from 3rd level
        ];

        foreach ($settings as $setting) {
            ReferralSetting::create($setting);
        }
    }
}