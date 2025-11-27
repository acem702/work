<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Task;
use App\Models\Transaction;
use App\Models\MembershipTier;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Overview stats
        $stats = [
            'total_users' => User::where('role', '!=', 'admin')->count(),
            'active_users' => User::where('role', '!=', 'admin')->where('status', 'active')->count(),
            'total_agents' => User::where('role', 'agent')->count(),
            'total_products' => Product::count(),
            'active_products' => Product::active()->count(),
            'total_tasks_completed' => Task::completed()->count(),
            'total_tasks_pending' => Task::pending()->count(),
            'total_points_distributed' => Transaction::where('type', 'task_commission')->sum('amount'),
            'total_referral_earnings' => Transaction::where('type', 'referral_bonus')->sum('amount'),
        ];

        // Recent activities
        $recentTasks = Task::with(['user', 'product'])
            ->latest()
            ->limit(10)
            ->get();

        $recentTransactions = Transaction::with('user')
            ->latest()
            ->limit(10)
            ->get();

        $recentUsers = User::where('role', '!=', 'admin')
            ->with('membershipTier')
            ->latest()
            ->limit(10)
            ->get();

        // Membership distribution
        $membershipDistribution = User::where('role', '!=', 'admin')
            ->select('membership_tier_id', DB::raw('count(*) as count'))
            ->groupBy('membership_tier_id')
            ->with('membershipTier')
            ->get();

        // Top performers
        $topUsers = User::where('role', '!=', 'admin')
            ->withCount(['tasks as completed_tasks' => function ($q) {
                $q->where('status', 'completed');
            }])
            ->withSum(['tasks as total_earned' => function ($q) {
                $q->where('status', 'completed');
            }], 'commission_earned')
            ->orderByDesc('total_earned')
            ->limit(10)
            ->get();

        // Daily task stats (last 7 days)
        $dailyStats = Task::where('completed_at', '>=', now()->subDays(7))
            ->where('status', 'completed')
            ->select(
                DB::raw('DATE(completed_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(commission_earned) as total_commission')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentTasks',
            'recentTransactions',
            'recentUsers',
            'membershipDistribution',
            'topUsers',
            'dailyStats'
        ));
    }
}