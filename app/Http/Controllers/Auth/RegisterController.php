<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request.
     */
    public function register(Request $request)
    {
        // Validate the request
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation', 'withdrawal_password'));
        }

        // Create the user
        $user = $this->create($request->all());

        // Fire the registered event
        event(new Registered($user));

        // Log the user in
        auth()->login($user);

        // Redirect to dashboard with success message
        return redirect()->route('dashboard')
            ->with('success', 'Welcome! Your account has been created successfully.');
    }

    /**
     * Get a validator for an incoming registration request.
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:users',
                'alpha_dash', // Only letters, numbers, dashes and underscores
                'regex:/^[a-zA-Z0-9_-]+$/'
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                'unique:users',
                'regex:/^[0-9+\-\s()]+$/' // Allow numbers, +, -, spaces, and parentheses
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed' // This checks password_confirmation matches
            ],
            'withdrawal_password' => [
                'required',
                'string',
                'digits:6', // Must be exactly 6 digits
                'regex:/^[0-9]{6}$/'
            ],
            'referral_code' => [
                'required',
                'string',
                'max:50',
                'exists:users,referral_code' // Check if referral code exists
            ],
            'agreement' => [
                'required',
                'accepted' // Must be true, yes, on, or 1
            ]
        ], [
            // Custom error messages
            'name.required' => 'Username is required.',
            'name.unique' => 'This username is already taken.',
            'name.alpha_dash' => 'Username may only contain letters, numbers, dashes and underscores.',
            'phone.required' => 'Phone number is required.',
            'phone.unique' => 'This phone number is already registered.',
            'phone.regex' => 'Please enter a valid phone number.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'withdrawal_password.required' => 'Withdrawal password is required.',
            'withdrawal_password.digits' => 'Withdrawal password must be exactly 6 digits.',
            'withdrawal_password.regex' => 'Withdrawal password must contain only numbers.',
            'referral_code.exists' => 'Invalid referral code.',
            'agreement.required' => 'You must agree to the terms and conditions.',
            'agreement.accepted' => 'You must agree to the terms and conditions.'
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     */
    protected function create(array $data)
    {
        DB::beginTransaction();

        try {
            // Find referrer if referral code provided
            $referrer = null;
            if (!empty($data['referral_code'])) {
                $referrer = User::where('referral_code', $data['referral_code'])->first();
            }

            // Create the user
            $user = User::create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'withdrawal_password' => Hash::make($data['withdrawal_password']),
                'referrer_id' => $referrer ? $referrer->id : null,
                'membership_tier_id' => 1, // Default membership tier
            ]);

            DB::commit();

            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

}