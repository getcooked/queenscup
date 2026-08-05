<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports | The Queen's Cup</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root{--bg:#f7fbf8;--deep:#ffffff;--card:#ffffff;--panel:#f1f8f3;--line:#cfe7d7;--text:#123524;--muted:#5f7f6b;--green:#16c76a;--gold:#2f9e62;--pink:#ef2f83;--blue:#5b8def;--yellow:#ffbd35;--danger:#ef3d5f}
        *{box-sizing:border-box;margin:0;padding:0}body{height:100vh;background:var(--bg);color:var(--text);font-family:"DM Sans",sans-serif;overflow:hidden}h1,h2,h3{font-family:"Playfair Display",serif}.layout{display:flex;height:100vh}.sidebar{width:260px;background:var(--deep);border-right:1px solid var(--line);display:flex;flex-direction:column;flex-shrink:0}.sidebar-brand{padding:22px 18px;border-bottom:1px solid var(--line);display:flex;align-items:center;gap:13px}.crown-icon{width:46px;height:46px;border-radius:50%;overflow:hidden;flex-shrink:0;border:2px solid rgba(22,199,106,.3);background:linear-gradient(135deg,#0c6f3f,#16c76a)}.crown-icon img{width:100%;height:100%;object-fit:cover}.sidebar-brand h2{font-size:14px;line-height:1.2;background:linear-gradient(135deg,#16a65f,#2f9e62);-webkit-background-clip:text;-webkit-text-fill-color:transparent}.tagline{font-size:10px;color:var(--muted);letter-spacing:.5px}.sidebar-nav{flex:1;padding:14px 10px;overflow-y:auto}.nav-section-title{padding:12px 10px 7px;color:var(--muted);font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase}.nav-item{display:flex;align-items:center;gap:10px;min-height:40px;padding:0 12px;border-radius:10px;color:var(--muted);cursor:pointer;font-size:13px;font-weight:600;text-decoration:none;transition:background .2s ease,color .2s ease}.nav-item:hover,.nav-item.active{background:rgba(22,199,106,.13);color:var(--green)}.sidebar-footer{padding:14px 18px;border-top:1px solid var(--line);display:flex;align-items:center;gap:12px}.avatar{width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,var(--green),var(--gold));display:grid;place-items:center;font-size:13px;font-weight:800;color:#fff}.user-info{flex:1;min-width:0}.user-info .name{font-size:12px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.user-info .role{font-size:10px;color:var(--muted)}.logout-btn{width:30px;height:30px;border-radius:8px;border:1px solid var(--line);background:var(--card);color:var(--muted);cursor:pointer}.logout-btn:hover{border-color:var(--danger);color:var(--danger)}
        .content{flex:1;min-width:0;overflow:auto;padding:30px}.top{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;margin-bottom:22px}.top h1{font-size:34px}.top p{color:var(--muted);margin-top:5px}.actions{display:flex;gap:10px;flex-wrap:wrap}.btn{height:38px;border:0;border-radius:10px;padding:0 12px;background:var(--green);color:#ffffff;font-weight:800;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:8px}.btn.secondary{background:var(--card);border:1px solid var(--line);color:var(--text)}.stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:18px}.stat{background:var(--card);border:1px solid var(--line);border-radius:14px;padding:18px}.stat .icon{width:38px;height:38px;border-radius:10px;display:grid;place-items:center;margin-bottom:12px}.stat strong{display:block;font-size:26px}.stat span{color:var(--muted);font-size:12px;text-transform:uppercase;letter-spacing:.8px}.card{background:var(--card);border:1px solid var(--line);border-radius:14px;overflow:hidden}.card-header{padding:16px 18px;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;gap:14px}.card-header h3{font-size:18px}.filters{display:flex;gap:10px;flex-wrap:wrap}.field{height:38px;border:1px solid var(--line);background:var(--panel);color:var(--text);border-radius:10px;padding:0 11px;font-family:"DM Sans",sans-serif;outline:none}.field:focus{border-color:var(--green)}.print-header{display:none}.table-wrap{overflow:auto}table{width:100%;border-collapse:collapse;min-width:880px}th{text-align:left;padding:12px 14px;color:var(--muted);font-size:10px;letter-spacing:1px;text-transform:uppercase;border-bottom:1px solid var(--line)}td{padding:13px 14px;border-bottom:1px solid var(--line);font-size:12px;vertical-align:middle}tr:hover td{background:rgba(22,199,106,.04)}.amount{font-weight:800;color:var(--gold)}.badge{display:inline-flex;align-items:center;gap:5px;border-radius:999px;padding:4px 9px;font-size:10px;font-weight:800}.paid{background:rgba(22,199,106,.14);color:var(--green)}.pending{background:rgba(255,189,53,.14);color:var(--yellow)}.cancelled{background:rgba(239,61,95,.14);color:var(--danger)}.empty{padding:34px;text-align:center;color:var(--muted)}.items{max-width:320px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:var(--muted)}@media(max-width:1100px){body{height:auto;overflow:auto}.layout{height:auto}.sidebar{display:none}.content{padding:20px}.top{align-items:flex-start;flex-direction:column}.stats{grid-template-columns:1fr 1fr}.card-header{align-items:flex-start;flex-direction:column}}@media(max-width:650px){.stats{grid-template-columns:1fr}.actions,.filters{width:100%}.btn,.field{width:100%}}@media print{@page{margin:14mm}body{height:auto;background:#fff;color:#111;overflow:visible}.sidebar,.top,.stats,.filters{display:none!important}.layout,.content{display:block;height:auto;padding:0;overflow:visible}.card{border:0;background:#fff;color:#111;border-radius:0;overflow:visible}.card-header{display:block;border-bottom:1px solid #222;padding:0 0 10px;margin-bottom:10px}.card-header h3{display:none}.print-header{display:flex;align-items:center;gap:12px;margin-bottom:12px}.print-header img{width:54px;height:54px;border-radius:50%;object-fit:cover}.print-header h2{font-size:22px;color:#111;margin:0}.print-header p{font-size:11px;color:#444;margin-top:2px}.table-wrap{overflow:visible}table{min-width:0;width:100%;font-size:10px}th,td{color:#111;border-bottom:1px solid #ddd;padding:7px 8px}.items{max-width:none;white-space:normal;color:#111}.amount{color:#111}.badge{background:transparent!important;color:#111!important;padding:0}.badge i{display:none}}
        body{background:radial-gradient(circle at top right,rgba(22,199,106,.08),transparent 34%),linear-gradient(180deg,#fbfefc 0%,var(--bg) 100%)}.sidebar,.card,.stat{box-shadow:0 12px 32px rgba(18,53,36,.07)}.card,.stat{border-color:#d8ebdf;background:#fff}.nav-item.active{box-shadow:inset 3px 0 0 var(--green)}.content h1{font-size:36px}.field{border-color:#d8ebdf}.btn{transition:transform .18s ease,box-shadow .18s ease}.btn:hover{transform:translateY(-1px);box-shadow:0 10px 24px rgba(18,134,78,.15)}tr:hover td{background:#f8fcf9}
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="crown-icon" id="sidebarCrown"><img src="https://z-cdn-media.chatglm.cn/files/af3b11e5-fe61-43c7-8ea7-25f782035ca7.jpg?auth_key=1879603096-24f93fd216a54bdcbe24ff044d3e749d-0-1994c8beadd0469c9164ce8d6c133719" alt="Logo"></div>
            <div><h2>The Queen's Cup</h2><span class="tagline">Crowned with Flavors</span></div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section"><div class="nav-section-title">Main</div><a class="nav-item" href="{{ url('/dashboard') }}"><i class="fas fa-chart-pie"></i> Dashboard</a><a class="nav-item" href="{{ url('/pos') }}"><i class="fas fa-cash-register"></i> Point of Sale</a></div>
            <div class="nav-section"><div class="nav-section-title">Management</div><a class="nav-item" href="{{ url('/orders') }}"><i class="fas fa-receipt"></i> Orders</a><a class="nav-item" href="{{ url('/inventory') }}"><i class="fas fa-boxes-stacked"></i> Inventory</a></div>
            <div class="nav-section"><div class="nav-section-title">System</div><a class="nav-item active" href="{{ url('/reports') }}"><i class="fas fa-chart-bar"></i> Reports</a><a class="nav-item" href="{{ url('/settings') }}"><i class="fas fa-gear"></i> Settings</a></div>
            <div class="nav-section"><div class="nav-section-title">Account</div><a class="nav-item" href="{{ url('/profile') }}"><i class="fas fa-user-circle"></i> My Profile</a></div>
        </nav>
        <div class="sidebar-footer">
            <div class="avatar" id="sidebarAvatar">?</div>
            <div class="user-info"><div class="name" id="sidebarName">User</div><div class="role" id="sidebarRole">Role</div></div>
            <button class="logout-btn" onclick="handleLogout()" title="Sign Out"><i class="fas fa-right-from-bracket"></i></button>
        </div>
    </aside>
    <main class="content">
        <div class="top">
            <div><h1>Reports</h1><p>Sales records from completed and paid orders.</p></div>
            <div class="actions">
                <button class="btn secondary" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                <button class="btn" onclick="exportReportCsv()"><i class="fas fa-file-export"></i> Export CSV</button>
            </div>
        </div>

        <div class="stats">
            <div class="stat"><div class="icon" style="background:rgba(224,182,76,.14);color:var(--gold)"><i class="fas fa-coins"></i></div><strong id="totalRevenue">&#8369;0.00</strong><span>Total Revenue</span></div>
            <div class="stat"><div class="icon" style="background:rgba(22,199,106,.14);color:var(--green)"><i class="fas fa-receipt"></i></div><strong id="totalOrders">0</strong><span>Paid Orders</span></div>
            <div class="stat"><div class="icon" style="background:rgba(91,141,239,.14);color:var(--blue)"><i class="fas fa-mug-hot"></i></div><strong id="itemsSold">0</strong><span>Items Sold</span></div>
            <div class="stat"><div class="icon" style="background:rgba(239,47,131,.14);color:var(--pink)"><i class="fas fa-chart-line"></i></div><strong id="averageOrder">&#8369;0.00</strong><span>Average Order</span></div>
        </div>

        <section class="card">
            <div class="card-header">
                <div class="print-header">
                    <img id="printLogo" src="https://z-cdn-media.chatglm.cn/files/af3b11e5-fe61-43c7-8ea7-25f782035ca7.jpg?auth_key=1879603096-24f93fd216a54bdcbe24ff044d3e749d-0-1994c8beadd0469c9164ce8d6c133719" alt="Queen's Cup Logo">
                    <div><h2>The Queen's Cup</h2><p>Report Records</p></div>
                </div>
                <h3>Report Records</h3>
                <div class="filters">
                    <input class="field" id="dateFilter" type="date" onchange="renderReports()">
                    <input class="field" id="searchFilter" type="search" placeholder="Search order or customer" oninput="renderReports()">
                </div>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="reportRows"></tbody>
                </table>
            </div>
        </section>
    </main>
</div>
<script>
    var defaultLogo = 'https://z-cdn-media.chatglm.cn/files/af3b11e5-fe61-43c7-8ea7-25f782035ca7.jpg?auth_key=1879603096-24f93fd216a54bdcbe24ff044d3e749d-0-1994c8beadd0469c9164ce8d6c133719';
    var reportRows = [];

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

    function getData(key, def) {
        try {
            var data = localStorage.getItem('qc_' + key);
            return data ? JSON.parse(data) : def;
        } catch (error) {
            return def;
        }
    }

    function money(value) {
        return '&#8369;' + Number(value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function itemSummary(order) {
        return (order.items || []).map(function(item) {
            return (item.qty || 1) + 'x ' + item.name + (item.size ? ' (' + item.size + ')' : '');
        }).join(', ');
    }

    function filteredOrders() {
        var date = document.getElementById('dateFilter').value;
        var search = document.getElementById('searchFilter').value.trim().toLowerCase();
        var orders = getData('orders', []);

        return orders.filter(function(order) {
            if (order.status === 'cancelled') return false;
            if (order.paymentStatus !== 'paid') return false;
            if (date && order.date !== date) return false;
            if (search) {
                var haystack = [order.id, order.customer, itemSummary(order)].join(' ').toLowerCase();
                if (haystack.indexOf(search) === -1) return false;
            }
            return true;
        }).sort(function(a, b) {
            return Number(a.id || 0) - Number(b.id || 0);
        });
    }

    function renderReports() {
        reportRows = filteredOrders();
        var revenue = reportRows.reduce(function(sum, order) { return sum + Number(order.total || 0); }, 0);
        var items = reportRows.reduce(function(sum, order) {
            return sum + (order.items || []).reduce(function(itemSum, item) { return itemSum + Number(item.qty || 0); }, 0);
        }, 0);
        var average = reportRows.length ? revenue / reportRows.length : 0;

        document.getElementById('totalRevenue').innerHTML = money(revenue);
        document.getElementById('totalOrders').textContent = reportRows.length;
        document.getElementById('itemsSold').textContent = items;
        document.getElementById('averageOrder').innerHTML = money(average);

        document.getElementById('reportRows').innerHTML = reportRows.map(function(order) {
            return '<tr>' +
                '<td><strong>#' + order.id + '</strong></td>' +
                '<td>' + (order.date || '') + '</td>' +
                '<td>' + (order.paidAt || order.time || '') + '</td>' +
                '<td>' + (order.customer || 'Walk-in') + '</td>' +
                '<td class="items" title="' + itemSummary(order).replace(/"/g, '&quot;') + '">' + itemSummary(order) + '</td>' +
                '<td><span class="badge paid"><i class="fas fa-money-bill-wave"></i> ' + (order.payment || 'Cash') + '</span></td>' +
                '<td><span class="badge paid"><i class="fas fa-check-circle"></i> Paid</span></td>' +
                '<td class="amount">' + money(order.total) + '</td>' +
            '</tr>';
        }).join('') || '<tr><td colspan="8"><div class="empty">No paid report records found.</div></td></tr>';
    }

    function exportReportCsv() {
        var headers = ['Order ID','Date','Time','Customer','Items','Payment','Total'];
        var lines = [headers.join(',')].concat(reportRows.map(function(order) {
            return [
                '#' + order.id,
                order.date || '',
                order.paidAt || order.time || '',
                order.customer || 'Walk-in',
                itemSummary(order),
                order.payment || 'Cash',
                Number(order.total || 0).toFixed(2)
            ].map(function(value) {
                return '"' + String(value).replace(/"/g, '""') + '"';
            }).join(',');
        }));
        var blob = new Blob([lines.join('\n')], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'queens-cup-reports.csv';
        link.click();
        URL.revokeObjectURL(link.href);
    }

    function setupSidebar() {
        var session = null;
        try { session = JSON.parse(localStorage.getItem('qc_session')); } catch (error) { session = null; }
        var logo = localStorage.getItem('qc_logo') || defaultLogo;
        document.getElementById('sidebarCrown').innerHTML = '<img src="' + logo + '" alt="Logo">';
        document.getElementById('printLogo').src = logo;
        if (!session) return;
        var fullName = session.fullName || session.username || 'User';
        var initials = fullName.split(' ').map(function(word) { return word[0]; }).join('').substring(0, 2).toUpperCase();
        var roleLabels = { admin: 'Branch Admin', cashier: 'Cashier', customer: 'Customer', guest: 'Guest' };
        document.getElementById('sidebarAvatar').textContent = initials || '?';
        document.getElementById('sidebarName').textContent = fullName;
        document.getElementById('sidebarRole').textContent = roleLabels[session.role] || session.role || 'Role';
    }

    function handleLogout() {
        localStorage.removeItem('qc_session');
        window.location.href = '{{ url('/staff-login') }}';
    }

    setupSidebar();
    renderReports();
</script>
</body>
</html>
