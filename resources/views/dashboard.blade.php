<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | The Queen's Cup</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --bg: #f7fbf8;
            --bg-deep: #ffffff;
            --card: #ffffff;
            --card-hover: #e9f7ee;
            --border: #cfe7d7;
            --fg: #123524;
            --fg-muted: #5f7f6b;
            --accent: #12864e;
            --accent-light: #16a65f;
            --accent-dark: #0c6f3f;
            --gold: #2f9e62;
            --gold-light: #45b873;
            --danger: #e53170;
            --warning: #f5a623;
            --info: #5b8def;
            --success: #2cb67d;
            --radius: 14px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { height: 100vh; overflow: hidden; background: var(--bg); color: var(--fg); font-family: "DM Sans", sans-serif; }
        body::-webkit-scrollbar, .page-content::-webkit-scrollbar, .sidebar-nav::-webkit-scrollbar { display: none; }
        h1, h2, h3 { font-family: "Playfair Display", serif; }
        button, select { font: inherit; }

        .app-layout { height: 100vh; display: flex; }
        .sidebar { width: 260px; background: var(--bg-deep); border-right: 1px solid var(--border); display: flex; flex-direction: column; flex-shrink: 0; }
        .sidebar-brand { padding: 22px 18px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 13px; }
        .crown-icon { width: 46px; height: 46px; border-radius: 50%; overflow: hidden; flex-shrink: 0; border: 2px solid rgba(14,140,74,0.3); background: linear-gradient(135deg, var(--accent-dark), var(--accent)); }
        .crown-icon img { width: 100%; height: 100%; object-fit: cover; }
        .sidebar-brand h2 { font-size: 14px; line-height: 1.2; background: linear-gradient(135deg, var(--accent-light), var(--gold-light)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .tagline { font-size: 10px; color: var(--fg-muted); letter-spacing: 0.5px; }
        .sidebar-nav { flex: 1; padding: 14px 10px; overflow-y: auto; scrollbar-width: none; -ms-overflow-style: none; }
        .nav-section-title { padding: 12px 10px 7px; color: var(--fg-muted); font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }
        .nav-item { display: flex; align-items: center; gap: 10px; min-height: 40px; padding: 0 12px; border-radius: 10px; color: var(--fg-muted); cursor: pointer; font-size: 13px; font-weight: 600; text-decoration: none; transition: background 0.2s ease, color 0.2s ease; }
        .nav-item:hover, .nav-item.active { background: rgba(14, 140, 74, 0.13); color: var(--accent-light); }
        .sidebar-footer { padding: 14px 18px; border-top: 1px solid var(--border); display: flex; align-items: center; gap: 12px; }
        .avatar { width: 34px; height: 34px; border-radius: 10px; background: linear-gradient(135deg, var(--accent), var(--gold)); display: grid; place-items: center; font-size: 13px; font-weight: 800; }
        .user-info { flex: 1; min-width: 0; }
        .user-info .name { font-size: 12px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-info .role { font-size: 10px; color: var(--fg-muted); }
        .logout-btn { width: 30px; height: 30px; border-radius: 8px; border: 1px solid var(--border); background: var(--card); color: var(--fg-muted); cursor: pointer; }
        .logout-btn:hover { border-color: var(--danger); color: var(--danger); }

        .main-content { flex: 1; min-width: 0; display: flex; flex-direction: column; }
        .topbar { height: 74px; padding: 0 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; background: rgba(255, 255, 255, 0.92); }
        .topbar-title { font-family: "Playfair Display", serif; font-size: 24px; font-weight: 700; }
        .topbar-breadcrumb { margin-top: 3px; color: var(--fg-muted); font-size: 11px; }
        .branch-select { height: 38px; padding: 0 12px; border: 1px solid var(--border); border-radius: 10px; background: var(--card); color: var(--fg); outline: none; }
        .page-content { flex: 1; overflow: auto; padding: 24px; scrollbar-width: none; -ms-overflow-style: none; }

        .stats-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; margin-bottom: 20px; }
        .stat-card, .card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: 0 8px 32px rgba(18,53,36,0.10); }
        .stat-card { padding: 20px; }
        .stat-icon { width: 42px; height: 42px; border-radius: 12px; display: grid; place-items: center; margin-bottom: 14px; }
        .stat-value { font-size: 28px; font-weight: 800; line-height: 1; }
        .stat-label { margin-top: 7px; color: var(--fg-muted); font-size: 12px; }
        .stat-change { margin-top: 12px; font-size: 11px; font-weight: 700; }
        .stat-change.up { color: var(--success); }
        .stat-change.down { color: var(--warning); }
        .grid-2 { display: grid; grid-template-columns: minmax(0, 1.15fr) minmax(320px, 0.85fr); gap: 16px; margin-bottom: 20px; }
        .card-header { min-height: 56px; padding: 0 18px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .card-header h3 { font-size: 17px; }
        .card-body { padding: 18px; }
        .sales-bars { height: 220px; display: grid; grid-template-columns: repeat(7, 1fr); align-items: end; gap: 12px; }
        .sales-bars.empty-state { display: flex; align-items: center; justify-content: center; color: var(--fg-muted); font-size: 12px; text-align: center; }
        .bar-wrap { height: 100%; display: flex; flex-direction: column; justify-content: end; gap: 8px; color: var(--fg-muted); font-size: 10px; text-align: center; }
        .bar { min-height: 18px; border-radius: 8px 8px 3px 3px; background: linear-gradient(180deg, var(--accent-light), var(--accent-dark)); box-shadow: 0 8px 20px rgba(14,140,74,0.2); }
        .category-sales { display: grid; grid-template-columns: minmax(220px, 280px) 1fr; gap: 18px; align-items: center; }
        .donut-wrap { position: relative; width: 100%; max-width: 280px; aspect-ratio: 1; margin: 0 auto; }
        .donut-wrap canvas { width: 100%; height: 100%; display: block; }
        .donut-center { position: absolute; inset: 32%; border-radius: 50%; background: #fff; border: 1px solid var(--border); display: grid; place-items: center; text-align: center; padding: 10px; }
        .donut-center strong { display: block; font-size: 20px; line-height: 1; color: var(--accent); }
        .donut-center span { display: block; margin-top: 5px; color: var(--fg-muted); font-size: 10px; text-transform: uppercase; letter-spacing: .6px; }
        .category-legend { display: grid; gap: 10px; }
        .legend-row { display: grid; grid-template-columns: 12px 1fr auto; gap: 10px; align-items: center; color: var(--fg-muted); font-size: 12px; }
        .legend-dot { width: 12px; height: 12px; border-radius: 50%; }
        .legend-row strong { color: var(--fg); font-size: 13px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 13px 16px; border-bottom: 1px solid var(--border); text-align: left; font-size: 12px; }
        th { color: var(--fg-muted); font-size: 10px; text-transform: uppercase; letter-spacing: 0.7px; }
        tr:hover td { background: rgba(18, 42, 29, 0.42); }
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 8px; border-radius: 999px; font-size: 10px; font-weight: 800; }
        .badge-success { background: rgba(44,182,125,0.13); color: var(--success); }
        .badge-warning { background: rgba(245,166,35,0.13); color: var(--warning); }
        .badge-info { background: rgba(91,141,239,0.13); color: var(--info); }
        .badge-gold { background: rgba(201,168,76,0.13); color: var(--gold-light); }
        .btn { min-height: 34px; display: inline-flex; align-items: center; gap: 7px; padding: 0 12px; border: 1px solid var(--border); border-radius: 10px; background: var(--card-hover); color: var(--fg); text-decoration: none; font-size: 12px; font-weight: 800; cursor: pointer; }
        .btn:hover { border-color: var(--accent); color: var(--accent-light); }
        .empty { padding: 28px; color: var(--fg-muted); text-align: center; }

        @media (max-width: 960px) {
            body { overflow: auto; height: auto; min-height: 100vh; }
            .app-layout { height: auto; min-height: 100vh; }
            .sidebar { display: none; }
            .stats-grid, .grid-2 { grid-template-columns: 1fr; }
            .category-sales { grid-template-columns: 1fr; }
            .topbar { padding: 0 16px; }
            .page-content { padding: 16px; }
        }
        body{background:radial-gradient(circle at top right,rgba(22,199,106,.08),transparent 34%),linear-gradient(180deg,#fbfefc 0%,var(--bg) 100%)}.sidebar,.stat-card,.card{box-shadow:0 12px 32px rgba(18,53,36,.07)}.stat-card,.card{border-color:#d8ebdf}.nav-item.active{box-shadow:inset 3px 0 0 var(--accent)}.stat-card{background:#fff}.card{background:#fff}.stat-card:hover,.card:hover{box-shadow:0 16px 36px rgba(18,53,36,.12);border-color:rgba(18,134,78,.28)}.topbar{background:rgba(255,255,255,.96);box-shadow:0 8px 26px rgba(18,53,36,.05)}.page-title{font-size:36px}.btn{transition:transform .18s ease,box-shadow .18s ease}.btn:hover{transform:translateY(-1px);box-shadow:0 10px 24px rgba(18,134,78,.15)}
    </style>
</head>
<body>
    <div class="app-layout">
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
            <nav class="sidebar-nav" id="sidebarNav"></nav>
            <div class="sidebar-footer">
                <div class="avatar" id="sidebarAvatar">?</div>
                <div class="user-info">
                    <div class="name" id="sidebarName">User</div>
                    <div class="role" id="sidebarRole">Role</div>
                </div>
                <button class="logout-btn" onclick="handleLogout()" title="Sign Out"><i class="fas fa-right-from-bracket"></i></button>
            </div>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <div>
                    <div class="topbar-title">Dashboard</div>
                </div>
            </header>

            <section class="page-content">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background:rgba(201,168,76,0.15);color:var(--gold)"><i class="fas fa-peso-sign"></i></div>
                        <div class="stat-value" id="statRevenue">&#8369;0</div>
                        <div class="stat-label">Today's Sales</div>
                        <div class="stat-change up"><i class="fas fa-arrow-up"></i> Live</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background:rgba(14,140,74,0.15);color:var(--accent-light)"><i class="fas fa-mug-hot"></i></div>
                        <div class="stat-value" id="statOrders">0</div>
                        <div class="stat-label">Orders Today</div>
                        <div class="stat-change up"><i class="fas fa-arrow-up"></i> Live</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background:rgba(245,166,35,0.15);color:var(--warning)"><i class="fas fa-money-bill-wave"></i></div>
                        <div class="stat-value" id="statCashPending">0</div>
                        <div class="stat-label">Cash Pending</div>
                        <div class="stat-change down"><i class="fas fa-clock"></i> Awaiting</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background:rgba(91,141,239,0.15);color:var(--info)"><i class="fas fa-users"></i></div>
                        <div class="stat-value" id="statUsers">1</div>
                        <div class="stat-label">Registered Staff</div>
                        <div class="stat-change up"><i class="fas fa-check"></i> Active</div>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="card">
                        <div class="card-header"><h3>Sales Overview</h3><span class="badge badge-gold">This Week</span></div>
                        <div class="card-body"><div class="sales-bars" id="salesBars"></div></div>
                    </div>
                    <div class="card">
                        <div class="card-header"><h3>Popular Items</h3><span class="badge badge-gold"><i class="fas fa-crown"></i> Today</span></div>
                        <div class="card-body" style="padding:0">
                            <table>
                                <thead><tr><th>Item</th><th>Sold</th><th>Revenue</th></tr></thead>
                                <tbody id="popularItemsTable"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h3>Sales by Category</h3><span class="badge badge-gold" id="topCategoryBadge">No sales yet</span></div>
                    <div class="card-body">
                        <div class="category-sales">
                            <div class="donut-wrap">
                                <canvas id="categoryDonutChart" aria-label="Sales by category donut chart"></canvas>
                                <div class="donut-center"><div><strong id="categoryDonutTotal">0</strong><span>Items Sold</span></div></div>
                            </div>
                            <div class="category-legend" id="categoryLegend"></div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Recent Orders</h3>
                        <a class="btn" href="{{ url('/orders') }}"><i class="fas fa-receipt"></i> View Orders</a>
                    </div>
                    <div class="card-body" style="padding:0">
                        <table>
                            <thead><tr><th>Order ID</th><th>Customer</th><th>Total</th><th>Payment</th><th>Status</th><th>Time</th></tr></thead>
                            <tbody id="recentOrdersTable"></tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>

    @php
        $staffUserPayload = [
            'id' => $staffUser->id,
            'username' => $staffUser->email,
            'role' => $staffUser->role,
            'fullName' => $staffUser->name,
            'since' => optional($staffUser->created_at)->toDateString(),
        ];
    @endphp

    <script>
        var staffUser = @json($staffUserPayload);
        var LOGO_URL = '{{ asset('icons/queens-cup-logo.png') }}';
        var todayStr = new Date().toLocaleDateString();
        var defaultOrders = [];

        function clearAllOrderRecords() {
            if (localStorage.getItem('qc_orderRecordsCleared_v1') === '1') return;
            ['qc_orders','qc_nextOrderId','qc_notifReadIds'].forEach(function(key) { localStorage.removeItem(key); });
            for (var i = localStorage.length - 1; i >= 0; i--) {
                var key = localStorage.key(i);
                if (key && key.indexOf('qc_guest_orders_') === 0) localStorage.removeItem(key);
            }
            localStorage.setItem('qc_orderRecordsCleared_v1', '1');
        }
        clearAllOrderRecords();

        function removeSeedOrders() {
            if (localStorage.getItem('qc_seedOrdersCleared') === '1') return;
            var orders = getData('orders', []);
            if (Array.isArray(orders)) {
                orders = orders.filter(function(order) {
                    return Number(order.id) < 1001 || Number(order.id) > 1008;
                });
                localStorage.setItem('qc_orders', JSON.stringify(orders));
            }
            localStorage.setItem('qc_seedOrdersCleared', '1');
        }

        function getData(key, fallback) {
            try {
                var stored = localStorage.getItem('qc_' + key);
                return stored ? JSON.parse(stored) : fallback;
            } catch (error) {
                return fallback;
            }
        }

        function money(value) {
            return '\u20B1' + Number(value || 0).toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }

        function escapeHtml(value) {
            return String(value || '').replace(/[&<>"']/g, function(char) {
                return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[char];
            });
        }

        function parseOrderDate(value) {
            var parsed = new Date(value);
            return isNaN(parsed.getTime()) ? null : parsed;
        }

        function sameDate(left, right) {
            return left && right &&
                left.getFullYear() === right.getFullYear() &&
                left.getMonth() === right.getMonth() &&
                left.getDate() === right.getDate();
        }

        function getStatusBadge(status) {
            var badges = {
                pending: '<span class="badge badge-warning"><i class="fas fa-clock"></i> Pending</span>',
                preparing: '<span class="badge badge-info"><i class="fas fa-blender"></i> Preparing</span>',
                serving: '<span class="badge badge-info"><i class="fas fa-concierge-bell"></i> Serving</span>',
                completed: '<span class="badge badge-success"><i class="fas fa-check"></i> Completed</span>',
                cancelled: '<span class="badge" style="background:rgba(229,49,112,0.13);color:var(--danger)"><i class="fas fa-times"></i> Cancelled</span>'
            };
            return badges[status] || status;
        }

        function getPaymentBadge(order) {
            if (order.paymentStatus === 'pending') {
                return '<span class="badge badge-warning"><i class="fas fa-money-bill-wave"></i> Pending</span>';
            }
            return '<span class="badge badge-success"><i class="fas fa-check"></i> Paid</span>';
        }

        function renderDashboard() {
            var branch = 'kotapark';
            var orders = getData('orders', defaultOrders);
            var users = getData('users', [staffUser]);
            var todayOrders = orders.filter(function(order) {
                return order.date === todayStr && order.branch === branch;
            });
            var revenue = todayOrders.filter(function(order) {
                return order.status !== 'cancelled' && order.paymentStatus !== 'pending';
            }).reduce(function(sum, order) { return sum + Number(order.total || 0); }, 0);
            var cashPending = todayOrders.filter(function(order) {
                return order.payment === 'Cash' && order.paymentStatus === 'pending';
            }).length;

            document.getElementById('statRevenue').textContent = money(revenue);
            document.getElementById('statOrders').textContent = todayOrders.length;
            document.getElementById('statCashPending').textContent = cashPending;
            document.getElementById('statUsers').textContent = users.length || 1;

            renderPopularItems(todayOrders);
            renderRecentOrders(orders.filter(function(order) { return order.branch === branch; }));
            renderSalesBars(orders.filter(function(order) { return order.branch === branch; }));
            renderCategoryDonut(todayOrders);
        }

        function findItemCategory(item, productLookup) {
            if (item.category) return item.category;
            var product = productLookup.byId[String(item.id)] || productLookup.byName[String(item.name || '').toLowerCase()];
            return product && product.category ? product.category : 'Uncategorized';
        }

        function categorySalesData(orders) {
            var products = getData('products', []);
            var productLookup = { byId: {}, byName: {} };
            if (Array.isArray(products)) {
                products.forEach(function(product) {
                    productLookup.byId[String(product.id)] = product;
                    productLookup.byName[String(product.name || '').toLowerCase()] = product;
                });
            }

            var categories = {};
            orders.filter(function(order) {
                return order.status !== 'cancelled' && order.paymentStatus !== 'pending';
            }).forEach(function(order) {
                (order.items || []).forEach(function(item) {
                    var category = findItemCategory(item, productLookup);
                    if (!categories[category]) categories[category] = { name: category, qty: 0, revenue: 0 };
                    categories[category].qty += Number(item.qty || 1);
                    categories[category].revenue += Number(item.price || 0) * Number(item.qty || 1);
                });
            });

            return Object.keys(categories).map(function(key) { return categories[key]; })
                .sort(function(a, b) { return b.qty - a.qty; });
        }

        function renderCategoryDonut(orders) {
            var canvas = document.getElementById('categoryDonutChart');
            var legend = document.getElementById('categoryLegend');
            var totalEl = document.getElementById('categoryDonutTotal');
            var badge = document.getElementById('topCategoryBadge');
            if (!canvas || !legend) return;

            var data = categorySalesData(orders);
            var totalQty = data.reduce(function(sum, category) { return sum + category.qty; }, 0);
            var colors = ['#12864e', '#5b8def', '#f5a623', '#e53170', '#2cb67d', '#45b873', '#8a6df1'];
            totalEl.textContent = totalQty;
            badge.textContent = data.length ? data[0].name : 'No sales yet';

            var rect = canvas.parentElement.getBoundingClientRect();
            var size = Math.max(220, Math.floor(Math.min(rect.width, rect.height || rect.width)));
            var dpr = window.devicePixelRatio || 1;
            canvas.width = size * dpr;
            canvas.height = size * dpr;

            var ctx = canvas.getContext('2d');
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
            ctx.clearRect(0, 0, size, size);

            if (!totalQty) {
                ctx.beginPath();
                ctx.arc(size / 2, size / 2, size * 0.36, 0, Math.PI * 2);
                ctx.lineWidth = size * 0.15;
                ctx.strokeStyle = '#e9f7ee';
                ctx.stroke();
                legend.innerHTML = '<div class="empty">No paid category sales yet today.</div>';
                return;
            }

            var start = -Math.PI / 2;
            data.forEach(function(category, index) {
                var slice = (category.qty / totalQty) * Math.PI * 2;
                ctx.beginPath();
                ctx.arc(size / 2, size / 2, size * 0.36, start, start + slice);
                ctx.lineWidth = size * 0.15;
                ctx.lineCap = 'round';
                ctx.strokeStyle = colors[index % colors.length];
                ctx.stroke();
                start += slice;
            });

            legend.innerHTML = data.slice(0, 6).map(function(category, index) {
                var pct = Math.round(category.qty / totalQty * 100);
                return '<div class="legend-row">' +
                    '<span class="legend-dot" style="background:' + colors[index % colors.length] + '"></span>' +
                    '<div><strong>' + escapeHtml(category.name) + '</strong><div>' + category.qty + ' sold · ' + money(category.revenue) + '</div></div>' +
                    '<strong>' + pct + '%</strong>' +
                '</div>';
            }).join('');
        }

        function renderPopularItems(orders) {
            var items = {};
            orders.forEach(function(order) {
                (order.items || []).forEach(function(item) {
                    if (!items[item.name]) items[item.name] = { name: item.name, qty: 0, revenue: 0 };
                    items[item.name].qty += Number(item.qty || 1);
                    items[item.name].revenue += Number(item.price || 0) * Number(item.qty || 1);
                });
            });

            var rows = Object.keys(items).map(function(key) { return items[key]; })
                .sort(function(a, b) { return b.qty - a.qty; })
                .slice(0, 5)
                .map(function(item) {
                    return '<tr><td style="font-weight:700">' + item.name + '</td><td>' + item.qty + '</td><td style="color:var(--gold-light);font-weight:700">' + money(item.revenue) + '</td></tr>';
                }).join('');

            document.getElementById('popularItemsTable').innerHTML = rows || '<tr><td colspan="3" class="empty">No item sales yet today.</td></tr>';
        }

        function renderRecentOrders(orders) {
            var rows = orders.slice(-6).reverse().map(function(order) {
                return '<tr><td style="font-weight:700">#' + order.id + '</td><td>' + order.customer + '</td><td style="font-weight:700">' + money(order.total) + '</td><td>' + getPaymentBadge(order) + '</td><td>' + getStatusBadge(order.status) + '</td><td style="color:var(--fg-muted)">' + order.time + '</td></tr>';
            }).join('');

            document.getElementById('recentOrdersTable').innerHTML = rows || '<tr><td colspan="6" class="empty">No recent orders for this branch.</td></tr>';
        }

        function renderSalesBars(orders) {
            var labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            var today = new Date();
            var monday = new Date(today);
            var day = monday.getDay() || 7;
            monday.setDate(monday.getDate() - day + 1);
            monday.setHours(0, 0, 0, 0);

            var days = labels.map(function(label, index) {
                var date = new Date(monday);
                date.setDate(monday.getDate() + index);
                return date;
            });

            var values = days.map(function(date) {
                return orders.filter(function(order) {
                    var orderDate = parseOrderDate(order.date);
                    return sameDate(orderDate, date) && order.status !== 'cancelled' && order.paymentStatus !== 'pending';
                }).reduce(function(sum, order) {
                    return sum + Number(order.total || 0);
                }, 0);
            });
            var max = Math.max.apply(Math, values);
            var salesBars = document.getElementById('salesBars');
            if (max <= 0) {
                salesBars.classList.add('empty-state');
                salesBars.innerHTML = 'No paid sales yet this week.';
                return;
            }
            salesBars.classList.remove('empty-state');
            salesBars.innerHTML = values.map(function(value, index) {
                var height = Math.max(12, value / max * 100);
                return '<div class="bar-wrap"><div class="bar" title="' + money(value) + '" style="height:' + height + '%"></div><span>' + labels[index] + '</span></div>';
            }).join('');
        }

        function setupSidebar() {
            localStorage.setItem('qc_session', JSON.stringify(staffUser));
            var logo = localStorage.getItem('qc_logo') || LOGO_URL;
            document.getElementById('sidebarCrown').innerHTML = '<img src="' + logo + '" alt="Logo">';
            document.getElementById('sidebarAvatar').textContent = staffUser.fullName.split(' ').map(function(word) { return word[0]; }).join('').substring(0, 2).toUpperCase();
            document.getElementById('sidebarName').textContent = staffUser.fullName;
            document.getElementById('sidebarRole').textContent = 'Branch Admin';
            document.getElementById('sidebarNav').innerHTML =
                '<div class="nav-section"><div class="nav-section-title">Main</div><a class="nav-item active" href="{{ url('/dashboard') }}"><i class="fas fa-chart-pie"></i> Dashboard</a><a class="nav-item" href="{{ url('/pos') }}"><i class="fas fa-cash-register"></i> Point of Sale</a></div>' +
                '<div class="nav-section"><div class="nav-section-title">Management</div><a class="nav-item" href="{{ url('/orders') }}"><i class="fas fa-receipt"></i> Orders</a><a class="nav-item" href="{{ url('/inventory') }}"><i class="fas fa-boxes-stacked"></i> Inventory</a></div>' +
                '<div class="nav-section"><div class="nav-section-title">System</div><a class="nav-item" href="{{ url('/reports') }}"><i class="fas fa-chart-bar"></i> Reports</a><a class="nav-item" href="{{ url('/settings') }}"><i class="fas fa-gear"></i> Settings</a></div>' +
                '<div class="nav-section"><div class="nav-section-title">Account</div><a class="nav-item" href="{{ url('/profile') }}"><i class="fas fa-user-circle"></i> My Profile</a></div>';
        }

        function handleLogout() {
            localStorage.removeItem('qc_session');
            window.location.href = '{{ url('/staff-login') }}';
        }

        setupSidebar();
        removeSeedOrders();
        renderDashboard();
        window.addEventListener('resize', renderDashboard);
    </script>
</body>
</html>
