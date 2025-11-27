<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display transactions page
     */
    public function index()
    {
        return view('transactions.index');
    }

    /**
     * Get transaction history (API endpoint)
     */
    public function history()
    {
        $user = auth()->user();
        
        $transactions = $user->transactions()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($transaction) {
                // Determine transaction type and format
                $type = 'commission'; // default
                $commissionType = null;
                $status = null;
                
                switch ($transaction->type) {
                    case 'admin_topup':
                        $type = 'recharge';
                        $status = 'SUCCESS';
                        break;
                        
                    case 'withdrawal':
                        $type = 'withdrawal';
                        $status = 'SUCCESS';
                        break;
                        
                    case 'task_commission':
                        $type = 'commission';
                        $commissionType = 'PROFIT';
                        break;
                        
                    case 'referral_bonus':
                        $type = 'commission';
                        $commissionType = 'REFERRAL BONUS';
                        break;
                        
                    case 'task_refund':
                        $type = 'commission';
                        $commissionType = 'COMPLETE AND RETURN OF CAPITAL';
                        break;
                        
                    case 'task_lock':
                        $type = 'commission';
                        $commissionType = 'SUBMIT THE TASKS';
                        break;
                }
                
                return [
                    'id' => $transaction->id,
                    'type' => $type,
                    'order_number' => $transaction->id . date('YmdHis', strtotime($transaction->created_at)) . rand(1000, 9999),
                    'amount' => number_format($transaction->amount, 2),
                    'status' => $status,
                    'commission_type' => $commissionType,
                    'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                ];
            });
        
        return response()->json([
            'success' => true,
            'transactions' => $transactions,
        ]);
    }
}