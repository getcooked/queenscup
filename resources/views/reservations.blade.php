<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reservations | The Queen's Cup</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="preload" href="{{ asset('vendor/fontawesome/webfonts/fa-solid-900.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link href="{{ asset('vendor/fontawesome/css/all.min.css') }}" rel="stylesheet">
    <style>
        :root{--bg:#f7fbf8;--deep:#ffffff;--card:#ffffff;--panel:#f1f8f3;--line:#cfe7d7;--text:#123524;--muted:#5f7f6b;--green:#16c76a;--gold:#2f9e62;--blue:#5b8def;--yellow:#ffbd35;--danger:#ef3d5f}
        *{box-sizing:border-box;margin:0;padding:0}
        body{height:100vh;background:var(--bg);color:var(--text);font-family:"DM Sans",sans-serif;overflow:hidden}
        h1,h2,h3{font-family:"Playfair Display",serif}
        .layout{display:flex;height:100vh}
        .sidebar{width:260px;background:var(--deep);border-right:1px solid var(--line);display:flex;flex-direction:column;flex-shrink:0}
        .sidebar-brand{padding:22px 18px;border-bottom:1px solid var(--line);display:flex;align-items:center;gap:13px}
        .crown-icon{width:46px;height:46px;border-radius:50%;overflow:hidden;flex-shrink:0;border:2px solid rgba(22,199,106,.3);background:linear-gradient(135deg,#0c6f3f,#16c76a)}
        .crown-icon img{width:100%;height:100%;object-fit:cover}
        .sidebar-brand h2{font-size:14px;line-height:1.2;background:linear-gradient(135deg,#16a65f,#2f9e62);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
        .tagline{font-size:10px;color:var(--muted);letter-spacing:.5px}
        .sidebar-nav{flex:1;padding:14px 10px;overflow-y:auto}
        .nav-section-title{padding:12px 10px 7px;color:var(--muted);font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase}
        .nav-item{display:flex;align-items:center;gap:10px;min-height:40px;padding:0 12px;border-radius:10px;color:var(--muted);cursor:pointer;font-size:13px;font-weight:600;text-decoration:none;transition:background .2s ease,color .2s ease}
        .nav-item:hover,.nav-item.active{background:rgba(22,199,106,.13);color:var(--green)}
        .sidebar-footer{padding:14px 18px;border-top:1px solid var(--line);display:flex;align-items:center;gap:12px}
        .avatar{width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,var(--green),var(--gold));display:grid;place-items:center;font-size:13px;font-weight:800;color:#fff}
        .user-info{flex:1;min-width:0}
        .user-info .name{font-size:12px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
        .user-info .role{font-size:10px;color:var(--muted)}
        .logout-btn{width:30px;height:30px;border-radius:8px;border:1px solid var(--line);background:var(--card);color:var(--muted);cursor:pointer}
        .logout-btn:hover{border-color:var(--danger);color:var(--danger)}

        .content{flex:1;min-width:0;overflow:auto;padding:30px}
        .top{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;margin-bottom:22px}
        .top h1{font-size:36px}
        .top p{color:var(--muted);margin-top:5px}
        .btn{height:38px;border:0;border-radius:10px;padding:0 13px;background:var(--green);color:#fff;font-weight:800;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:7px;font-family:"DM Sans",sans-serif;font-size:12px;transition:transform .18s ease,box-shadow .18s ease}
        .btn:hover{transform:translateY(-1px);box-shadow:0 10px 24px rgba(18,134,78,.15)}
        .btn.secondary{background:var(--card);border:1px solid #d8ebdf;color:var(--text)}
        .btn.blue{background:var(--blue)}
        .btn.gold{background:var(--gold)}
        .btn.danger{background:var(--danger)}
        .btn.sm{height:32px;padding:0 10px;font-size:11px}
        .btn:disabled{opacity:.5;cursor:not-allowed;transform:none;box-shadow:none}

        .stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:18px}
        .stat{background:#fff;border:1px solid #d8ebdf;border-radius:14px;padding:18px;box-shadow:0 12px 32px rgba(18,53,36,.07)}
        .stat strong{display:block;font-size:26px}
        .stat span{color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:.8px}

        .filters{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px}
        .chip{height:34px;border:1px solid #d8ebdf;background:#fff;color:var(--text);border-radius:999px;padding:0 14px;font-size:12px;font-weight:700;cursor:pointer;font-family:"DM Sans",sans-serif}
        .chip.active{background:linear-gradient(135deg,rgba(22,199,106,.15),rgba(47,158,98,.08));border-color:var(--green);color:var(--gold)}

        .card{background:#fff;border:1px solid #d8ebdf;border-radius:14px;overflow:hidden;box-shadow:0 12px 32px rgba(18,53,36,.07)}
        .table-wrap{overflow:auto}
        table{width:100%;border-collapse:collapse;min-width:1040px}
        th{text-align:left;padding:12px 14px;color:var(--muted);font-size:10px;letter-spacing:1px;text-transform:uppercase;border-bottom:1px solid var(--line);white-space:nowrap}
        td{padding:13px 14px;border-bottom:1px solid var(--line);font-size:12px;vertical-align:top}
        tr:hover td{background:#f8fcf9}
        .ref{font-weight:800;color:var(--gold);font-size:13px}
        .items{color:var(--muted);max-width:260px}
        .amount{font-weight:800}
        .fee{color:var(--muted);font-size:11px}
        .badge{display:inline-flex;align-items:center;gap:5px;border-radius:999px;padding:4px 9px;font-size:10px;font-weight:800;white-space:nowrap}
        .b-pending{background:rgba(255,189,53,.16);color:#a97400}
        .b-preparing{background:rgba(91,141,239,.16);color:var(--blue)}
        .b-ready{background:rgba(22,199,106,.16);color:#0c8a4a}
        .b-completed{background:rgba(95,127,107,.16);color:var(--muted)}
        .b-cancelled{background:rgba(239,61,95,.16);color:var(--danger)}
        .b-paid{background:rgba(22,199,106,.16);color:#0c8a4a}
        .b-unpaid{background:rgba(255,189,53,.16);color:#a97400}
        .b-takeout{background:rgba(47,158,98,.14);color:var(--gold)}
        .b-dinein{background:rgba(91,141,239,.14);color:var(--blue)}
        .row-actions{display:flex;gap:6px;flex-wrap:wrap}
        .empty{padding:40px;text-align:center;color:var(--muted)}
        .toast{position:fixed;right:22px;bottom:22px;background:#123524;color:#fff;padding:12px 16px;border-radius:12px;font-size:13px;font-weight:600;opacity:0;transform:translateY(10px);transition:all .25s ease;pointer-events:none;z-index:80;max-width:340px}
        .toast.show{opacity:1;transform:translateY(0)}
        .toast.bad{background:var(--danger)}

        .modal-back{position:fixed;inset:0;background:rgba(9,26,17,.5);display:none;align-items:center;justify-content:center;padding:20px;z-index:70}
        .modal-back.open{display:flex}
        .modal{background:#fff;border-radius:16px;width:min(420px,100%);padding:22px;box-shadow:0 24px 70px rgba(18,53,36,.22)}
        .modal h3{font-size:20px;margin-bottom:6px}
        .modal p{color:var(--muted);font-size:12px;margin-bottom:16px}
        .pay-options{display:grid;gap:8px;margin-bottom:16px}
        .pay-opt{display:flex;align-items:center;gap:10px;border:1px solid #d8ebdf;border-radius:12px;padding:12px;cursor:pointer;font-size:13px;font-weight:700}
        .pay-opt:hover{border-color:var(--green);background:rgba(22,199,106,.05)}
        .pay-opt i{width:20px;text-align:center;color:var(--gold)}
        .modal-actions{display:flex;justify-content:flex-end;gap:8px}

        @media(max-width:1100px){body{height:auto;overflow:auto}.layout{height:auto}.sidebar{display:none}.content{padding:20px}.stats{grid-template-columns:1fr 1fr}.top{flex-direction:column;align-items:flex-start}}
        @media(max-width:650px){.stats{grid-template-columns:1fr}}
        body{background:radial-gradient(circle at top right,rgba(22,199,106,.08),transparent 34%),linear-gradient(180deg,#fbfefc 0%,var(--bg) 100%)}
        .sidebar{box-shadow:0 12px 32px rgba(18,53,36,.07)}
        .nav-item.active{box-shadow:inset 3px 0 0 var(--green)}
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
            <div class="nav-section"><div class="nav-section-title">Main</div><a class="nav-item" href="{{ url('/dashboard') }}"><i class="fas fa-chart-pie"></i> Dashboard</a><a class="nav-item" href="{{ url('/pos') }}"><i class="fas fa-cash-register"></i> Point of Sale</a></div>
            <div class="nav-section"><div class="nav-section-title">Management</div><a class="nav-item" href="{{ url('/orders') }}"><i class="fas fa-receipt"></i> Orders</a><a class="nav-item active" href="{{ url('/reservations') }}"><i class="fas fa-calendar-check"></i> Reservations</a><a class="nav-item" href="{{ url('/inventory') }}"><i class="fas fa-boxes-stacked"></i> Inventory</a></div>
            <div class="nav-section"><div class="nav-section-title">System</div><a class="nav-item" href="{{ url('/reports') }}"><i class="fas fa-chart-bar"></i> Reports</a><a class="nav-item" href="{{ url('/activity') }}"><i class="fas fa-clock-rotate-left"></i> Activity</a><a class="nav-item" href="{{ url('/settings') }}"><i class="fas fa-gear"></i> Settings</a></div>
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
            <div>
                <h1>Reservations</h1>
                <p>Move each reservation along as you make it, and record how the customer paid.</p>
            </div>
            <div>
                <button class="btn secondary" onclick="loadReservations()"><i class="fas fa-rotate-right"></i> Refresh</button>
            </div>
        </div>

        <div class="stats">
            <div class="stat"><strong id="statPending">0</strong><span>To start</span></div>
            <div class="stat"><strong id="statPreparing">0</strong><span>Preparing</span></div>
            <div class="stat"><strong id="statReady">0</strong><span>Ready for pick up</span></div>
            <div class="stat"><strong id="statUnpaid">0</strong><span>Awaiting payment</span></div>
        </div>

        <div class="filters" id="filters">
            <button class="chip active" data-filter="active" onclick="setFilter('active', this)">In progress</button>
            <button class="chip" data-filter="all" onclick="setFilter('all', this)">All</button>
            <button class="chip" data-filter="pending" onclick="setFilter('pending', this)">To start</button>
            <button class="chip" data-filter="preparing" onclick="setFilter('preparing', this)">Preparing</button>
            <button class="chip" data-filter="ready" onclick="setFilter('ready', this)">Ready</button>
            <button class="chip" data-filter="completed" onclick="setFilter('completed', this)">Completed</button>
            <button class="chip" data-filter="cancelled" onclick="setFilter('cancelled', this)">Cancelled</button>
        </div>

        <div class="card">
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Customer</th>
                            <th>Serving</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="rows">
                        <tr><td colspan="8" class="empty">Loading reservations…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<div class="modal-back" id="payModal">
    <div class="modal">
        <h3>Record payment</h3>
        <p id="payModalRef">How did the customer pay?</p>
        <div class="pay-options">
            <div class="pay-opt" onclick="submitPayment('cash')"><i class="fas fa-money-bill-wave"></i> Cash</div>
            <div class="pay-opt" onclick="submitPayment('gcash')"><i class="fas fa-mobile-screen"></i> GCash</div>
            <div class="pay-opt" onclick="submitPayment('paymaya')"><i class="fas fa-credit-card"></i> PayMaya</div>
        </div>
        <div class="modal-actions">
            <button class="btn secondary" onclick="closePayModal()">Cancel</button>
        </div>
    </div>
</div>

<div class="toast" id="toast"></div>

<script>
    var reservations = [];
    var filter = 'active';
    var payingId = null;
    var CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    var STATUS_LABELS = {
        pending: 'To start', preparing: 'Preparing', ready: 'Ready',
        completed: 'Completed', cancelled: 'Cancelled'
    };

    // Mirrors Reservation::ALLOWED_TRANSITIONS on the server. The server is the
    // authority; this only decides which buttons are worth showing.
    var NEXT_ACTIONS = {
        pending: [{ status: 'preparing', label: 'Start preparing', icon: 'fa-blender', cls: 'blue' }],
        preparing: [{ status: 'ready', label: 'Mark ready', icon: 'fa-bell-concierge', cls: '' }],
        ready: [{ status: 'completed', label: 'Handed over', icon: 'fa-check', cls: 'gold' }]
    };

    function peso(value) {
        return '₱' + Number(value || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    function escapeHtml(value) {
        return String(value == null ? '' : value).replace(/[&<>"']/g, function (character) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[character];
        });
    }

    function showToast(message, isBad) {
        var toast = document.getElementById('toast');
        toast.textContent = message;
        toast.className = 'toast show' + (isBad ? ' bad' : '');
        setTimeout(function () { toast.className = 'toast'; }, 3200);
    }

    function setFilter(value, button) {
        filter = value;
        document.querySelectorAll('#filters .chip').forEach(function (chip) { chip.classList.remove('active'); });
        if (button) button.classList.add('active');
        render();
    }

    function loadReservations() {
        fetch(@json(route('staff.reservations.index')), {
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin'
        })
            .then(function (response) {
                if (!response.ok) throw new Error('Could not load reservations.');
                return response.json();
            })
            .then(function (payload) {
                reservations = payload.data || [];
                render();
            })
            .catch(function (error) {
                document.getElementById('rows').innerHTML =
                    '<tr><td colspan="8" class="empty">' + escapeHtml(error.message) + '</td></tr>';
            });
    }

    function visibleReservations() {
        if (filter === 'all') return reservations;
        if (filter === 'active') {
            return reservations.filter(function (item) {
                return ['pending', 'preparing', 'ready'].indexOf(item.status) !== -1;
            });
        }
        return reservations.filter(function (item) { return item.status === filter; });
    }

    function render() {
        var counts = { pending: 0, preparing: 0, ready: 0, unpaid: 0 };

        reservations.forEach(function (item) {
            if (counts[item.status] !== undefined) counts[item.status]++;
            // Only orders still in play are actually awaiting money.
            if (item.payment_status !== 'paid' && item.status !== 'cancelled' && item.status !== 'completed') {
                counts.unpaid++;
            }
        });

        document.getElementById('statPending').textContent = counts.pending;
        document.getElementById('statPreparing').textContent = counts.preparing;
        document.getElementById('statReady').textContent = counts.ready;
        document.getElementById('statUnpaid').textContent = counts.unpaid;

        var rows = visibleReservations();
        var body = document.getElementById('rows');

        if (!rows.length) {
            body.innerHTML = '<tr><td colspan="8" class="empty">No reservations here yet.</td></tr>';
            return;
        }

        body.innerHTML = rows.map(function (item) {
            var items = (item.items || []).map(function (line) {
                return line.quantity + '× ' + escapeHtml(line.name) + ' (' + line.size_label + ')';
            }).join('<br>');

            var serving = item.service_type === 'take_out'
                ? '<span class="badge b-takeout"><i class="fas fa-bag-shopping"></i> Take out</span>'
                : '<span class="badge b-dinein"><i class="fas fa-utensils"></i> Dine in</span>';

            var fee = Number(item.takeout_fee) > 0
                ? '<div class="fee">incl. ' + peso(item.takeout_fee) + ' cups</div>' : '';

            var payment = item.payment_status === 'paid'
                ? '<span class="badge b-paid"><i class="fas fa-check"></i> ' + escapeHtml((item.payment_method || '').toUpperCase()) + '</span>'
                : '<span class="badge b-unpaid"><i class="fas fa-clock"></i> Unpaid</span>';

            var actions = (NEXT_ACTIONS[item.status] || []).map(function (action) {
                return '<button class="btn sm ' + action.cls + '" onclick="updateStatus(' + item.id + ', \'' + action.status + '\')">' +
                    '<i class="fas ' + action.icon + '"></i> ' + action.label + '</button>';
            }).join('');

            if (item.payment_status !== 'paid' && item.status !== 'cancelled') {
                actions += '<button class="btn sm gold" onclick="openPayModal(' + item.id + ', \'' + item.reference + '\')">' +
                    '<i class="fas fa-peso-sign"></i> Record payment</button>';
            }

            if (item.status === 'pending' || item.status === 'preparing' || item.status === 'ready') {
                actions += '<button class="btn sm danger" onclick="updateStatus(' + item.id + ', \'cancelled\')">' +
                    '<i class="fas fa-xmark"></i> Cancel</button>';
            }

            return '<tr>' +
                '<td><span class="ref">' + escapeHtml(item.reference) + '</span></td>' +
                '<td>' + escapeHtml(item.customer_name) +
                    (item.customer_contact ? '<div class="fee">' + escapeHtml(item.customer_contact) + '</div>' : '') + '</td>' +
                '<td>' + serving + '</td>' +
                '<td class="items">' + items + '</td>' +
                '<td><span class="amount">' + peso(item.total) + '</span>' + fee + '</td>' +
                '<td>' + payment + '</td>' +
                '<td><span class="badge b-' + item.status + '">' + (STATUS_LABELS[item.status] || item.status) + '</span></td>' +
                '<td><div class="row-actions">' + (actions || '<span class="fee">Nothing to do</span>') + '</div></td>' +
                '</tr>';
        }).join('');
    }

    function patch(url, body, successMessage) {
        return fetch(url, {
            method: 'PATCH',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF
            },
            body: JSON.stringify(body)
        })
            .then(function (response) {
                return response.json().then(function (payload) {
                    if (!response.ok) {
                        // Laravel returns 422 with an errors bag for a refused
                        // transition, e.g. trying to reopen a completed order.
                        var message = payload.message || 'That change was refused.';
                        if (payload.errors) {
                            var first = Object.keys(payload.errors)[0];
                            if (first) message = payload.errors[first][0];
                        }
                        throw new Error(message);
                    }
                    return payload;
                });
            })
            .then(function (updated) {
                reservations = reservations.map(function (item) {
                    return item.id === updated.id ? updated : item;
                });
                render();
                showToast(successMessage);
            })
            .catch(function (error) { showToast(error.message, true); });
    }

    function updateStatus(id, status) {
        if (status === 'cancelled' && !confirm('Cancel this reservation? This cannot be undone.')) return;

        patch('{{ url('/staff/reservations') }}/' + id + '/status', { status: status },
            status === 'ready'
                ? 'Marked ready. The customer has been notified.'
                : 'Reservation moved to ' + (STATUS_LABELS[status] || status) + '.');
    }

    function openPayModal(id, reference) {
        payingId = id;
        document.getElementById('payModalRef').textContent = 'How did the customer pay for ' + reference + '?';
        document.getElementById('payModal').classList.add('open');
    }

    function closePayModal() {
        payingId = null;
        document.getElementById('payModal').classList.remove('open');
    }

    function submitPayment(method) {
        if (!payingId) return;
        var id = payingId;
        closePayModal();
        patch('{{ url('/staff/reservations') }}/' + id + '/payment', { payment_method: method },
            'Payment recorded as ' + method.toUpperCase() + '.');
    }

    document.getElementById('payModal').addEventListener('click', function (event) {
        if (event.target === this) closePayModal();
    });

    function setupSidebar() {
        var defaultLogo = @json(asset('icons/queens-cup-logo.png'));
        var session = null;
        try { session = JSON.parse(localStorage.getItem('qc_session')); } catch (error) { session = null; }
        var logo = localStorage.getItem('qc_logo') || defaultLogo;
        document.getElementById('sidebarCrown').innerHTML = '<img src="' + logo + '" alt="Logo">';
        if (!session) return;
        var fullName = session.fullName || session.username || 'User';
        var initials = fullName.split(' ').map(function (word) { return word[0]; }).join('').substring(0, 2).toUpperCase();
        var roleLabels = { admin: 'Branch Admin', cashier: 'Cashier' };
        document.getElementById('sidebarAvatar').textContent = initials || '?';
        document.getElementById('sidebarName').textContent = fullName;
        document.getElementById('sidebarRole').textContent = roleLabels[session.role] || session.role || 'Role';
    }

    function handleLogout() {
        localStorage.removeItem('qc_session');
        fetch(@json(route('staff.logout')), {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'X-CSRF-TOKEN': CSRF }
        }).finally(function () {
            window.location.href = @json(url('/'));
        });
    }

    setupSidebar();
    loadReservations();

    // Reservations arrive from the app while this page is open, so keep the
    // queue current without the cashier having to refresh.
    setInterval(function () { if (!document.hidden) loadReservations(); }, 15000);
</script>
</body>
</html>
