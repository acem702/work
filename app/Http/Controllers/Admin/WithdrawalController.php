<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WithdrawalController extends Controller
{
    /**
     * Display all withdrawal requests
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        
        $query = Withdrawal::with('user')->latest();
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $withdrawals = $query->paginate(20);
        
        $stats = [
            'pending_count' => Withdrawal::where('status', 'pending')->count(),
            'pending_amount' => Withdrawal::where('status', 'pending')->sum('amount'),
            'completed_today' => Withdrawal::where('status', 'completed')
                ->whereDate('processed_at', today())
                ->count(),
            'completed_today_amount' => Withdrawal::where('status', 'completed')
                ->whereDate('processed_at', today())
                ->sum('amount'),
        ];
        
        return view('admin.withdrawals.index', compact('withdrawals', 'stats', 'status'));
    }

    /**
     * Approve withdrawal request
     */
    public function approve(Request $request, Withdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending withdrawals can be approved.'
            ], 400);
        }

        DB::beginTransaction();

        try {
            $withdrawal->update([
                'status' => 'completed',
                'processed_at' => now(),
                'admin_note' => $request->admin_note ?? 'Approved and processed',
            ]);

            DB::commit();

            Log::info('Withdrawal approved', [
                'withdrawal_id' => $withdrawal->id,
                'user_id' => $withdrawal->user_id,
                'amount' => $withdrawal->amount,
                'admin_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal approved successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Withdrawal approval failed', [
                'withdrawal_id' => $withdrawal->id,
                'error' => $e->getMessage(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve withdrawal.'
            ], 500);
        }
    }

    /**
     * Reject withdrawal request (refund to commission balance)
     */
    public function reject(Request $request, Withdrawal $withdrawal)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        if ($withdrawal->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending withdrawals can be rejected.'
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Refund commission balance to user
            $user = $withdrawal->user;
            $user->increment('point_balance', $withdrawal->amount);

            // Update withdrawal status
            $withdrawal->update([
                'status' => 'rejected',
                'processed_at' => now(),
                'admin_note' => $request->reason,
            ]);

            DB::commit();

            Log::info('Withdrawal rejected', [
                'withdrawal_id' => $withdrawal->id,
                'user_id' => $withdrawal->user_id,
                'amount' => $withdrawal->amount,
                'reason' => $request->reason,
                'admin_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal rejected and amount refunded to user.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Withdrawal rejection failed', [
                'withdrawal_id' => $withdrawal->id,
                'error' => $e->getMessage(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject withdrawal.'
            ], 500);
        }
    }
}