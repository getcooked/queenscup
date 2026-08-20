<?php

use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Inventory;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CustomerAccountController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PointOfSaleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Api\StaffReservationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\QueryException;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

$ordersView = function (Request $request) {
    $authenticatedStaff = null;
    $staffId = $request->session()->get('staff_user_id');

    if ($staffId) {
        try {
            $staff = User::whereIn('role', ['admin', 'cashier'])->find($staffId);

            if ($staff) {
                $authenticatedStaff = [
                    'id' => $staff->id,
                    'username' => $staff->email,
                    'role' => $staff->role,
                    'fullName' => $staff->name,
                    'email' => $staff->email,
                ];
            }
        } catch (QueryException $exception) {
            // The public ordering page can still load while the database is unavailable.
        }
    }

    $manualImages = [
        'bananush milktea' => 'bananush-milktea.png',
        'brown sugar milktea' => 'brown-sugar-milktea.png',
        'brulee milktea' => 'brulee-milktea.png',
        'classic milktea' => 'classic-milktea.png',
        'green apple milky fruit jam' => 'green-apple-milky-fruit-jam.png',
        'guava dragon fruit' => 'guava-dragon-fruit.png',
        'honey dew' => 'honey-dew.png',
        'mango milky fruit jam' => 'mango-milky-fruit-jam.png',
        'mulberry lime' => 'mulberry-lime.png',
        'oreo and cream milktea' => 'oreo-and-cream-milktea.png',
        'passion fruit pineapple' => 'passion-fruit-pineapple.png',
        'peach milky fruit jam' => 'peach-milky-fruit-jam.png',
        'peach puff milktea' => 'peach-puff-milktea.png',
        'queens cake milktea' => 'queens-cake-milktea.png',
        "queen's cake milktea" => 'queens-cake-milktea.png',
        'sakura pomelo' => 'sakura-pomelo.png',
        'strawberry milky fruit jam' => 'strawberry-milky-fruit-jam.png',
        'wintermelon cheesecake' => 'wintermelon-cheesecake.png',
        'wintermelon milktea' => 'wintermelon-milktea.png',
    ];
    $manualImageUrl = function (string $name) use ($manualImages) {
        $key = preg_replace('/\s+/', ' ', preg_replace("/[^a-z0-9'\s]/", '', strtolower($name)));
        $key = trim($key);

        return isset($manualImages[$key]) ? asset('images/manual-menu-products/'.$manualImages[$key]) : '';
    };

    try {
        $inventoryProducts = Inventory::query()
            ->orderBy('name')
            ->get()
            ->map(function (Inventory $item) use ($manualImageUrl) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'category' => $item->category ?: 'Milktea Series',
                    'prices' => [
                        'R' => (float) $item->regular_price,
                        'L' => (float) $item->large_price,
                    ],
                    'stock' => (int) $item->stock,
                    'desc' => $item->description ?: '',
                    'image_url' => $manualImageUrl($item->name) ?: ($item->image_path ? asset('storage/'.$item->image_path) : ''),
                    'updated_at' => optional($item->updated_at)->toISOString(),
                ];
            })
            ->values();
    } catch (QueryException $exception) {
        $inventoryProducts = collect();
    }

    return view('orders', [
        'authenticatedStaff' => $authenticatedStaff,
        'inventoryProducts' => $inventoryProducts,
        'takeoutFeePerCup' => (float) config('queenscup.takeout_fee_per_cup', 5.00),
    ]);
};

/*
| The public landing page. The reservation app itself stays on /orders, so a
| first-time visitor gets the menu and the app download rather than being
| dropped straight into a login screen.
*/
Route::get('/', function () {
    try {
        $products = Inventory::orderBy('category')->orderBy('name')->get();
    } catch (QueryException $exception) {
        // The shop front is the last page that should 500. If the database
        // is unreachable it still renders, just without the menu.
        $products = collect();
    }

    return view('landing', [
        'products' => $products,
        'categories' => $products->pluck('category')->filter()->unique()->values(),
    ]);
})->name('landing');


