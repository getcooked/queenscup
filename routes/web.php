<?php

use App\Models\User;
use App\Models\Inventory;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PointOfSaleController;
use App\Http\Controllers\ProfileController;
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

$ordersView = function () {
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
        'inventoryProducts' => $inventoryProducts,
    ]);
};

Route::get('/', $ordersView);

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

    $request->session()->put('staff_user_id', $user->id);

    return response()->json([
        'user' => [
            'id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'fullName' => $user->name,
            'since' => optional($user->created_at)->toDateString(),
        ],
    ]);
});

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
});

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

Route::get('/dashboard', function () {
    $userId = session('staff_user_id');
    try {
        $user = $userId ? User::find($userId) : null;
    } catch (QueryException $exception) {
        return redirect('/staff-login')
            ->withErrors(['database' => 'We could not connect to the database. Please try again later.']);
    }

    if (! $user || $user->role !== 'admin') {
        return redirect('/staff-login');
    }

    return view('dashboard', [
        'staffUser' => $user,
    ]);
});

Route::get('/orders', $ordersView);

Route::resource('inventory', InventoryController::class)
    ->only(['index', 'store', 'update', 'destroy']);

Route::get('/reports', function () {
    return view('reports');
});

Route::get('/settings', function () {
    return view('settings');
});

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

    return redirect('/settings')->with('success', strtoupper($data['payment_method']).' QR code uploaded.');
});

Route::get('/profile', [ProfileController::class, 'show']);
Route::patch('/profile', [ProfileController::class, 'update']);
Route::patch('/profile/password', [ProfileController::class, 'updatePassword']);

Route::resource('pos', PointOfSaleController::class)
    ->only(['index', 'store', 'update', 'destroy'])
    ->parameters(['pos' => 'pointOfSale'])
    ->names('point-of-sales');
