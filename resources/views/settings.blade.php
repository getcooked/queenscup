<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Settings | The Queen's Cup</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root{--bg:#f7fbf8;--card:#fff;--soft:#f1f8f3;--line:#cfe7d7;--text:#123524;--muted:#5f7f6b;--green:#16c76a;--deep:#0c6f3f;--gold:#2f9e62;--danger:#ef3d5f;--shadow:0 18px 44px rgba(18,53,36,.1)}
        *{box-sizing:border-box;margin:0;padding:0}body{min-height:100vh;background:var(--bg);color:var(--text);font-family:"DM Sans",sans-serif}h1,h2,h3{font-family:"Playfair Display",serif}.layout{display:flex;min-height:100vh}.sidebar{width:260px;background:#fff;border-right:1px solid var(--line);display:flex;flex-direction:column;flex-shrink:0}.sidebar-brand{padding:22px 18px;border-bottom:1px solid var(--line);display:flex;align-items:center;gap:13px}.crown-icon{width:46px;height:46px;border-radius:50%;overflow:hidden;border:2px solid rgba(22,199,106,.3);background:linear-gradient(135deg,#0c6f3f,#16c76a)}.crown-icon img{width:100%;height:100%;object-fit:cover}.sidebar-brand h2{font-size:14px;line-height:1.2;background:linear-gradient(135deg,#16a65f,#2f9e62);-webkit-background-clip:text;-webkit-text-fill-color:transparent}.tagline{font-size:10px;color:var(--muted);letter-spacing:.5px}.sidebar-nav{flex:1;padding:14px 10px}.nav-section-title{padding:12px 10px 7px;color:var(--muted);font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase}.nav-item{display:flex;align-items:center;gap:10px;min-height:40px;padding:0 12px;border-radius:10px;color:var(--muted);font-size:13px;font-weight:600;text-decoration:none}.nav-item:hover,.nav-item.active{background:rgba(22,199,106,.13);color:var(--green)}.content{flex:1;padding:30px;overflow:auto}.top{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;margin-bottom:22px}.top h1{font-size:36px}.top p{color:var(--muted);margin-top:5px}.page-note{display:flex;gap:12px;align-items:flex-start;background:#fff;border:1px solid var(--line);border-radius:14px;padding:14px 16px;margin-bottom:18px;color:var(--muted);font-size:13px;line-height:1.5}.page-note i{color:var(--green);font-size:18px;margin-top:2px}.grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}.grid.spaced{margin-top:18px}.card{background:var(--card);border:1px solid var(--line);border-radius:14px;overflow:hidden;box-shadow:var(--shadow)}.card-head{padding:18px 20px;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;gap:12px}.title-wrap{display:flex;align-items:center;gap:12px}.pay-icon{width:44px;height:44px;border-radius:12px;background:rgba(22,199,106,.12);display:grid;place-items:center;color:var(--deep);font-size:18px}.card h3{font-size:21px}.status{display:inline-flex;align-items:center;gap:6px;border-radius:999px;padding:5px 10px;font-size:11px;font-weight:800}.status.ready{background:rgba(22,199,106,.13);color:#11884b}.status.missing{background:rgba(239,61,95,.1);color:var(--danger)}.card-body{padding:20px}.hint{color:var(--muted);font-size:13px;line-height:1.5;margin-bottom:16px}.preview-shell{display:grid;grid-template-columns:210px 1fr;gap:18px;align-items:center;margin-bottom:18px}.preview{width:210px;aspect-ratio:1;border-radius:14px;border:1px solid var(--line);background:var(--soft);display:grid;place-items:center;overflow:hidden;color:var(--muted);font-size:42px}.preview img{width:100%;height:100%;object-fit:contain;background:#fff}.meta{display:grid;gap:10px}.meta-item{background:var(--soft);border:1px solid var(--line);border-radius:10px;padding:10px 12px}.meta-item span{display:block;color:var(--muted);font-size:10px;text-transform:uppercase;font-weight:800;letter-spacing:.7px}.meta-item strong{display:block;margin-top:3px;font-size:13px}.upload-box{border:1px dashed var(--line);border-radius:12px;background:#fbfdfb;padding:14px}.field{margin-bottom:12px}.field label{display:block;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:.8px;font-weight:800;margin-bottom:7px}.field input,.field select{width:100%;border:1px solid var(--line);border-radius:10px;background:#fff;color:var(--text);padding:10px 12px;font:inherit}.btn{height:42px;border:0;border-radius:10px;padding:0 15px;background:linear-gradient(135deg,var(--green),var(--deep));color:#fff;font-weight:800;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:8px;min-width:170px}.alert{border:1px solid rgba(22,199,106,.35);background:rgba(22,199,106,.1);color:#15864d;border-radius:12px;padding:12px 14px;margin-bottom:16px}.error{border-color:rgba(239,61,95,.4);background:rgba(239,61,95,.1);color:var(--danger)}@media(max-width:1100px){.preview-shell{grid-template-columns:1fr}.preview{width:100%;max-width:260px}}@media(max-width:900px){.layout{display:block}.sidebar{display:none}.grid{grid-template-columns:1fr}.content{padding:18px}.top{display:block}.top h1{font-size:30px}}
    </style>
    <link href="{{ asset('css/admin-shell.css') }}" rel="stylesheet">
    <script src="{{ asset('js/admin-sidebar.js') }}" defer></script>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="crown-icon"><img src="{{ asset('icons/queens-cup-logo.png') }}" alt="Logo"></div>
            <div><h2>The Queen's Cup</h2><span class="tagline">Crowned with Flavors</span></div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section-title">Main</div>
            <a class="nav-item" href="{{ url('/dashboard') }}"><i class="fas fa-chart-pie"></i> Dashboard</a>
            <a class="nav-item" href="{{ url('/pos') }}"><i class="fas fa-cash-register"></i> Point of Sale</a>
            <div class="nav-section-title">Management</div>
            <a class="nav-item" href="{{ url('/orders') }}"><i class="fas fa-receipt"></i> Orders</a>
            <a class="nav-item" href="{{ url('/inventory') }}"><i class="fas fa-boxes-stacked"></i> Inventory</a>
            <div class="nav-section-title">System</div>
            <a class="nav-item" href="{{ url('/reports') }}"><i class="fas fa-chart-bar"></i> Reports</a>
            <a class="nav-item active" href="{{ url('/settings') }}"><i class="fas fa-gear"></i> Settings</a>
            <div class="nav-section-title">Account</div>
            <a class="nav-item" href="{{ url('/profile') }}"><i class="fas fa-user-circle"></i> My Profile</a>
        </nav>
    </aside>
    <main class="content">
        <div class="top">
            <div><h1>Settings</h1><p>Upload payment QR codes for customer checkout.</p></div>
            <button class="btn" type="button" onclick="handleLogout()"><i class="fas fa-right-from-bracket"></i> Sign Out</button>
        </div>

        <div class="page-note">
            <i class="fas fa-circle-info"></i>
            <div>These QR codes appear in the customer checkout modal when GCash QR or Maya QR is selected. Replace an existing QR anytime by uploading a new image.</div>
        </div>

        @if (session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert error">{{ $errors->first() }}</div>
        @endif

        <div class="grid">
            @foreach ([
                'gcash' => ['label' => 'GCash QR', 'file' => 'gcash-qr.png'],
                'maya' => ['label' => 'Maya QR', 'file' => 'maya-qr.png'],
            ] as $method => $qr)
                @php
                    $path = public_path('images/'.$qr['file']);
                    $exists = file_exists($path);
                @endphp
                <section class="card">
                    <div class="card-head">
                        <div class="title-wrap">
                            <div class="pay-icon"><i class="fas fa-qrcode"></i></div>
                            <div>
                                <h3>{{ $qr['label'] }}</h3>
                                <p class="hint" style="margin:3px 0 0">Customer scan code</p>
                            </div>
                        </div>
                        <span class="status {{ $exists ? 'ready' : 'missing' }}">
                            <i class="fas {{ $exists ? 'fa-check-circle' : 'fa-triangle-exclamation' }}"></i>
                            {{ $exists ? 'Uploaded' : 'Missing' }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="preview-shell">
                            <div class="preview">
                                @if ($exists)
                                    <img src="{{ asset('images/'.$qr['file']).'?v='.filemtime($path) }}" alt="{{ $qr['label'] }}">
                                @else
                                    <i class="fas fa-qrcode"></i>
                                @endif
                            </div>
                            <div class="meta">
                                <div class="meta-item"><span>Saved File</span><strong>{{ $qr['file'] }}</strong></div>
                                <div class="meta-item"><span>Accepted</span><strong>JPG, PNG, WEBP, GIF up to 2MB</strong></div>
                            </div>
                        </div>
                        <form class="upload-box" method="POST" action="{{ url('/settings/qr-code') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="payment_method" value="{{ $method }}">
                            <div class="field">
                                <label>{{ $exists ? 'Replace' : 'Upload' }} {{ $qr['label'] }} Image</label>
                                <input type="file" name="qr_code" accept="image/png,image/jpeg,image/webp,image/gif" required>
                            </div>
                            <button class="btn" type="submit"><i class="fas fa-upload"></i> {{ $exists ? 'Replace QR' : 'Upload QR' }}</button>
                        </form>
                    </div>
                </section>
            @endforeach
        </div>

        <div class="grid spaced">
            <section class="card">
                <div class="card-head">
                    <div class="title-wrap">
                        <div class="pay-icon"><i class="fas fa-user-plus"></i></div>
                        <div>
                            <h3>Create Staff Account</h3>
                            <p class="hint" style="margin:3px 0 0">Add cashier or admin login access</p>
                        </div>
                    </div>
                    <span class="status ready"><i class="fas fa-crown"></i> Admin Only</span>
                </div>
                <div class="card-body">
                    <div class="upload-box">
                        <div class="field">
                            <label>Full Name</label>
                            <input type="text" id="staffName" placeholder="Enter full name">
                        </div>
                        <div class="field">
                            <label>Email</label>
                            <input type="email" id="staffEmail" placeholder="staff@example.com">
                        </div>
                        <div class="field">
                            <label>Password</label>
                            <input type="password" id="staffPassword" placeholder="Min 6 characters">
                        </div>
                        <div class="field">
                            <label>Role</label>
                            <select id="staffRole">
                                <option value="cashier">Cashier</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <button class="btn" type="button" onclick="createStaffAccount()"><i class="fas fa-user-plus"></i> Create Account</button>
                    </div>
                </div>
            </section>
        </div>
    </main>
</div>
<script>
function csrfToken(){var token=document.querySelector('meta[name="csrf-token"]');return token?token.getAttribute('content'):'{{ csrf_token() }}';}
function handleLogout(){
    localStorage.removeItem('qc_session');
    fetch(@json(route('staff.logout')),{method:'POST',credentials:'same-origin',headers:{'X-CSRF-TOKEN':csrfToken()}})
        .finally(function(){window.location.href=@json(route('login'));});
}
function isValidEmail(value){return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(value||'').trim());}
function showStaffMessage(message,type){
    var existing=document.getElementById('staffAccountMessage');
    if(existing)existing.remove();
    var el=document.createElement('div');
    el.id='staffAccountMessage';
    el.className='alert '+(type==='error'?'error':'');
    el.textContent=message;
    var target=document.querySelector('.grid.spaced');
    target.parentNode.insertBefore(el,target);
    setTimeout(function(){el.remove();},5000);
}
function createStaffAccount(){
    var name=document.getElementById('staffName').value.trim();
    var email=document.getElementById('staffEmail').value.trim();
    var password=document.getElementById('staffPassword').value;
    var role=document.getElementById('staffRole').value;
    if(!name){showStaffMessage('Please enter full name.','error');return;}
    if(!email||!isValidEmail(email)){showStaffMessage('Please enter a valid email.','error');return;}
    if(password.length<6){showStaffMessage('Password must be at least 6 characters.','error');return;}
    fetch('{{ url('/staff') }}',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrfToken()},body:JSON.stringify({name:name,email:email,password:password,role:role})})
        .then(function(response){return response.json().then(function(data){if(!response.ok)throw data;return data;});})
        .then(function(data){
            document.getElementById('staffName').value='';
            document.getElementById('staffEmail').value='';
            document.getElementById('staffPassword').value='';
            showStaffMessage((data&&data.message)||'Staff account created.','success');
        })
        .catch(function(error){
            var message=(error&&error.message)||'Unable to create staff account.';
            if(error&&error.errors){var keys=Object.keys(error.errors);if(keys.length&&error.errors[keys[0]][0])message=error.errors[keys[0]][0];}
            showStaffMessage(message,'error');
        });
}
</script>
</body>
</html>
