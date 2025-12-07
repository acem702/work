<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\TaskQueueController as AdminTaskQueueController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\PageViewController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WithdrawalController;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Registration Routes
Route::get('/register', action: [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// User routes (authenticated)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        // Redirect admin to admin dashboard
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        
        return view('dashboard');
    })->name('dashboard');

    Route::get('/account', [UserController::class,'index'])->name('account');
    Route::get('/recharge', [UserController::class,'recharge'])->name('recharge');

    Route::get('/api/tasks/stats', [TaskController::class, 'stats'])->name('api.tasks.stats');

    // Tasks
    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('index');
        Route::get('/orders', [TaskController::class, 'orders'])->name('orders'); // New route
        Route::get('/history', [TaskController::class, 'history'])->name('history'); // API for history
        Route::get('/next', [TaskController::class, 'getNext'])->name('next');
        Route::post('/start', [TaskController::class, 'start'])->name('start');
        Route::post('/submit', [TaskController::class, 'submit'])->name('submit');
        Route::post('/cancel', [TaskController::class, 'cancel'])->name('cancel');
    });

    // Membership
    Route::prefix('membership')->name('membership.')->group(function () {
        Route::get('/', [MembershipController::class, 'index'])->name('index');
        Route::get('/tiers', [MembershipController::class, 'tiers'])->name('tiers');
        Route::post('/upgrade', [MembershipController::class, 'upgrade'])->name('upgrade');
    });

    // Transactions
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::get('/history', [TransactionController::class, 'history'])->name('history');
    });

    // Referrals
    Route::prefix('referrals')->name('referrals.')->group(function () {
        Route::get('/', [ReferralController::class, 'index'])->name('index');
        Route::get('/data', [ReferralController::class, 'data'])->name('data');
    });

    // Password Management
    Route::post('/settings/password/update', [UserController::class, 'updatePassword'])->name('password.update');
    Route::post('/settings/withdrawal-password/update', [UserController::class, 'updateWithdrawalPassword'])->name('withdrawal-password.update');
    
    // Account
    Route::get('/account', [UserController::class, 'index'])->name('account.index');
    Route::post('/account/password/update', [UserController::class, 'updatePassword'])->name('password.update');
    Route::post('/account/withdrawal-password/update', [UserController::class, 'updateWithdrawalPassword'])->name('withdrawal-password.update');
    
    // Withdrawal Method
    Route::get('/withdraw/bind', [UserController::class, 'bindWallet'])->name('withdrawal-method.index');
    Route::post('/withdraw/bind', [UserController::class, 'updateWithdrawlMethod'])->name('withdrawal-method.update');
    
    // Withdrawals
    Route::get('/withdraw', [UserController::class, 'showWithdraw'])->name('withdrawals.index');
    Route::post('/withdraw', [WithdrawalController::class, 'store'])->name('withdrawals.store');

    // Public Page Routes
    Route::get('/pages/{slug}', [PageViewController::class, 'show'])->name('pages.show');

});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Users management
    Route::resource('users', AdminUserController::class);
    Route::post('users/{user}/topup', [AdminUserController::class, 'topUp'])->name('users.topup');
    Route::post('users/{user}/status', [AdminUserController::class, 'updateStatus'])->name('users.status');

    // Products management
    Route::resource('products', AdminProductController::class);
    Route::resource('pages', AdminPageController::class);

    Route::prefix('combo-tasks')->name('combo-tasks.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ComboTaskController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Admin\ComboTaskController::class, 'store'])->name('store');
        Route::get('/{comboTask}', [App\Http\Controllers\Admin\ComboTaskController::class, 'show'])->name('show');
        Route::put('/{comboTask}', [App\Http\Controllers\Admin\ComboTaskController::class, 'update'])->name('update');
        Route::delete('/{comboTask}', [App\Http\Controllers\Admin\ComboTaskController::class, 'destroy'])->name('destroy');
        Route::post('/{comboTask}/toggle-status', [App\Http\Controllers\Admin\ComboTaskController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Task Queue management
    Route::prefix('task-queue')->name('task-queue.')->group(function () {
        Route::get('/', [AdminTaskQueueController::class, 'index'])->name('index');
        Route::post('/assign-user', [AdminTaskQueueController::class, 'assignToUser'])->name('assign.user');
        Route::post('/assign-users', [AdminTaskQueueController::class, 'assignToMultipleUsers'])->name('assign.users');
        Route::post('/assign-tier', [AdminTaskQueueController::class, 'assignToTier'])->name('assign.tier');
        Route::get('/user/{user}', [AdminTaskQueueController::class, 'userQueue'])->name('user.queue');
        Route::delete('/{taskQueue}', [AdminTaskQueueController::class, 'destroy'])->name('destroy');
    });
    
    // Membership Tiers
    Route::prefix('membership-tiers')->name('membership-tiers.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\MembershipTierController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Admin\MembershipTierController::class, 'store'])->name('store');
        Route::put('/{membershipTier}', [App\Http\Controllers\Admin\MembershipTierController::class, 'update'])->name('update');
        Route::delete('/{membershipTier}', [App\Http\Controllers\Admin\MembershipTierController::class, 'destroy'])->name('destroy');
        Route::post('/{membershipTier}/toggle-status', [App\Http\Controllers\Admin\MembershipTierController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Task Queue - Remove from queue
    Route::delete('task-queue/{taskQueue}', function(App\Models\TaskQueue $taskQueue) {
        if ($taskQueue->status !== 'queued') {
            return response()->json(['success' => false, 'message' => 'Can only remove queued tasks'], 400);
        }
        $taskQueue->delete();
        return response()->json(['success' => true, 'message' => 'Task removed from queue']);
    })->name('task-queue.destroy');
});