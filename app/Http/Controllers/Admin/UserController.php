<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TransactionService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Exception;

class UserController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->middleware('admin');
        $this->transactionService = $transactionService;
    }

    /**
     * Display users list
     */
    public function index()
    {
        $users = User::with('membershipTier', 'referrer')
            ->where('role', '!=', 'admin')
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Create new user
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|unique:users',
            'password' => 'required|min:8',
            'withdrawal_password' => 'required|digits:6',
            'role' => 'required|in:user,agent',
            'membership_tier_id' => 'required|exists:membership_tiers,id',
            'initial_points' => 'nullable|numeric|min:0',
            'referrer_code' => 'nullable|exists:users,referral_code',
        ]);

        try {
            $referrer = null;
            if ($request->referrer_code) {
                $referrer = User::where('referral_code', $request->referrer_code)->first();
            }

            $user = User::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'withdrawal_password' => Hash::make($request->withdrawal_password),
                'role' => $request->role,
                'membership_tier_id' => $request->membership_tier_id,
                'point_balance' => $request->initial_points ?? 0,
                'referrer_id' => $referrer?->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'user' => $user,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Top up user points
     */
    public function topUp(Request $request, $userId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:255',
        ]);

        try {
            $user = User::findOrFail($userId);
            $admin = auth()->user();

            $this->transactionService->topUpPoints(
                $user,
                $request->amount,
                $request->reason,
                $admin
            );

            return response()->json([
                'success' => true,
                'message' => 'Points added successfully',
                'new_balance' => $user->fresh()->point_balance,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update user status
     */
    public function updateStatus(Request $request, $userId)
    {
        $request->validate([
            'status' => 'required|in:active,suspended,banned',
        ]);

        $user = User::findOrFail($userId);
        $user->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'User status updated',
        ]);
    }

    /**
     * Update user credi
     */
    public function updateCred(Request $request, $userId)
    {
        $request->validate([
            'cp' => 'required',
        ]);

        $user = User::findOrFail($userId);
        $user->update(['cp' => $request->cp]);

        return response()->json([
            'success' => true,
            'message' => 'User Credibility updated',
        ]);
    }

    /**
     * Password Reset
     */
    public function resetPasswords($userId)
    {
        try {
            $user = User::findOrFail($userId);
            $admin = auth()->user();

            $user->update([
                'password' => Hash::make('123456789'),
                'withdrawal_password' => Hash::make('123456'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User login and withdrawal passwords have been reset',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * View user details
     */
    public function show($userId)
    {
        $user = User::with([
            'membershipTier',
            'referrer',
            'referrals',
            'tasks' => fn($q) => $q->latest()->limit(10),
            'transactions' => fn($q) => $q->latest()->limit(20),
            'taskQueues.product',
        ])->findOrFail($userId);

        $stats = [
            'total_tasks' => $user->tasks()->completed()->count(),
            'total_earned' => $user->tasks()->completed()->sum('commission_earned'),
            'pending_tasks' => $user->tasks()->pending()->count(),
            'queued_tasks' => $user->taskQueues()->queued()->count(),
            'total_referrals' => $user->referrals()->count(),
            'referral_earnings' => $user->referralEarnings()->sum('amount'),
        ];

        return view('admin.users.show', compact('user', 'stats'));
    }
}