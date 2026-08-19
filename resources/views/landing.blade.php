@php
    // The APK is dropped into public/downloads by whoever cuts the release, so
    // the button degrades to "coming soon" instead of 404ing before then.
    $apkPath = public_path('downloads/queens-cup.apk');
    $apkReady = file_exists($apkPath);
    $apkSize = $apkReady ? round(filesize($apkPath) / 1048576, 1) : null;
    $takeoutFee = (float) config('queenscup.takeout_fee_per_cup', 5.00);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Queen's Cup — Milktea in Madridejos, Cebu</title>
    <meta name="description" content="Reserve your milktea ahead of time at The Queen's Cup, Madridejos, Cebu. Browse the menu, reserve for dine in or take out, and pick it up when it is ready.">
    <link rel="icon" href="{{ asset('icons/queens-cup-logo.png') }}" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="preload" href="{{ asset('vendor/fontawesome/webfonts/fa-solid-900.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link href="{{ asset('vendor/fontawesome/css/all.min.css') }}" rel="stylesheet">
    <style>
        :root{
            --bg:#f7fbf8;--card:#ffffff;--line:#d8ebdf;--text:#123524;--muted:#5f7f6b;
            --green:#12864e;--green-light:#16a65f;--green-dark:#0c6f3f;--gold:#2f9e62;
            --shadow:0 12px 32px rgba(18,53,36,.07);--shadow-lg:0 24px 60px rgba(18,53,36,.14);
        }
        *{box-sizing:border-box;margin:0;padding:0}
        html{scroll-behavior:smooth}
        body{background:var(--bg);color:var(--text);font-family:"DM Sans",sans-serif;line-height:1.6;-webkit-font-smoothing:antialiased}
        h1,h2,h3{font-family:"Playfair Display",serif;line-height:1.15}
        a{color:inherit;text-decoration:none}
        img{max-width:100%;display:block}
        .wrap{width:min(1160px,100% - 40px);margin-inline:auto}

        /* ---------- nav ---------- */
        .nav{position:sticky;top:0;z-index:50;background:rgba(255,255,255,.9);backdrop-filter:blur(14px);border-bottom:1px solid var(--line)}
        .nav-inner{display:flex;align-items:center;gap:16px;height:74px}
        .brand{display:flex;align-items:center;gap:12px;margin-right:auto}
        .brand img{width:44px;height:44px;border-radius:50%;object-fit:cover;border:2px solid rgba(18,134,78,.25)}
        .brand b{display:block;font-family:"Playfair Display",serif;font-size:16px;line-height:1.1}
        .brand span{font-size:10px;color:var(--muted);letter-spacing:1.5px;text-transform:uppercase}
        .nav-links{display:flex;gap:26px;font-size:14px;font-weight:600;color:var(--muted)}
        .nav-links a:hover{color:var(--green)}
        .nav-cta{display:flex;gap:10px}

        .btn{display:inline-flex;align-items:center;justify-content:center;gap:9px;height:46px;padding:0 22px;border-radius:12px;font-weight:700;font-size:14px;border:0;cursor:pointer;font-family:"DM Sans",sans-serif;transition:transform .18s ease,box-shadow .18s ease}
        .btn:hover{transform:translateY(-2px)}
        .btn-primary{background:linear-gradient(135deg,var(--green),var(--green-light));color:#fff;box-shadow:0 10px 24px rgba(18,134,78,.22)}
        .btn-primary:hover{box-shadow:0 16px 34px rgba(18,134,78,.3)}
        .btn-ghost{background:var(--card);border:1px solid var(--line);color:var(--text)}
        .btn-ghost:hover{border-color:var(--green);color:var(--green)}
        .btn-dark{background:var(--text);color:#fff}
        .btn-sm{height:40px;padding:0 16px;font-size:13px}
        .btn[disabled]{opacity:.55;cursor:not-allowed;transform:none}

        /* ---------- hero ---------- */
        .hero{padding:76px 0 64px;background:
            radial-gradient(circle at 88% 8%,rgba(22,199,106,.12),transparent 42%),
            linear-gradient(180deg,#fbfefc 0%,var(--bg) 100%)}
        .hero-grid{display:grid;grid-template-columns:1.05fr .95fr;gap:56px;align-items:center}
        .eyebrow{display:inline-flex;align-items:center;gap:8px;background:rgba(18,134,78,.1);color:var(--green);font-size:12px;font-weight:700;padding:7px 14px;border-radius:999px;margin-bottom:20px}
        .hero h1{font-size:clamp(38px,5vw,60px);font-weight:900;margin-bottom:18px}
        .hero h1 em{font-style:normal;background:linear-gradient(135deg,var(--green-light),var(--gold));-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent}
        .hero p.lead{font-size:17px;color:var(--muted);max-width:52ch;margin-bottom:28px}
        .hero-cta{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:30px}
        .hero-points{display:flex;gap:24px;flex-wrap:wrap;font-size:13px;color:var(--muted);font-weight:600}
        .hero-points i{color:var(--green);margin-right:7px}
        .hero-art{position:relative;display:grid;place-items:center}
        .hero-art .disc{width:min(400px,100%);aspect-ratio:1;border-radius:50%;background:linear-gradient(145deg,var(--green-dark),var(--green-light));display:grid;place-items:center;box-shadow:var(--shadow-lg)}
        .hero-art .disc img{width:72%;border-radius:50%}
        .float-card{position:absolute;background:var(--card);border:1px solid var(--line);border-radius:16px;padding:13px 16px;box-shadow:var(--shadow);display:flex;align-items:center;gap:11px;font-size:13px;font-weight:700}
        .float-card i{width:34px;height:34px;border-radius:10px;background:rgba(18,134,78,.1);color:var(--green);display:grid;place-items:center}
        .float-card small{display:block;font-weight:500;color:var(--muted);font-size:11px}
        .fc-1{top:6%;left:-4%}
        .fc-2{bottom:8%;right:-4%}

        /* ---------- sections ---------- */
        section{padding:78px 0}
        .sec-head{text-align:center;max-width:640px;margin:0 auto 44px}
        .sec-head .kicker{color:var(--green);font-size:12px;font-weight:800;letter-spacing:2px;text-transform:uppercase;margin-bottom:10px}
        .sec-head h2{font-size:clamp(28px,3.6vw,40px);margin-bottom:12px}
        .sec-head p{color:var(--muted)}

        /* ---------- menu ---------- */
        .cats{display:flex;gap:9px;justify-content:center;flex-wrap:wrap;margin-bottom:32px}
        .cat{height:38px;padding:0 18px;border-radius:999px;border:1px solid var(--line);background:var(--card);font-size:13px;font-weight:700;color:var(--muted);cursor:pointer;font-family:"DM Sans",sans-serif}
        .cat:hover{border-color:var(--green);color:var(--green)}
        .cat.active{background:linear-gradient(135deg,var(--green),var(--green-light));border-color:transparent;color:#fff}
        .menu-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:20px}
        .drink{background:var(--card);border:1px solid var(--line);border-radius:18px;overflow:hidden;box-shadow:var(--shadow);transition:transform .2s ease,box-shadow .2s ease}
        .drink:hover{transform:translateY(-4px);box-shadow:var(--shadow-lg)}
        .drink-img{aspect-ratio:1;background:linear-gradient(145deg,#eef8f2,#dcf0e5);display:grid;place-items:center;overflow:hidden}
        .drink-img img{width:100%;height:100%;object-fit:cover}
        .drink-img i{font-size:42px;color:rgba(18,134,78,.28)}
        .drink-body{padding:16px}
        .drink-cat{font-size:10px;font-weight:800;letter-spacing:1.2px;text-transform:uppercase;color:var(--gold);margin-bottom:5px}
        .drink h3{font-size:16px;font-family:"DM Sans",sans-serif;font-weight:700;margin-bottom:4px}
        .drink p{font-size:12px;color:var(--muted);margin-bottom:12px;min-height:34px}
        .prices{display:flex;gap:8px}
        .price{flex:1;background:#f4fbf6;border:1px solid var(--line);border-radius:10px;padding:7px 9px;text-align:center}
        .price span{display:block;font-size:9px;color:var(--muted);text-transform:uppercase;letter-spacing:.8px}
        .price b{font-size:14px;color:var(--green)}
        .sold-out{position:absolute}
        .menu-empty{text-align:center;padding:48px;color:var(--muted);background:var(--card);border:1px dashed var(--line);border-radius:18px}

        /* ---------- steps ---------- */
        .steps{display:grid;grid-template-columns:repeat(4,1fr);gap:20px}
        .step{background:var(--card);border:1px solid var(--line);border-radius:18px;padding:26px 22px;box-shadow:var(--shadow);position:relative}
        .step .n{width:38px;height:38px;border-radius:12px;background:linear-gradient(135deg,var(--green),var(--green-light));color:#fff;display:grid;place-items:center;font-weight:800;margin-bottom:14px}
        .step h3{font-size:17px;font-family:"DM Sans",sans-serif;font-weight:700;margin-bottom:6px}
        .step p{font-size:13px;color:var(--muted)}
        .fee-note{margin-top:26px;background:linear-gradient(135deg,rgba(18,134,78,.07),rgba(47,158,98,.04));border:1px solid var(--line);border-radius:16px;padding:20px 24px;display:flex;align-items:center;gap:16px}
        .fee-note i{width:44px;height:44px;border-radius:12px;background:rgba(18,134,78,.12);color:var(--green);display:grid;place-items:center;font-size:18px;flex-shrink:0}
        .fee-note b{display:block;margin-bottom:2px}
        .fee-note span{font-size:13px;color:var(--muted)}

        /* ---------- app ---------- */
        .app{background:linear-gradient(150deg,var(--text),#0d2a1c);color:#fff}
        .app-grid{display:grid;grid-template-columns:1fr 1fr;gap:52px;align-items:center}
        .app h2{font-size:clamp(28px,3.6vw,40px);margin-bottom:14px}
        .app .kicker{color:#5fd694;font-size:12px;font-weight:800;letter-spacing:2px;text-transform:uppercase;margin-bottom:10px}
        .app p{color:#b6d2c1;margin-bottom:26px}
        .app-feats{display:grid;gap:14px;margin-bottom:30px}
        .feat{display:flex;gap:13px;align-items:flex-start}
        .feat i{width:36px;height:36px;border-radius:11px;background:rgba(95,214,148,.14);color:#5fd694;display:grid;place-items:center;flex-shrink:0}
        .feat b{display:block;font-size:14px}
        .feat span{font-size:13px;color:#b6d2c1}
        .app-dl{display:flex;gap:12px;flex-wrap:wrap;align-items:center}
        .btn-app{background:#fff;color:var(--text)}
        .dl-note{font-size:12px;color:#8fb6a0}
        .phone{justify-self:center;width:min(280px,100%);border-radius:34px;border:9px solid #0a1f15;background:#fff;box-shadow:0 30px 70px rgba(0,0,0,.4);overflow:hidden}
        .phone-top{height:26px;background:#0a1f15;display:grid;place-items:center}
        .phone-top span{width:82px;height:5px;border-radius:99px;background:#1d3c2c}
        .phone-body{padding:16px;background:var(--bg);min-height:400px;color:var(--text)}
        .ph-title{font-family:"Playfair Display",serif;font-size:17px;margin-bottom:3px}
        .ph-sub{font-size:10px;color:var(--muted);margin-bottom:13px}
        .ph-row{display:flex;align-items:center;gap:9px;background:#fff;border:1px solid var(--line);border-radius:12px;padding:9px;margin-bottom:8px}
        .ph-thumb{width:34px;height:34px;border-radius:9px;background:linear-gradient(145deg,#dcf0e5,#c6e6d3);flex-shrink:0}
        .ph-row b{font-size:11px;display:block}
        .ph-row small{font-size:9px;color:var(--muted)}
        .ph-chip{margin-left:auto;background:rgba(18,134,78,.1);color:var(--green);font-size:9px;font-weight:800;padding:4px 8px;border-radius:99px}
        .ph-bar{display:flex;justify-content:space-around;border-top:1px solid var(--line);padding-top:11px;margin-top:13px;color:var(--muted);font-size:15px}
        .ph-bar i.on{color:var(--green)}

        /* ---------- branches ---------- */
        .branches{display:grid;grid-template-columns:1fr 1fr;gap:20px}
        .branch{background:var(--card);border:1px solid var(--line);border-radius:18px;padding:26px;box-shadow:var(--shadow)}
        .branch i{width:44px;height:44px;border-radius:12px;background:rgba(18,134,78,.1);color:var(--green);display:grid;place-items:center;margin-bottom:14px;font-size:17px}
        .branch h3{font-size:19px;margin-bottom:5px}
        .branch p{color:var(--muted);font-size:14px}

        /* ---------- footer ---------- */
        footer{background:var(--text);color:#9dbfab;padding:44px 0 30px;font-size:13px}
        .foot-grid{display:flex;justify-content:space-between;gap:26px;flex-wrap:wrap;align-items:center;padding-bottom:24px;border-bottom:1px solid rgba(255,255,255,.09)}
        .foot-brand{display:flex;align-items:center;gap:12px;color:#fff}
        .foot-brand img{width:40px;height:40px;border-radius:50%}
        .foot-links{display:flex;gap:22px;flex-wrap:wrap}
        .foot-links a:hover{color:#fff}
        .foot-bottom{padding-top:20px;display:flex;justify-content:space-between;gap:14px;flex-wrap:wrap;font-size:12px}

        @media(max-width:900px){
            .hero-grid,.app-grid,.branches{grid-template-columns:1fr}
            .steps{grid-template-columns:1fr 1fr}
            .nav-links{display:none}
            .hero-art{order:-1}
            .float-card{display:none}
            section{padding:56px 0}
        }
        @media(max-width:560px){
            .steps{grid-template-columns:1fr}
            .nav-cta .label{display:none}
            .btn{padding:0 15px}
            .foot-grid,.foot-bottom{flex-direction:column;align-items:flex-start}
        }
    </style>
</head>
<body>

<header class="nav">
    <div class="wrap nav-inner">
        <a href="#top" class="brand">
            <img src="{{ asset('icons/queens-cup-logo.png') }}" alt="The Queen's Cup logo">
            <span><b>The Queen's Cup</b><span>Crowned with Flavors</span></span>
        </a>
        <nav class="nav-links">
            <a href="#menu">Menu</a>
            <a href="#how">How it works</a>
            <a href="#app">Get the app</a>
            <a href="#branches">Branches</a>
        </nav>
        <div class="nav-cta">
            <a class="btn btn-ghost btn-sm" href="#app"><i class="fas fa-download"></i> <span class="label">Download app</span></a>
            <a class="btn btn-primary btn-sm" href="{{ url('/orders') }}">Reserve now</a>
        </div>
    </div>
</header>

<main id="top">

    <section class="hero">
        <div class="wrap hero-grid">
            <div>
                <span class="eyebrow"><i class="fas fa-crown"></i> Madridejos, Cebu</span>
                <h1>Skip the wait.<br><em>Reserve your cup</em> before you arrive.</h1>
                <p class="lead">
                    Pick your drinks, choose dine in or take out, and we will have them
                    waiting. You pay at the counter when you pick up — nothing is charged
                    online.
                </p>
                <div class="hero-cta">
                    <a class="btn btn-primary" href="{{ url('/orders') }}"><i class="fas fa-mug-hot"></i> Reserve your drinks</a>
                    <a class="btn btn-ghost" href="#menu"><i class="fas fa-list"></i> See the menu</a>
                </div>
                <div class="hero-points">
                    <span><i class="fas fa-bolt"></i> Ready when you arrive</span>
                    <span><i class="fas fa-bell"></i> We notify you when it is ready</span>
                    <span><i class="fas fa-wallet"></i> Cash, GCash or PayMaya</span>
                </div>
            </div>
            <div class="hero-art">
                <div class="disc"><img src="{{ asset('icons/queens-cup-logo.png') }}" alt="The Queen's Cup"></div>
                <div class="float-card fc-1">
                    <i class="fas fa-check"></i>
                    <span>Reservation ready<small>Order QC-4KP2XM</small></span>
                </div>
                <div class="float-card fc-2">
                    <i class="fas fa-store"></i>
                    <span>2 branches<small>Kota Park &amp; MCC</small></span>
                </div>
            </div>
        </div>
    </section>

    <section id="menu">
        <div class="wrap">
            <div class="sec-head">
                <div class="kicker">Our menu</div>
                <h2>Made fresh, crowned with flavors</h2>
                <p>Every drink comes in 16oz and 22oz. Prices are the same whether you reserve ahead or order at the counter.</p>
            </div>

            @if($products->isEmpty())
                <div class="menu-empty">
                    <i class="fas fa-mug-hot" style="font-size:32px;margin-bottom:12px;display:block"></i>
                    Our menu is being updated. Please check back shortly.
                </div>
            @else
                @if($categories->count() > 1)
                    <div class="cats">
                        <button class="cat active" data-cat="all">All drinks</button>
                        @foreach($categories as $category)
                            <button class="cat" data-cat="{{ $category }}">{{ $category }}</button>
                        @endforeach
                    </div>
                @endif

                <div class="menu-grid" id="menuGrid">
                    @foreach($products as $product)
                        <article class="drink" data-cat="{{ $product->category }}">
                            <div class="drink-img">
                                @if($product->image_path)
                                    <img src="{{ asset('storage/'.ltrim($product->image_path, '/')) }}" alt="{{ $product->name }}" loading="lazy">
                                @else
                                    <i class="fas fa-mug-hot"></i>
                                @endif
                            </div>
                            <div class="drink-body">
                                @if($product->category)
                                    <div class="drink-cat">{{ $product->category }}</div>
                                @endif
                                <h3>{{ $product->name }}</h3>
                                <p>{{ $product->description ?: 'A Queen\'s Cup favourite, made to order.' }}</p>
                                <div class="prices">
                                    <div class="price"><span>16oz</span><b>₱{{ number_format($product->regular_price, 0) }}</b></div>
                                    <div class="price"><span>22oz</span><b>₱{{ number_format($product->large_price, 0) }}</b></div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section id="how" style="background:#fff;border-block:1px solid var(--line)">
        <div class="wrap">
            <div class="sec-head">
                <div class="kicker">How it works</div>
                <h2>Reserved in four steps</h2>
                <p>Reserving holds your drinks. Payment happens in person, at the counter.</p>
            </div>

            <div class="steps">
                <div class="step">
                    <div class="n">1</div>
                    <h3>Pick your drinks</h3>
                    <p>Browse the menu and choose 16oz or 22oz for each one.</p>
                </div>
                <div class="step">
                    <div class="n">2</div>
                    <h3>Dine in or take out</h3>
                    <p>Tell us how you will have it. Take out is packed and sealed for the trip.</p>
                </div>
                <div class="step">
                    <div class="n">3</div>
                    <h3>Get your code</h3>
                    <p>You receive a reference like QC-4KP2XM. Track your reservation with it any time.</p>
                </div>
                <div class="step">
                    <div class="n">4</div>
                    <h3>Pick up and pay</h3>
                    <p>We tell you the moment it is ready. Pay with cash, GCash or PayMaya at the counter.</p>
                </div>
            </div>

            <div class="fee-note">
                <i class="fas fa-cubes-stacked"></i>
                <div>
                    <b>Take out adds ₱{{ number_format($takeoutFee, 0) }} per cup</b>
                    <span>That covers the cup and sealed lid. Dine in is never charged this — your total is always shown before you confirm.</span>
                </div>
            </div>
        </div>
    </section>

    <section id="app" class="app">
        <div class="wrap app-grid">
            <div>
                <div class="kicker">Android app</div>
                <h2>Reserve from your phone</h2>
                <p>
                    The Queen's Cup app keeps your reservations in one place and tells you
                    the moment your drinks are ready — even when the app is closed.
                </p>

                <div class="app-feats">
                    <div class="feat">
                        <i class="fas fa-bell"></i>
                        <span><b>Ready alerts</b><span>A notification the second the counter finishes your order.</span></span>
                    </div>
                    <div class="feat">
                        <i class="fas fa-location-arrow"></i>
                        <span><b>Live tracking</b><span>Watch it move from reserved, to preparing, to ready.</span></span>
                    </div>
                    <div class="feat">
                        <i class="fas fa-clock-rotate-left"></i>
                        <span><b>Your usual, faster</b><span>Past reservations are kept so reordering takes seconds.</span></span>
                    </div>
                </div>

                <div class="app-dl">
                    @if($apkReady)
                        <a class="btn btn-app" href="{{ asset('downloads/queens-cup.apk') }}" download>
                            <i class="fab fa-android" style="font-size:18px"></i> Download for Android
                        </a>
                        <span class="dl-note">APK · {{ $apkSize }} MB · Android 7.0 and up</span>
                    @else
                        <button class="btn btn-app" disabled>
                            <i class="fab fa-android" style="font-size:18px"></i> Download for Android
                        </button>
                        <span class="dl-note">Coming soon — reserve on the web in the meantime.</span>
                    @endif
                </div>
            </div>

            <div class="phone" aria-hidden="true">
                <div class="phone-top"><span></span></div>
                <div class="phone-body">
                    <div class="ph-title">Queen's Cup</div>
                    <div class="ph-sub">Reserve your drinks and pick them up.</div>

                    <div class="ph-row"><div class="ph-thumb"></div><span><b>Wintermelon Milktea</b><small>16oz · ₱79</small></span><span class="ph-chip">Add</span></div>
                    <div class="ph-row"><div class="ph-thumb"></div><span><b>Brown Sugar Milktea</b><small>22oz · ₱105</small></span><span class="ph-chip">Add</span></div>
                    <div class="ph-row"><div class="ph-thumb"></div><span><b>Mulberry Lime</b><small>16oz · ₱85</small></span><span class="ph-chip">Add</span></div>

                    <div class="ph-row" style="background:rgba(18,134,78,.08);border-color:rgba(18,134,78,.3)">
                        <span><b>QC-4KP2XM</b><small>Ready for pick up</small></span>
                        <span class="ph-chip">Ready</span>
                    </div>

                    <div class="ph-bar">
                        <i class="fas fa-mug-hot on"></i>
                        <i class="fas fa-bag-shopping"></i>
                        <i class="fas fa-receipt"></i>
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="branches">
        <div class="wrap">
            <div class="sec-head">
                <div class="kicker">Find us</div>
                <h2>Two branches in Madridejos</h2>
                <p>Reserve at either one and pick up whenever you are ready.</p>
            </div>
            <div class="branches">
                <div class="branch">
                    <i class="fas fa-umbrella-beach"></i>
                    <h3>Kota Park</h3>
                    <p>Beside the famous boardwalk, Madridejos, Cebu.</p>
                </div>
                <div class="branch">
                    <i class="fas fa-graduation-cap"></i>
                    <h3>MCC</h3>
                    <p>Inside Madridejos Community College, Madridejos, Cebu.</p>
                </div>
            </div>
        </div>
    </section>

</main>

<footer>
    <div class="wrap">
        <div class="foot-grid">
            <div class="foot-brand">
                <img src="{{ asset('icons/queens-cup-logo.png') }}" alt="">
                <span><b style="font-family:'Playfair Display',serif">The Queen's Cup</b><br><small>Crowned with Flavors</small></span>
            </div>
            <div class="foot-links">
                <a href="#menu">Menu</a>
                <a href="#how">How it works</a>
                <a href="#app">Get the app</a>
                <a href="#branches">Branches</a>
                <a href="{{ url('/orders') }}">Reserve</a>
            </div>
        </div>
        <div class="foot-bottom">
            <span>&copy; {{ date('Y') }} The Queen's Cup, Madridejos, Cebu.</span>
            <a href="{{ url('/staff-login') }}">Staff sign in</a>
        </div>
    </div>
</footer>

<script>
    // Category filter. Plain DOM work; the menu is already server rendered so
    // the page is complete and indexable before this runs.
    document.querySelectorAll('.cat').forEach(function (button) {
        button.addEventListener('click', function () {
            var wanted = button.dataset.cat;

            document.querySelectorAll('.cat').forEach(function (other) { other.classList.remove('active'); });
            button.classList.add('active');

            document.querySelectorAll('.drink').forEach(function (card) {
                card.style.display = (wanted === 'all' || card.dataset.cat === wanted) ? '' : 'none';
            });
        });
    });
</script>

<!-- The assistant. Open to visitors, but only a signed-in customer gets a
     stored conversation, so it carries a CSRF token to post with. -->
<script src="{{ asset('js/queens-chat.js') }}"
        data-queens-chat
        data-base="{{ url('/') }}"
        data-csrf="{{ csrf_token() }}"
        data-logo="{{ asset('icons/queens-cup-logo.png') }}"></script>
</body>
</html>
