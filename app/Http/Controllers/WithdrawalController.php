<?php

namespace App\Http\Controllers;

use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WithdrawalController extends Controller
{
    /**
     * Process withdrawal request.
     */
    public function store(Request $request)
    {
        Log::info('Withdrawal request attempt', [
            'user_id' => auth()->id(),
            'amount' => $request->amount,
        ]);

        $request->validate([
            'amount' => ['required', 'numeric', 'min:10', 'max:100000'],
            'withdrawal_password' => ['required', 'string', 'digits:6'],
        ], [
            'amount.required' => 'Please enter withdrawal amount.',
            'amount.numeric' => 'Amount must be a valid number.',
            'amount.min' => 'Minimum withdrawal amount is $10.',
            'amount.max' => 'Maximum withdrawal amount is $100,000.',
            'withdrawal_password.required' => 'Withdrawal password is required.',
            'withdrawal_password.digits' => 'Withdrawal password must be 6 digits.',
        ]);

        $user = auth()->user();

        // Check if withdrawal method is set
        if (empty($user->withdrawal_address) || empty($user->exchanger)) {
            Log::warning('Withdrawal method not set', ['user_id' => $user->id]);
            return response()->json([
                'success' => false,
                'errors' => ['withdrawal_method' => ['Please set up your withdrawal method first.']]
            ], 422);
        }

        // Verify withdrawal password
        if (!Hash::check($request->withdrawal_password, $user->withdrawal_password)) {
            Log::warning('Incorrect withdrawal password', ['user_id' => $user->id]);
            return response()->json([
                'success' => false,
                'errors' => ['withdrawal_password' => ['Incorrect withdrawal password.']]
            ], 422);
        }

        // Check if user has sufficient balance
        if ($user->point_balance < $request->amount) {
            Log::warning('Insufficient balance', [
                'user_id' => $user->id,
                'balance' => $user->point_balance,
                'requested' => $request->amount
            ]);
            return response()->json([
                'success' => false,
                'errors' => ['amount' => ['Insufficient balance. Your available balance is $' . number_format($user->point_balance, 2)]]
            ], 422);
        }

        // Check if user has credibility score to withdraw
        if ($user->cp < 80) {
            Log::warning('Insufficient credibility score', [
                'user_id' => $user->id,
                'credibility_score' => $user->cp
            ]);
            return response()->json([
                'success' => false,
                'errors' => ['amount' => ['Insufficient credibility score. Your current credibility score is ' . $user->cp . '. Please contact customer service.']]
            ], status: 422);
        }

        // Check for pending withdrawals
        $pendingWithdrawals = Withdrawal::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        if ($pendingWithdrawals > 0) {
            Log::warning('Pending withdrawal exists', ['user_id' => $user->id]);
            return response()->json([
                'success' => false,
                'errors' => ['withdrawal' => ['You have a pending withdrawal request. Please wait for it to be processed.']]
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Deduct from user balance
            $user->decrement('point_balance', $request->amount);

            // Create withdrawal record
            $withdrawal = Withdrawal::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'exchanger' => $user->exchanger,
                'withdrawal_address' => $user->withdrawal_address,
                'status' => 'pending',
                'requested_at' => now(),
            ]);

            DB::commit();

            Log::info('Withdrawal created successfully', [
                'user_id' => $user->id,
                'withdrawal_id' => $withdrawal->id,
                'amount' => $withdrawal->amount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal request submitted successfully. Your request will be processed within 24-48 hours.',
                'data' => [
                    'withdrawal_id' => $withdrawal->id,
                    'new_balance' => $user->fresh()->point_balance,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Withdrawal creation failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'errors' => ['withdrawal' => ['Failed to process withdrawal request. Please try again.']]
            ], 500);
        }
    }
}