<?php

if (!function_exists('format_points')) {
    function format_points($points)
    {
        return number_format($points, 2);
    }
}

if (!function_exists('membership_badge_color')) {
    function membership_badge_color($level)
    {
        return match($level) {
            1 => 'bg-orange-100 text-orange-800',
            2 => 'bg-gray-100 text-gray-800',
            3 => 'bg-yellow-100 text-yellow-800',
            4 => 'bg-cyan-100 text-cyan-800',
            5 => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}

if (!function_exists('status_badge_color')) {
    function status_badge_color($status)
    {
        return match($status) {
            'active' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'completed' => 'bg-blue-100 text-blue-800',
            'suspended' => 'bg-red-100 text-red-800',
            'banned' => 'bg-red-100 text-red-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            'queued' => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}

if (!function_exists('transaction_type_label')) {
    function transaction_type_label($type)
    {
        return match($type) {
            'task_commission' => 'Task Commission',
            'referral_bonus' => 'Referral Bonus',
            'admin_topup' => 'Admin Top-up',
            'membership_upgrade' => 'Membership Upgrade',
            'task_lock' => 'Task Lock',
            'task_refund' => 'Task Refund',
            'withdrawal' => 'Withdrawal',
            default => ucwords(str_replace('_', ' ', $type)),
        };
    }
}