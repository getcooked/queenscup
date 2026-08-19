<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login — The Queen's Cup</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="preload" href="{{ asset('vendor/fontawesome/webfonts/fa-solid-900.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link href="{{ asset('vendor/fontawesome/css/all.min.css') }}" rel="stylesheet">
    <style>
        :root{
            --bg:#f7fbf8;--bg-deep:#ffffff;--card:#ffffff;--card-hover:#e9f7ee;
            --border:#cfe7d7;--fg:#123524;--fg-muted:#5f7f6b;
            --accent:#12864e;--accent-light:#16a65f;--accent-dark:#0c6f3f;
            --accent-glow:rgba(14,140,74,0.25);--accent-glow-strong:rgba(14,140,74,0.45);
            --gold:#2f9e62;--gold-light:#45b873;--gold-dark:#1f7a4b;
            --gold-glow:rgba(201,168,76,0.25);--gold-glow-strong:rgba(201,168,76,0.45);
            --danger:#E53170;
            --radius:14px;--radius-sm:10px;
            --shadow:0 8px 32px rgba(18,53,36,0.12);
            --glass:rgba(255,255,255,0.86);--glass-border:rgba(207,231,215,0.9);
        }
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--fg);min-height:100vh;display:flex;align-items:center;justify-content:center;overflow:hidden}
        h1,h2,h3,h4{font-family:'Playfair Display',serif}

        .staff-login-container{position:fixed;inset:0;z-index:5000;display:flex;align-items:center;justify-content:center;background:var(--bg-deep);overflow:hidden}
        .staff-login-bg-orb{position:absolute;border-radius:50%;filter:blur(140px);pointer-events:none}
        .staff-login-bg-orb.a{width:650px;height:650px;background:var(--accent);top:-250px;right:-180px;opacity:0.1;animation:orbFloat 8s ease-in-out infinite}
        .staff-login-bg-orb.b{width:550px;height:550px;background:var(--gold);bottom:-250px;left:-180px;opacity:0.06;animation:orbFloat 10s ease-in-out infinite reverse}
        .staff-login-bg-orb.c{width:300px;height:300px;background:var(--accent-light);bottom:15%;right:25%;opacity:0.04;animation:orbFloat 12s ease-in-out infinite}
        @keyframes orbFloat{0%,100%{transform:translate(0,0)}50%{transform:translate(30px,-20px)}}

        .staff-login-card{width:440px;max-width:92vw;position:relative;z-index:2;background:var(--glass);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);border:1px solid var(--glass-border);border-radius:20px;box-shadow:0 32px 64px rgba(18,53,36,0.16),inset 0 1px 0 rgba(255,255,255,0.04);overflow:hidden;animation:loginCardIn 0.7s cubic-bezier(0.16,1,0.3,1)}
        @keyframes loginCardIn{from{opacity:0;transform:translateY(40px) scale(0.94)}to{opacity:1;transform:translateY(0) scale(1)}}

        .staff-login-header{padding:36px 36px 0;text-align:center}
        .staff-login-crown{width:72px;height:72px;border-radius:50%;margin:0 auto 18px;display:flex;align-items:center;justify-content:center;box-shadow:0 6px 30px var(--accent-glow-strong),0 0 60px var(--accent-glow);overflow:hidden;border:2px solid rgba(14,140,74,0.3);position:relative;background:linear-gradient(135deg,var(--accent-dark),var(--accent))}
        .staff-login-crown img{width:100%;height:100%;object-fit:cover}
        .staff-login-crown::after{content:'';position:absolute;inset:-2px;border-radius:50%;background:conic-gradient(from 0deg,var(--accent),var(--gold),var(--accent));z-index:-1;animation:crownSpin 6s linear infinite;opacity:0.5}
        @keyframes crownSpin{to{transform:rotate(360deg)}}
        .staff-login-header h2{font-size:24px;margin-bottom:4px;background:linear-gradient(135deg,var(--accent-light),var(--gold-light));-webkit-background-clip:text;-webkit-text-fill-color:transparent}
        .staff-login-header p{font-size:12px;color:var(--fg-muted);letter-spacing:1.5px;text-transform:uppercase}

        .staff-login-body{padding:28px 36px 36px}
        .staff-login-field{margin-bottom:16px}
        .staff-login-field label{display:block;font-size:11px;font-weight:600;color:var(--fg-muted);margin-bottom:6px;text-transform:uppercase;letter-spacing:0.8px}
        .staff-login-field input{width:100%;padding:12px 14px;background:rgba(255,255,255,0.92);border:1px solid var(--border);border-radius:var(--radius-sm);color:var(--fg);font-size:13px;font-family:'DM Sans';transition:all 0.3s;outline:none}
        .staff-login-field input:focus{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-glow)}
        .staff-login-field .input-icon{position:relative}
        .staff-login-field .input-icon > i{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:var(--fg-muted);font-size:13px;pointer-events:none}
        .staff-login-field .input-icon input{padding-left:40px}
        .staff-login-field .input-icon input[type="password"],
        .staff-login-field .input-icon input[type="text"]{padding-right:44px}
        .staff-login-field .toggle-pw{position:absolute;right:8px;top:50%;transform:translateY(-50%);width:32px;height:32px;display:flex;align-items:center;justify-content:center;color:var(--fg-muted);cursor:pointer;font-size:13px;background:none;border:none;border-radius:8px;transition:color 0.3s,background 0.3s}
        .staff-login-field .toggle-pw i{position:static;transform:none;pointer-events:none}
        .staff-login-field .toggle-pw:hover{color:var(--accent)}

        .staff-login-btn{width:100%;padding:13px;border:none;border-radius:var(--radius-sm);background:linear-gradient(135deg,var(--accent),var(--accent-dark));color:#fff;font-size:14px;font-weight:700;cursor:pointer;font-family:'DM Sans';transition:all 0.3s;margin-top:6px;position:relative;overflow:hidden}
        .staff-login-btn::before{content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.1),transparent);transition:0.5s}
        .staff-login-btn:hover::before{left:100%}
        .staff-login-btn:hover{box-shadow:0 6px 28px var(--accent-glow-strong);transform:translateY(-2px)}

        .staff-login-error{background:rgba(229,49,112,0.1);border:1px solid rgba(229,49,112,0.3);border-radius:var(--radius-sm);padding:10px 14px;font-size:12px;color:var(--danger);margin-bottom:14px;display:none}
        .staff-login-error.show{display:block;animation:shake 0.4s ease}
        @keyframes shake{0%,100%{transform:translateX(0)}25%{transform:translateX(-6px)}75%{transform:translateX(6px)}}

        .staff-login-back{text-align:center;margin-top:16px;padding-top:16px;border-top:1px solid var(--border)}
        .staff-login-back{display:flex;align-items:center;justify-content:center;gap:10px}
        .staff-login-sep{color:var(--fg-muted);font-size:12px}
        .staff-login-back a{font-size:12px;color:var(--accent-light);text-decoration:none;transition:color 0.3s}
        .staff-login-back a:hover{color:var(--gold-light)}

    </style>