/*
| Customer accounts.
|
| Registering creates the account unverified and emails a code; nothing can
| be reserved until the address is confirmed, so every order has a contact
| that actually reaches someone.
*/
Route::post('/customer/register', [CustomerAccountController::class, 'register'])->name('customer.register');
Route::post('/customer/verify', [CustomerAccountController::class, 'verify'])->name('customer.verify');
Route::post('/customer/resend', [CustomerAccountController::class, 'resend'])->name('customer.resend');
Route::post('/customer/login', [CustomerAccountController::class, 'login'])->name('customer.login');
/*
| The customer assistant. Open to anyone so it works on the landing page,
| but only a signed-in customer gets a stored conversation.
*/
Route::get('/chat', [ChatController::class, 'history'])->name('chat.history');
Route::post('/chat', [ChatController::class, 'send'])->name('chat.send');
Route::delete('/chat', [ChatController::class, 'clear'])->name('chat.clear');

Route::post('/customer/logout', [CustomerAccountController::class, 'logout'])->name('customer.logout');

Route::get('/staff-login', function () {
    return view('staff-login');
})->name('login');

Route::post('/staff-login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    try {
        $user = User::where('email', $credentials['email'])
            ->whereIn('role', ['admin', 'cashier'])
            ->first();
    } catch (QueryException $exception) {
        return response()->json([
            'message' => 'We could not connect to the database. Please try again later.',
        ], 503);
    }

    if (! $user || ! Hash::check($credentials['password'], $user->password)) {
        return response()->json([
            'message' => 'Invalid staff email or password.',
        ], 422);
    }

    $request->session()->regenerate();
    $request->session()->put('staff_user_id', $user->id);

    ActivityLog::record('staff.login', "{$user->name} signed in", $user);

    return response()->json([
        'redirect_to' => $user->role === 'admin'
            ? route('dashboard')
            : route('point-of-sales.index'),
        'user' => [
            'id' => $user->id,
            'email' => $user->email,
            'username' => $user->email,
            'role' => $user->role,
            'fullName' => $user->name,
            'since' => optional($user->created_at)->toDateString(),
        ],
    ]);
})->middleware('throttle:10,1')->name('staff.login');

Route::post('/staff-logout', function (Request $request) {
    // Read before the session goes, so the line still names who left.
    $leaving = User::find($request->session()->get('staff_user_id'));

    if ($leaving) {
        ActivityLog::record('staff.logout', "{$leaving->name} signed out", $leaving);
    }

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->name('staff.logout');

Route::post('/staff', function (Request $request) {
    $adminId = $request->session()->get('staff_user_id');

    try {
        $admin = $adminId ? User::find($adminId) : null;
    } catch (QueryException $exception) {
        return response()->json([
            'message' => 'We could not connect to the database. Please try again later.',
        ], 503);
    }

    if (! $admin || $admin->role !== 'admin') {
        return response()->json([
            'message' => 'Only admins can create staff accounts.',
        ], 403);
    }

    $data = $request->validate([
        'name' => ['required', 'string', 'min:2', 'max:255'],
        'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', 'string', 'min:6'],
        'role' => ['required', 'in:admin,cashier'],
    ]);

    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
        'role' => $data['role'],
    ]);

    return response()->json([
        'message' => ucfirst($data['role']).' account created.',
        'user' => [
            'id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'fullName' => $user->name,
            'since' => optional($user->created_at)->toDateString(),
        ],
    ], 201);
})->middleware(['staff', 'admin'])->name('staff.store');

Route::post('/customer/otp/send', function (Request $request) {
    $data = $request->validate([
        'name' => ['required', 'string', 'min:2', 'max:255'],
        'email' => ['required', 'email', 'max:255'],
    ]);

    $otp = (string) random_int(100000, 999999);
    $expiresAt = now()->addMinutes(5);

    $request->session()->put('customer_otp', [
        'code' => Hash::make($otp),
        'name' => $data['name'],
        'email' => $data['email'],
        'expires_at' => $expiresAt->timestamp,
        'attempts' => 0,
    ]);

    try {
        Mail::raw(
            "Hi {$data['name']},\n\nYour Queen's Cup verification code is {$otp}.\n\nThis code expires in 5 minutes. If you did not request this code, you can ignore this email.",
            function ($mail) use ($data) {
                $mail->to($data['email'], $data['name'])
                    ->subject("Queen's Cup verification code");
            }
        );
    } catch (Throwable $exception) {
        Log::warning('Customer OTP email failed.', [
            'email' => $data['email'],
            'error' => $exception->getMessage(),
        ]);

        return response()->json([
            'message' => 'We could not send the verification code right now. Please try again.',
            'debug_otp' => app()->environment('local') ? $otp : null,
        ], 502);
    }

    return response()->json([
        'message' => 'Verification code sent to your email address.',
    ]);
});

