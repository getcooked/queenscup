@php
    $pageKey = 'activity';

    $tone = function (string $action) {
        return match (true) {
            str_starts_with($action, 'sale.'), $action === 'order.payment' => 'gold',
            str_starts_with($action, 'order.') => 'blue',
            str_starts_with($action, 'inventory.') => 'green',
            $action === 'staff.logout' => 'muted',
            default => 'slate',
        };
    };

    $glyph = [
        'order.status' => 'fa-receipt',
        'order.payment' => 'fa-peso-sign',
        'sale.recorded' => 'fa-cash-register',
        'inventory.created' => 'fa-plus',
        'inventory.updated' => 'fa-pen',
        'inventory.deleted' => 'fa-trash',
        'staff.login' => 'fa-right-to-bracket',
        'staff.logout' => 'fa-right-from-bracket',
        'staff.created' => 'fa-user-plus',
        'settings.qr' => 'fa-qrcode',
    ];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity | The Queen's Cup</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="preload" href="{{ asset('vendor/fontawesome/webfonts/fa-solid-900.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link href="{{ asset('vendor/fontawesome/css/all.min.css') }}" rel="stylesheet">
    <style>
        :root{--bg:#f7fbf8;--deep:#fff;--card:#fff;--line:#cfe7d7;--text:#123524;--muted:#5f7f6b;--green:#16c76a;--gold:#2f9e62;--blue:#5b8def;--danger:#ef3d5f}
        *{box-sizing:border-box;margin:0;padding:0}
        body{min-height:100vh;background:var(--bg);color:var(--text);font-family:"DM Sans",sans-serif}
        h1,h2,h3{font-family:"Playfair Display",serif}
        .layout{display:flex;min-height:100vh}
        .sidebar{width:260px;background:var(--deep);border-right:1px solid var(--line);display:flex;flex-direction:column;flex-shrink:0}
        .sidebar-brand{padding:22px 18px;border-bottom:1px solid var(--line);display:flex;align-items:center;gap:13px}
        .crown-icon{width:46px;height:46px;border-radius:50%;overflow:hidden;flex-shrink:0;border:2px solid rgba(22,199,106,.3);background:linear-gradient(135deg,#0c6f3f,#16c76a)}
        .crown-icon img{width:100%;height:100%;object-fit:cover}
        .sidebar-brand h2{font-size:14px;line-height:1.2;background:linear-gradient(135deg,#16a65f,#2f9e62);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
        .tagline{font-size:10px;color:var(--muted);letter-spacing:.5px}
        .sidebar-nav{flex:1;padding:14px 10px;overflow-y:auto}
        .nav-section-title{padding:12px 10px 7px;color:var(--muted);font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase}
        .nav-item{display:flex;align-items:center;gap:10px;min-height:40px;padding:0 12px;border-radius:10px;color:var(--muted);font-size:13px;font-weight:600;text-decoration:none}
        .nav-item:hover,.nav-item.active{background:rgba(22,199,106,.13);color:var(--green)}
        .sidebar-footer{padding:14px 18px;border-top:1px solid var(--line);display:flex;align-items:center;gap:12px}
        .avatar{width:34px;height:34px;border-radius:10px;display:grid;place-items:center;font-size:13px;font-weight:800;color:#fff}
        .user-info{flex:1;min-width:0}
        .user-info .name{font-size:12px;font-weight:700}
        .user-info .role{font-size:10px;color:var(--muted)}
        .logout-btn{width:30px;height:30px;border-radius:8px;border:1px solid var(--line);background:var(--card);color:var(--muted);cursor:pointer}

        .content{flex:1;min-width:0;padding:26px 30px}
        .top{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;margin-bottom:18px;flex-wrap:wrap}
        .top h1{font-size:32px}
        .top p{color:var(--muted);margin-top:4px;font-size:13px}
        .tally{display:flex;gap:10px}
        .tally div{background:#fff;border:1px solid #d8ebdf;border-radius:12px;padding:9px 14px;text-align:right;box-shadow:0 8px 22px rgba(18,53,36,.06)}
        .tally strong{display:block;font-size:18px}
        .tally span{font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:.8px}

        form.filters{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px}
        .field{height:38px;border:1px solid #d8ebdf;border-radius:10px;background:#fff;color:var(--text);padding:0 11px;font:inherit;font-size:13px;outline:none}
        .field:focus{border-color:var(--green)}
        .btn{height:38px;border:0;border-radius:10px;padding:0 14px;background:var(--green);color:#fff;font-weight:800;font-size:12px;cursor:pointer;display:inline-flex;align-items:center;gap:7px;text-decoration:none}
        .btn.ghost{background:#fff;border:1px solid #d8ebdf;color:var(--text)}

        .card{background:#fff;border:1px solid #d8ebdf;border-radius:14px;overflow:hidden;box-shadow:0 12px 32px rgba(18,53,36,.07)}
        .row{display:flex;gap:13px;padding:13px 16px;border-bottom:1px solid var(--line);align-items:flex-start}
        .row:last-child{border-bottom:0}
        .row:hover{background:#f8fcf9}
        .mark{width:32px;height:32px;border-radius:9px;display:grid;place-items:center;flex-shrink:0;font-size:12px}
        .mark.gold{background:rgba(47,158,98,.14);color:var(--gold)}
        .mark.blue{background:rgba(91,141,239,.14);color:var(--blue)}
        .mark.green{background:rgba(22,199,106,.16);color:#0c8a4a}
        .mark.muted{background:rgba(95,127,107,.14);color:var(--muted)}
        .mark.slate{background:rgba(18,53,36,.08);color:var(--text)}
        .body{flex:1;min-width:0}
        .what{font-size:13px;font-weight:600;line-height:1.4}
        .who{font-size:11px;color:var(--muted);margin-top:3px}
        .who b{color:var(--text);font-weight:700}
        .props{margin-top:6px;display:flex;gap:6px;flex-wrap:wrap}
        .prop{font-size:10px;background:#f1f8f3;border:1px solid var(--line);border-radius:6px;padding:2px 7px;color:var(--muted)}
        .when{font-size:11px;color:var(--muted);text-align:right;white-space:nowrap;flex-shrink:0}
        .when b{display:block;color:var(--text);font-weight:700}
        .empty{padding:44px;text-align:center;color:var(--muted)}
        .pager{margin-top:16px}
        .pager svg{width:16px;height:16px}

        @media(max-width:1000px){.sidebar{display:none}.content{padding:18px}.row{flex-wrap:wrap}.when{text-align:left}}
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
            <div class="nav-section"><div class="nav-section-title">Management</div><a class="nav-item" href="{{ url('/orders') }}"><i class="fas fa-receipt"></i> Orders</a><a class="nav-item" href="{{ url('/reservations') }}"><i class="fas fa-calendar-check"></i> Reservations</a><a class="nav-item" href="{{ url('/inventory') }}"><i class="fas fa-boxes-stacked"></i> Inventory</a></div>
            <div class="nav-section"><div class="nav-section-title">System</div><a class="nav-item" href="{{ url('/reports') }}"><i class="fas fa-chart-bar"></i> Reports</a><a class="nav-item active" href="{{ url('/activity') }}"><i class="fas fa-clock-rotate-left"></i> Activity</a><a class="nav-item" href="{{ url('/settings') }}"><i class="fas fa-gear"></i> Settings</a></div>
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
                <h1>Activity</h1>
                <p>Everything staff have done, newest first.</p>
            </div>
            <div class="tally">
                <div><strong>{{ number_format($todayCount) }}</strong><span>Today</span></div>
                <div><strong>{{ number_format($totalCount) }}</strong><span>All time</span></div>
            </div>
        </div>

        <form class="filters" method="GET" action="{{ url('/activity') }}">
            <input class="field" type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search name, reference…">

            <select class="field" name="action">
                <option value="">All activity</option>
                @foreach($actions as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['action'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>

            <select class="field" name="user">
                <option value="">Anyone</option>
                @foreach($actors as $actor)
                    <option value="{{ $actor->id }}" @selected((string) ($filters['user'] ?? '') === (string) $actor->id)>{{ $actor->name }}</option>
                @endforeach
            </select>

            <input class="field" type="date" name="from" value="{{ $filters['from'] ?? '' }}">
            <input class="field" type="date" name="to" value="{{ $filters['to'] ?? '' }}">

            <button class="btn" type="submit"><i class="fas fa-filter"></i> Filter</button>
            <a class="btn ghost" href="{{ url('/activity') }}">Clear</a>
        </form>

        <div class="card">
            @forelse($logs as $log)
                <div class="row">
                    <div class="mark {{ $tone($log->action) }}">
                        <i class="fas {{ $glyph[$log->action] ?? 'fa-circle-dot' }}"></i>
                    </div>

                    <div class="body">
                        <div class="what">{{ $log->description }}</div>
                        <div class="who">
                            <b>{{ $log->actor_name ?? 'System' }}</b>
                            @if($log->actor_role) · {{ ucfirst($log->actor_role) }} @endif
                            · {{ $log->label() }}
                            @if($log->ip) · {{ $log->ip }} @endif
                        </div>

                        @if($log->properties)
                            <div class="props">
                                @foreach($log->properties as $key => $value)
                                    <span class="prop">{{ $key }}: {{ is_scalar($value) ? $value : json_encode($value) }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="when">
                        <b>{{ $log->created_at->format('g:i A') }}</b>
                        {{ $log->created_at->format('d M Y') }}
                    </div>
                </div>
            @empty
                <div class="empty">
                    <i class="fas fa-clock-rotate-left" style="font-size:26px;opacity:.4"></i>
                    <p style="margin-top:10px">Nothing recorded yet for this filter.</p>
                </div>
            @endforelse
        </div>

        <div class="pager">{{ $logs->links() }}</div>
    </main>
</div>

<script>
    function setupSidebar() {
        var defaultLogo = @json(asset('icons/queens-cup-logo.png'));
        var session = null;
        try { session = JSON.parse(localStorage.getItem('qc_session')); } catch (error) { session = null; }
        document.getElementById('sidebarCrown').innerHTML =
            '<img src="' + (localStorage.getItem('qc_logo') || defaultLogo) + '" alt="Logo">';
        if (!session) return;
        var fullName = session.fullName || session.username || 'User';
        document.getElementById('sidebarAvatar').textContent =
            fullName.split(' ').map(function (w) { return w[0]; }).join('').substring(0, 2).toUpperCase();
        document.getElementById('sidebarName').textContent = fullName;
        document.getElementById('sidebarRole').textContent =
            ({ admin: 'Branch Admin', cashier: 'Cashier' })[session.role] || session.role || 'Role';
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

    setupSidebar();
</script>
</body>
</html>
