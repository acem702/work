<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        return view("account.index");
    }

    public function recharge()
    {
        return view("recharge.index");
    }

    /**
     * Update user's login password.
     */
    public function updatePassword(Request $request)
    {
        // Add logging to debug
        Log::info('Password update attempt', [
            'user_id' => auth()->id(),
            'has_current' => !empty($request->current_password),
            'has_new' => !empty($request->password),
        ]);

        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.required' => 'Current password is required.',
            'password.required' => 'New password is required.',
            'password.min' => 'New password must be at least 6 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        $user = auth()->user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            Log::warning('Current password verification failed', ['user_id' => $user->id]);
            return response()->json([
                'success' => false,
                'errors' => ['current_password' => ['The current password is incorrect.']]
            ], 422);
        }

        // Check if new password is same as current
        if (Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'errors' => ['password' => ['New password must be different from current password.']]
            ], 422);
        }

        // Update password
        $updated = $user->update([
            'password' => Hash::make($request->password)
        ]);

        if ($updated) {
            Log::info('Password updated successfully', ['user_id' => $user->id]);
            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully.'
            ]);
        }

        return response()->json([
            'success' => false,
            'errors' => ['password' => ['Failed to update password.']]
        ], 500);
    }

    /**
     * Update user's withdrawal password.
     */
    public function updateWithdrawalPassword(Request $request)
    {
        Log::info('Withdrawal password update attempt', [
            'user_id' => auth()->id(),
            'has_current' => !empty($request->current_withdrawal_password),
            'has_new' => !empty($request->withdrawal_password),
        ]);

        $request->validate([
            'current_withdrawal_password' => ['required', 'string', 'digits:6'],
            'withdrawal_password' => ['required', 'string', 'digits:6', 'confirmed', 'regex:/^[0-9]{6}$/'],
        ], [
            'current_withdrawal_password.required' => 'Current withdrawal password is required.',
            'current_withdrawal_password.digits' => 'Current withdrawal password must be 6 digits.',
            'withdrawal_password.required' => 'New withdrawal password is required.',
            'withdrawal_password.digits' => 'New withdrawal password must be exactly 6 digits.',
            'withdrawal_password.regex' => 'Withdrawal password must contain only numbers.',
            'withdrawal_password.confirmed' => 'Withdrawal password confirmation does not match.',
        ]);

        $user = auth()->user();

        // Verify current withdrawal password
        if (!Hash::check($request->current_withdrawal_password, $user->withdrawal_password)) {
            Log::warning('Current withdrawal password verification failed', ['user_id' => $user->id]);
            return response()->json([
                'success' => false,
                'errors' => ['current_withdrawal_password' => ['The current withdrawal password is incorrect.']]
            ], 422);
        }

        // Check if new password is same as current
        if (Hash::check($request->withdrawal_password, $user->withdrawal_password)) {
            return response()->json([
                'success' => false,
                'errors' => ['withdrawal_password' => ['New withdrawal password must be different from current password.']]
            ], 422);
        }

        // Update withdrawal password
        $updated = $user->update([
            'withdrawal_password' => Hash::make($request->withdrawal_password)
        ]);

        if ($updated) {
            Log::info('Withdrawal password updated successfully', ['user_id' => $user->id]);
            return response()->json([
                'success' => true,
                'message' => 'Withdrawal password updated successfully.'
            ]);
        }

        return response()->json([
            'success' => false,
            'errors' => ['withdrawal_password' => ['Failed to update withdrawal password.']]
        ], 500);
    }

    /**
     * Show bind wallet page.
     */
    public function bindWallet()
    {
        return view('withdraw.bind');
    }

    /**
     * Show withdraw page.
     */
    public function showWithdraw()
    {
        $withdrawals = auth()->user()->withdrawals()
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('withdraw.index', compact('withdrawals'));
    }

    /**
     * Update withdrawal method.
     */
    public function updateWithdrawlMethod(Request $request)
    {
        Log::info('Withdrawal method update attempt', [
            'user_id' => auth()->id(),
            'exchanger' => $request->exchanger,
            'has_address' => !empty($request->withdrawal_address),
        ]);

        $request->validate([
            'exchanger' => ['required', 'string', 'max:100'],
            'withdrawal_address' => ['required', 'string', 'max:255'],
        ], [
            'exchanger.required' => 'Please select an exchanger.',
            'withdrawal_address.required' => 'Withdrawal address is required.',
            'withdrawal_address.max' => 'Withdrawal address is too long.',
        ]);

        $user = auth()->user();

        // Check if user model has these fields in $fillable
        if (!in_array('exchanger', $user->getFillable()) || !in_array('withdrawal_address', $user->getFillable())) {
            Log::error('Fields not fillable', [
                'fillable' => $user->getFillable()
            ]);
            return response()->json([
                'success' => false,
                'errors' => ['error' => ['Configuration error. Please contact support.']]
            ], 500);
        }

        // Update withdrawal method
        $updated = $user->update([
            'exchanger' => $request->exchanger,
            'withdrawal_address' => $request->withdrawal_address,
        ]);

        if ($updated) {
            Log::info('Withdrawal method updated successfully', [
                'user_id' => $user->id,
                'exchanger' => $user->exchanger,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Withdrawal method updated successfully.'
            ]);
        }

        return response()->json([
            'success' => false,
            'errors' => ['error' => ['Failed to update withdrawal method.']]
        ], 500);
    }
}
