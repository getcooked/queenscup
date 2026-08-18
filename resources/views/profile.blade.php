<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | The Queen's Cup</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="preload" href="{{ asset('vendor/fontawesome/webfonts/fa-solid-900.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link href="{{ asset('vendor/fontawesome/css/all.min.css') }}" rel="stylesheet">
    <style>
        :root{--bg:#f7fbf8;--deep:#ffffff;--card:#ffffff;--line:#cfe7d7;--text:#123524;--muted:#5f7f6b;--green:#16c76a;--gold:#2f9e62;--danger:#ef3d5f}
        *{box-sizing:border-box;margin:0;padding:0}body{height:100vh;background:var(--bg);color:var(--text);font-family:"DM Sans",sans-serif;overflow:hidden}h1,h2,h3{font-family:"Playfair Display",serif}.layout{display:flex;height:100vh}.sidebar{width:260px;background:var(--deep);border-right:1px solid var(--line);display:flex;flex-direction:column;flex-shrink:0}.sidebar-brand{padding:22px 18px;border-bottom:1px solid var(--line);display:flex;align-items:center;gap:13px}.crown-icon{width:46px;height:46px;border-radius:50%;overflow:hidden;flex-shrink:0;border:2px solid rgba(22,199,106,.3);background:linear-gradient(135deg,#0c6f3f,#16c76a)}.crown-icon img{width:100%;height:100%;object-fit:cover}.sidebar-brand h2{font-size:14px;line-height:1.2;background:linear-gradient(135deg,#16a65f,#2f9e62);-webkit-background-clip:text;-webkit-text-fill-color:transparent}.tagline{font-size:10px;color:var(--muted);letter-spacing:.5px}.sidebar-nav{flex:1;padding:14px 10px;overflow-y:auto}.nav-section-title{padding:12px 10px 7px;color:var(--muted);font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase}.nav-item{display:flex;align-items:center;gap:10px;min-height:40px;padding:0 12px;border-radius:10px;color:var(--muted);cursor:pointer;font-size:13px;font-weight:600;text-decoration:none;transition:background .2s ease,color .2s ease}.nav-item:hover,.nav-item.active{background:rgba(22,199,106,.13);color:var(--green)}.sidebar-footer{padding:14px 18px;border-top:1px solid var(--line);display:flex;align-items:center;gap:12px}.avatar{width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,var(--green),var(--gold));display:grid;place-items:center;font-size:13px;font-weight:800;color:#fff}.user-info{flex:1;min-width:0}.user-info .name{font-size:12px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.user-info .role{font-size:10px;color:var(--muted)}.logout-btn{width:30px;height:30px;border-radius:8px;border:1px solid var(--line);background:var(--card);color:var(--muted);cursor:pointer}.logout-btn:hover{border-color:var(--danger);color:var(--danger)}.content{flex:1;min-width:0;overflow:auto;padding:30px}.top{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;margin-bottom:22px}.top h1{font-size:34px}.top p{color:var(--muted);margin-top:5px}.grid{display:grid;grid-template-columns:minmax(0,1.6fr) minmax(300px,.9fr);gap:18px}.card{background:var(--card);border:1px solid var(--line);border-radius:14px;padding:22px}.card h3{font-size:20px;margin-bottom:16px}.profile-head{display:flex;align-items:center;gap:16px;margin-bottom:20px}.profile-photo{width:76px;height:76px;border-radius:50%;display:grid;place-items:center;overflow:hidden;background:linear-gradient(135deg,var(--green),var(--gold));color:#fff;font-size:26px;font-weight:800;flex-shrink:0}.profile-photo img{width:100%;height:100%;object-fit:cover}.role-pill{display:inline-flex;margin-top:6px;border-radius:999px;background:rgba(22,199,106,.13);color:var(--green);font-size:11px;font-weight:800;padding:5px 10px}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}.field.full{grid-column:1/-1}.field label{display:block;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px}.field input,.field textarea{width:100%;border:1px solid var(--line);border-radius:10px;background:#ffffff;color:var(--text);padding:11px 12px;font:inherit;outline:none}.field input:focus,.field textarea:focus{border-color:var(--green)}.field textarea{min-height:120px;resize:vertical}.btn{height:40px;border:0;border-radius:10px;padding:0 14px;background:var(--green);color:#ffffff;font-weight:800;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:8px;margin-top:16px}.alert{border:1px solid rgba(22,199,106,.35);background:rgba(22,199,106,.1);color:#91f0be;border-radius:12px;padding:12px 14px;margin-bottom:16px}.error{border-color:rgba(239,61,95,.4);background:rgba(239,61,95,.1);color:#ff9bad}.meta{display:grid;gap:12px;color:var(--muted);font-size:13px}.meta strong{display:block;color:var(--text);font-size:14px;margin-top:3px}@media(max-width:1100px){body{overflow:auto;height:auto}.layout{height:auto}.sidebar{display:none}.content{padding:20px}.grid{grid-template-columns:1fr}.form-grid{grid-template-columns:1fr}.top{align-items:flex-start;flex-direction:column}}
        body{background:radial-gradient(circle at top right,rgba(22,199,106,.08),transparent 34%),linear-gradient(180deg,#fbfefc 0%,var(--bg) 100%)}.sidebar,.card{box-shadow:0 12px 32px rgba(18,53,36,.07)}.card{border-color:#d8ebdf;background:#fff}.nav-item.active{box-shadow:inset 3px 0 0 var(--green)}.top h1{font-size:36px}.field input,.field textarea{border-color:#d8ebdf}.field input:focus,.field textarea:focus{box-shadow:0 0 0 3px rgba(22,199,106,.14)}.btn{transition:transform .18s ease,box-shadow .18s ease}.btn:hover{transform:translateY(-1px);box-shadow:0 10px 24px rgba(18,134,78,.15)}.profile-photo{box-shadow:0 10px 24px rgba(18,134,78,.18)}
    </style>
    <link href="{{ asset('css/admin-shell.css') }}" rel="stylesheet">
    <script src="{{ asset('js/admin-sidebar.js') }}" defer></script>
</head>
<body>
@php
    $roleLabels = ['admin' => 'Branch Admin', 'cashier' => 'Cashier', 'customer' => 'Customer'];
    $displayRole = $roleLabels[$user->role] ?? ucfirst($user->role);
    $initials = collect(explode(' ', $user->name))->filter()->map(fn ($part) => substr($part, 0, 1))->join('');
@endphp
<div class="layout">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="crown-icon" id="sidebarCrown"><img src="{{ asset('icons/queens-cup-logo.png') }}" alt="Logo"></div>
            <div><h2>The Queen's Cup</h2><span class="tagline">Crowned with Flavors</span></div>
        </div>
        <nav class="sidebar-nav">
            @if($user->role === 'admin')
                <div class="nav-section"><div class="nav-section-title">Main</div><a class="nav-item" href="{{ url('/dashboard') }}"><i class="fas fa-chart-pie"></i> Dashboard</a><a class="nav-item" href="{{ url('/pos') }}"><i class="fas fa-cash-register"></i> Point of Sale</a></div>
                <div class="nav-section"><div class="nav-section-title">Management</div><a class="nav-item" href="{{ url('/orders') }}"><i class="fas fa-receipt"></i> Orders</a><a class="nav-item" href="{{ url('/inventory') }}"><i class="fas fa-boxes-stacked"></i> Inventory</a></div>
                <div class="nav-section"><div class="nav-section-title">System</div><a class="nav-item" href="{{ url('/reports') }}"><i class="fas fa-chart-bar"></i> Reports</a><a class="nav-item" href="{{ url('/settings') }}"><i class="fas fa-gear"></i> Settings</a></div>
            @else
                <div class="nav-section"><div class="nav-section-title">Counter</div><a class="nav-item" href="{{ url('/pos') }}"><i class="fas fa-cash-register"></i> Point of Sale</a><a class="nav-item" href="{{ url('/orders') }}"><i class="fas fa-receipt"></i> Orders</a></div>
            @endif
            <div class="nav-section"><div class="nav-section-title">Account</div><a class="nav-item active" href="{{ url('/profile') }}"><i class="fas fa-user-circle"></i> My Profile</a></div>
        </nav>
        <div class="sidebar-footer">
            <div class="avatar" id="sidebarAvatar">{{ strtoupper(substr($initials ?: '?', 0, 2)) }}</div>
            <div class="user-info"><div class="name" id="sidebarName">{{ $user->name }}</div><div class="role" id="sidebarRole">{{ $displayRole }}</div></div>
            <button class="logout-btn" onclick="handleLogout()" title="Sign Out"><i class="fas fa-right-from-bracket"></i></button>
        </div>
    </aside>
    <main class="content">
        <div class="top">
            <div><h1>My Profile</h1><p>View and update your account details.</p></div>
        </div>

        @if(session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert error">{{ $errors->first() }}</div>
        @endif

        <div class="grid">
            <form class="card" method="POST" action="{{ url('/profile') }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="profile-head">
                    <div class="profile-photo">
                        @if($profile->avatar_path)
                            <img src="{{ asset('storage/'.$profile->avatar_path) }}" alt="{{ $user->name }}">
                        @else
                            {{ strtoupper(substr($initials ?: '?', 0, 2)) }}
                        @endif
                    </div>
                    <div>
                        <h3>{{ $user->name }}</h3>
                        <div class="role-pill">{{ $displayRole }}</div>
                    </div>
                </div>
                <div class="form-grid">
                    <div class="field"><label>Full Name</label><input name="name" value="{{ old('name', $user->name) }}" required></div>
                    <div class="field"><label>Email</label><input type="email" name="email" value="{{ old('email', $user->email) }}" required></div>
                    <div class="field"><label>Phone</label><input name="phone" value="{{ old('phone', $profile->phone) }}"></div>
                    <div class="field"><label>Profile Photo</label><input type="file" name="avatar" accept="image/*"></div>
                    <div class="field full"><label>Address</label><input name="address" value="{{ old('address', $profile->address) }}"></div>
                    <div class="field full"><label>Bio</label><textarea name="bio">{{ old('bio', $profile->bio) }}</textarea></div>
                </div>
                <button class="btn" type="submit"><i class="fas fa-save"></i> Save Profile</button>
            </form>

            <div>
                <div class="card">
                    <h3>Account</h3>
                    <div class="meta">
                        <div>Email<strong>{{ $user->email }}</strong></div>
                        <div>Role<strong>{{ $displayRole }}</strong></div>
                        <div>Member Since<strong>{{ optional($user->created_at)->format('F d, Y') }}</strong></div>
                    </div>
                </div>
                <form class="card" method="POST" action="{{ url('/profile/password') }}" style="margin-top:18px">
                    @csrf
                    @method('PATCH')
                    <h3>Change Password</h3>
                    <div class="form-grid">
                        <div class="field full"><label>Current Password</label><input type="password" name="current_password" required></div>
                        <div class="field full"><label>New Password</label><input type="password" name="password" required></div>
                        <div class="field full"><label>Confirm Password</label><input type="password" name="password_confirmation" required></div>
                    </div>
                    <button class="btn" type="submit"><i class="fas fa-key"></i> Update Password</button>
                </form>
            </div>
        </div>
    </main>
</div>
<script>
    var defaultLogo = '{{ asset('icons/queens-cup-logo.png') }}';
    document.getElementById('sidebarCrown').innerHTML = '<img src="' + (localStorage.getItem('qc_logo') || defaultLogo) + '" alt="Logo">';
    localStorage.setItem('qc_session', JSON.stringify({
        id: {{ $user->id }},
        username: @json($user->email),
        role: @json($user->role),
        fullName: @json($user->name),
        since: @json(optional($user->created_at)->toDateString())
    }));

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
</script>
</body>
</html>