Route::post('/customer/otp/verify', function (Request $request) {
    $data = $request->validate([
        'otp' => ['required', 'digits:6'],
    ]);

    $otp = $request->session()->get('customer_otp');

    if (! $otp) {
        return response()->json(['message' => 'Please request a new OTP.'], 422);
    }

    if (now()->timestamp > ($otp['expires_at'] ?? 0)) {
        $request->session()->forget('customer_otp');
        return response()->json(['message' => 'Your OTP has expired. Please request a new one.'], 422);
    }

    $otp['attempts'] = (int) ($otp['attempts'] ?? 0) + 1;
    $request->session()->put('customer_otp', $otp);

    if ($otp['attempts'] > 5) {
        $request->session()->forget('customer_otp');
        return response()->json(['message' => 'Too many attempts. Please request a new OTP.'], 429);
    }

    if (! Hash::check($data['otp'], $otp['code'])) {
        return response()->json(['message' => 'Invalid OTP. Please check the code and try again.'], 422);
    }

    $request->session()->forget('customer_otp');

    return response()->json(['message' => 'OTP verified.']);
});

Route::get('/orders', $ordersView)->name('orders');

Route::middleware(['staff', 'admin'])->group(function () {
    Route::get('/dashboard', function (Request $request) {
        return view('dashboard', [
            'staffUser' => $request->attributes->get('staff_user'),
        ]);
    })->name('dashboard');

    Route::resource('inventory', InventoryController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    // The activity log. Admin only: it names who did what, which is not
    // something every cashier needs to read.
    Route::get('/activity', [ActivityLogController::class, 'index'])
        ->middleware('admin')->name('activity');

    Route::get('/reports', function () {
        return view('reports');
    })->name('reports');

    Route::get('/settings', function () {
        return view('settings');
    })->name('settings');

    Route::post('/settings/qr-code', function (Request $request) {
        $data = $request->validate([
            'payment_method' => ['required', 'in:gcash,maya'],
            'qr_code' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'],
        ]);

        $directory = public_path('images');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = $data['payment_method'] === 'gcash' ? 'gcash-qr.png' : 'maya-qr.png';
        $request->file('qr_code')->move($directory, $filename);

        return redirect()->route('settings')
            ->with('success', strtoupper($data['payment_method']).' QR code uploaded.');
    })->name('settings.qr-code');
});

Route::middleware('staff')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    Route::resource('pos', PointOfSaleController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->parameters(['pos' => 'pointOfSale'])
        ->names('point-of-sales');
});

/*
| Reservations, counter side.
|
| The Manage Reservations screen plus the endpoints it reads and writes.
| These live in web.php rather than api.php because the admin panel calls
| them from the browser with the staff session it already holds. The
| customer app never touches these - it uses the token routes in api.php.
*/
Route::middleware('staff')->group(function () {
    Route::get('/reservations', function () {
        return view('reservations');
    })->name('reservations');

    Route::prefix('staff/reservations')->name('staff.reservations.')->group(function () {
        Route::get('/', [StaffReservationController::class, 'index'])->name('index');
        Route::get('/counts', [StaffReservationController::class, 'counts'])->name('counts');
        Route::patch('/{reservation}/status', [StaffReservationController::class, 'updateStatus'])->name('status');
        Route::patch('/{reservation}/payment', [StaffReservationController::class, 'recordPayment'])->name('payment');
    });

    // Walk-in sales rung up at the till. Stored as completed, paid orders so
    // there is one record of every sale rather than two parallel tables.
    Route::post('/staff/pos/sales', [StaffReservationController::class, 'storeSale'])->name('staff.pos.sales');
    // Till log for the sales report.
    Route::get('/staff/pos/sales', [StaffReservationController::class, 'salesLog'])->name('staff.pos.log');

    // Paid sales from both channels, shaped for the dashboard charts.
    Route::get('/staff/dashboard/sales', [StaffReservationController::class, 'dashboardSales'])->name('staff.dashboard.sales');
});