</head>
<body>
    <div class="staff-login-container">
        <div class="staff-login-bg-orb a"></div>
        <div class="staff-login-bg-orb b"></div>
        <div class="staff-login-bg-orb c"></div>

        <div class="staff-login-card">
            <div class="staff-login-header">
                <div class="staff-login-crown">
                    <img src="{{ asset('icons/queens-cup-logo.png') }}" alt="Queen's Cup Logo">
                </div>
                <h2>Staff Portal</h2>
                <p>The Queen's Cup Management</p>
            </div>

            <div class="staff-login-body">
                <form id="staffLoginForm" onsubmit="handleStaffLogin(event)">
                    @csrf

                    <div class="staff-login-error {{ $errors->any() ? 'show' : '' }}" id="staffLoginError">
                        {{ $errors->first() }}
                    </div>

                <div class="staff-login-field">
                    <label>Email</label>
                    <div class="input-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="staffEmail" name="email" placeholder="Enter email" autocomplete="email">
                    </div>
                </div>

                <div class="staff-login-field">
                    <label>Password</label>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="staffPassword" name="password" placeholder="Enter password" autocomplete="current-password">
                        <button type="button" class="toggle-pw" onclick="toggleStaffPassword(event)">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="staff-login-btn">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
                </form>


                <div class="staff-login-back">
                    <a href="{{ url('/') }}"><i class="fas fa-house"></i> Home</a>
                    <span class="staff-login-sep">·</span>
                    <a href="{{ url('/orders') }}">Customer sign in</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleStaffPassword(event) {
            const input = document.getElementById('staffPassword');
            const icon = event.target.closest('button').querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }

        function showStaffLoginError(msg) {
            const el = document.getElementById('staffLoginError');
            el.textContent = msg;
            el.classList.add('show');
            setTimeout(() => {
                el.classList.remove('show');
            }, 4000);
        }

        function handleStaffLogin(event) {
            event.preventDefault();

            const form = document.getElementById('staffLoginForm');
            const submitButton = form.querySelector('button[type="submit"]');
            const email = document.getElementById('staffEmail').value.trim();
            const password = document.getElementById('staffPassword').value;

            if (!email || !password) {
                showStaffLoginError('Please enter email and password.');
                return;
            }

            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In';

            fetch('/staff-login', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                },
                body: new FormData(form),
            })
                .then(async (response) => {
                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Invalid staff email or password.');
                    }

                    localStorage.setItem('qc_session', JSON.stringify(data.user));
                    window.location.href = data.redirect_to || '/dashboard';
                })
                .catch((error) => {
                    showStaffLoginError(error.message);
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="fas fa-sign-in-alt"></i> Sign In';
                });
        }
    </script>
</body>
</html>
