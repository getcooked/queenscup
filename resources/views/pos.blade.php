@php
    $pageTitle = $pageTitle ?? 'Point of Sale';
    $pageKey = $pageKey ?? 'pos';
    $pageDescription = $pageDescription ?? 'Create and manage sales transactions.';
    $takeoutFee = (float) config('queenscup.takeout_fee_per_cup', 5.00);
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
    $manualImageUrl = function ($name) use ($manualImages) {
        $key = trim(preg_replace('/\s+/', ' ', preg_replace("/[^a-z0-9'\s]/", '', strtolower($name ?? ''))));

        return isset($manualImages[$key]) ? asset('images/manual-menu-products/'.$manualImages[$key]) : '';
    };
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }} | The Queen's Cup</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="preload" href="{{ asset('vendor/fontawesome/webfonts/fa-solid-900.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link href="{{ asset('vendor/fontawesome/css/all.min.css') }}" rel="stylesheet">
    <style>
        :root{--bg:#f7fbf8;--deep:#ffffff;--card:#ffffff;--line:#cfe7d7;--text:#123524;--muted:#5f7f6b;--green:#16c76a;--gold:#2f9e62;--pink:#ef2f83;--yellow:#ffbd35;--danger:#ef3d5f}
        *{box-sizing:border-box;margin:0;padding:0}body{height:100vh;background:var(--bg);color:var(--text);font-family:"DM Sans",sans-serif;overflow:hidden}h1,h2,h3{font-family:"Playfair Display",serif}.layout{display:flex;height:100vh}.sidebar{width:260px;background:#ffffff;border-right:1px solid var(--line);display:flex;flex-direction:column;flex-shrink:0}.sidebar-brand{padding:22px 18px;border-bottom:1px solid var(--line);display:flex;align-items:center;gap:13px}.crown-icon{width:46px;height:46px;border-radius:50%;overflow:hidden;flex-shrink:0;border:2px solid rgba(22,199,106,.3);background:linear-gradient(135deg,#0c6f3f,#16c76a)}.crown-icon img{width:100%;height:100%;object-fit:cover}.sidebar-brand h2{font-size:14px;line-height:1.2;background:linear-gradient(135deg,#16a65f,#2f9e62);-webkit-background-clip:text;-webkit-text-fill-color:transparent}.tagline{font-size:10px;color:var(--muted);letter-spacing:.5px}.sidebar-nav{flex:1;padding:14px 10px;overflow-y:auto}.nav-section-title{padding:12px 10px 7px;color:var(--muted);font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase}.nav-item{display:flex;align-items:center;gap:10px;min-height:40px;padding:0 12px;border-radius:10px;color:var(--muted);cursor:pointer;font-size:13px;font-weight:600;text-decoration:none;transition:background .2s ease,color .2s ease}.nav-item:hover,.nav-item.active{background:rgba(22,199,106,.13);color:var(--green)}.sidebar-footer{padding:14px 18px;border-top:1px solid var(--line);display:flex;align-items:center;gap:12px}.avatar{width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,var(--green),var(--gold));display:grid;place-items:center;font-size:13px;font-weight:800;color:#fff}.user-info{flex:1;min-width:0}.user-info .name{font-size:12px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.user-info .role{font-size:10px;color:var(--muted)}.logout-btn{width:30px;height:30px;border-radius:8px;border:1px solid var(--line);background:var(--card);color:var(--muted);cursor:pointer}.logout-btn:hover{border-color:var(--danger);color:var(--danger)}.content{flex:1;min-width:0;overflow:auto;padding:30px}.top{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;margin-bottom:22px}.top h1{font-size:34px}.top p{color:var(--muted);margin-top:5px}.card{background:var(--card);border:1px solid var(--line);border-radius:14px;padding:22px}.stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin-bottom:18px}.stat{background:var(--card);border:1px solid var(--line);border-radius:14px;padding:18px}.stat strong{display:block;font-size:26px}.stat span{color:var(--muted);font-size:12px}.product-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px}.product-card{background:#f1f8f3;border:1px solid var(--line);border-radius:12px;padding:15px;display:flex;flex-direction:column;gap:10px}.product-image{width:100%;aspect-ratio:4/3;border-radius:10px;object-fit:cover;border:1px solid var(--line);background:#f1f8f3}.product-placeholder{width:100%;aspect-ratio:4/3;border-radius:10px;border:1px dashed var(--line);background:#f1f8f3;color:var(--muted);display:grid;place-items:center;font-size:32px}.product-top{display:flex;justify-content:space-between;gap:10px}.product-name{font-weight:800;font-size:15px}.category{display:inline-flex;align-items:center;width:max-content;padding:4px 9px;border-radius:999px;background:rgba(224,182,76,.14);color:var(--gold);font-size:11px;font-weight:800}.prices{display:flex;gap:8px}.price{flex:1;border:1px solid var(--line);border-radius:10px;padding:8px;background:#f1f8f3}.price span{display:block;color:var(--muted);font-size:10px}.price strong{font-size:15px}.desc{min-height:34px;color:var(--muted);font-size:12px;line-height:1.45}.stock{font-size:12px;font-weight:800}.stock.ok{color:var(--green)}.stock.low{color:var(--yellow)}.stock.out{color:var(--danger)}.btn{height:38px;border:0;border-radius:10px;padding:0 12px;background:var(--green);color:#ffffff;font-weight:800;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:8px}.btn.disabled{background:#e8f2ec;color:var(--muted);pointer-events:none}.empty{color:var(--muted);padding:30px;text-align:center;border:1px dashed var(--line);border-radius:12px}@media(max-width:1100px){body{overflow:auto;height:auto}.layout{height:auto}.sidebar{display:none}.top{align-items:flex-start;flex-direction:column}.stats{grid-template-columns:1fr}}
        .stats{display:none}
        /* ---------- till ---------- */
        .content{display:flex;flex-direction:column;min-height:0}
        .top{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;flex-wrap:wrap}
        .till-stats{display:flex;gap:12px}
        .till-stats .stat{background:#fff;border:1px solid #d8ebdf;border-radius:12px;padding:9px 14px;text-align:right;box-shadow:0 8px 22px rgba(18,53,36,.06)}
        .till-stats .stat strong{display:block;font-size:17px}
        .till-stats .stat span{font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:.8px}

        .till{display:grid;grid-template-columns:minmax(0,1fr) 360px;gap:16px;flex:1;min-height:0;margin-top:18px}
        .till-products{display:flex;flex-direction:column;min-height:0;background:#fff;border:1px solid #d8ebdf;border-radius:16px;padding:14px;box-shadow:0 12px 32px rgba(18,53,36,.07)}
        .till-search{position:relative;margin-bottom:12px;flex-shrink:0}
        .till-search i{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:12px}
        .till-search input{width:100%;height:40px;border:1px solid #d8ebdf;border-radius:11px;background:#f4fbf6;padding:0 12px 0 34px;font-family:"DM Sans",sans-serif;font-size:13px;outline:none;color:var(--text)}
        .till-search input:focus{border-color:var(--green)}

        .product-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(132px,1fr));gap:11px;overflow:auto;min-height:0;padding-right:2px;scrollbar-width:none}
        .product-grid::-webkit-scrollbar{display:none}
        .product-card{background:#fff;border:1px solid #d8ebdf;border-radius:13px;padding:9px;text-align:left;cursor:pointer;font-family:"DM Sans",sans-serif;color:var(--text);transition:transform .14s ease,box-shadow .14s ease,border-color .14s ease;display:flex;flex-direction:column;gap:5px}
        .product-card:hover:not(:disabled){transform:translateY(-2px);border-color:var(--green);box-shadow:0 10px 22px rgba(18,134,78,.14)}
        .product-card:active:not(:disabled){transform:translateY(0)}
        .product-card.is-out{opacity:.45;cursor:not-allowed}
        .product-image,.product-placeholder{width:100%;aspect-ratio:1;border-radius:9px;object-fit:cover;background:linear-gradient(145deg,#eef8f2,#dcf0e5);display:grid;place-items:center;color:rgba(18,134,78,.3);font-size:22px}
        .product-name{font-size:12px;font-weight:700;line-height:1.25}
        .product-meta{font-size:11px;color:var(--gold);font-weight:700}
        .product-stock{font-size:9px;color:var(--muted);text-transform:uppercase;letter-spacing:.6px}

        .till-cart{display:flex;flex-direction:column;min-height:0;background:#fff;border:1px solid #d8ebdf;border-radius:16px;padding:14px;box-shadow:0 12px 32px rgba(18,53,36,.07)}
        .cart-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px}
        .cart-head h3{font-size:17px}
        .link-btn{background:none;border:0;color:var(--muted);font-size:11px;font-weight:700;cursor:pointer;font-family:"DM Sans",sans-serif}
        .link-btn:hover{color:var(--pink)}

        .size-row,.serve-row,.pay-row{display:flex;gap:6px;align-items:center;margin-bottom:10px;flex-wrap:wrap}
        .size-row span{font-size:11px;color:var(--muted);font-weight:700;margin-right:2px}
        .seg,.pay{flex:1;min-width:0;height:34px;border:1px solid #d8ebdf;background:#fff;border-radius:10px;font-size:11px;font-weight:700;color:var(--muted);cursor:pointer;font-family:"DM Sans",sans-serif;display:inline-flex;align-items:center;justify-content:center;gap:5px}
        .size-row .seg{flex:0 0 auto;padding:0 14px}
        .seg.on,.pay.on{background:linear-gradient(135deg,rgba(22,199,106,.16),rgba(47,158,98,.08));border-color:var(--green);color:var(--gold)}

        .cart-lines{flex:1;min-height:60px;overflow:auto;margin-bottom:10px;scrollbar-width:none}
        .cart-lines::-webkit-scrollbar{display:none}
        .cart-empty{color:var(--muted);font-size:12px;text-align:center;padding:22px 0}
        .cart-line{display:flex;align-items:center;gap:8px;padding:7px 0;border-bottom:1px solid var(--line)}
        .cart-line .cl-name{flex:1;min-width:0}
        .cart-line b{display:block;font-size:12px;line-height:1.3}
        .cart-line small{font-size:10px;color:var(--muted)}
        .qty{display:flex;align-items:center;gap:5px}
        .qty button{width:22px;height:22px;border-radius:7px;border:1px solid #d8ebdf;background:#fff;cursor:pointer;font-weight:800;color:var(--text);line-height:1}
        .qty button:hover{border-color:var(--green);color:var(--green)}
        .qty span{min-width:16px;text-align:center;font-size:12px;font-weight:800}
        .cl-total{font-size:12px;font-weight:800;min-width:56px;text-align:right}

        .totals{border-top:1px solid var(--line);padding-top:9px;margin-bottom:10px}
        .t-row{display:flex;justify-content:space-between;font-size:12px;padding:2px 0;color:var(--muted)}
        /* display:flex would otherwise beat the hidden attribute. */
        .t-row[hidden]{display:none}
        .t-row.total{font-size:15px;font-weight:800;color:var(--text);padding-top:6px}
        .t-row.total span:last-child{color:var(--gold)}

        .tender{margin-bottom:10px}
        .tender label{display:block;font-size:11px;color:var(--muted);font-weight:700;margin-bottom:5px}
        .tender input{width:100%;height:38px;border:1px solid #d8ebdf;border-radius:10px;background:#f4fbf6;padding:0 11px;font-family:"DM Sans",sans-serif;font-size:15px;font-weight:800;color:var(--text);outline:none}
        .tender input:focus{border-color:var(--green)}
        .change{display:flex;justify-content:space-between;font-size:12px;color:var(--muted);margin-top:6px;font-weight:700}
        .change strong{color:var(--green);font-size:14px}

        .qr-pay{display:flex;gap:10px;align-items:center;border:1px solid #d8ebdf;border-radius:12px;padding:10px;margin-bottom:10px;background:#f7fdf9}
        .qr-pay[hidden]{display:none}
        .qr-pay img{width:82px;height:82px;object-fit:contain;border-radius:8px;background:#fff;border:1px solid #e6f2ea;flex-shrink:0}
        .qr-pay-info{display:flex;flex-direction:column;gap:2px;min-width:0}
        .qr-pay-info strong{font-size:12px}
        .qr-pay-info span{font-size:17px;font-weight:800;color:var(--gold)}
        .qr-pay-info small{font-size:10px;color:var(--muted);line-height:1.35}
        .qr-missing{font-size:11px;color:var(--pink)}
        .cl-size{display:flex;gap:3px;margin-top:3px}
        .cl-size button{border:1px solid #d8ebdf;background:#fff;border-radius:6px;font-size:9px;font-weight:700;padding:1px 5px;cursor:pointer;color:var(--muted);font-family:"DM Sans",sans-serif}
        .cl-size button.on{background:rgba(22,199,106,.16);border-color:var(--green);color:var(--gold)}
        .cl-size button:disabled{opacity:.4;cursor:not-allowed}
        .btn-complete{width:100%;height:44px;border:0;border-radius:12px;background:linear-gradient(135deg,#12864e,#16a65f);color:#fff;font-weight:800;font-size:14px;cursor:pointer;font-family:"DM Sans",sans-serif;display:inline-flex;align-items:center;justify-content:center;gap:8px}
        .btn-complete:disabled{opacity:.45;cursor:not-allowed}
        .cart-note{font-size:11px;margin-top:7px;text-align:center;min-height:14px}
        .cart-note.bad{color:var(--pink)}
        .cart-note.good{color:var(--green)}

        @media(max-width:1180px){.till{grid-template-columns:1fr;overflow:auto}.till-cart{position:static}}
        body{background:radial-gradient(circle at top right,rgba(22,199,106,.08),transparent 34%),linear-gradient(180deg,#fbfefc 0%,var(--bg) 100%)}.sidebar,.card,.stat,.product-card{box-shadow:0 12px 32px rgba(18,53,36,.07)}.card,.stat,.product-card{border-color:#d8ebdf}.nav-item.active{box-shadow:inset 3px 0 0 var(--green)}.btn{transition:transform .18s ease,box-shadow .18s ease}.btn:hover{transform:translateY(-1px);box-shadow:0 10px 24px rgba(18,134,78,.15)}.product-card{transition:transform .18s ease,box-shadow .18s ease;background:#fff}.product-card:hover{transform:translateY(-2px);box-shadow:0 16px 36px rgba(18,53,36,.12)}.top h1{font-size:36px}.content{background:transparent}
    </style>
    <link href="{{ asset('css/admin-shell.css') }}" rel="stylesheet">
    <script src="{{ asset('js/admin-sidebar.js') }}" defer></script>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="crown-icon" id="sidebarCrown"><img src="{{ asset('icons/queens-cup-logo.png') }}" alt="Logo"></div>
            <div><h2>The Queen's Cup</h2><span class="tagline">Crowned with Flavors</span></div>
        </div>
        <nav class="sidebar-nav">
            @if($staffUser->role === 'admin')
                <div class="nav-section"><div class="nav-section-title">Main</div><a class="nav-item {{ $pageKey === 'dashboard' ? 'active' : '' }}" href="{{ url('/dashboard') }}"><i class="fas fa-chart-pie"></i> Dashboard</a><a class="nav-item {{ $pageKey === 'pos' ? 'active' : '' }}" href="{{ url('/pos') }}"><i class="fas fa-cash-register"></i> Point of Sale</a></div>
                <div class="nav-section"><div class="nav-section-title">Management</div><a class="nav-item {{ $pageKey === 'orders' ? 'active' : '' }}" href="{{ url('/orders') }}"><i class="fas fa-receipt"></i> Orders</a><a class="nav-item" href="{{ url('/reservations') }}"><i class="fas fa-calendar-check"></i> Reservations</a><a class="nav-item {{ $pageKey === 'inventory' ? 'active' : '' }}" href="{{ url('/inventory') }}"><i class="fas fa-boxes-stacked"></i> Inventory</a></div>
                <div class="nav-section"><div class="nav-section-title">System</div><a class="nav-item {{ $pageKey === 'reports' ? 'active' : '' }}" href="{{ url('/reports') }}"><i class="fas fa-chart-bar"></i> Reports</a><a class="nav-item" href="{{ url('/activity') }}"><i class="fas fa-clock-rotate-left"></i> Activity</a><a class="nav-item {{ $pageKey === 'settings' ? 'active' : '' }}" href="{{ url('/settings') }}"><i class="fas fa-gear"></i> Settings</a></div>
            @else
                <div class="nav-section"><div class="nav-section-title">Counter</div><a class="nav-item active" href="{{ url('/pos') }}"><i class="fas fa-cash-register"></i> Point of Sale</a><a class="nav-item" href="{{ url('/orders') }}"><i class="fas fa-receipt"></i> Orders</a></div>
            @endif
            <div class="nav-section"><div class="nav-section-title">Account</div><a class="nav-item {{ $pageKey === 'profile' ? 'active' : '' }}" href="{{ url('/profile') }}"><i class="fas fa-user-circle"></i> My Profile</a></div>
        </nav>
        <div class="sidebar-footer">
            <div class="avatar" id="sidebarAvatar">?</div>
            <div class="user-info"><div class="name" id="sidebarName">User</div><div class="role" id="sidebarRole">Role</div></div>
            <button class="logout-btn" onclick="handleLogout()" title="Sign Out"><i class="fas fa-right-from-bracket"></i></button>
        </div>
    </aside>
    <main class="content">
        <div class="top">
            <div><h1>{{ $pageTitle }}</h1><p>Ring up a walk-in customer buying at the counter.</p></div>
            <div class="till-stats">
                <div class="stat"><strong>₱{{ number_format((float) ($todaySales ?? 0), 2) }}</strong><span>Today's Sales</span></div>
                <div class="stat"><strong id="tillCount">{{ $todayTransactions ?? 0 }}</strong><span>Transactions</span></div>
            </div>
        </div>

        <div class="till">
            <section class="till-products">
                @if(($inventoryItems ?? collect())->isEmpty())
                    <div class="empty">No products yet. Add them in Inventory first.</div>
                @else
                    <div class="till-search">
                        <i class="fas fa-search"></i>
                        <input type="text" id="productSearch" placeholder="Search drinks…" autocomplete="off">
                    </div>
                    <div class="product-grid" id="productGrid">
                        @foreach($inventoryItems as $item)
                            @php
                                $itemImageUrl = $manualImageUrl($item->name) ?: ($item->image_path ? asset('storage/'.$item->image_path) : '');
                            @endphp
                            <button type="button" class="product-card {{ $item->stock <= 0 ? 'is-out' : '' }}"
                                data-id="{{ $item->id }}"
                                data-name="{{ $item->name }}"
                                data-regular="{{ (float) $item->regular_price }}"
                                data-large="{{ (float) $item->large_price }}"
                                data-stock="{{ (int) $item->stock }}"
                                {{ $item->stock <= 0 ? 'disabled' : '' }}>
                                @if($itemImageUrl)
                                    <img class="product-image" src="{{ $itemImageUrl }}" alt="{{ $item->name }}">
                                @else
                                    <div class="product-placeholder"><i class="fas fa-mug-hot"></i></div>
                                @endif
                                <div class="product-name">{{ $item->name }}</div>
                                <div class="product-meta">₱{{ number_format((float) $item->regular_price, 0) }} · ₱{{ number_format((float) $item->large_price, 0) }}</div>
                                <div class="product-stock">{{ $item->stock <= 0 ? 'Out of stock' : $item->stock.' left' }}</div>
                            </button>
                        @endforeach
                    </div>
                @endif
            </section>

            <aside class="till-cart">
                <div class="cart-head">
                    <h3>Current sale</h3>
                    <button type="button" class="link-btn" onclick="clearCart()">Clear</button>
                </div>

                <div class="size-row">
                    <span>Add as</span>
                    <button type="button" class="seg on" data-size="regular" onclick="setSize('regular', this)">16oz</button>
                    <button type="button" class="seg" data-size="large" onclick="setSize('large', this)">22oz</button>
                </div>

                <div class="cart-lines" id="cartLines">
                    <p class="cart-empty">Tap a drink to start a sale.</p>
                </div>

                <div class="serve-row">
                    <button type="button" class="seg on" data-serve="dine_in" onclick="setServe('dine_in', this)"><i class="fas fa-utensils"></i> Dine in</button>
                    <button type="button" class="seg" data-serve="take_out" onclick="setServe('take_out', this)"><i class="fas fa-bag-shopping"></i> Take out</button>
                </div>

                <div class="totals">
                    <div class="t-row"><span>Subtotal</span><span id="tSubtotal">₱0.00</span></div>
                    <div class="t-row" id="tFeeRow" hidden><span id="tFeeLabel">Take-out cups</span><span id="tFee">₱0.00</span></div>
                    <div class="t-row total"><span>Total</span><span id="tTotal">₱0.00</span></div>
                </div>

                <div class="pay-row">
                    <button type="button" class="pay on" data-pay="cash" onclick="setPay('cash', this)"><i class="fas fa-money-bill-wave"></i> Cash</button>
                    <button type="button" class="pay" data-pay="gcash" onclick="setPay('gcash', this)"><i class="fas fa-mobile-screen"></i> GCash</button>
                    <button type="button" class="pay" data-pay="paymaya" onclick="setPay('paymaya', this)"><i class="fas fa-credit-card"></i> PayMaya</button>
                </div>

                <div class="qr-pay" id="qrPay" hidden>
                    <img id="qrPayImage" src="" alt="Payment QR code">
                    <div class="qr-pay-info">
                        <strong id="qrPayTitle">Scan to pay</strong>
                        <span id="qrPayAmount">&#8369;0.00</span>
                        <small id="qrPayHint">Confirm the transfer on the customer's phone before completing.</small>
                    </div>
                </div>

                <div class="tender" id="tenderRow">
                    <label>Cash received
                        <input type="number" id="tendered" min="0" step="1" inputmode="numeric" placeholder="0" oninput="renderTotals()">
                    </label>
                    <div class="change">Change <strong id="changeDue">₱0.00</strong></div>
                </div>

                <button type="button" class="btn-complete" id="completeBtn" onclick="completeSale()" disabled>
                    <i class="fas fa-check"></i> Complete sale
                </button>
                <p class="cart-note" id="cartNote"></p>
            </aside>
        </div>
    </main>
</div>
<script>
    var defaultLogo = '{{ asset('icons/queens-cup-logo.png') }}';

    function setupSidebar() {
        var session = null;
        try { session = JSON.parse(localStorage.getItem('qc_session')); } catch (error) { session = null; }
        document.getElementById('sidebarCrown').innerHTML = '<img src="' + (localStorage.getItem('qc_logo') || defaultLogo) + '" alt="Logo">';
        if (!session) return;
        var fullName = session.fullName || session.username || 'User';
        var initials = fullName.split(' ').map(function (word) { return word[0]; }).join('').substring(0, 2).toUpperCase();
        var roleLabels = { admin: 'Branch Admin', cashier: 'Cashier', customer: 'Customer', guest: 'Guest' };
        document.getElementById('sidebarAvatar').textContent = initials || '?';
        document.getElementById('sidebarName').textContent = fullName;
        document.getElementById('sidebarRole').textContent = roleLabels[session.role] || session.role || 'Role';
    }

    function handleLogout() {
        localStorage.removeItem('qc_session');
        fetch(@json(route('staff.logout')), {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'X-CSRF-TOKEN': @json(csrf_token()) }
        }).finally(function () {
            window.location.href = @json(url('/'));
        });
    }


    /* ---------------------------------------------------------------
     * Till
     *
     * The totals here are for the cashier's eyes only. The sale is priced
     * again on the server from the product ids, so what is charged can
     * never drift from the catalogue.
     * --------------------------------------------------------------- */
    var TAKEOUT_FEE = {{ $takeoutFee }};
    var CSRF = @json(csrf_token());
    var SALE_URL = @json(route('staff.pos.sales'));

    var cart = [];
    var size = 'regular';
    var serve = 'dine_in';
    var pay = 'cash';
    var busy = false;

    var QR_IMAGES = {
        gcash: @json(asset('images/gcash-qr.png')),
        paymaya: @json(asset('images/maya-qr.png'))
    };

    function peso(value) {
        return '₱' + Number(value || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    function escapeHtml(value) {
        return String(value == null ? '' : value).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function setSize(value, button) {
        size = value;
        document.querySelectorAll('.size-row .seg').forEach(function (b) { b.classList.remove('on'); });
        button.classList.add('on');
    }

    function setServe(value, button) {
        serve = value;
        document.querySelectorAll('.serve-row .seg').forEach(function (b) { b.classList.remove('on'); });
        button.classList.add('on');
        renderTotals();
    }

    function setPay(value, button) {
        pay = value;
        document.querySelectorAll('.pay').forEach(function (b) { b.classList.remove('on'); });
        button.classList.add('on');
        // Change is only meaningful for cash; the wallets show a QR instead.
        document.getElementById('tenderRow').style.display = value === 'cash' ? '' : 'none';
        document.getElementById('qrPay').hidden = value === 'cash';
        if (value !== 'cash') {
            var image = document.getElementById('qrPayImage');
            image.src = QR_IMAGES[value] || '';
            image.onerror = function () {
                image.style.display = 'none';
                document.getElementById('qrPayHint').textContent =
                    'No ' + value.toUpperCase() + ' QR uploaded yet. Add one in Settings.';
                document.getElementById('qrPayHint').className = 'qr-missing';
            };
            image.onload = function () {
                image.style.display = '';
                document.getElementById('qrPayHint').className = '';
                document.getElementById('qrPayHint').textContent =
                    'Confirm the transfer on the phone before completing.';
            };
            document.getElementById('qrPayTitle').textContent =
                'Scan to pay with ' + (value === 'gcash' ? 'GCash' : 'PayMaya');
        }
        renderTotals();
    }

    function addToCart(card) {
        var id = Number(card.dataset.id);
        var unit = size === 'large' ? Number(card.dataset.large) : Number(card.dataset.regular);

        // Drinks that only come in 16oz leave large_price at 0. The server
        // refuses these, so stop them at the till rather than showing a free line.
        if (unit <= 0) {
            note(card.dataset.name + ' is not sold in ' + (size === 'large' ? '22oz' : '16oz') + '.', true);
            return;
        }
        var stock = Number(card.dataset.stock);
        var key = id + '-' + size;
        var line = cart.filter(function (l) { return l.key === key; })[0];

        // Both sizes draw on the same stock, so count every line for this drink.
        var already = cart.reduce(function (n, l) { return l.id === id ? n + l.qty : n; }, 0);

        if (already + 1 > stock) {
            note('Only ' + stock + ' left of ' + card.dataset.name + '.', true);
            return;
        }

        if (line) {
            line.qty += 1;
        } else {
            cart.push({ key: key, id: id, name: card.dataset.name, size: size, unit: unit, qty: 1, stock: stock,
                regular: Number(card.dataset.regular), large: Number(card.dataset.large) });
        }

        note('');
        renderCart();
    }

    function changeQty(key, delta) {
        cart = cart.map(function (line) {
            if (line.key !== key) return line;

            var next = line.qty + delta;
            var otherSizes = cart.reduce(function (n, l) {
                return l.id === line.id && l.key !== key ? n + l.qty : n;
            }, 0);

            if (delta > 0 && otherSizes + next > line.stock) {
                note('Only ' + line.stock + ' left of ' + line.name + '.', true);
                return line;
            }

            return next <= 0 ? null : Object.assign({}, line, { qty: next });
        }).filter(Boolean);

        renderCart();
    }

    /**
     * Switch a line between 16oz and 22oz after it has been added. If the
     * basket already holds that drink in the target size the two lines are
     * merged rather than left as duplicates.
     */
    function changeSize(key, size) {
        var line = cart.filter(function (l) { return l.key === key; })[0];
        if (!line || line.size === size) return;

        var unit = size === 'large' ? line.large : line.regular;
        if (unit <= 0) {
            note(line.name + ' is not sold in ' + (size === 'large' ? '22oz' : '16oz') + '.', true);
            return;
        }

        var targetKey = line.id + '-' + size;
        var existing = cart.filter(function (l) { return l.key === targetKey; })[0];

        if (existing) {
            existing.qty += line.qty;
            cart = cart.filter(function (l) { return l.key !== key; });
        } else {
            line.key = targetKey;
            line.size = size;
            line.unit = unit;
        }

        note('');
        renderCart();
    }

    function clearCart() {
        cart = [];
        document.getElementById('tendered').value = '';
        note('');
        renderCart();
    }

    function renderCart() {
        var box = document.getElementById('cartLines');

        if (!cart.length) {
            box.innerHTML = '<p class="cart-empty">Tap a drink to start a sale.</p>';
        } else {
            box.innerHTML = cart.map(function (line) {
                return '<div class="cart-line">' +
                    '<div class="cl-name"><b>' + escapeHtml(line.name) + '</b>' +
                    '<small>' + (line.size === 'large' ? '22oz' : '16oz') + ' · ' + peso(line.unit) + '</small></div>' +
                    '<div class="cl-size">' +
                        '<button type="button" data-size="regular" data-key="' + line.key + '"' +
                            (line.size === 'regular' ? ' class="on"' : '') + '>16oz</button>' +
                        '<button type="button" data-size="large" data-key="' + line.key + '"' +
                            (line.size === 'large' ? ' class="on"' : '') +
                            (line.large > 0 ? '' : ' disabled title="Not sold in 22oz"') + '>22oz</button>' +
                    '</div>' +
                    '<div class="qty">' +
                        '<button type="button" data-step="-1" data-key="' + line.key + '">−</button>' +
                        '<span>' + line.qty + '</span>' +
                        '<button type="button" data-step="1" data-key="' + line.key + '">+</button>' +
                    '</div>' +
                    '<div class="cl-total">' + peso(line.unit * line.qty) + '</div>' +
                '</div>';
            }).join('');

            box.querySelectorAll('.qty button').forEach(function (button) {
                button.addEventListener('click', function () {
                    changeQty(button.dataset.key, Number(button.dataset.step));
                });
            });

            box.querySelectorAll('.cl-size button').forEach(function (button) {
                button.addEventListener('click', function () {
                    changeSize(button.dataset.key, button.dataset.size);
                });
            });
        }

        renderTotals();
    }

    function renderTotals() {
        var subtotal = cart.reduce(function (sum, l) { return sum + l.unit * l.qty; }, 0);
        var cups = cart.reduce(function (n, l) { return n + l.qty; }, 0);
        var fee = serve === 'take_out' ? TAKEOUT_FEE * cups : 0;
        var total = subtotal + fee;

        document.getElementById('tSubtotal').textContent = peso(subtotal);
        document.getElementById('tTotal').textContent = peso(total);
        var qrAmount = document.getElementById('qrPayAmount');
        if (qrAmount) qrAmount.textContent = peso(total);

        var feeRow = document.getElementById('tFeeRow');
        feeRow.hidden = serve !== 'take_out';
        document.getElementById('tFeeLabel').textContent = 'Take-out cups (' + cups + ' × ' + peso(TAKEOUT_FEE) + ')';
        document.getElementById('tFee').textContent = peso(fee);

        var tendered = Number(document.getElementById('tendered').value || 0);
        var change = tendered - total;
        document.getElementById('changeDue').textContent = change > 0 ? peso(change) : peso(0);

        // Cash cannot complete until enough has actually been handed over.
        var shortOnCash = pay === 'cash' && tendered > 0 && tendered < total;
        document.getElementById('completeBtn').disabled = busy || !cart.length || shortOnCash;
    }

    function note(message, isBad) {
        var el = document.getElementById('cartNote');
        el.textContent = message || '';
        el.className = 'cart-note' + (message ? (isBad ? ' bad' : ' good') : '');
    }


    /* ---------------------------------------------------------------
     * Receipt
     *
     * Two copies on one sheet: the customer takes theirs, the store keeps
     * the other for the drawer. Written into a popup so the panel behind
     * stays untouched and the browser handles paper size.
     * --------------------------------------------------------------- */
    var STORE_NAME = "The Queen's Cup";
    var STORE_TAGLINE = 'Crowned with Flavors';

    function receiptRows(sale) {
        return sale.items.map(function (line) {
            var label = line.quantity + ' x ' + line.name + ' (' + line.size_label + ')';
            return '<tr><td class="qty">' + escapeHtml(label) + '</td>' +
                '<td class="amt">' + peso(line.line_total) + '</td></tr>';
        }).join('');
    }

    function receiptCopy(sale, tendered, change, cashier, stamp, copyLabel) {
        var feeRow = Number(sale.takeout_fee) > 0
            ? '<tr><td>Take-out cups</td><td class="amt">' + peso(sale.takeout_fee) + '</td></tr>'
            : '';

        var cashRows = '';
        if (sale.payment_method === 'cash' && tendered > 0) {
            cashRows = '<tr><td>Cash</td><td class="amt">' + peso(tendered) + '</td></tr>' +
                '<tr><td>Change</td><td class="amt">' + peso(change) + '</td></tr>';
        }

        return '<div class="copy">' +
            '<div class="head">' +
                '<h1>' + STORE_NAME + '</h1>' +
                '<p>' + STORE_TAGLINE + '</p>' +
                '<p class="copy-label">' + copyLabel + '</p>' +
            '</div>' +
            '<div class="meta">' +
                '<div><span>Receipt</span><b>' + escapeHtml(sale.reference) + '</b></div>' +
                '<div><span>Date</span><b>' + stamp + '</b></div>' +
                '<div><span>Cashier</span><b>' + escapeHtml(cashier) + '</b></div>' +
                '<div><span>Serving</span><b>' + (sale.service_type === 'take_out' ? 'Take out' : 'Dine in') + '</b></div>' +
            '</div>' +
            '<table class="lines">' + receiptRows(sale) + '</table>' +
            '<table class="totals">' +
                '<tr><td>Subtotal</td><td class="amt">' + peso(sale.subtotal) + '</td></tr>' +
                feeRow +
                '<tr class="grand"><td>TOTAL</td><td class="amt">' + peso(sale.total) + '</td></tr>' +
                '<tr><td>Paid by</td><td class="amt">' + escapeHtml((sale.payment_method || '').toUpperCase()) + '</td></tr>' +
                cashRows +
            '</table>' +
            '<div class="foot"><p>Thank you, come again!</p><p>This serves as your official receipt.</p></div>' +
        '</div>';
    }

    function printReceipt(sale, tendered, change) {
        var cashier = 'Counter';
        try {
            var session = JSON.parse(localStorage.getItem('qc_session'));
            if (session) cashier = session.fullName || session.username || 'Counter';
        } catch (error) { /* fall back to the generic label */ }

        var stamp = new Date().toLocaleString('en-PH', {
            year: 'numeric', month: 'short', day: '2-digit',
            hour: '2-digit', minute: '2-digit'
        });

        var win = window.open('', 'qc-receipt', 'width=380,height=680');
        if (!win) {
            note('Allow pop-ups to print the receipt.', true);
            return;
        }

        var styles = 'body{font-family:"Courier New",monospace;margin:0;padding:10px;color:#000;background:#fff;font-size:12px}' +
            '.copy{width:74mm;margin:0 auto 6mm;padding-bottom:5mm}' +
            '.copy+.copy{border-top:1px dashed #000;padding-top:5mm}' +
            '.head{text-align:center;margin-bottom:8px}' +
            '.head h1{font-size:15px;margin:0 0 2px;font-family:Georgia,serif}' +
            '.head p{margin:0;font-size:10px}' +
            '.copy-label{margin-top:5px;font-weight:bold;letter-spacing:1px;text-transform:uppercase}' +
            '.meta{border-top:1px dashed #000;border-bottom:1px dashed #000;padding:5px 0;margin-bottom:6px}' +
            '.meta div{display:flex;justify-content:space-between;font-size:10px;padding:1px 0}' +
            'table{width:100%;border-collapse:collapse;table-layout:fixed}' +'.lines .qty{word-break:break-word}' +'.amt{width:22mm}' +
            '.lines td{padding:2px 0;font-size:11px;vertical-align:top}' +
            '.lines .qty{padding-right:6px}' +
            '.amt{text-align:right;white-space:nowrap}' +
            '.totals{border-top:1px dashed #000;margin-top:5px;padding-top:4px}' +
            '.totals td{padding:2px 0;font-size:11px}' +
            '.grand td{font-size:13px;font-weight:bold;border-top:1px solid #000;padding-top:4px}' +
            '.foot{text-align:center;margin-top:8px;font-size:10px}' +
            '.foot p{margin:2px 0}' +
            '@media print{body{padding:0}@page{margin:4mm}}';

        // Built as one string and written in a single go so the popup has the
        // whole document before print() is called.
        var doc = '<!doctype html><html><head><meta charset="utf-8"><title>Receipt ' +
            escapeHtml(sale.reference) + '</title><style>' + styles + '</style></head><body>' +
            receiptCopy(sale, tendered, change, cashier, stamp, 'Customer Copy') +
            receiptCopy(sale, tendered, change, cashier, stamp, 'Store Copy') +
            '</body></html>';

        win.document.open();
        win.document.write(doc);
        win.document.close();

        win.onload = function () {
            win.focus();
            win.print();
        };
    }

    function completeSale() {
        if (busy || !cart.length) return;

        var cups = cart.reduce(function (n, l) { return n + l.qty; }, 0);
        var total = cart.reduce(function (sum, l) { return sum + l.unit * l.qty; }, 0) +
            (serve === 'take_out' ? TAKEOUT_FEE * cups : 0);
        var tendered = Number(document.getElementById('tendered').value || 0);

        if (pay === 'cash' && tendered && tendered < total) {
            note('Cash received is less than the total.', true);
            return;
        }

        busy = true;
        document.getElementById('completeBtn').disabled = true;
        note('Recording sale...');

        fetch(SALE_URL, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({
                service_type: serve,
                payment_method: pay,
                items: cart.map(function (l) { return { inventory_id: l.id, size: l.size, quantity: l.qty }; })
            })
        })
            .then(function (response) {
                return response.json().then(function (payload) {
                    if (!response.ok) {
                        var message = payload.message || 'The sale was refused.';
                        if (payload.errors) {
                            var first = Object.keys(payload.errors)[0];
                            if (first) message = payload.errors[first][0];
                        }
                        throw new Error(message);
                    }
                    return payload;
                });
            })
            .then(function (sale) {
                var change = pay === 'cash' && tendered ? tendered - Number(sale.total) : 0;
                clearCart();
                note('Sale ' + sale.reference + ' recorded' + (change > 0 ? ' · change ' + peso(change) : '') + '.');
                printReceipt(sale, tendered, change);

                var counter = document.getElementById('tillCount');
                counter.textContent = Number(counter.textContent || 0) + 1;

                // Stock has moved, so reload to show what is really left.
                setTimeout(function () { window.location.reload(); }, 2600);
            })
            .catch(function (error) { note(error.message, true); })
            .finally(function () {
                busy = false;
                renderTotals();
            });
    }

    document.querySelectorAll('.product-card').forEach(function (card) {
        card.addEventListener('click', function () { addToCart(card); });
    });

    var productSearch = document.getElementById('productSearch');
    if (productSearch) {
        productSearch.addEventListener('input', function () {
            var needle = productSearch.value.trim().toLowerCase();
            document.querySelectorAll('.product-card').forEach(function (card) {
                card.style.display = card.dataset.name.toLowerCase().indexOf(needle) === -1 ? 'none' : '';
            });
        });
    }

    renderCart();


    setupSidebar();
</script>
</body>
</html>


