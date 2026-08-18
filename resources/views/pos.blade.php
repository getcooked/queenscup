@php
    $pageTitle = $pageTitle ?? 'Point of Sale';
    $pageKey = $pageKey ?? 'pos';
    $pageDescription = $pageDescription ?? 'Create and manage sales transactions.';
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
                <div class="nav-section"><div class="nav-section-title">Management</div><a class="nav-item {{ $pageKey === 'orders' ? 'active' : '' }}" href="{{ url('/orders') }}"><i class="fas fa-receipt"></i> Orders</a><a class="nav-item {{ $pageKey === 'inventory' ? 'active' : '' }}" href="{{ url('/inventory') }}"><i class="fas fa-boxes-stacked"></i> Inventory</a></div>
                <div class="nav-section"><div class="nav-section-title">System</div><a class="nav-item {{ $pageKey === 'reports' ? 'active' : '' }}" href="{{ url('/reports') }}"><i class="fas fa-chart-bar"></i> Reports</a><a class="nav-item {{ $pageKey === 'settings' ? 'active' : '' }}" href="{{ url('/settings') }}"><i class="fas fa-gear"></i> Settings</a></div>
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
            <div><h1>{{ $pageTitle }}</h1><p>{{ $pageDescription }}</p></div>
        </div>
        <div class="stats">
            <div class="stat"><strong>₱{{ number_format((float) ($todaySales ?? 0), 2) }}</strong><span>Today's Sales</span></div>
            <div class="stat"><strong>{{ $todayTransactions ?? 0 }}</strong><span>Today's Transactions</span></div>
            <div class="stat"><strong>{{ ($inventoryItems ?? collect())->count() }}</strong><span>Available Products</span></div>
        </div>
        <div class="card">
            @if(($inventoryItems ?? collect())->isEmpty())
                <div class="empty">No inventory products yet. Add products in Inventory first.</div>
            @else
                <div class="product-grid">
                    @foreach($inventoryItems as $item)
                        @php
                            $stockClass = $item->stock <= 0 ? 'out' : ($item->stock <= 10 ? 'low' : 'ok');
                            $stockText = $item->stock <= 0 ? 'Out of stock' : ($item->stock <= 10 ? 'Low stock: '.$item->stock : 'In stock: '.$item->stock);
                            $itemImageUrl = $manualImageUrl($item->name) ?: ($item->image_path ? asset('storage/'.$item->image_path) : '');
                        @endphp
                        <div class="product-card">
                            @if($itemImageUrl)
                                <img class="product-image" src="{{ $itemImageUrl }}" alt="{{ $item->name }}">
                            @else
                                <div class="product-placeholder"><i class="fas fa-mug-hot"></i></div>
                            @endif
                            <div class="product-top">
                                <div class="product-name">{{ $item->name }}</div>
                                <div class="stock {{ $stockClass }}">{{ $stockText }}</div>
                            </div>
                            <div class="category">{{ $item->category ?: 'Uncategorized' }}</div>
                            <div class="prices">
                                <div class="price"><span>Regular</span><strong>₱{{ number_format((float) $item->regular_price, 2) }}</strong></div>
                                @if((float) $item->large_price > 0)
                                    <div class="price"><span>Large</span><strong>₱{{ number_format((float) $item->large_price, 2) }}</strong></div>
                                @endif
                            </div>
                            <div class="desc">{{ $item->description ?: 'No description' }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
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
            window.location.href = @json(route('login'));
        });
    }

    setupSidebar();
</script>
</body>
</html>


