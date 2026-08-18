@php
    $categories = [
        'Milktea Series',
        'Fruit Teas',
        'Milky Fruit Jams',
        'Lemonade',
        'Coffee & Non-Coffee',
        'Fruit Milk Shake',
        'Sticky Milk Drinks',
    ];
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
    <title>Inventory | The Queen's Cup</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="preload" href="{{ asset('vendor/fontawesome/webfonts/fa-solid-900.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link href="{{ asset('vendor/fontawesome/css/all.min.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --bg: #f7fbf8;
            --card: #ffffff;
            --line: #cfe7d7;
            --text: #123524;
            --muted: #5f7f6b;
            --green: #16c76a;
            --gold: #2f9e62;
            --yellow: #ffbd35;
            --danger: #ef3d5f;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            height: 100vh;
            background: radial-gradient(circle at top right, rgba(22, 199, 106, .12), transparent 34%), linear-gradient(180deg, #fbfefc 0%, var(--bg) 100%);
            color: var(--text);
            font-family: "DM Sans", sans-serif;
            overflow: hidden;
        }
        h1, h2, h3 { font-family: "Playfair Display", serif; }
        .layout { display: flex; height: 100vh; }
        .sidebar {
            width: 260px;
            background: #ffffff;
            border-right: 1px solid var(--line);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            box-shadow: 0 12px 32px rgba(18, 53, 36, .06);
        }
        .sidebar-brand {
            padding: 22px 18px;
            border-bottom: 1px solid var(--line);
            display: flex;
            align-items: center;
            gap: 13px;
        }
        .crown-icon {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
            border: 2px solid rgba(22, 199, 106, .3);
            background: linear-gradient(135deg, #0c6f3f, #16c76a);
        }
        .crown-icon img { width: 100%; height: 100%; object-fit: cover; }
        .sidebar-brand h2 {
            font-size: 14px;
            line-height: 1.2;
            background: linear-gradient(135deg, #16a65f, #2f9e62);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .tagline { font-size: 10px; color: var(--muted); letter-spacing: .5px; }
        .sidebar-nav { flex: 1; padding: 14px 10px; overflow-y: auto; }
        .nav-section-title {
            padding: 12px 10px 7px;
            color: var(--muted);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            min-height: 40px;
            padding: 0 12px;
            border-radius: 10px;
            color: var(--muted);
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: background .2s ease, color .2s ease;
        }
        .nav-item i { width: 20px; text-align: center; }
        .nav-item:hover, .nav-item.active {
            background: rgba(22, 199, 106, .13);
            color: var(--green);
            box-shadow: inset 3px 0 0 var(--green);
        }
        .sidebar-footer {
            padding: 14px 18px;
            border-top: 1px solid var(--line);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .avatar {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--green), var(--gold));
            display: grid;
            place-items: center;
            font-size: 13px;
            font-weight: 800;
            color: #fff;
        }
        .user-info { flex: 1; min-width: 0; }
        .user-info .name { font-size: 12px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-info .role { font-size: 10px; color: var(--muted); }
        .logout-btn {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            border: 1px solid var(--line);
            background: var(--card);
            color: var(--muted);
            cursor: pointer;
        }
        .logout-btn:hover { border-color: var(--danger); color: var(--danger); }
        .content { flex: 1; min-width: 0; overflow: auto; padding: 30px; }
        .top { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 22px; }
        .top h1 { font-size: 36px; }
        .top p { color: var(--muted); margin-top: 5px; }
        .stats { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; margin-bottom: 18px; }
        .stat, .card {
            background: var(--card);
            border: 1px solid #d8ebdf;
            border-radius: 14px;
            box-shadow: 0 12px 32px rgba(18, 53, 36, .07);
        }
        .stat { padding: 18px; }
        .stat strong { display: block; font-size: 28px; }
        .stat span { display: block; margin-top: 5px; color: var(--muted); font-size: 13px; }
        .card { padding: 20px; margin-bottom: 18px; }
        .toolbar { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 16px; }
        .toolbar h3 { font-size: 20px; }
        .toolbar-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .table-filter { position: relative; width: 260px; }
        .table-filter i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 13px; }
        .table-filter input, .search input {
            height: 40px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #fff;
            color: var(--text);
            font: inherit;
            font-size: 13px;
        }
        .table-filter input { width: 100%; padding: 0 12px 0 34px; }
        .table-filter input:focus, .search input:focus { outline: 0; border-color: var(--green); box-shadow: 0 0 0 3px rgba(22, 199, 106, .12); }
        .btn {
            min-height: 40px;
            border: 0;
            border-radius: 10px;
            padding: 0 14px;
            background: var(--green);
            color: #ffffff;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: transform .18s ease, box-shadow .18s ease;
        }
        .btn:hover { transform: translateY(-1px); box-shadow: 0 10px 24px rgba(18, 134, 78, .15); }
        .btn.secondary { background: #eef8f1; color: var(--muted); border: 1px solid var(--line); }
        .btn.danger { background: rgba(239, 61, 95, .14); color: #ff7390; border: 1px solid rgba(239, 61, 95, .35); }
        .search { display: flex; gap: 8px; }
        .search input { width: 280px; padding: 0 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 13px 12px; border-bottom: 1px solid var(--line); text-align: left; font-size: 13px; vertical-align: middle; }
        th { color: var(--muted); font-size: 11px; text-transform: uppercase; letter-spacing: .8px; }
        td:last-child { white-space: nowrap; }
        tr:hover td { background: #f8fcf9; }
        .item-cell { display: flex; align-items: center; gap: 12px; }
        .item-cell strong { font-size: 14px; }
        .item-desc { color: var(--muted); font-size: 12px; margin-top: 4px; }
        .item-thumb, .item-placeholder {
            width: 54px;
            height: 54px;
            border-radius: 10px;
            background: #ffffff;
            flex-shrink: 0;
        }
        .item-thumb { object-fit: cover; border: 1px solid var(--line); }
        .item-placeholder { border: 1px dashed var(--line); display: grid; place-items: center; color: var(--muted); }
        .status { display: inline-flex; padding: 4px 9px; border-radius: 999px; font-size: 11px; font-weight: 800; white-space: nowrap; }
        .ok { background: rgba(22, 199, 106, .14); color: #168a51; }
        .low { background: rgba(255, 189, 53, .14); color: var(--yellow); }
        .out { background: rgba(239, 47, 131, .14); color: #d82875; }
        .alert { border: 1px solid rgba(22, 199, 106, .35); background: rgba(22, 199, 106, .1); color: #168a51; border-radius: 12px; padding: 12px 14px; margin-bottom: 16px; }
        .error { border-color: rgba(239, 61, 95, .4); background: rgba(239, 61, 95, .1); color: #d82850; }
        .pagination { margin-top: 16px; color: var(--muted); font-size: 13px; }
        .pagination nav > div { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .pagination nav > div:first-child { display: none; }
        .pagination nav > div:last-child { display: flex; }
        .pagination p { margin: 0; }
        .pagination a, .pagination span { font-size: 13px; }
        .pagination span[aria-current="page"] span, .pagination a[aria-label], .pagination span[aria-disabled="true"] span {
            min-width: 36px;
            min-height: 36px;
            padding: 0 12px;
            border: 1px solid var(--line);
            background: #fff;
            color: var(--muted);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-weight: 700;
        }
        .pagination span[aria-current="page"] span { background: var(--green); border-color: var(--green); color: #fff; }
        .pagination a:hover { border-color: var(--green); color: var(--green); }
        .pagination svg { width: 16px; height: 16px; display: block; }
        .swal2-popup.inventory-modal { background: #fff; color: var(--text); border: 1px solid var(--line); border-radius: 14px; }
        .swal2-title { font-family: "Playfair Display", serif; }
        .swal-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; text-align: left; }
        .swal-field.full { grid-column: 1 / -1; }
        .swal-field label { display: block; color: var(--muted); font-size: 11px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
        .swal-field input, .swal-field select, .swal-field textarea { width: 100%; border: 1px solid var(--line); border-radius: 10px; background: #ffffff; color: var(--text); padding: 10px 12px; font: inherit; }
        .swal-field textarea { min-height: 84px; resize: vertical; }
        .swal-image-preview { width: 88px; height: 88px; border-radius: 12px; object-fit: cover; border: 1px solid var(--line); background: #ffffff; margin-bottom: 8px; }
        @media (max-width: 1100px) {
            body { overflow: auto; height: auto; }
            .layout { height: auto; }
            .content { padding: 20px; }
            .stats { grid-template-columns: 1fr; }
            .top, .toolbar { align-items: flex-start; flex-direction: column; }
            .search, .toolbar-actions, .table-filter, .search input { width: 100%; }
            .swal-grid { grid-template-columns: 1fr; }
        }
    </style>
    <link href="{{ asset('css/admin-shell.css') }}" rel="stylesheet">
    <script src="{{ asset('js/admin-sidebar.js') }}" defer></script>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="crown-icon" id="sidebarCrown">
                <img src="{{ asset('icons/queens-cup-logo.png') }}" alt="Logo">
            </div>
            <div>
                <h2>The Queen's Cup</h2>
                <span class="tagline">Crowned with Flavors</span>
            </div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section"><div class="nav-section-title">Main</div><a class="nav-item" href="{{ url('/dashboard') }}"><i class="fas fa-chart-pie"></i> Dashboard</a><a class="nav-item" href="{{ url('/pos') }}"><i class="fas fa-cash-register"></i> Point of Sale</a></div>
            <div class="nav-section"><div class="nav-section-title">Management</div><a class="nav-item" href="{{ url('/orders') }}"><i class="fas fa-receipt"></i> Orders</a><a class="nav-item" href="{{ url('/reservations') }}"><i class="fas fa-calendar-check"></i> Reservations</a><a class="nav-item active" href="{{ url('/inventory') }}"><i class="fas fa-boxes-stacked"></i> Inventory</a></div>
            <div class="nav-section"><div class="nav-section-title">System</div><a class="nav-item" href="{{ url('/reports') }}"><i class="fas fa-chart-bar"></i> Reports</a><a class="nav-item" href="{{ url('/settings') }}"><i class="fas fa-gear"></i> Settings</a></div>
            <div class="nav-section"><div class="nav-section-title">Account</div><a class="nav-item" href="{{ url('/profile') }}"><i class="fas fa-user-circle"></i> My Profile</a></div>
        </nav>
        <div class="sidebar-footer">
            <div class="avatar" id="sidebarAvatar">?</div>
            <div class="user-info">
                <div class="name" id="sidebarName">User</div>
                <div class="role" id="sidebarRole">Role</div>
            </div>
            <button class="logout-btn" onclick="handleLogout()" title="Sign Out"><i class="fas fa-right-from-bracket"></i></button>
        </div>
    </aside>

    <main class="content">
        <div class="top">
            <div><h1>Inventory</h1><p>Create, update, search, and delete inventory items.</p></div>
            <form class="search" method="GET" action="{{ url('/inventory') }}">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search inventory">
                <button class="btn secondary" type="submit"><i class="fas fa-search"></i> Search</button>
            </form>
        </div>

        @if (session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert error">{{ $errors->first() }}</div>
        @endif

        <div class="stats">
            <div class="stat"><strong>{{ $totalItems }}</strong><span>Total Items</span></div>
            <div class="stat"><strong>{{ $lowStock }}</strong><span>Low Stock</span></div>
            <div class="stat"><strong>{{ $outOfStock }}</strong><span>Out of Stock</span></div>
        </div>

        <section class="card">
            <div class="toolbar">
                <h3>Inventory Items</h3>
                <div class="toolbar-actions">
                    <div class="table-filter">
                        <i class="fas fa-search"></i>
                        <input id="inventoryItemFilter" type="search" placeholder="Filter items" oninput="filterInventoryItems()">
                    </div>
                    <button class="btn" type="button" onclick="openInventoryModal()"><i class="fas fa-plus"></i> Add Item</button>
                </div>
            </div>
            <table>
                <thead><tr><th>Item</th><th>Category</th><th>Prices</th><th>Stock</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody id="inventoryItemsBody">
                @forelse ($items as $item)
                    @php
                        $itemImageUrl = $manualImageUrl($item->name) ?: ($item->image_path ? asset('storage/'.$item->image_path) : '');
                    @endphp
                    <tr class="inventory-row" data-filter="{{ strtolower($item->name.' '.$item->category.' '.$item->description.' '.$item->stock) }}">
                        <td>
                            <div class="item-cell">
                                @if ($itemImageUrl)
                                    <img class="item-thumb" src="{{ $itemImageUrl }}" alt="{{ $item->name }}">
                                @else
                                    <div class="item-placeholder"><i class="fas fa-image"></i></div>
                                @endif
                                <div><strong>{{ $item->name }}</strong><div class="item-desc">{{ $item->description ?: 'No description' }}</div></div>
                            </div>
                        </td>
                        <td>{{ $item->category ?: '-' }}</td>
                        <td>
                            &#8369;{{ number_format($item->regular_price, 2) }}
                            @if((float) $item->large_price > 0)
                                / &#8369;{{ number_format($item->large_price, 2) }}
                            @endif
                        </td>
                        <td>{{ $item->stock }}</td>
                        <td>
                            @if ($item->stock == 0)<span class="status out">Out</span>
                            @elseif ($item->stock <= 10)<span class="status low">Low</span>
                            @else<span class="status ok">In Stock</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn secondary" type="button" onclick="openInventoryModal({{ $item->id }})"><i class="fas fa-pen"></i> Edit</button>
                            <button class="btn danger" type="button" onclick="confirmDelete({{ $item->id }})"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:28px">No inventory items yet.</td></tr>
                @endforelse
                    <tr id="inventoryFilterEmpty" style="display:none"><td colspan="6" style="text-align:center;color:var(--muted);padding:28px">No items match your filter.</td></tr>
                </tbody>
            </table>
            <div class="pagination">{{ $items->links() }}</div>
        </section>

        <form id="inventoryCrudForm" method="POST" enctype="multipart/form-data" style="display:none">
            @csrf
            <input type="hidden" name="_method" id="crudMethod" value="POST">
            <input type="hidden" name="name" id="crudName">
            <input type="hidden" name="category" id="crudCategory">
            <input type="hidden" name="regular_price" id="crudRegularPrice">
            <input type="hidden" name="large_price" id="crudLargePrice">
            <input type="hidden" name="stock" id="crudStock">
            <input type="hidden" name="description" id="crudDescription">
            <input type="file" name="image" id="crudImage" accept="image/*">
        </form>
    </main>
</div>
<script>
    var inventoryItems = @json($items->items());
    var categories = @json($categories);
    var storeUrl = '{{ url('/inventory') }}';
    var storageBaseUrl = '{{ asset('storage') }}';
    var defaultLogo = '{{ asset('icons/queens-cup-logo.png') }}';
    var manualProductImageBase = '{{ asset('images/manual-menu-products') }}';
    var manualProductImages = @json($manualImages);

    function manualImageUrl(name) {
        var key = String(name || '').toLowerCase().replace(/[^a-z0-9'\s]/g, '').replace(/\s+/g, ' ').trim();
        return manualProductImages[key] ? manualProductImageBase + '/' + manualProductImages[key] : '';
    }

    function setupSidebar() {
        var session = null;
        try {
            session = JSON.parse(localStorage.getItem('qc_session'));
        } catch (error) {
            session = null;
        }

        var logo = localStorage.getItem('qc_logo') || defaultLogo;
        document.getElementById('sidebarCrown').innerHTML = '<img src="' + logo + '" alt="Logo">';

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

    function filterInventoryItems() {
        var input = document.getElementById('inventoryItemFilter');
        var emptyRow = document.getElementById('inventoryFilterEmpty');
        var query = input ? input.value.trim().toLowerCase() : '';
        var visibleCount = 0;

        document.querySelectorAll('.inventory-row').forEach(function (row) {
            var matches = !query || (row.dataset.filter || '').indexOf(query) !== -1;
            row.style.display = matches ? '' : 'none';
            if (matches) visibleCount++;
        });

        if (emptyRow) {
            emptyRow.style.display = query && visibleCount === 0 ? '' : 'none';
        }
    }

    function escapeHtml(value) {
        return String(value || '').replace(/[&<>"']/g, function (char) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[char];
        });
    }

    function categoryOptions(selected) {
        return '<option value="">Select category</option>' + categories.map(function (category) {
            var isSelected = category === selected ? ' selected' : '';
            return '<option value="' + escapeHtml(category) + '"' + isSelected + '>' + escapeHtml(category) + '</option>';
        }).join('');
    }

    function modalHtml(item) {
        item = item || {};
        var imageUrl = manualImageUrl(item.name) || (item.image_path ? storageBaseUrl + '/' + item.image_path : '');
        return '<div class="swal-grid">' +
            '<div class="swal-field full">' +
                '<label>Product Picture</label>' +
                (imageUrl ? '<img class="swal-image-preview" id="swalImagePreview" src="' + escapeHtml(imageUrl) + '" alt="Preview">' : '<div class="item-placeholder" id="swalImagePreview" style="margin-bottom:8px"><i class="fas fa-image"></i></div>') +
                '<input id="swalImage" type="file" accept="image/*">' +
                '<div style="color:var(--muted);font-size:11px;margin-top:6px">Upload JPG, PNG, WEBP, or GIF up to 2MB. This picture appears in the customer menu.</div>' +
            '</div>' +
            '<div class="swal-field full"><label>Name</label><input id="swalName" value="' + escapeHtml(item.name) + '"></div>' +
            '<div class="swal-field"><label>Category</label><select id="swalCategory">' + categoryOptions(item.category) + '</select></div>' +
            '<div class="swal-field"><label>Stock</label><input id="swalStock" type="number" min="0" value="' + escapeHtml(item.stock || 0) + '"></div>' +
            '<div class="swal-field"><label>Regular Price</label><input id="swalRegularPrice" type="number" min="0" step="0.01" value="' + escapeHtml(item.regular_price || 0) + '"></div>' +
            '<div class="swal-field"><label>Large Price</label><input id="swalLargePrice" type="number" min="0" step="0.01" value="' + escapeHtml(item.large_price || 0) + '"></div>' +
            '<div class="swal-field full"><label>Description</label><textarea id="swalDescription">' + escapeHtml(item.description) + '</textarea></div>' +
        '</div>';
    }

    function readModalValues() {
        return {
            name: document.getElementById('swalName').value.trim(),
            category: document.getElementById('swalCategory').value,
            regular_price: document.getElementById('swalRegularPrice').value,
            large_price: document.getElementById('swalLargePrice').value,
            stock: document.getElementById('swalStock').value,
            description: document.getElementById('swalDescription').value.trim()
        };
    }

    function submitInventory(action, method, values) {
        localStorage.removeItem('qc_products');
        var form = document.getElementById('inventoryCrudForm');
        var formData = new FormData(form);
        var imageInput = document.getElementById('swalImage');

        formData.set('_method', method);
        formData.set('name', values.name || '');
        formData.set('category', values.category || '');
        formData.set('regular_price', values.regular_price || 0);
        formData.set('large_price', values.large_price || 0);
        formData.set('stock', values.stock || 0);
        formData.set('description', values.description || '');

        if (imageInput && imageInput.files.length > 0) {
            formData.set('image', imageInput.files[0]);
        } else {
            formData.delete('image');
        }

        Swal.fire({
            title: 'Saving item...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            customClass: { popup: 'inventory-modal' },
            didOpen: function () {
                Swal.showLoading();
            }
        });

        fetch(action, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
            headers: {
                'Accept': 'text/html,application/xhtml+xml'
            }
        }).then(function (response) {
            window.location.href = response.url || '{{ url('/inventory') }}';
        }).catch(function () {
            Swal.fire({
                icon: 'error',
                title: 'Unable to save',
                text: 'Please check your connection and try again.',
                customClass: { popup: 'inventory-modal' }
            });
        });
    }

    function removeCachedOrderProduct(id) {
        try {
            var products = JSON.parse(localStorage.getItem('qc_products') || '[]');
            if (!Array.isArray(products)) return;

            products = products.filter(function (product) {
                return Number(product.id) !== Number(id);
            });

            localStorage.setItem('qc_products', JSON.stringify(products));
        } catch (error) {
            localStorage.removeItem('qc_products');
        }
    }

    async function openInventoryModal(id) {
        var item = inventoryItems.find(function (entry) { return Number(entry.id) === Number(id); }) || null;
        var result = await Swal.fire({
            title: item ? 'Edit Inventory Item' : 'Add Inventory Item',
            html: modalHtml(item),
            customClass: { popup: 'inventory-modal' },
            showCancelButton: true,
            confirmButtonText: item ? 'Update Item' : 'Add Item',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#16c76a',
            cancelButtonColor: '#eef8f1',
            focusConfirm: false,
            didOpen: function () {
                var imageInput = document.getElementById('swalImage');
                var preview = document.getElementById('swalImagePreview');
                imageInput.addEventListener('change', function () {
                    if (!imageInput.files.length) return;
                    var file = imageInput.files[0];
                    if (!file.type.match(/^image\//)) {
                        Swal.showValidationMessage('Please choose an image file.');
                        imageInput.value = '';
                        return;
                    }
                    if (file.size > 2 * 1024 * 1024) {
                        Swal.showValidationMessage('Image must be 2MB or smaller.');
                        imageInput.value = '';
                        return;
                    }
                    var url = URL.createObjectURL(file);
                    if (preview.tagName.toLowerCase() === 'img') {
                        preview.src = url;
                    } else {
                        preview.outerHTML = '<img class="swal-image-preview" id="swalImagePreview" src="' + url + '" alt="Preview">';
                    }
                });
            },
            preConfirm: function () {
                var values = readModalValues();
                var imageInput = document.getElementById('swalImage');
                if (!values.name) {
                    Swal.showValidationMessage('Product name is required.');
                    return false;
                }
                if (Number(values.regular_price) < 0 || Number(values.large_price) < 0 || Number(values.stock) < 0) {
                    Swal.showValidationMessage('Prices and stock must be zero or higher.');
                    return false;
                }
                if (imageInput.files.length > 0 && imageInput.files[0].size > 2 * 1024 * 1024) {
                    Swal.showValidationMessage('Image must be 2MB or smaller.');
                    return false;
                }
                return values;
            }
        });

        if (!result.isConfirmed) return;
        submitInventory(item ? storeUrl + '/' + item.id : storeUrl, item ? 'PUT' : 'POST', result.value);
    }

    async function confirmDelete(id) {
        var item = inventoryItems.find(function (entry) { return Number(entry.id) === Number(id); });
        var result = await Swal.fire({
            title: 'Delete item?',
            text: item ? 'This will remove "' + item.name + '" from inventory.' : 'This item will be removed from inventory.',
            icon: 'warning',
            customClass: { popup: 'inventory-modal' },
            showCancelButton: true,
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#ef3d5f',
            cancelButtonColor: '#eef8f1'
        });

        if (!result.isConfirmed) return;
        removeCachedOrderProduct(id);
        submitInventory(storeUrl + '/' + id, 'DELETE', {});
    }

    @if (session('success'))
        Swal.fire({ icon: 'success', title: 'Saved', text: @json(session('success')), timer: 1800, showConfirmButton: false, customClass: { popup: 'inventory-modal' } });
    @endif

    setupSidebar();
</script>
</body>
</html>
