<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#12864e">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="Queen's Cup">
<link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
<link rel="icon" href="{{ asset('icons/queens-cup-logo.png') }}" type="image/png">
<link rel="apple-touch-icon" href="{{ asset('icons/queens-cup-logo.png') }}">
<title>The Queen's Cup — Madridejos, Cebu</title>
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
  --wine:#7B2D3B;--wine-deep:#5A1F2B;--wine-light:#9E3A4E;
  --success:#2CB67D;--danger:#E53170;--info:#5B8DEF;--warning:#F5A623;
  --radius:14px;--radius-sm:10px;
  --shadow:0 8px 32px rgba(18,53,36,0.12);
  --shadow-accent:0 4px 24px var(--accent-glow);
  --shadow-gold:0 4px 24px var(--gold-glow);
  --transition:0.3s cubic-bezier(0.4,0,0.2,1);
  --glass:rgba(255,255,255,0.86);--glass-border:rgba(207,231,215,0.9);
}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--fg);overflow:hidden;height:100vh}
h1,h2,h3,h4{font-family:'Playfair Display',serif}
::-webkit-scrollbar{width:5px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:var(--accent-dark);border-radius:10px}
::-webkit-scrollbar-thumb:hover{background:var(--accent)}

.login-page{position:fixed;inset:0;z-index:5000;display:flex;align-items:center;justify-content:center;background:var(--bg-deep);overflow:hidden}
.login-page.hidden{display:none}
.login-bg-orb{position:absolute;border-radius:50%;filter:blur(140px);pointer-events:none}
.login-bg-orb.a{width:650px;height:650px;background:var(--accent);top:-250px;right:-180px;opacity:0.1;animation:orbFloat 8s ease-in-out infinite}
.login-bg-orb.b{width:550px;height:550px;background:var(--gold);bottom:-250px;left:-180px;opacity:0.06;animation:orbFloat 10s ease-in-out infinite reverse}
.login-bg-orb.c{width:300px;height:300px;background:var(--accent-light);bottom:15%;right:25%;opacity:0.04;animation:orbFloat 12s ease-in-out infinite}
@keyframes orbFloat{0%,100%{transform:translate(0,0)}50%{transform:translate(30px,-20px)}}
.login-card{width:440px;max-width:92vw;position:relative;z-index:2;background:var(--glass);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);border:1px solid var(--glass-border);border-radius:20px;box-shadow:0 32px 64px rgba(18,53,36,0.16),inset 0 1px 0 rgba(255,255,255,0.04);overflow:hidden;animation:loginCardIn 0.7s cubic-bezier(0.16,1,0.3,1)}
@keyframes loginCardIn{from{opacity:0;transform:translateY(40px) scale(0.94)}to{opacity:1;transform:translateY(0) scale(1)}}
.login-header{padding:36px 36px 0;text-align:center}
.login-crown{width:72px;height:72px;border-radius:50%;margin:0 auto 18px;display:flex;align-items:center;justify-content:center;box-shadow:0 6px 30px var(--accent-glow-strong),0 0 60px var(--accent-glow);overflow:hidden;border:2px solid rgba(14,140,74,0.3);position:relative;background:linear-gradient(135deg,var(--accent-dark),var(--accent))}
.login-crown img{width:100%;height:100%;object-fit:cover}
.login-crown::after{content:'';position:absolute;inset:-2px;border-radius:50%;background:conic-gradient(from 0deg,var(--accent),var(--gold),var(--accent));z-index:-1;animation:crownSpin 6s linear infinite;opacity:0.5}
@keyframes crownSpin{to{transform:rotate(360deg)}}
.login-header h2{font-size:24px;margin-bottom:4px;background:linear-gradient(135deg,var(--accent-light),var(--gold-light));-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.login-header p{font-size:12px;color:var(--fg-muted);letter-spacing:1.5px;text-transform:uppercase}
.login-body{padding:28px 36px 36px}
.link-inline{background:none;border:0;color:var(--accent-light);font-weight:800;cursor:pointer;font-family:'DM Sans';font-size:11px;text-decoration:underline}
.pw-toggle{position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:0;color:var(--fg-muted);cursor:pointer;font-size:12px}
.input-icon{position:relative}
.login-tabs{display:flex;gap:0;margin-bottom:24px;background:rgba(255,255,255,0.92);border-radius:var(--radius-sm);padding:3px;border:1px solid var(--border)}
.login-tab{flex:1;padding:10px;text-align:center;border-radius:8px;cursor:pointer;font-size:12px;font-weight:600;transition:all var(--transition);color:var(--fg-muted);user-select:none;border:none;background:none;font-family:'DM Sans'}
.login-tab.active{background:linear-gradient(135deg,var(--accent),var(--accent-dark));color:#fff;box-shadow:0 2px 12px var(--accent-glow)}
.login-tab:hover:not(.active){color:var(--fg)}
.login-form{display:none}
.login-form.active{display:block}
.login-field{margin-bottom:16px}
.login-field label{display:block;font-size:11px;font-weight:600;color:var(--fg-muted);margin-bottom:6px;text-transform:uppercase;letter-spacing:0.8px}
.login-field input,.login-field select{width:100%;padding:12px 14px;background:rgba(255,255,255,0.92);border:1px solid var(--border);border-radius:var(--radius-sm);color:var(--fg);font-size:13px;font-family:'DM Sans';transition:all var(--transition);outline:none}
.login-field input:focus,.login-field select:focus{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-glow)}
.login-field .input-icon{position:relative}
.login-field .input-icon i{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:var(--fg-muted);font-size:13px;pointer-events:none}
.login-field .input-icon input{padding-left:40px}
.login-field .toggle-pw{position:absolute;right:12px;top:50%;transform:translateY(-50%);color:var(--fg-muted);cursor:pointer;font-size:13px;background:none;border:none;transition:color var(--transition)}
.login-field .toggle-pw:hover{color:var(--accent)}
.login-btn{width:100%;padding:13px;border:none;border-radius:var(--radius-sm);background:linear-gradient(135deg,var(--accent),var(--accent-dark));color:#fff;font-size:14px;font-weight:700;cursor:pointer;font-family:'DM Sans';transition:all var(--transition);margin-top:6px;position:relative;overflow:hidden}
.login-btn::before{content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.1),transparent);transition:0.5s}
.login-btn:hover::before{left:100%}
.login-btn:hover{box-shadow:0 6px 28px var(--accent-glow-strong);transform:translateY(-2px)}
.login-btn.wine-btn{background:linear-gradient(135deg,var(--accent-light),var(--accent));color:#fff}
.login-btn.wine-btn:hover{box-shadow:0 6px 28px var(--accent-glow-strong)}
.login-btn.gold-btn{background:linear-gradient(135deg,var(--gold),var(--gold-dark));color:var(--bg-deep)}
.login-btn.gold-btn:hover{box-shadow:0 6px 28px var(--gold-glow-strong)}
.login-home{text-align:center;margin-top:18px;padding-top:16px;border-top:1px solid var(--border)}
.login-home a{font-size:12px;font-weight:600;color:var(--fg-muted);text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:color .2s ease}
.login-home a:hover{color:var(--accent-light)}
.login-error{background:rgba(229,49,112,0.1);border:1px solid rgba(229,49,112,0.3);border-radius:var(--radius-sm);padding:10px 14px;font-size:12px;color:var(--danger);margin-bottom:14px;display:none}
.login-error.show{display:block;animation:shake 0.4s ease}
@keyframes shake{0%,100%{transform:translateX(0)}25%{transform:translateX(-6px)}75%{transform:translateX(6px)}}
.login-demo{margin-top:20px;padding-top:16px;border-top:1px solid var(--border);font-size:11px;color:var(--fg-muted);text-align:center;line-height:1.8}
.login-demo code{background:rgba(255,255,255,0.92);padding:2px 7px;border-radius:5px;font-size:10px;color:var(--gold);border:1px solid var(--border)}
.guest-welcome{text-align:center;margin-bottom:20px}
.guest-welcome i{font-size:40px;color:var(--accent);margin-bottom:10px;display:block;opacity:0.7}
.guest-welcome p{font-size:13px;color:var(--fg-muted);line-height:1.6}

.app-layout{display:flex;height:100vh}
.sidebar{width:260px;background:var(--bg-deep);border-right:1px solid var(--border);display:flex;flex-direction:column;flex-shrink:0;position:relative;z-index:10}
.sidebar-brand{padding:22px 18px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:13px}
.sidebar-brand .crown-icon{width:46px;height:46px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;color:#fff;box-shadow:0 4px 20px var(--accent-glow);overflow:hidden;flex-shrink:0;border:2px solid rgba(14,140,74,0.3);background:linear-gradient(135deg,var(--accent-dark),var(--accent))}
.sidebar-brand .crown-icon img{width:100%;height:100%;object-fit:cover}
.sidebar-brand h2{font-size:14px;font-weight:700;background:linear-gradient(135deg,var(--accent-light),var(--gold-light));-webkit-background-clip:text;-webkit-text-fill-color:transparent;line-height:1.2}
.sidebar-brand span{font-size:10px;color:var(--fg-muted);font-family:'DM Sans';font-weight:400;letter-spacing:0.5px}
.sidebar-nav{flex:1;padding:14px 10px;overflow-y:auto}
.nav-section{margin-bottom:6px}
.nav-section-title{padding:12px 10px 7px;color:var(--fg-muted);font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;font-family:'DM Sans'}
.nav-item{display:flex;align-items:center;gap:10px;min-height:40px;padding:0 12px;border-radius:10px;cursor:pointer;transition:background .2s ease,color .2s ease;color:var(--fg-muted);font-size:13px;font-weight:600;position:relative;user-select:none;text-decoration:none}
.nav-item:hover,.nav-item.active{background:rgba(14,140,74,0.13);color:var(--accent-light)}
.nav-item i{width:20px;text-align:center;font-size:14px}
.nav-badge{margin-left:auto;background:var(--danger);color:#fff;font-size:10px;padding:2px 7px;border-radius:20px;font-weight:700}
.nav-badge.cash-pending{background:var(--warning);color:var(--bg-deep)}
.sidebar-footer{padding:14px 18px;border-top:1px solid var(--border);display:flex;align-items:center;gap:12px}
.sidebar-footer .avatar{width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,var(--accent),var(--gold));display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:#fff}
.sidebar-footer .user-info{flex:1}
.sidebar-footer .user-info .name{font-size:12px;font-weight:600}
.sidebar-footer .user-info .role{font-size:10px;color:var(--fg-muted)}
.sidebar-footer .logout-btn{width:30px;height:30px;border-radius:8px;border:1px solid var(--border);background:var(--card);color:var(--fg-muted);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:12px;transition:all var(--transition)}
.sidebar-footer .logout-btn:hover{border-color:var(--danger);color:var(--danger)}

.main-content{flex:1;display:flex;flex-direction:column;overflow:hidden;position:relative}
.topbar{height:60px;border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 24px;gap:14px;background:var(--bg-deep);flex-shrink:0}
.topbar-title{font-family:'Playfair Display';font-size:18px;font-weight:700}
.topbar-breadcrumb{font-size:11px;color:var(--fg-muted);margin-left:4px}
.topbar-right{margin-left:auto;display:flex;align-items:center;gap:10px;position:relative}
.topbar-btn{width:36px;height:36px;border-radius:10px;border:1px solid var(--border);background:var(--card);color:var(--fg-muted);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all var(--transition);font-size:13px;position:relative}
.topbar-btn:hover{border-color:var(--accent);color:var(--accent);box-shadow:0 0 12px var(--accent-glow)}
.topbar-btn .notif-count{position:absolute;top:-4px;right:-4px;min-width:16px;height:16px;background:var(--danger);color:#fff;font-size:9px;font-weight:700;border-radius:10px;display:flex;align-items:center;justify-content:center;padding:0 4px;border:2px solid var(--bg-deep)}
.topbar-btn .notif-count:empty{display:none}
.customer-install-btn{height:36px;padding:0 12px;border-radius:10px;border:1px solid rgba(18,134,78,0.24);background:linear-gradient(135deg,var(--accent),var(--accent-dark));color:#fff;display:none;align-items:center;gap:7px;font-size:12px;font-weight:800;font-family:'DM Sans';cursor:pointer;transition:all var(--transition);white-space:nowrap}
.customer-install-btn:hover{box-shadow:0 0 14px var(--accent-glow-strong);transform:translateY(-1px)}
.customer-install-btn.is-installed{background:var(--card);color:var(--success);border-color:rgba(44,182,125,0.35);cursor:default}
.customer-chat-btn{display:none}
.header-logout-btn{height:36px;padding:0 12px;border-radius:10px;border:1px solid var(--border);background:var(--card);color:var(--fg-muted);display:inline-flex;align-items:center;gap:7px;font-size:12px;font-weight:700;font-family:'DM Sans';cursor:pointer;transition:all var(--transition);white-space:nowrap}
.header-logout-btn:hover{border-color:var(--danger);color:var(--danger);box-shadow:0 0 12px rgba(229,49,112,0.2)}
.customer-signout-btn{display:none}
.customer-mobile .customer-signout-btn{display:inline-flex}

.cash-pending-indicator{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:var(--radius-sm);background:rgba(245,166,35,0.1);border:1px solid rgba(245,166,35,0.3);color:var(--warning);font-size:11px;font-weight:600;cursor:pointer;transition:all var(--transition);white-space:nowrap}
.cash-pending-indicator:hover{background:rgba(245,166,35,0.18);border-color:var(--warning)}
.cash-pending-indicator i{font-size:12px}
.branch-select{background:var(--card);border:1px solid var(--border);color:var(--fg);padding:7px 12px;border-radius:var(--radius-sm);font-size:12px;font-family:'DM Sans';cursor:pointer;outline:none;max-width:260px;transition:all var(--transition)}
.branch-select:focus{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-glow)}
.page-content{flex:1;overflow-y:auto;padding:24px}
/* Decorative texture for the customer-facing menu only. Staff share the flat
   shell background every other admin page uses, so the framing does not change
   when moving between Orders and the rest of the panel. */
body.customer-mobile .page-content{
  background:
    linear-gradient(135deg,rgba(18,134,78,.035) 25%,transparent 25%) 0 0/28px 28px,
    linear-gradient(315deg,rgba(47,158,98,.03) 25%,transparent 25%) 0 0/28px 28px,
    linear-gradient(180deg,rgba(255,255,255,.82),rgba(247,251,248,.95));
}

.notif-wrapper{position:relative}
.notification-panel{position:absolute;top:48px;right:0;width:380px;max-height:480px;background:var(--glass);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid var(--glass-border);border-radius:var(--radius);box-shadow:0 20px 60px rgba(18,53,36,0.16);overflow:hidden;z-index:200;transform:scale(0.95) translateY(-8px);opacity:0;pointer-events:none;transition:all 0.25s cubic-bezier(0.16,1,0.3,1);transform-origin:top right}
.notification-panel.open{transform:scale(1) translateY(0);opacity:1;pointer-events:all}
.notif-panel-header{padding:14px 18px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.notif-panel-header h4{font-size:14px;font-weight:700}
.notif-mark-all{font-size:10px;color:var(--accent);cursor:pointer;background:none;border:none;font-family:'DM Sans';font-weight:600;transition:color var(--transition)}
.notif-mark-all:hover{color:var(--fg)}
.notif-list{max-height:400px;overflow-y:auto}
.notif-item{display:flex;align-items:flex-start;gap:12px;padding:14px 18px;border-bottom:1px solid var(--border);cursor:pointer;transition:background var(--transition);position:relative}
.notif-item:hover{background:rgba(14,140,74,0.05)}
.notif-item.unread{background:rgba(14,140,74,0.04)}
.notif-item.unread::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;background:linear-gradient(180deg,var(--accent),var(--gold));border-radius:0 3px 3px 0}
.notif-icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0}
.notif-icon.cash-pending{background:rgba(245,166,35,0.15);color:var(--warning)}
.notif-icon.new-order{background:rgba(91,141,239,0.15);color:var(--info)}
.notif-icon.low-stock{background:rgba(229,49,112,0.15);color:var(--danger)}
.notif-icon.out-of-stock{background:rgba(229,49,112,0.2);color:var(--danger)}
.notif-icon.completed{background:rgba(44,182,125,0.15);color:var(--success)}
.notif-icon.preparing{background:rgba(91,141,239,0.15);color:var(--info)}
.notif-body{flex:1;min-width:0}
.notif-body .notif-msg{font-size:12px;line-height:1.5;color:var(--fg)}
.notif-body .notif-msg strong{font-weight:600}
.notif-body .notif-time{font-size:10px;color:var(--fg-muted);margin-top:3px}
.notif-empty{padding:40px 20px;text-align:center;color:var(--fg-muted)}
.notif-empty i{font-size:32px;opacity:0.3;margin-bottom:8px;display:block}
.notif-empty p{font-size:12px}
.notif-progress{margin-top:10px}
.notif-progress .customer-progress{min-width:0}
.customer-mobile-nav{display:none}

.card{background:var(--glass);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border:1px solid var(--glass-border);border-radius:var(--radius);overflow:hidden;transition:all var(--transition)}
.card:hover{border-color:rgba(14,140,74,0.35);box-shadow:0 4px 20px rgba(0,0,0,0.3)}
.card-header{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.card-header h3{font-size:15px;font-weight:700}
.card-body{padding:20px}
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px}
.stat-card{background:var(--glass);backdrop-filter:blur(12px);border:1px solid var(--glass-border);border-radius:var(--radius);padding:20px;position:relative;overflow:hidden;transition:all var(--transition)}
.stat-card:hover{transform:translateY(-3px);box-shadow:0 12px 36px rgba(0,0,0,0.4);border-color:rgba(14,140,74,0.3)}
.stat-card::after{content:'';position:absolute;top:0;right:0;width:80px;height:80px;border-radius:50%;filter:blur(40px);opacity:0.08;pointer-events:none}
.stat-card:nth-child(1)::after{background:var(--gold)}
.stat-card:nth-child(2)::after{background:var(--accent)}
.stat-card:nth-child(3)::after{background:var(--warning)}
.stat-card:nth-child(4)::after{background:var(--info)}
.stat-card .stat-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:17px;margin-bottom:14px}
.stat-card .stat-value{font-size:26px;font-weight:700;font-family:'Playfair Display'}
.stat-card .stat-label{font-size:10px;color:var(--fg-muted);margin-top:4px;text-transform:uppercase;letter-spacing:1.5px}
.stat-card .stat-change{font-size:11px;margin-top:6px;font-weight:600}
.stat-card .stat-change.up{color:var(--success)}
.stat-card .stat-change.down{color:var(--danger)}

.btn{padding:8px 16px;border-radius:var(--radius-sm);font-size:12px;font-weight:600;cursor:pointer;transition:all var(--transition);border:none;font-family:'DM Sans';display:inline-flex;align-items:center;gap:7px;user-select:none;position:relative;overflow:hidden}
.btn-primary{background:linear-gradient(135deg,var(--accent),var(--accent-dark));color:#fff}
.btn-primary:hover{box-shadow:0 4px 20px var(--accent-glow-strong);transform:translateY(-1px)}
.btn-secondary{background:var(--card);border:1px solid var(--border);color:var(--fg)}
.btn-secondary:hover{border-color:var(--accent);color:var(--accent)}
.btn-danger{background:var(--danger);color:#fff}
.btn-success{background:var(--success);color:#fff}
.btn-warning{background:var(--warning);color:var(--bg-deep)}
.btn-gold{background:linear-gradient(135deg,var(--gold),var(--gold-dark));color:var(--bg-deep)}
.btn-gold:hover{box-shadow:0 4px 20px var(--gold-glow-strong);transform:translateY(-1px)}
.btn-transparent-info{background:transparent;color:var(--info);border:1px solid transparent}
.btn-transparent-info:hover{border-color:rgba(91,141,239,0.35);box-shadow:none;transform:none}
.btn-sm{padding:5px 10px;font-size:11px}
/* The order filter buttons had no selected state at all, so the active filter
   was indistinguishable from the rest. Mirrors .pos-cat-btn.active. */
.order-filter.active{background:linear-gradient(135deg,rgba(14,140,74,0.15),rgba(201,168,76,0.08));border-color:var(--accent);color:var(--accent);box-shadow:0 0 12px var(--accent-glow);font-weight:700}
.btn-icon{width:32px;height:32px;padding:0;justify-content:center}

.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse}
th{text-align:left;padding:11px 14px;font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--fg-muted);font-weight:600;border-bottom:1px solid var(--border);font-family:'DM Sans'}
td{padding:12px 14px;font-size:12px;border-bottom:1px solid var(--border);vertical-align:middle}
tr:hover td{background:rgba(14,140,74,0.03)}
tr:last-child td{border-bottom:none}

.badge{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:600}
.badge-success{background:rgba(44,182,125,0.15);color:var(--success)}
.badge-warning{background:rgba(245,166,35,0.15);color:var(--warning)}
.badge-danger{background:rgba(229,49,112,0.15);color:var(--danger)}
.badge-info{background:rgba(91,141,239,0.15);color:var(--info)}
.badge-wine{background:rgba(123,45,59,0.2);color:var(--wine-light)}
.badge-accent{background:rgba(14,140,74,0.15);color:var(--accent-light)}
.badge-cashier{background:rgba(245,166,35,0.15);color:var(--warning)}
.badge-gold{background:linear-gradient(135deg,rgba(201,168,76,0.25),rgba(224,200,120,0.12));color:var(--gold-light);border:1px solid rgba(201,168,76,0.3)}
.badge-bestseller{background:linear-gradient(135deg,rgba(201,168,76,0.3),rgba(224,200,120,0.15));color:var(--gold-light);border:1px solid rgba(201,168,76,0.35)}
.badge-guest{background:rgba(91,141,239,0.15);color:var(--info)}
.badge-cash-pending{background:rgba(245,166,35,0.15);color:var(--warning);border:1px solid rgba(245,166,35,0.3);animation:cashPulse 2s infinite}
.badge-paid{background:rgba(44,182,125,0.15);color:var(--success)}
.badge-qr-pending{background:rgba(91,141,239,0.15);color:var(--info);border:1px solid rgba(91,141,239,0.3);animation:cashPulse 2s infinite}
@keyframes cashPulse{0%,100%{box-shadow:0 0 0 0 rgba(245,166,35,0.3)}50%{box-shadow:0 0 0 4px rgba(245,166,35,0)}}

.form-group{margin-bottom:16px}
.form-group label{display:block;font-size:11px;font-weight:600;color:var(--fg-muted);margin-bottom:6px;text-transform:uppercase;letter-spacing:0.5px}
.form-input,.form-select,.form-textarea{width:100%;padding:10px 13px;background:rgba(255,255,255,0.92);border:1px solid var(--border);border-radius:var(--radius-sm);color:var(--fg);font-size:12px;font-family:'DM Sans';transition:all var(--transition);outline:none}
.form-input:focus,.form-select:focus,.form-textarea:focus{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-glow)}
.form-textarea{resize:vertical;min-height:70px}

.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,0.75);backdrop-filter:blur(6px);z-index:1000;display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:all 0.3s}
.modal-overlay.active{opacity:1;pointer-events:all}
.modal{background:var(--card);border:1px solid var(--glass-border);border-radius:var(--radius);width:90%;max-width:520px;max-height:90vh;overflow-y:auto;transform:scale(0.9) translateY(20px);transition:all 0.3s;box-shadow:0 24px 64px rgba(18,53,36,0.16)}
.modal-overlay.active .modal{transform:scale(1) translateY(0)}
.modal-header{padding:18px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.modal-header h3{font-size:17px}
.modal-close{width:30px;height:30px;border-radius:8px;border:none;background:var(--bg);color:var(--fg-muted);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all var(--transition)}
.modal-close:hover{color:var(--danger)}
.modal-body{padding:22px}
.modal-footer{padding:14px 22px;border-top:1px solid var(--border);display:flex;gap:8px;justify-content:flex-end}

.pos-layout{display:grid;grid-template-columns:1fr 360px;gap:20px;height:calc(100vh - 110px);min-height:0}
.pos-products{overflow-y:scroll;min-height:0;padding-right:10px;scrollbar-gutter:stable;scrollbar-width:thin;scrollbar-color:var(--accent-dark) rgba(13,26,19,0.55)}
.pos-products::-webkit-scrollbar{width:8px}
.pos-products::-webkit-scrollbar-track{background:rgba(13,26,19,0.55);border-radius:10px}
.pos-products::-webkit-scrollbar-thumb{background:var(--accent-dark);border-radius:10px;border:2px solid rgba(13,26,19,0.55)}
.pos-products::-webkit-scrollbar-thumb:hover{background:var(--accent)}
.pos-categories{display:flex;gap:7px;margin-bottom:16px;flex-wrap:wrap}
.pos-cat-btn{padding:7px 14px;border-radius:20px;border:1px solid var(--border);background:var(--card);color:var(--fg-muted);cursor:pointer;font-size:11px;font-weight:600;transition:all var(--transition);font-family:'DM Sans'}
.pos-cat-btn:hover,.pos-cat-btn.active{background:linear-gradient(135deg,rgba(14,140,74,0.15),rgba(201,168,76,0.08));border-color:var(--accent);color:var(--accent-light);box-shadow:0 0 12px var(--accent-glow)}
.pos-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}
.pos-item{background:var(--glass);backdrop-filter:blur(8px);border:1px solid var(--glass-border);border-radius:var(--radius);padding:16px;cursor:pointer;transition:all var(--transition);text-align:center;position:relative}
.pos-item:hover{border-color:var(--accent);transform:translateY(-3px);box-shadow:0 8px 24px rgba(0,0,0,0.3),0 0 20px var(--accent-glow)}
.pos-item.out-of-stock{opacity:0.35;pointer-events:none}
.pos-item-img{width:64px;height:64px;border-radius:14px;margin:0 auto 10px;display:flex;align-items:center;justify-content:center;font-size:28px}
.pos-item-img img{width:100%;height:100%;object-fit:cover;border-radius:inherit;display:block}
.item-thumb-sm{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;object-fit:cover;border:1px solid var(--border);background:rgba(255,255,255,0.92);color:var(--fg-muted);font-size:20px;flex-shrink:0;overflow:hidden}
.item-thumb-sm img{width:100%;height:100%;object-fit:cover;display:block}
.item-line{display:flex;align-items:center;gap:10px;min-width:180px}
.item-upload-row{display:flex;align-items:center;gap:12px;padding:10px;border:1px solid var(--border);border-radius:var(--radius-sm);background:rgba(7,15,11,0.5)}
.item-upload-row .form-input{flex:1}
.pos-item-name{font-size:12px;font-weight:600;margin-bottom:3px;line-height:1.3}
.pos-item-price{font-size:14px;font-weight:700;color:var(--gold-light)}
.pos-item-stock{font-size:10px;color:var(--fg-muted);margin-top:3px}
.pos-item-sizes{display:flex;gap:3px;justify-content:center;margin-top:5px}
.pos-size-tag{font-size:8px;padding:2px 6px;border-radius:6px;border:1px solid var(--border);color:var(--fg-muted)}
.pos-item-bestseller{position:absolute;top:8px;right:8px;font-size:7px;padding:2px 7px;border-radius:8px;background:linear-gradient(135deg,var(--gold),var(--gold-dark));color:var(--bg-deep);font-weight:800;text-transform:uppercase;letter-spacing:0.5px}
.pos-subcat{grid-column:1/-1;font-size:11px;font-weight:700;color:var(--fg-muted);text-transform:uppercase;letter-spacing:1.5px;padding:8px 0 4px;border-bottom:1px solid var(--border);margin-bottom:4px}

.pos-cart{background:var(--glass);backdrop-filter:blur(12px);border:1px solid var(--glass-border);border-radius:var(--radius);display:flex;flex-direction:column;height:100%}
.cart-header{padding:16px 18px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.cart-items{flex:1;overflow-y:auto;padding:10px}
.cart-item{display:flex;align-items:center;gap:10px;padding:9px;border-radius:var(--radius-sm);margin-bottom:6px;background:rgba(7,15,11,0.6);border:1px solid var(--border);transition:all var(--transition)}
.cart-item:hover{border-color:rgba(14,140,74,0.3)}
.cart-item-info{flex:1}
.cart-item-name{font-size:12px;font-weight:600;line-height:1.3}
.cart-item-size{font-size:10px;color:var(--fg-muted)}
.cart-item-qty{display:flex;align-items:center;gap:5px}
.cart-qty-btn{width:24px;height:24px;border-radius:6px;border:1px solid var(--border);background:var(--card);color:var(--fg);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:11px;transition:all var(--transition)}
.cart-qty-btn:hover{border-color:var(--accent);color:var(--accent)}
.cart-qty-val{font-size:12px;font-weight:700;min-width:18px;text-align:center}
.cart-item-remove{color:var(--fg-muted);cursor:pointer;font-size:11px;transition:color var(--transition);background:none;border:none}
.cart-item-remove:hover{color:var(--danger)}
.cart-summary{padding:14px 18px;border-top:1px solid var(--border)}
.cart-summary-row{display:flex;justify-content:space-between;font-size:12px;margin-bottom:6px}
.cart-summary-row.total{font-size:17px;font-weight:700;color:var(--gold-light);margin-top:10px;padding-top:10px;border-top:1px solid var(--border)}
.cart-actions{padding:14px 18px;display:flex;gap:8px}
.cart-actions .btn{flex:1;justify-content:center}

.chatbot-container{position:absolute;top:68px;right:14px;z-index:90;width:min(340px,calc(100% - 28px));pointer-events:none}
.chatbot-container .chatbot-toggle{display:none}
.chatbot-toggle{width:54px;height:54px;border-radius:16px;border:none;background:linear-gradient(135deg,var(--accent),var(--gold));color:#fff;font-size:20px;cursor:pointer;box-shadow:0 6px 28px var(--accent-glow-strong);transition:all var(--transition);display:flex;align-items:center;justify-content:center}
.chatbot-toggle:hover{transform:scale(1.08);box-shadow:0 8px 36px var(--accent-glow-strong)}
.chatbot-window{position:absolute;top:0;right:0;width:100%;height:360px;background:var(--glass);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid var(--glass-border);border-radius:var(--radius);overflow:hidden;box-shadow:0 20px 60px rgba(18,53,36,0.16);display:flex;flex-direction:column;transform:scale(0.8) translateY(-10px);opacity:0;pointer-events:none;transition:all 0.3s;transform-origin:top right}
.chatbot-window.open{transform:scale(1) translateY(0);opacity:1;pointer-events:all}
.chat-header{padding:14px 18px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px;background:linear-gradient(135deg,rgba(14,140,74,0.1),rgba(201,168,76,0.05))}
.chat-close{margin-left:auto;width:28px;height:28px;border:1px solid var(--border);border-radius:8px;background:rgba(255,255,255,0.85);color:var(--fg-muted);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all var(--transition)}
.chat-close:hover{color:var(--danger);border-color:rgba(229,49,112,0.35)}
.chat-header .bot-avatar{width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;color:#fff;overflow:hidden;border:1px solid rgba(14,140,74,0.3);background:linear-gradient(135deg,var(--accent-dark),var(--accent))}
.chat-header .bot-avatar img{width:100%;height:100%;object-fit:cover}
.chat-header .bot-name{font-weight:700;font-size:13px}
.chat-header .bot-status{font-size:10px;color:var(--success)}
.chat-messages{flex:1;overflow-y:auto;padding:14px;display:flex;flex-direction:column;gap:10px}
.chat-msg{max-width:85%;padding:10px 14px;border-radius:14px;font-size:12px;line-height:1.5;animation:msgIn 0.3s ease}
@keyframes msgIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
.chat-msg.bot{background:rgba(255,255,255,0.92);border:1px solid var(--border);align-self:flex-start;border-bottom-left-radius:4px}
.chat-msg.user{background:linear-gradient(135deg,var(--accent),var(--accent-dark));color:#fff;align-self:flex-end;border-bottom-right-radius:4px}
.chat-msg.bot .quick-replies{display:flex;flex-wrap:wrap;gap:5px;margin-top:7px}
.chat-msg.bot .quick-reply{padding:4px 10px;border-radius:14px;border:1px solid var(--border);background:var(--card);color:var(--accent-light);cursor:pointer;font-size:10px;font-weight:600;transition:all var(--transition)}
.chat-msg.bot .quick-reply:hover{background:var(--accent);color:#fff;box-shadow:0 2px 10px var(--accent-glow)}
.chat-input-area{padding:10px 14px;border-top:1px solid var(--border);display:flex;gap:7px}
.chat-input{flex:1;padding:9px 12px;background:rgba(255,255,255,0.92);border:1px solid var(--border);border-radius:var(--radius-sm);color:var(--fg);font-size:12px;font-family:'DM Sans';outline:none;transition:all var(--transition)}
.chat-input:focus{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-glow)}
.chat-send{width:36px;height:36px;border-radius:var(--radius-sm);border:none;background:linear-gradient(135deg,var(--accent),var(--accent-dark));color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all var(--transition);font-size:13px}
.chat-send:hover{box-shadow:0 2px 12px var(--accent-glow)}

.toast-container{position:fixed;top:18px;right:18px;z-index:2000;display:flex;flex-direction:column;gap:7px}
.toast{padding:12px 18px;border-radius:var(--radius-sm);background:var(--glass);backdrop-filter:blur(16px);border:1px solid var(--glass-border);box-shadow:var(--shadow);display:flex;align-items:center;gap:9px;font-size:12px;animation:toastIn 0.3s ease;min-width:260px}
@keyframes toastIn{from{opacity:0;transform:translateX(40px)}to{opacity:1;transform:translateX(0)}}
.toast.success{border-left:3px solid var(--success)}
.toast.error{border-left:3px solid var(--danger)}
.toast.info{border-left:3px solid var(--info)}
.toast.warning{border-left:3px solid var(--warning)}

.page-section{display:none}
.page-section.active{display:block}
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:18px}
.grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:18px}
.flex-between{display:flex;align-items:center;justify-content:space-between}
.mb-6{margin-bottom:22px}
.fade-in{animation:fadeIn 0.4s ease}
@keyframes fadeIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
.search-box{position:relative;display:inline-flex;align-items:center}
.search-box i{position:absolute;left:11px;color:var(--fg-muted);font-size:12px}
.search-box input{padding:8px 12px 8px 32px;background:var(--card);border:1px solid var(--border);border-radius:var(--radius-sm);color:var(--fg);font-size:12px;font-family:'DM Sans';outline:none;width:220px;transition:all var(--transition)}
.search-box input:focus{border-color:var(--accent);width:280px;box-shadow:0 0 0 3px var(--accent-glow)}
.order-timeline{display:flex;align-items:center;margin:10px 0}
.timeline-step{display:flex;flex-direction:column;align-items:center;flex:1;position:relative}
.timeline-dot{width:26px;height:26px;border-radius:50%;border:2px solid var(--border);background:var(--bg);display:flex;align-items:center;justify-content:center;font-size:9px;color:var(--fg-muted);z-index:1;transition:all var(--transition)}
.timeline-dot.done{background:var(--accent);border-color:var(--accent);color:#fff}
.timeline-dot.current{background:var(--gold);border-color:var(--gold);color:var(--bg-deep);animation:pulse 2s infinite}
@keyframes pulse{0%,100%{box-shadow:0 0 0 0 var(--gold-glow)}50%{box-shadow:0 0 0 8px transparent}}
.timeline-label{font-size:9px;color:var(--fg-muted);margin-top:5px;text-transform:uppercase;letter-spacing:0.5px}
.timeline-step:not(:last-child)::after{content:'';position:absolute;top:13px;left:calc(50% + 13px);width:calc(100% - 26px);height:2px;background:var(--border)}
.timeline-step.done:not(:last-child)::after{background:var(--accent)}
.customer-progress{min-width:220px;padding:2px 0}
.customer-progress-top{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:8px}
.customer-progress-title{font-size:11px;font-weight:800;color:var(--fg);text-transform:uppercase;letter-spacing:0.6px}
.customer-progress-percent{font-size:11px;font-weight:800;color:var(--accent)}
.customer-progress-track{height:8px;border-radius:999px;background:rgba(207,231,215,0.75);overflow:hidden;border:1px solid rgba(207,231,215,0.95)}
.customer-progress-fill{height:100%;width:0;border-radius:999px;background:linear-gradient(90deg,var(--accent-dark),var(--accent-light),var(--gold-light));box-shadow:0 0 14px var(--accent-glow);transition:width 0.35s ease}
.customer-progress-steps{display:grid;grid-template-columns:repeat(4,1fr);gap:5px;margin-top:8px}
.customer-progress-step{font-size:9px;color:var(--fg-muted);font-weight:700;text-transform:uppercase;text-align:center;white-space:nowrap}
.customer-progress-step.done,.customer-progress-step.current{color:var(--accent-dark)}
.customer-progress.cancelled .customer-progress-title,.customer-progress.cancelled .customer-progress-percent,.customer-progress.cancelled .customer-progress-step.current{color:var(--danger)}
.customer-progress.cancelled .customer-progress-fill{background:var(--danger);box-shadow:0 0 14px rgba(229,49,112,0.22)}
@media(max-width:720px){.customer-progress{min-width:180px}.customer-progress-step{font-size:8px}}
.receipt{background:#fff;color:#111;padding:22px;border-radius:var(--radius);max-width:310px;margin:0 auto;font-family:'DM Sans',monospace}
.receipt h4{text-align:center;font-size:15px;margin-bottom:3px}
.receipt .receipt-sub{text-align:center;font-size:10px;color:#666;margin-bottom:14px}
.receipt hr{border:none;border-top:1px dashed #ccc;margin:8px 0}
.receipt .receipt-row{display:flex;justify-content:space-between;font-size:11px;margin:3px 0}
.receipt .receipt-total{font-weight:700;font-size:13px}
.receipt .receipt-logo{text-align:center;margin-bottom:10px}
.receipt .receipt-logo img{width:48px;height:48px;border-radius:50%;object-fit:cover}
.bg-orb{position:fixed;border-radius:50%;filter:blur(120px);pointer-events:none;opacity:0.06}
.bg-orb-1{width:500px;height:500px;background:var(--accent);top:-200px;right:-100px;animation:orbFloat 10s ease-in-out infinite}
.bg-orb-2{width:400px;height:400px;background:var(--gold);bottom:-150px;left:-100px;animation:orbFloat 12s ease-in-out infinite reverse}
.menu-hero{text-align:center;padding:24px 0 20px;border-bottom:1px solid var(--border);margin-bottom:20px}
.menu-hero{position:relative;overflow:hidden}
.menu-hero::after{content:'';position:absolute;left:50%;bottom:-1px;transform:translateX(-50%);width:86px;height:3px;border-radius:999px;background:linear-gradient(90deg,var(--accent-dark),var(--gold-light))}
.menu-hero h2{font-size:28px;background:linear-gradient(135deg,var(--accent-light),var(--gold-light));-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.menu-hero p{color:var(--fg-muted);font-size:13px;margin-top:4px}
.menu-empty{grid-column:1/-1;text-align:center;padding:42px 18px;color:var(--fg-muted);border:1px dashed var(--border);border-radius:var(--radius);background:rgba(255,255,255,.82);box-shadow:0 12px 28px rgba(18,53,36,.06)}
.menu-empty i{width:54px;height:54px;border-radius:16px;background:rgba(18,134,78,.09);color:var(--accent);display:inline-flex;align-items:center;justify-content:center;font-size:26px;margin-bottom:12px}
.menu-empty strong{display:block;color:var(--fg);font-size:14px;margin-bottom:4px}
.menu-empty span{font-size:11px}

.cash-pending-banner{background:linear-gradient(135deg,rgba(245,166,35,0.08),rgba(201,168,76,0.04));border:1px solid rgba(245,166,35,0.2);border-radius:var(--radius);padding:16px 20px;margin-bottom:20px;display:flex;align-items:center;gap:14px}
.cash-pending-banner i{font-size:22px;color:var(--warning)}
.cash-pending-banner .info{flex:1}
.cash-pending-banner .info h4{font-size:14px;color:var(--warning);margin-bottom:2px}
.cash-pending-banner .info p{font-size:11px;color:var(--fg-muted)}

.logo-upload-zone{border:2px dashed var(--border);border-radius:var(--radius);padding:24px;text-align:center;cursor:pointer;transition:all var(--transition);background:rgba(7,15,11,0.5);position:relative}
.logo-upload-zone:hover{border-color:var(--accent);background:rgba(14,140,74,0.03)}
.logo-upload-zone.dragover{border-color:var(--accent);background:rgba(14,140,74,0.08);transform:scale(1.01)}
.logo-upload-zone i{font-size:28px;color:var(--fg-muted);margin-bottom:8px;display:block}
.logo-upload-zone .upload-text{font-size:12px;color:var(--fg-muted)}
.logo-upload-zone .upload-text strong{color:var(--accent-light)}
.logo-upload-zone input[type="file"]{position:absolute;inset:0;opacity:0;cursor:pointer}
.logo-preview-area{display:flex;align-items:center;gap:18px;margin-bottom:18px;padding:16px;background:rgba(7,15,11,0.5);border-radius:var(--radius);border:1px solid var(--border)}
.logo-preview-current{width:64px;height:64px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:24px;color:#fff;overflow:hidden;flex-shrink:0;box-shadow:0 4px 16px var(--accent-glow);border:2px solid rgba(14,140,74,0.3);background:linear-gradient(135deg,var(--accent-dark),var(--accent))}
.logo-preview-current img{width:100%;height:100%;object-fit:cover}
.logo-preview-info{flex:1}
.logo-preview-info .label{font-size:10px;color:var(--fg-muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:2px}
.logo-preview-info .name{font-size:13px;font-weight:600}
.logo-preview-info .hint{font-size:10px;color:var(--fg-muted);margin-top:2px}
.logo-actions{display:flex;gap:8px;margin-top:14px;flex-wrap:wrap}

.payment-method-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:8px}
.payment-method-option{min-height:74px;border:1px solid var(--border);border-radius:var(--radius-sm);background:rgba(255,255,255,0.92);color:var(--fg);cursor:pointer;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;font-family:'DM Sans';font-size:11px;font-weight:700;transition:all var(--transition)}
.payment-method-option i{font-size:17px;color:var(--fg-muted)}
.payment-method-option.active{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-glow);background:rgba(14,140,74,0.08)}
.payment-method-option.active i{color:var(--accent)}
.qr-payment-panel{display:none;background:rgba(91,141,239,0.08);border:1px solid rgba(91,141,239,0.22);border-radius:var(--radius-sm);padding:14px;margin-bottom:12px}
.qr-payment-panel.active{display:grid;grid-template-columns:132px 1fr;gap:14px;align-items:center}
.qr-frame{width:132px;height:132px;border-radius:12px;background:#fff;border:1px solid var(--border);display:flex;align-items:center;justify-content:center;overflow:hidden;color:#111}
.qr-frame img{width:100%;height:100%;object-fit:contain;display:block}
.qr-placeholder{width:104px;height:104px;background:repeating-linear-gradient(90deg,#111 0 7px,#fff 7px 14px);position:relative}
.qr-placeholder::before{content:'';position:absolute;inset:10px;background:repeating-linear-gradient(0deg,#fff 0 6px,#111 6px 12px);mix-blend-mode:difference}
.qr-details h4{font-family:'DM Sans';font-size:13px;margin-bottom:4px}
.qr-details p{font-size:11px;color:var(--fg-muted);line-height:1.5}
.qr-details .amount{font-size:18px;font-weight:900;color:var(--info);margin:8px 0}

/* Tagline styling */
.tagline{font-size:10px;color:var(--gold);letter-spacing:2px;text-transform:uppercase;font-weight:500;margin-top:2px}

@media(max-width:1024px){.stats-grid{grid-template-columns:repeat(2,1fr)}.pos-layout{grid-template-columns:1fr}.pos-cart{height:auto;max-height:380px}.pos-grid{grid-template-columns:repeat(2,1fr)}.notification-panel{width:320px;right:-40px}}
@media(max-width:768px){.sidebar{display:none}.stats-grid{grid-template-columns:1fr}.grid-2{grid-template-columns:1fr}.pos-grid{grid-template-columns:1fr 1fr}.notification-panel{width:300px;right:-60px}}
.customer-mobile .app-layout{display:flex;max-width:430px;margin:0 auto;background:var(--bg-deep);box-shadow:0 0 0 1px var(--border)}
.customer-mobile .main-content{width:100%}
.customer-mobile .topbar{height:64px;padding:0 14px;position:sticky;top:0;z-index:60;justify-content:space-between}
.customer-mobile .topbar-title-wrap{display:block}
.topbar-title-wrap{display:none}
.customer-mobile .topbar-title{font-size:17px}
.customer-mobile .topbar-breadcrumb{display:none}
.customer-mobile .page-content{padding:14px 14px calc(190px + env(safe-area-inset-bottom,0px));scroll-padding-bottom:calc(190px + env(safe-area-inset-bottom,0px))}
.customer-mobile .menu-hero{text-align:left;padding:6px 0 14px;margin-bottom:14px}
.customer-mobile .menu-hero h2{font-size:24px}
.customer-mobile .pos-layout{display:block}
.customer-mobile .pos-products{margin-bottom:14px;overflow:visible;padding-right:0;padding-bottom:34px}
.customer-mobile .pos-cart{height:auto;max-height:none;border-radius:14px;box-shadow:0 14px 40px rgba(18,53,36,0.12);margin-top:14px}
.customer-mobile .pos-cart.cart-empty{display:none}
.customer-mobile .cart-items{max-height:220px;flex:initial}
.customer-mobile .flex-between{align-items:flex-start;gap:10px}
.customer-mobile .search-box,.customer-mobile .search-box input{width:100%}
.customer-mobile .search-box input:focus{width:100%}
.customer-mobile .customer-mobile-nav{position:fixed;left:50%;bottom:0;transform:translateX(-50%);width:min(430px,100%);height:calc(68px + env(safe-area-inset-bottom,0px));padding-bottom:env(safe-area-inset-bottom,0px);background:rgba(255,255,255,0.96);border-top:1px solid var(--border);display:grid;grid-template-columns:repeat(4,1fr);z-index:70}
.customer-mobile .customer-mobile-nav button{border:0;background:transparent;color:var(--fg-muted);font-family:'DM Sans';font-size:10px;font-weight:800;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:5px}
.customer-mobile .customer-mobile-nav button i{font-size:17px}
.customer-mobile .customer-mobile-nav button.active{color:var(--accent)}
.customer-checkout-bar{display:none}
.customer-mobile .customer-checkout-bar{position:fixed;left:50%;bottom:calc(68px + env(safe-area-inset-bottom,0px));transform:translateX(-50%);width:min(430px,100%);z-index:72;background:rgba(255,255,255,0.98);border-top:1px solid var(--border);padding:10px 14px;box-shadow:0 -10px 28px rgba(18,53,36,0.1);display:flex;align-items:center;gap:10px}
.customer-checkout-info{flex:1;min-width:0}
.customer-checkout-info strong{display:block;font-size:13px;color:var(--fg)}
.customer-checkout-info span{display:block;font-size:10px;color:var(--fg-muted);margin-top:2px}
.customer-checkout-btn{height:42px;border:0;border-radius:10px;padding:0 15px;background:linear-gradient(135deg,var(--accent),var(--accent-dark));color:#fff;font-family:'DM Sans';font-size:12px;font-weight:900;display:inline-flex;align-items:center;justify-content:center;gap:7px;cursor:pointer;white-space:nowrap}
.customer-checkout-btn.is-empty{background:#e7f1eb;color:var(--fg-muted)}
.customer-mobile .notification-panel{position:fixed;top:70px;right:12px;left:12px;width:auto;max-height:70vh;transform-origin:top right}
.customer-mobile .chatbot-container{display:block!important;top:76px;right:14px;z-index:950;width:calc(100% - 28px)}
.customer-mobile .chatbot-window{top:0;right:0;width:100%;height:310px;max-height:calc(100vh - 190px)}
.customer-mobile table,.customer-mobile thead,.customer-mobile tbody,.customer-mobile tr,.customer-mobile td{display:block;width:100%}
.customer-mobile thead{display:none}
.customer-mobile tr{border-bottom:1px solid var(--border);padding:12px}
.customer-mobile td{border:0;padding:6px 0}

/* UI polish layer */
body{background:radial-gradient(circle at top right,rgba(22,199,106,.08),transparent 34%),linear-gradient(180deg,#fbfefc 0%,var(--bg) 100%)}
.login-card,.modal,.notification-panel,.chatbot-window{box-shadow:0 24px 70px rgba(18,53,36,.16)}
.topbar{box-shadow:0 8px 26px rgba(18,53,36,.05)}
.card,.stat-card,.pos-cart{box-shadow:0 12px 34px rgba(18,53,36,.08)}
.card:hover,.stat-card:hover{box-shadow:0 18px 44px rgba(18,53,36,.12);border-color:rgba(18,134,78,.28)}
.btn,.login-btn,.customer-checkout-btn,.chat-send,.topbar-btn{transition:transform .18s ease,box-shadow .18s ease,border-color .18s ease,color .18s ease,background .18s ease}
.btn:hover,.login-btn:hover,.customer-checkout-btn:hover{transform:translateY(-1px)}
.pos-cat-btn{background:#fff;border-color:#d8ebdf}
.pos-cat-btn.active{box-shadow:0 8px 18px rgba(18,134,78,.14)}
.pos-grid{gap:14px}
.pos-item{background:#fff;border-color:#d8ebdf;box-shadow:0 10px 24px rgba(18,53,36,.07);min-height:178px;display:flex;flex-direction:column;align-items:center;justify-content:flex-start}
.pos-item::after{content:'';position:absolute;left:12px;right:12px;top:10px;height:3px;border-radius:999px;background:linear-gradient(90deg,rgba(18,134,78,.55),rgba(69,184,115,.28));opacity:.75}
.pos-item:hover{box-shadow:0 16px 34px rgba(18,53,36,.14);transform:translateY(-2px)}
.pos-item-img{background:#f1f8f3;box-shadow:inset 0 0 0 1px rgba(207,231,215,.8),0 8px 18px rgba(18,53,36,.08);margin-top:6px}
.pos-item-name{font-size:13px;color:var(--fg);min-height:34px;display:flex;align-items:center;text-align:center}
.pos-item-price{font-size:15px;color:var(--accent)}
.pos-item-stock{font-size:11px}
.cart-item{background:#fff;border-color:#d8ebdf}
.payment-method-option:hover{border-color:var(--accent);box-shadow:0 8px 18px rgba(18,134,78,.1)}
.customer-progress-track{height:10px}
.customer-mobile .app-layout{min-height:100vh}
.customer-mobile .topbar{background:rgba(255,255,255,.96);backdrop-filter:blur(18px)}
.customer-mobile .menu-hero{background:#fff;border:1px solid var(--border);border-radius:14px;padding:14px;margin-bottom:14px;box-shadow:0 10px 24px rgba(18,53,36,.06)}
.customer-mobile .menu-hero h2{font-size:26px}
.customer-mobile .pos-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
.customer-mobile .pos-item{min-height:166px;padding:13px 10px}
.customer-mobile .pos-item-img{width:58px;height:58px}
.customer-mobile .pos-item-name{font-size:12px;min-height:38px}
.customer-mobile .customer-checkout-bar{border:1px solid var(--border);border-left:0;border-right:0;background:rgba(255,255,255,.98);backdrop-filter:blur(16px)}
.customer-mobile .customer-mobile-nav{box-shadow:0 -10px 24px rgba(18,53,36,.08);backdrop-filter:blur(16px)}
.customer-mobile .customer-mobile-nav button.active{background:rgba(22,199,106,.08)}
@media(max-width:390px){.customer-mobile .pos-grid{gap:10px}.customer-mobile .pos-item{padding:12px 8px}.customer-mobile .customer-signout-btn{padding:0 9px;font-size:11px}.customer-mobile .topbar{gap:8px}}
@media(min-width:769px){
  .customer-mobile .app-layout{max-width:none;width:100%;margin:0;background:transparent;box-shadow:none}
  .customer-mobile .main-content{width:100%}
  .customer-mobile .topbar{height:60px;padding:0 24px;position:static}
  .customer-mobile .topbar-title{font-size:18px}
  .customer-mobile .topbar-breadcrumb{display:block}
  .customer-mobile .page-content{padding:24px;scroll-padding-bottom:24px}
  .customer-mobile .menu-hero{text-align:center;padding:22px;margin-bottom:20px}
  .customer-mobile .menu-hero h2{font-size:34px}
  .customer-mobile .pos-layout{display:grid;grid-template-columns:minmax(0,1fr) 360px;gap:20px}
  .customer-mobile .pos-products{overflow:visible;margin-bottom:0;padding-right:0;padding-bottom:0}
  .customer-mobile .pos-cart{display:flex;height:calc(100vh - 132px);max-height:none;margin-top:0}
  .customer-mobile .pos-cart.cart-empty{display:flex}
  .customer-mobile .cart-items{max-height:none;flex:1}
  .customer-mobile .pos-grid{grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:14px}
  .customer-mobile .pos-item{min-height:178px;padding:16px 12px}
  .customer-mobile .pos-item-img{width:64px;height:64px}
  .customer-mobile .pos-item-name{font-size:13px;min-height:34px}
  .customer-mobile .customer-mobile-nav{display:none!important}
  .customer-mobile .customer-checkout-bar{display:none!important}
  .customer-mobile .chatbot-container{top:68px;right:24px;width:340px}
  .customer-mobile .chatbot-window{height:360px;max-height:none}
  .customer-mobile table{display:table;width:100%}
  .customer-mobile thead{display:table-header-group}
  .customer-mobile tbody{display:table-row-group}
  .customer-mobile tr{display:table-row;border-bottom:0;padding:0}
  .customer-mobile td{display:table-cell;width:auto;border-bottom:1px solid var(--border);padding:12px 14px}
}
</style>
<link href="{{ asset('css/admin-shell.css') }}" rel="stylesheet">
<script src="{{ asset('js/admin-sidebar.js') }}" defer></script>
</head>
<body>
<div class="bg-orb bg-orb-1"></div>
<div class="bg-orb bg-orb-2"></div>
<div class="toast-container" id="toastContainer"></div>

<!-- LOGIN / GUEST ENTRY -->
<div class="login-page" id="loginPage">
  <div class="login-bg-orb a"></div><div class="login-bg-orb b"></div><div class="login-bg-orb c"></div>
  <div class="login-card">
    <div class="login-header"><div class="login-crown" id="loginCrown"><img src="{{ asset('icons/queens-cup-logo.png') }}" alt="Queen's Cup Logo"></div><h2>The Queen's Cup</h2><p>Crowned with Flavors</p></div>
    <div class="login-body">
      <div class="login-tabs"><button class="login-tab active" data-login-tab="signin" onclick="switchLoginTab('signin')">Sign In</button><button class="login-tab" data-login-tab="register" onclick="switchLoginTab('register')">Create Account</button><button class="login-tab" onclick="window.location.href='/staff-login'">Staff</button></div>
      <div class="login-error" id="loginError"></div>

      <div class="login-form active" id="signinForm">
        <div class="guest-welcome"><i class="fas fa-mug-hot"></i><p>Welcome back! Sign in to reserve your favourite drinks.</p></div>
        <div class="login-field"><label>Email</label><div class="input-icon"><i class="fas fa-envelope"></i><input type="email" id="signinEmail" placeholder="you@example.com" autocomplete="email"></div></div>
        <div class="login-field"><label>Password</label><div class="input-icon"><i class="fas fa-lock"></i><input type="password" id="signinPassword" placeholder="Your password" autocomplete="current-password"><button type="button" class="pw-toggle" onclick="togglePw('signinPassword',this)"><i class="fas fa-eye"></i></button></div></div>
        <button class="login-btn" id="signinBtn" onclick="handleCustomerSignIn()">Sign In</button>
        <div style="margin-top:16px;text-align:center;font-size:11px;color:var(--fg-muted)">New here? <button type="button" class="link-inline" onclick="switchLoginTab('register')">Create an account</button></div>
      </div>

      <div class="login-form" id="registerForm">
        <div class="guest-welcome"><i class="fas fa-user-plus"></i><p>Create an account to reserve drinks and keep track of every order.</p></div>
        <div class="login-field"><label>Full Name</label><div class="input-icon"><i class="fas fa-user"></i><input type="text" id="regName" placeholder="Enter your full name" autocomplete="name"></div></div>
        <div class="login-field"><label>Email</label><div class="input-icon"><i class="fas fa-envelope"></i><input type="email" id="regEmail" placeholder="you@example.com" autocomplete="email"></div></div>
        <div class="login-field"><label>Mobile Number <span style="text-transform:none;font-weight:400">(optional)</span></label><div class="input-icon"><i class="fas fa-phone"></i><input type="tel" id="regContact" placeholder="09XX XXX XXXX" autocomplete="tel"></div></div>
        <div class="login-field"><label>Password</label><div class="input-icon"><i class="fas fa-lock"></i><input type="password" id="regPassword" placeholder="At least 8 characters" autocomplete="new-password"><button type="button" class="pw-toggle" onclick="togglePw('regPassword',this)"><i class="fas fa-eye"></i></button></div></div>
        <div class="login-field"><label>Confirm Password</label><div class="input-icon"><i class="fas fa-lock"></i><input type="password" id="regPasswordConfirm" placeholder="Repeat your password" autocomplete="new-password"></div></div>
        <button class="login-btn wine-btn" id="registerBtn" onclick="handleCustomerRegister()"><i class="fas fa-paper-plane" style="margin-right:6px"></i>Create Account</button>
        <div style="margin-top:16px;text-align:center;font-size:11px;color:var(--fg-muted)">Already registered? <button type="button" class="link-inline" onclick="switchLoginTab('signin')">Sign in</button></div>
      </div>

      <div class="login-form" id="verifyForm">
        <div class="guest-welcome"><i class="fas fa-envelope-open-text"></i><p id="verifyBlurb">We sent a 6 digit code to your email. Enter it below to finish.</p></div>
        <div class="login-field"><label>Verification Code</label><div class="input-icon"><i class="fas fa-key"></i><input type="text" id="verifyCode" inputmode="numeric" maxlength="6" placeholder="6 digit code" autocomplete="one-time-code" style="letter-spacing:6px;font-weight:800"></div></div>
        <button class="login-btn" id="verifyBtn" onclick="handleCustomerVerify()"><i class="fas fa-check" style="margin-right:6px"></i>Verify &amp; Continue</button>
        <button class="login-btn gold-btn" id="resendCodeBtn" onclick="handleResendCode()" style="margin-top:10px"><i class="fas fa-rotate-right" style="margin-right:6px"></i>Send a New Code</button>
        <div style="margin-top:16px;text-align:center;font-size:11px;color:var(--fg-muted)"><button type="button" class="link-inline" onclick="switchLoginTab('register')">Use a different email</button></div>
      </div>
      <div class="login-form" id="staffForm">
        <div class="login-field"><label>Username</label><div class="input-icon"><i class="fas fa-user"></i><input type="text" id="loginUsername" placeholder="Enter username" autocomplete="username"></div></div>
        <div class="login-field"><label>Password</label><div class="input-icon"><i class="fas fa-lock"></i><input type="password" id="loginPassword" placeholder="Enter password" autocomplete="current-password"></div></div>
        <button class="login-btn" onclick="handleLogin()">Sign In</button>
      </div>

      <div class="login-home">
        <a href="{{ url('/') }}"><i class="fas fa-house"></i> Back to home</a>
      </div>
    </div>
  </div>
</div>

<!-- MAIN APP -->
<div class="app-layout" id="appLayout" style="display:none">

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
        <nav class="sidebar-nav" id="sidebarNav">
            <div class="nav-section"><div class="nav-section-title">Main</div><a class="nav-item" href="{{ url('/dashboard') }}"><i class="fas fa-chart-pie"></i> Dashboard</a><a class="nav-item" href="{{ url('/pos') }}"><i class="fas fa-cash-register"></i> Point of Sale</a></div>
            <div class="nav-section"><div class="nav-section-title">Management</div><a class="nav-item active" href="{{ url('/orders') }}"><i class="fas fa-receipt"></i> Orders</a><a class="nav-item" href="{{ url('/reservations') }}"><i class="fas fa-calendar-check"></i> Reservations</a><a class="nav-item" href="{{ url('/inventory') }}"><i class="fas fa-boxes-stacked"></i> Inventory</a></div>
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
  <div class="main-content">
    <header class="topbar">
      <div class="topbar-title-wrap">
        <div class="topbar-title" id="pageTitle">Menu & Order</div>
        <div class="topbar-breadcrumb" id="pageBreadcrumb">Home / Menu & Order</div>
      </div>
      <div class="topbar-right">
        <button type="button" class="topbar-btn customer-chat-btn" id="customerChatBtn" onclick="toggleChatbot()" title="Customer Support"><i class="fas fa-comments"></i></button>
        <div class="notif-wrapper">
          <button class="topbar-btn" onclick="toggleNotifPanel()" title="Notifications"><i class="fas fa-bell"></i><span class="notif-count" id="notifBadge"></span></button>
          <div class="notification-panel" id="notifPanel">
            <div class="notif-panel-header"><h4>Notifications</h4><button class="notif-mark-all" onclick="markAllNotifRead()">Mark all read</button></div>
            <div class="notif-list" id="notifList"></div>
          </div>
        </div>
        <button type="button" class="customer-install-btn" id="customerInstallBtn" onclick="installCustomerApp()" title="Download app"><i class="fas fa-download"></i> Download App</button>
        <button type="button" class="header-logout-btn customer-signout-btn" onclick="handleLogout()" title="Sign Out"><i class="fas fa-right-from-bracket"></i> Sign Out</button>
        <div class="cash-pending-indicator" id="topCashPending" style="display:none" onclick="navigateTo('orders');filterOrders('cash_pending',document.querySelector('.order-filter[data-filter=cash_pending]'))"><i class="fas fa-money-bill-wave"></i><span id="topCashPendingCount">0</span> Payment Pending</div>
        <input type="hidden" id="branchSelect" value="kotapark">
      </div>
    </header>
    <div class="page-content">
      <!-- POS -->
      <div class="page-section" id="page-pos">
        <div id="customerMenuHero" style="display:none" class="menu-hero fade-in"><h2>Our Menu</h2><p>Choose your favorite drink and place an order</p></div>
        <div class="pos-layout fade-in">
          <div class="pos-products">
            <div class="flex-between" style="margin-bottom:14px"><div class="search-box"><i class="fas fa-search"></i><input type="text" placeholder="Search menu..." id="posSearch" oninput="filterPOSItems()"></div><span style="font-size:11px;color:var(--fg-muted)">Tap drink, pick size (R/L)</span></div>
            <div class="pos-categories" id="posCategories"></div>
            <div class="pos-grid" id="posGrid"></div>
          </div>
          <div class="pos-cart">
            <div class="cart-header"><h3 style="font-size:15px"><i class="fas fa-shopping-cart" style="color:var(--accent-light);margin-right:7px"></i>Your Order</h3><button class="btn btn-secondary btn-sm" onclick="clearCart()"><i class="fas fa-trash"></i> Clear</button></div>
            <div class="cart-items" id="cartItems"><div style="text-align:center;padding:36px 18px;color:var(--fg-muted)"><i class="fas fa-mug-hot" style="font-size:36px;opacity:0.3;margin-bottom:10px;display:block"></i><p>No items in cart</p><p style="font-size:10px;margin-top:3px">Tap a drink to add</p></div></div>
            <div class="cart-summary" id="cartSummary" style="display:none"><div class="cart-summary-row"><span>Subtotal</span><span id="cartSubtotal">&#8369;0.00</span></div><div class="cart-summary-row" id="cartDiscountRow"><span>Discount</span><span id="cartDiscount" style="color:var(--success)">-&#8369;0.00</span></div><div class="cart-summary-row" id="cartFeeRow" style="display:none"><span id="cartFeeLabel">Take-out cups</span><span id="cartFee">&#8369;0.00</span></div><div class="cart-summary-row total"><span>Total</span><span id="cartTotal">&#8369;0.00</span></div></div>
            <div class="cart-actions"><button class="btn btn-secondary" onclick="holdOrder()"><i class="fas fa-pause"></i> Hold</button><button class="btn btn-gold" onclick="checkout()"><i class="fas fa-credit-card"></i> Checkout</button></div>
          </div>
        </div>
      </div>
      <!-- ORDERS -->
      <div class="page-section" id="page-orders">
        <div id="cashPendingBanner" style="display:none" class="cash-pending-banner fade-in"><i class="fas fa-money-bill-wave"></i><div class="info"><h4 id="cashPendingBannerTitle">Payment Pending Orders</h4><p id="cashPendingBannerDesc">These orders are awaiting cash or QR payment confirmation.</p></div><button class="btn btn-warning btn-sm" onclick="filterOrders('cash_pending',document.querySelector('.order-filter[data-filter=cash_pending]'))"><i class="fas fa-filter"></i> View Pending</button></div>
        <div id="adminOrderFilters" style="display:none" class="flex-between mb-6 fade-in"><div style="display:flex;gap:6px;flex-wrap:wrap"><button class="btn btn-secondary btn-sm order-filter active" data-filter="all" onclick="filterOrders('all',this)">All</button><button class="btn btn-secondary btn-sm order-filter" data-filter="pending" onclick="filterOrders('pending',this)">Pending</button><button class="btn btn-secondary btn-sm order-filter" data-filter="preparing" onclick="filterOrders('preparing',this)">Preparing</button><button class="btn btn-secondary btn-sm order-filter" data-filter="serving" onclick="filterOrders('serving',this)">Serving</button><button class="btn btn-secondary btn-sm order-filter" data-filter="completed" onclick="filterOrders('completed',this)">Completed</button><button class="btn btn-secondary btn-sm order-filter" data-filter="cancelled" onclick="filterOrders('cancelled',this)">Cancelled</button><button class="btn btn-secondary btn-sm order-filter" data-filter="cash_pending" onclick="filterOrders('cash_pending',this)" style="border-color:rgba(245,166,35,0.4);color:var(--warning)"><i class="fas fa-money-bill-wave"></i> Payment Pending</button></div><div class="search-box"><i class="fas fa-search"></i><input type="text" placeholder="Search orders..." id="orderSearch" oninput="renderOrders()"></div></div>
        <div id="customerOrderHeader" style="display:none" class="mb-6 fade-in flex-between">
          <div>
            <h3 style="font-family:'Playfair Display';font-size:22px;margin-bottom:4px">My Reservations</h3>
            <p style="font-size:12px;color:var(--fg-muted)">Track your reservations and pick them up at the counter</p>
          </div>
          <button class="btn btn-gold btn-sm" onclick="navigateTo('pos')"><i class="fas fa-mug-hot"></i> Back to Menu</button>
        </div>
        <div id="customerReservationList" style="display:none"></div>
        <div class="card fade-in" id="staffOrdersCard"><div class="card-body" style="padding:0"><div class="table-wrap"><table><thead id="ordersThead"></thead><tbody id="ordersTable"></tbody></table></div></div></div>
      </div>
      <!-- INVENTORY -->
      <div class="page-section" id="page-inventory">
        <div class="flex-between mb-6 fade-in"><div class="search-box"><i class="fas fa-search"></i><input type="text" placeholder="Search inventory..." id="invSearch" oninput="filterInventory()"></div><div style="display:flex;gap:8px"><button class="btn btn-secondary" onclick="exportInventory()"><i class="fas fa-download"></i> Export</button><button class="btn btn-primary" onclick="openInventoryModal()"><i class="fas fa-plus"></i> Add Item</button></div></div>
        <div class="stats-grid fade-in" style="grid-template-columns:repeat(3,1fr);margin-bottom:20px">
          <div class="stat-card"><div class="stat-icon" style="background:rgba(44,182,125,0.15);color:var(--success)"><i class="fas fa-check-circle"></i></div><div class="stat-value" id="invInStock">0</div><div class="stat-label">In Stock</div></div>
          <div class="stat-card"><div class="stat-icon" style="background:rgba(245,166,35,0.15);color:var(--warning)"><i class="fas fa-exclamation-triangle"></i></div><div class="stat-value" id="invLowStock">0</div><div class="stat-label">Low Stock</div></div>
          <div class="stat-card"><div class="stat-icon" style="background:rgba(229,49,112,0.15);color:var(--danger)"><i class="fas fa-times-circle"></i></div><div class="stat-value" id="invOutOfStock">0</div><div class="stat-label">Out of Stock</div></div>
        </div>
        <div class="card fade-in"><div class="card-body" style="padding:0"><div class="table-wrap"><table><thead><tr><th>Product</th><th>Category</th><th>Prices (R/L)</th><th id="invStockHeader">Stock</th><th>Status</th><th>Actions</th></tr></thead><tbody id="inventoryTable"></tbody></table></div></div></div>
      </div>
      <!-- REPORTS -->
      <div class="page-section" id="page-reports">
        <div class="stats-grid fade-in">
          <div class="stat-card"><div class="stat-icon" style="background:rgba(201,168,76,0.15);color:var(--gold)"><i class="fas fa-coins"></i></div><div class="stat-value" id="rptRevenue">&#8369;0</div><div class="stat-label">Total Revenue</div></div>
          <div class="stat-card"><div class="stat-icon" style="background:rgba(14,140,74,0.15);color:var(--accent-light)"><i class="fas fa-chart-line"></i></div><div class="stat-value" id="rptAvgSales">&#8369;0</div><div class="stat-label">Avg. Order Value</div></div>
          <div class="stat-card"><div class="stat-icon" style="background:rgba(91,141,239,0.15);color:var(--info)"><i class="fas fa-receipt"></i></div><div class="stat-value" id="rptOrders">0</div><div class="stat-label">Total Orders</div></div>
          <div class="stat-card"><div class="stat-icon" style="background:rgba(201,168,76,0.15);color:var(--gold)"><i class="fas fa-mug-hot"></i></div><div class="stat-value" id="rptItemsSold">0</div><div class="stat-label">Items Sold</div></div>
        </div>
        <div class="grid-2 fade-in"><div class="card"><div class="card-header"><h3>Monthly Revenue</h3></div><div class="card-body"><canvas id="revenueChart" height="250"></canvas></div></div><div class="card"><div class="card-header"><h3>Sales by Category</h3></div><div class="card-body"><canvas id="categoryChart" height="250"></canvas></div></div></div>
      </div>
      <!-- SETTINGS -->
      <div class="page-section" id="page-settings">
        <div class="grid-2 fade-in">
          <div class="card"><div class="card-header"><h3>Branch Information</h3></div><div class="card-body">
            <div class="form-group"><label>Branch</label><select class="form-select" id="settingsBranch" onchange="document.getElementById('settingsAddress').value=BRANCHES[this.value]?BRANCHES[this.value].address:''"><option value="kotapark">Kota Park, Madridejos</option><option value="mcc">Madridejos Community College</option></select></div>
            <div class="form-group"><label>Address</label><input class="form-input" id="settingsAddress" value="Kota Park, Madridejos, Cebu"></div>
            <div class="form-group"><label>Phone</label><input class="form-input" value="+63 917 123 4567"></div>
            <button class="btn btn-primary" onclick="saveBranchInfo()"><i class="fas fa-save"></i> Save</button>
          </div></div>
          <div class="card"><div class="card-header"><h3>POS & System</h3></div><div class="card-body">
            <div class="form-group"><label>Default Discount (%)</label><input class="form-input" type="number" value="0" id="defaultDiscount"></div>
            <div class="form-group"><label>Low Stock Threshold</label><input class="form-input" type="number" value="10" id="lowStockThreshold"></div>
            <button class="btn btn-primary" onclick="saveSettings()"><i class="fas fa-save"></i> Save</button>
          </div></div>
        </div>
        <div class="grid-2 fade-in" style="margin-top:18px">
          <div class="card"><div class="card-header"><h3>Branch Logo</h3><span class="badge badge-gold"><i class="fas fa-crown" style="font-size:8px"></i> Admin Only</span></div><div class="card-body">
            <div class="logo-preview-area">
              <div class="logo-preview-current" id="logoPreviewCurrent"><img src="{{ asset('icons/queens-cup-logo.png') }}" alt="Logo"></div>
              <div class="logo-preview-info">
                <div class="label">Current Logo</div>
                <div class="name" id="logoPreviewName">Queen's Cup Brand Logo</div>
                <div class="hint">Shown in sidebar, login page, receipts & chat</div>
              </div>
            </div>
            <div class="logo-upload-zone" id="logoUploadZone">
              <i class="fas fa-cloud-arrow-up"></i>
              <div class="upload-text"><strong>Click to upload</strong> or drag and drop</div>
              <div style="font-size:10px;color:var(--fg-muted);margin-top:4px">PNG, JPG, SVG or WEBP (max 2MB)</div>
              <input type="file" id="logoFileInput" accept="image/png,image/jpeg,image/svg+xml,image/webp" onchange="previewLogoUpload(event)">
            </div>
            <div id="logoNewPreview" style="display:none;margin-top:14px;padding:12px;background:rgba(14,140,74,0.06);border:1px solid rgba(14,140,74,0.2);border-radius:var(--radius-sm)">
              <div style="display:flex;align-items:center;gap:12px">
                <div style="width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0;border:1px solid rgba(14,140,74,0.3)" id="logoNewThumb"></div>
                <div><div style="font-size:12px;font-weight:600" id="logoNewFileName">New logo</div><div style="font-size:10px;color:var(--fg-muted)" id="logoNewFileSize">-</div></div>
              </div>
            </div>
            <div class="logo-actions">
              <button class="btn btn-primary" id="logoSaveBtn" onclick="saveLogo()" disabled><i class="fas fa-save"></i> Save Logo</button>
              <button class="btn btn-secondary" onclick="resetLogo()"><i class="fas fa-undo"></i> Reset to Default</button>
            </div>
          </div></div>
          <div class="card"><div class="card-header"><h3>Create Staff Account</h3><span class="badge badge-gold"><i class="fas fa-crown" style="font-size:8px"></i> Admin Only</span></div><div class="card-body">
            <div class="form-group"><label>Full Name</label><input class="form-input" id="staffName" placeholder="Enter full name"></div>
            <div class="form-group"><label>Email</label><input class="form-input" type="email" id="staffEmail" placeholder="staff@example.com"></div>
            <div class="grid-2">
              <div class="form-group"><label>Password</label><input class="form-input" type="password" id="staffPassword" placeholder="Min 6 characters"></div>
              <div class="form-group"><label>Role</label><select class="form-select" id="staffRole"><option value="cashier">Cashier</option><option value="admin">Admin</option></select></div>
            </div>
            <button class="btn btn-primary" onclick="createStaffAccount()"><i class="fas fa-user-plus"></i> Create Account</button>
          </div></div>
        </div>
      </div>
      <!-- PROFILE -->
      <div class="page-section" id="page-history">
        <div class="mb-6 fade-in flex-between">
          <div>
            <h3 style="font-family:'Playfair Display';font-size:22px;margin-bottom:4px">Reservation History</h3>
            <p style="font-size:12px;color:var(--fg-muted)">Everything you have picked up or cancelled</p>
          </div>
          <button class="btn btn-gold btn-sm" onclick="navigateTo('pos')"><i class="fas fa-mug-hot"></i> Order Again</button>
        </div>
        <div id="customerHistoryList"></div>
      </div>

      <div class="page-section" id="page-profile">
        <div class="grid-2 fade-in">
          <div class="card"><div class="card-header"><h3>Profile</h3></div><div class="card-body">
            <div style="text-align:center;margin-bottom:20px"><div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--gold));display:flex;align-items:center;justify-content:center;font-size:26px;font-weight:700;color:#fff;margin:0 auto 10px;box-shadow:0 4px 20px var(--accent-glow)" id="profileAvatar">?</div><div style="font-size:18px;font-weight:700;font-family:'Playfair Display'" id="profileName">-</div><div style="font-size:12px;color:var(--fg-muted)" id="profileRole">-</div></div>
            <div class="form-group"><label>Full Name</label><input class="form-input" id="profileFullName"></div>
            <div class="form-group"><label>Email</label><input class="form-input" type="email" id="profileEmail" placeholder="you@example.com"></div>
            <div class="form-group"><label>Contact Number</label><input class="form-input" type="tel" id="profileContact" placeholder="09xxxxxxxxx"></div>
            <div class="form-group"><label>Username</label><input class="form-input" id="profileUsername" disabled style="opacity:0.6"></div>
            <div class="form-group"><label>Member Since</label><input class="form-input" id="profileSince" disabled style="opacity:0.6"></div>
            <button class="btn btn-primary" id="profileUpdateBtn" onclick="updateProfile()"><i class="fas fa-save"></i> Update</button>
          </div></div>
          <div class="card" id="profilePasswordCard"><div class="card-header"><h3>Change Password</h3></div><div class="card-body">
            <div class="form-group"><label>Current Password</label><input class="form-input" type="password" id="pwCurrent"></div>
            <div class="form-group"><label>New Password</label><input class="form-input" type="password" id="pwNew"></div>
            <div class="form-group"><label>Confirm New Password</label><input class="form-input" type="password" id="pwConfirm"></div>
            <button class="btn btn-primary" onclick="changePassword()"><i class="fas fa-key"></i> Change</button>
          </div></div>
        </div>
      </div>
      <!-- Chatbot -->
      <div class="chatbot-container" id="chatbotContainer" style="display:none">
        <div class="chatbot-window" id="chatWindow"><div class="chat-header"><div class="bot-avatar" id="chatBotAvatar"><img src="{{ asset('icons/queens-cup-logo.png') }}" alt="Logo"></div><div><div class="bot-name">Queen's Cup Assistant</div><div class="bot-status"><i class="fas fa-circle" style="font-size:6px;margin-right:3px"></i>Online</div></div><button type="button" class="chat-close" onclick="toggleChatbot()" title="Close assistant"><i class="fas fa-times"></i></button></div><div class="chat-messages" id="chatMessages"></div><div class="chat-input-area"><input class="chat-input" id="chatInput" placeholder="Type a message..." onkeypress="if(event.key==='Enter')sendChat()"><button class="chat-send" onclick="sendChat()"><i class="fas fa-paper-plane"></i></button></div></div>
        <button class="chatbot-toggle" onclick="toggleChatbot()"><i class="fas fa-comments"></i></button>
      </div>
    </div>
  </div>
</div>
<div class="customer-checkout-bar" id="customerCheckoutBar">
  <div class="customer-checkout-info">
    <strong id="customerCheckoutTotal">&#8369;0.00</strong>
    <span id="customerCheckoutCount">No items selected</span>
  </div>
  <button type="button" class="customer-checkout-btn is-empty" id="customerCheckoutBtn" onclick="checkout()"><i class="fas fa-credit-card"></i> Checkout</button>
</div>
<nav class="customer-mobile-nav" id="customerMobileNav" aria-label="Customer navigation">
  <button type="button" data-page="pos" onclick="navigateTo('pos');history.replaceState(null,'','#pos')"><i class="fas fa-mug-hot"></i><span>Menu</span></button>
  <button type="button" data-page="orders" onclick="navigateTo('orders');history.replaceState(null,'','#orders')"><i class="fas fa-receipt"></i><span>Orders</span></button>
  <button type="button" data-page="profile" onclick="navigateTo('profile');history.replaceState(null,'','#profile')"><i class="fas fa-user-circle"></i><span>Profile</span></button>
  <button type="button" onclick="installCustomerApp()"><i class="fas fa-download"></i><span>Download</span></button>
</nav>

<!-- Modals -->
<div class="modal-overlay" id="inventoryModal"><div class="modal"><div class="modal-header"><h3 id="invModalTitle">Add New Item</h3><button class="modal-close" onclick="closeModal('inventoryModal')"><i class="fas fa-times"></i></button></div><div class="modal-body"><input type="hidden" id="editItemId"><input type="hidden" id="invImageUrl"><div class="form-group"><label>Picture</label><div class="item-upload-row"><div class="item-thumb-sm" id="invImagePreview"><i class="fas fa-image"></i></div><input class="form-input" type="file" id="invImageFile" accept="image/*" onchange="previewInventoryImage(event)"></div></div><div class="form-group"><label>Product Name</label><input class="form-input" id="invName"></div><div class="form-group"><label>Category</label><select class="form-select" id="invCategory"><option>Milktea Series</option><option>Fruit Teas</option><option>Milky Fruit Jams</option><option>Lemonade</option><option>Coffee & Non-Coffee</option><option>Fruit Milk Shake</option><option>Sticky Milk Drinks</option></select></div><div class="grid-2"><div class="form-group"><label>Price R (Regular / 16oz)</label><input class="form-input" type="number" id="invPriceR"></div><div class="form-group"><label>Price L (Large / 22oz)</label><input class="form-input" type="number" id="invPriceL"></div></div><div class="form-group"><label>Stock <span id="invStockLabelBranch"></span></label><input class="form-input" type="number" id="invStock"></div><div class="form-group"><label>Description</label><textarea class="form-textarea" id="invDesc"></textarea></div></div><div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('inventoryModal')">Cancel</button><button class="btn btn-primary" onclick="saveInventoryItem()"><i class="fas fa-save"></i> Save</button></div></div></div>
<div class="modal-overlay" id="sizeModal"><div class="modal" style="max-width:400px"><div class="modal-header"><h3>Select Size</h3><button class="modal-close" onclick="closeModal('sizeModal')"><i class="fas fa-times"></i></button></div><div class="modal-body" id="sizeModalBody"></div></div></div>
<div class="modal-overlay" id="checkoutModal"><div class="modal"><div class="modal-header"><h3 id="checkoutHeading">Confirm Order</h3><button class="modal-close" onclick="closeModal('checkoutModal')"><i class="fas fa-times"></i></button></div><div class="modal-body"><div class="form-group" id="checkoutNameField"><label>Customer Name</label><input class="form-input" id="checkoutCustomerName" placeholder="Walk-in"></div><div class="form-group"><label>How will you have it?</label><select class="form-select" id="checkoutType" onchange="updateCartTotals()"><option>Dine In</option><option>Take Out</option><option id="checkoutTypePickup">Pick Up</option></select><div id="takeoutFeeHint" style="display:none;margin-top:6px;font-size:11px;color:var(--fg-muted)"></div></div><div class="form-group" id="paymentMethodField"><label>Payment Method</label><div class="payment-method-grid"><button type="button" class="payment-method-option active" data-payment-method="Cash" onclick="selectPaymentMethod('Cash')"><i class="fas fa-money-bill-wave"></i><span>Cash</span></button><button type="button" class="payment-method-option" data-payment-method="GCash QR" onclick="selectPaymentMethod('GCash QR')"><i class="fas fa-qrcode"></i><span>GCash QR</span></button><button type="button" class="payment-method-option" data-payment-method="Maya QR" onclick="selectPaymentMethod('Maya QR')"><i class="fas fa-qrcode"></i><span>Maya QR</span></button></div><input type="hidden" id="checkoutPaymentMethod" value="Cash"></div><div class="form-group" id="discountField"><label>Discount (%)</label><input class="form-input" type="number" id="checkoutDiscount" value="0" min="0" max="100" oninput="updateCartTotals()"></div><div class="form-group" id="cashTenderedField"><label>Cash Tendered (&#8369;)</label><input class="form-input" type="number" id="checkoutCashTendered" min="0" step="1" oninput="updateCartTotals()" placeholder="0.00"></div><div class="qr-payment-panel" id="qrPaymentPanel"><div class="qr-frame" id="qrPaymentCode"></div><div class="qr-details"><h4 id="qrPaymentTitle">Scan to Pay</h4><p>Ask the customer to scan this QR code, then confirm payment after the app shows a successful transfer.</p><div class="amount" id="qrPaymentAmount">&#8369;0.00</div><p id="qrPaymentHint">Place your real QR image at public/images/gcash-qr.png or public/images/maya-qr.png.</p></div></div><div id="paymentNotice" style="background:rgba(245,166,35,0.08);border:1px solid rgba(245,166,35,0.2);border-radius:var(--radius-sm);padding:10px 14px;margin-bottom:12px;font-size:11px;color:var(--warning)"><i class="fas fa-info-circle" style="margin-right:5px"></i>Cash orders are marked <strong>Cash Pending</strong> until the cashier confirms payment.</div><div style="background:rgba(255,255,255,0.92);border-radius:var(--radius-sm);padding:14px;margin-top:6px"><div class="cart-summary-row"><span>Subtotal</span><span id="chkSubtotal">&#8369;0.00</span></div><div class="cart-summary-row"><span>Discount</span><span id="chkDiscount" style="color:var(--success)">-&#8369;0.00</span></div><div class="cart-summary-row" id="chkFeeRow" style="display:none"><span id="chkFeeLabel">Take-out cups</span><span id="chkFee">&#8369;0.00</span></div><div class="cart-summary-row total"><span>Total Due</span><span id="chkTotal">&#8369;0.00</span></div><div class="cart-summary-row" id="changeRow" style="display:none;margin-top:8px;padding-top:8px;border-top:1px dashed var(--border)"><span style="font-weight:700">Change</span><span id="chkChange" style="font-weight:700;color:var(--success)">&#8369;0.00</span></div></div></div><div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('checkoutModal')">Cancel</button><button class="btn btn-gold" id="reserveConfirmBtn" onclick="processPayment()"><i class="fas fa-check"></i> Confirm</button></div></div></div>
<div class="modal-overlay" id="receiptModal"><div class="modal" style="max-width:370px"><div class="modal-header"><h3>Receipt</h3><button class="modal-close" onclick="closeModal('receiptModal')"><i class="fas fa-times"></i></button></div><div class="modal-body" id="receiptContent"></div><div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('receiptModal')">Close</button><button class="btn btn-primary" onclick="printReceipt()"><i class="fas fa-print"></i> Print</button></div></div></div>
<div class="modal-overlay" id="orderDetailModal"><div class="modal" style="max-width:540px"><div class="modal-header"><h3>Order Details</h3><button class="modal-close" onclick="closeModal('orderDetailModal')"><i class="fas fa-times"></i></button></div><div class="modal-body" id="orderDetailContent"></div><div class="modal-footer" id="orderDetailFooter"></div></div></div>

<script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
<script>
/* ========== LOGO URL ========== */
var LOGO_URL='{{ asset('icons/queens-cup-logo.png') }}';

/* ========== BRANCH DATA ========== */
var BRANCHES={
  kotapark:{name:"Kota Park, Madridejos",address:"Kota Park, Madridejos, Cebu",landmark:"Beside the famous Kota Park & boardwalk"},
  mcc:{name:"Madridejos Community College",address:"Madridejos Community College, Madridejos, Cebu",landmark:"Inside MCC campus, main building"}
};
var PAYMENT_METHODS={
  Cash:{label:'Cash',icon:'fa-money-bill-wave',qrImage:'',pendingLabel:'Cash Pending',paidLabel:'Cash Paid'},
  'GCash QR':{label:'GCash QR',icon:'fa-qrcode',qrImage:'{{ asset('images/gcash-qr.png') }}',pendingLabel:'GCash QR Pending',paidLabel:'GCash QR Paid'},
  'Maya QR':{label:'Maya QR',icon:'fa-qrcode',qrImage:'{{ asset('images/maya-qr.png') }}',pendingLabel:'Maya QR Pending',paidLabel:'Maya QR Paid'}
};
function getBranch(){return document.getElementById('branchSelect').value;}
function getBranchInfo(){return BRANCHES[getBranch()]||BRANCHES.kotapark;}
function paymentInfo(method){return PAYMENT_METHODS[method]||PAYMENT_METHODS.Cash;}
function isQrPayment(method){return method==='GCash QR'||method==='Maya QR';}

function getBranchStock(p){
  var b=getBranch();if(!p.stock)return 0;if(typeof p.stock==='number')return p.stock;return p.stock[b]!==undefined?p.stock[b]:0;
}
function setBranchStock(p,val){var b=getBranch();if(!p.stock||typeof p.stock==='number')p.stock={};p.stock[b]=val;}
function getBranchSold(p){var b=getBranch();if(!p.sold)return 0;if(typeof p.sold==='number')return p.sold;return p.sold[b]!==undefined?p.sold[b]:0;}
function setBranchSold(p,val){var b=getBranch();if(!p.sold||typeof p.sold==='number')p.sold={};p.sold[b]=val;}

function onBranchChange(){
  setData('branch',getBranch());if(window.refreshAdminSidebar)window.refreshAdminSidebar();
  var info=getBranchInfo();var sa=document.getElementById('settingsAddress');if(sa)sa.value=info.address;
  showToast('Switched to '+info.name,'info');
  var activeEl=document.querySelector('.page-section.active');if(activeEl)navigateTo(activeEl.id.replace('page-',''));
}

/* ========== DATA VERSION ========== */
var DATA_VERSION=26;
function migrateData(){
  var stored=localStorage.getItem('qc_dataVersion');
  if(!stored||parseInt(stored)!==DATA_VERSION){
    for(var i=0;i<localStorage.length;i++){var key=localStorage.key(i);if(key&&key.indexOf('qc_')===0&&key!=='qc_logo')localStorage.removeItem(key);}
    localStorage.setItem('qc_dataVersion',String(DATA_VERSION));
  }
}
migrateData();

function simpleHash(str){var hash=0;for(var i=0;i<str.length;i++){var ch=str.charCodeAt(i);hash=((hash<<5)-hash)+ch;hash|=0;}return 'h_'+Math.abs(hash).toString(36);}

function isAdmin(){return currentUser&&currentUser.role==='admin';}
function isCashier(){return currentUser&&currentUser.role==='cashier';}
function isStaff(){return isAdmin()||isCashier();}
function isCustomer(){return currentUser&&currentUser.role==='customer';}
function isGuest(){return currentUser&&currentUser.role==='guest';}
function isCustomerOrGuest(){return isCustomer()||isGuest();}
function isValidEmail(value){return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(value||'').trim());}
function isValidContact(value){return /^[0-9+\-\s()]{7,20}$/.test(String(value||'').trim());}

var DEFAULT_USERS=[];
var AUTHENTICATED_STAFF=@json($authenticatedStaff ?? null);

var MANUAL_PRODUCT_IMAGE_BASE='{{ asset('images/manual-menu-products') }}';
function manualProductImage(file){return MANUAL_PRODUCT_IMAGE_BASE+'/'+file;}
function productImageFileFromName(name){
  var files={
    'bananush milktea':'bananush-milktea.png',
    'brown sugar milktea':'brown-sugar-milktea.png',
    'brulee milktea':'brulee-milktea.png',
    'classic milktea':'classic-milktea.png',
    'green apple milky fruit jam':'green-apple-milky-fruit-jam.png',
    'guava dragon fruit':'guava-dragon-fruit.png',
    'honey dew':'honey-dew.png',
    'mango milky fruit jam':'mango-milky-fruit-jam.png',
    'mulberry lime':'mulberry-lime.png',
    'oreo and cream milktea':'oreo-and-cream-milktea.png',
    'passion fruit pineapple':'passion-fruit-pineapple.png',
    'peach milky fruit jam':'peach-milky-fruit-jam.png',
    'peach puff milktea':'peach-puff-milktea.png',
    'queens cake milktea':'queens-cake-milktea.png',
    "queen's cake milktea":'queens-cake-milktea.png',
    'sakura pomelo':'sakura-pomelo.png',
    'strawberry milky fruit jam':'strawberry-milky-fruit-jam.png',
    'wintermelon cheesecake':'wintermelon-cheesecake.png',
    'wintermelon milktea':'wintermelon-milktea.png'
  };
  return files[String(name||'').toLowerCase().replace(/[^a-z0-9'\s]/g,'').replace(/\s+/g,' ').trim()]||'';
}
function manualProductImageFromName(name){
  var file=productImageFileFromName(name);
  return file?manualProductImage(file):'';
}
function fallbackProduct(id,name,category,regular,large,stock,file,desc,bestSeller){
  return {
    id:id,
    name:name,
    category:category,
    prices:{R:regular,L:large},
    stock:stockForAllBranches(stock),
    sold:stockForAllBranches(0),
    desc:desc||'',
    imageUrl:manualProductImage(file),
    icon:productIcon(category),
    bestSeller:!!bestSeller
  };
}
var FALLBACK_PRODUCTS=[
  fallbackProduct(1,'Classic Milktea','Milktea Series',39,49,25,'classic-milktea.png','Smooth black tea with creamy milk.',true),
  fallbackProduct(2,'Wintermelon Milktea','Milktea Series',39,49,25,'wintermelon-milktea.png','Sweet wintermelon milk tea blend.',true),
  fallbackProduct(3,'Brown Sugar Milktea','Milktea Series',49,59,20,'brown-sugar-milktea.png','Caramel brown sugar milk tea.'),
  fallbackProduct(4,'Brulee Milktea','Milktea Series',49,59,20,'brulee-milktea.png','Creamy brulee-style milk tea.'),
  fallbackProduct(5,'Oreo and Cream Milktea','Milktea Series',49,59,20,'oreo-and-cream-milktea.png','Cookies and cream milk tea.'),
  fallbackProduct(6,'Queens Cake Milktea','Milktea Series',49,59,20,'queens-cake-milktea.png','Signature cake-inspired milk tea.',true),
  fallbackProduct(7,'Peach Puff Milktea','Milktea Series',49,59,20,'peach-puff-milktea.png','Peachy milk tea with a soft finish.'),
  fallbackProduct(8,'Bananush Milktea','Milktea Series',49,59,20,'bananush-milktea.png','Banana milk tea blend.'),
  fallbackProduct(9,'Wintermelon Cheesecake','Milktea Series',49,59,20,'wintermelon-cheesecake.png','Wintermelon with cheesecake cream.'),
  fallbackProduct(10,'Passion Fruit Pineapple','Fruit Teas',39,49,25,'passion-fruit-pineapple.png','Tropical passion fruit and pineapple.'),
  fallbackProduct(11,'Mulberry Lime','Fruit Teas',39,49,25,'mulberry-lime.png','Bright mulberry tea with lime.'),
  fallbackProduct(12,'Sakura Pomelo','Fruit Teas',39,49,25,'sakura-pomelo.png','Floral sakura with pomelo citrus.'),
  fallbackProduct(13,'Guava Dragon Fruit','Fruit Teas',39,49,25,'guava-dragon-fruit.png','Guava and dragon fruit refresher.'),
  fallbackProduct(14,'Mango Milky Fruit Jam','Milky Fruit Jams',49,59,20,'mango-milky-fruit-jam.png','Mango fruit jam with creamy milk.'),
  fallbackProduct(15,'Strawberry Milky Fruit Jam','Milky Fruit Jams',49,59,20,'strawberry-milky-fruit-jam.png','Strawberry fruit jam with creamy milk.'),
  fallbackProduct(16,'Peach Milky Fruit Jam','Milky Fruit Jams',49,59,20,'peach-milky-fruit-jam.png','Peach fruit jam with creamy milk.'),
  fallbackProduct(17,'Green Apple Milky Fruit Jam','Milky Fruit Jams',49,59,20,'green-apple-milky-fruit-jam.png','Green apple fruit jam with creamy milk.'),
  fallbackProduct(18,'Honey Dew','Fruit Milk Shake',49,59,20,'honey-dew.png','Creamy honeydew drink.')
];
var SERVER_PRODUCTS=@json($inventoryProducts ?? []);
var DEFAULT_PRODUCTS=SERVER_PRODUCTS.length?SERVER_PRODUCTS.map(function(p){return normalizeServerProduct(p,null);}):FALLBACK_PRODUCTS;
var todayStr=new Date().toLocaleDateString();
var DEFAULT_ORDERS=[];
var STAFF_PAGE_ROUTES={
  pos:@json(route('point-of-sales.index')),
  inventory:@json(route('inventory.index')),
  reports:@json(route('reports')),
  settings:@json(route('settings')),
  profile:@json(route('profile.show'))
};

/* ========== DATA ACCESS ========== */
function clearLegacyOrderPageRecords(){
  if(localStorage.getItem('qc_orderPageRecordsCleared')==='1')return;
  ['qc_users','qc_products','qc_orders','qc_nextOrderId','qc_notifReadIds'].forEach(function(key){localStorage.removeItem(key);});
  for(var i=localStorage.length-1;i>=0;i--){var key=localStorage.key(i);if(key&&key.indexOf('qc_guest_orders_')===0)localStorage.removeItem(key);}
  localStorage.setItem('qc_orderPageRecordsCleared','1');
}
clearLegacyOrderPageRecords();

function clearAllOrderRecords(){
  if(localStorage.getItem('qc_orderRecordsCleared_v1')==='1')return;
  ['qc_orders','qc_nextOrderId','qc_notifReadIds'].forEach(function(key){localStorage.removeItem(key);});
  for(var i=localStorage.length-1;i>=0;i--){var key=localStorage.key(i);if(key&&key.indexOf('qc_guest_orders_')===0)localStorage.removeItem(key);}
  localStorage.setItem('qc_orderRecordsCleared_v1','1');
}
clearAllOrderRecords();

function getData(key,def){try{var d=localStorage.getItem('qc_'+key);if(d)return JSON.parse(d);}catch(e){localStorage.removeItem('qc_'+key);}if(def!==null&&def!==undefined){localStorage.setItem('qc_'+key,JSON.stringify(def));}return def;}
function setData(key,val){localStorage.setItem('qc_'+key,JSON.stringify(val));}

var users=getData('users',DEFAULT_USERS);
var products=getData('products',DEFAULT_PRODUCTS);
if((!Array.isArray(products)||products.length===0)&&Array.isArray(DEFAULT_PRODUCTS)&&DEFAULT_PRODUCTS.length>0){
  products=JSON.parse(JSON.stringify(DEFAULT_PRODUCTS));
  setData('products',products);
}
var orders=getData('orders',DEFAULT_ORDERS);
var cart=[];
var nextOrderId=getData('nextOrderId',1);
var currentPOSCategory='All';
var currentUser=null;
var notifReadIds=getData('notifReadIds',[]);
var _pendingLogo=null;
var deferredInstallPrompt=null;

function productIcon(category){
  var icons={'Milktea Series':'\uD83E\uDDCB','Fruit Teas':'\uD83C\uDF4B','Milky Fruit Jams':'\uD83C\uDF53','Lemonade':'\uD83C\uDF4B','Coffee & Non-Coffee':'\u2615','Fruit Milk Shake':'\uD83C\uDF53','Sticky Milk Drinks':'\uD83C\uDF4B'};
  return icons[category]||'\uD83E\uDDCB';
}

function escapeHtml(value){
  return String(value||'').replace(/[&<>"']/g,function(char){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[char];});
}

function productVisual(p){
  if(p.imageUrl)return '<img src="'+escapeHtml(p.imageUrl)+'" alt="'+escapeHtml(p.name)+'">';
  return p.icon||'\uD83E\uDDCB';
}

function productThumbHtml(item){
  var name=escapeHtml(item.name||'Product');
  if(item.imageUrl)return '<span class="item-thumb-sm"><img src="'+escapeHtml(item.imageUrl)+'" alt="'+name+'"></span>';
  return '<span class="item-thumb-sm">'+escapeHtml(item.icon||'\uD83E\uDDCB')+'</span>';
}

function orderItemVisual(item){
  var product=products.find(function(p){return Number(p.id)===Number(item.id);});
  return productThumbHtml({name:item.name,imageUrl:item.imageUrl||(product&&product.imageUrl)||'',icon:item.icon||(product&&product.icon)||'\uD83E\uDDCB'});
}

function stockForAllBranches(stock){
  var stockByBranch={};
  Object.keys(BRANCHES).forEach(function(b){stockByBranch[b]=stock;});
  return stockByBranch;
}

function normalizeServerProduct(p,existing){
  var category=p.category||'Milktea Series';
  var stock=parseInt(p.stock,10);if(isNaN(stock))stock=0;
  var sold=(existing&&existing.sold)?existing.sold:{};
  Object.keys(BRANCHES).forEach(function(b){if(sold[b]===undefined)sold[b]=0;});
  return {
    id:parseInt(p.id,10),
    name:p.name||'Unnamed Product',
    category:category,
    prices:{R:parseFloat(p.prices&&p.prices.R)||0,L:parseFloat(p.prices&&p.prices.L)||0},
    stock:stockForAllBranches(stock),
    sold:sold,
    desc:p.desc||'',
    imageUrl:manualProductImageFromName(p.name)||p.image_url||'',
    icon:(existing&&existing.icon)||productIcon(category),
    bestSeller:existing?!!existing.bestSeller:false,
    serverUpdatedAt:p.updated_at||'',
    fromInventory:true
  };
}

function syncInventoryProducts(){
  if(!Array.isArray(SERVER_PRODUCTS))SERVER_PRODUCTS=[];
  if(!Array.isArray(products))products=[];
  var needsSave=false;
  var serverProductIds={};
  SERVER_PRODUCTS.forEach(function(serverProduct){
    var serverId=parseInt(serverProduct.id,10);
    if(!isNaN(serverId))serverProductIds[serverId]=true;
  });
  var syncedProducts=products.filter(function(product){
    var productId=parseInt(product.id,10);
    return product.fromInventory!==true||serverProductIds[productId]===true;
  });
  if(syncedProducts.length!==products.length){
    products=syncedProducts;
    var availableProductIds={};
    products.forEach(function(product){availableProductIds[parseInt(product.id,10)]=true;});
    cart=cart.filter(function(item){return availableProductIds[parseInt(item.id,10)]===true;});
    needsSave=true;
  }
  SERVER_PRODUCTS.forEach(function(serverProduct){
    var serverId=parseInt(serverProduct.id,10);
    if(isNaN(serverId))return;
    var idx=products.findIndex(function(p){return parseInt(p.id,10)===serverId;});
    if(idx===-1){
      products.push(normalizeServerProduct(serverProduct,null));
      needsSave=true;
      return;
    }
    var existing=products[idx];
    if(existing.serverUpdatedAt!==serverProduct.updated_at||existing.imageUrl!==(manualProductImageFromName(serverProduct.name)||serverProduct.image_url||'')||existing.fromInventory!==true){
      products[idx]=normalizeServerProduct(serverProduct,existing);
      needsSave=true;
    }
  });
  if(needsSave)setData('products',products);
}
syncInventoryProducts();

function removeSeedOrders(){
  if(localStorage.getItem('qc_seedOrdersCleared')==='1')return;
  if(Array.isArray(orders)){
    orders=orders.filter(function(order){return Number(order.id)<1001||Number(order.id)>1008;});
    setData('orders',orders);
  }
  localStorage.setItem('qc_seedOrdersCleared','1');
}
removeSeedOrders();

function validateProducts(){
  products=JSON.parse(JSON.stringify(DEFAULT_PRODUCTS));
  setData('products',products);
  if(!Array.isArray(products)||products.length===0)return;
  var branches=Object.keys(BRANCHES);var needsSave=false;
  products.forEach(function(p){
    if(typeof p.stock==='number'){var old=p.stock;p.stock={};branches.forEach(function(b){p.stock[b]=old;});needsSave=true;}
    else if(!p.stock){p.stock={};branches.forEach(function(b){p.stock[b]=0;});needsSave=true;}
    else{branches.forEach(function(b){if(p.stock[b]===undefined){p.stock[b]=0;needsSave=true;}});}
    if(typeof p.sold==='number'){var olds=p.sold;p.sold={};branches.forEach(function(b){p.sold[b]=olds;});needsSave=true;}
    else if(!p.sold){p.sold={};branches.forEach(function(b){p.sold[b]=0;});needsSave=true;}
    else{branches.forEach(function(b){if(p.sold[b]===undefined){p.sold[b]=0;needsSave=true;}});}
  });
  if(needsSave)setData('products',products);
}
validateProducts();

function migrateOrders(){
  var needsSave=false;
  orders.forEach(function(o){
    if(!PAYMENT_METHODS[o.payment]){o.payment='Cash';needsSave=true;}
    if(o.paymentStatus===undefined){o.paymentStatus=(o.status==='completed'||o.status==='cancelled')?'paid':'pending';needsSave=true;}
    if(!o.paidBy)o.paidBy=o.paymentStatus==='paid'?'admin':'';
    if(!o.paidAt)o.paidAt=o.paymentStatus==='paid'?o.time:'';
  });
  if(needsSave)setData('orders',orders);
}
migrateOrders();

function getSession(){try{var s=localStorage.getItem('qc_session');if(s)return JSON.parse(s);}catch(e){}return null;}
function setSession(user){localStorage.setItem('qc_session',JSON.stringify({id:user.id,username:user.username||'',role:user.role,fullName:user.fullName,since:user.since||'',email:user.email||'',contactNumber:user.contactNumber||''}));}
function clearSession(){localStorage.removeItem('qc_session');}
function userFromSession(session){
  if(!session||!session.role)return null;
  if(session.role==='guest')return {id:session.id,username:'',fullName:session.fullName,role:'guest',since:session.since||new Date().toISOString().split('T')[0],password:'',email:session.email||'',contactNumber:session.contactNumber||'',isGuest:true};
  for(var i=0;i<users.length;i++){
    if(String(users[i].id)===String(session.id)||String(users[i].username||'')===String(session.username||''))return users[i];
  }
  if(session.role==='admin'||session.role==='cashier'){
    return {id:session.id,username:session.username||'',fullName:session.fullName||session.username||'Staff',role:session.role,since:session.since||new Date().toISOString().split('T')[0],password:'',email:session.email||'',contactNumber:session.contactNumber||''};
  }
  if(session.role==='customer'){
    return {id:session.id,username:session.username||'',fullName:session.fullName||session.username||'Customer',role:'customer',since:session.since||new Date().toISOString().split('T')[0],password:'',email:session.email||'',contactNumber:session.contactNumber||''};
  }
  return null;
}

/* ========== LOGO MANAGEMENT ========== */
function getCustomLogo(){try{var d=localStorage.getItem('qc_logo');if(d)return d;}catch(e){}return null;}
function getLogoSrc(){var cl=getCustomLogo();return cl||LOGO_URL;}

function updateAllLogos(){
  var src=getLogoSrc();
  var loginCrown=document.getElementById('loginCrown');
  if(loginCrown){loginCrown.innerHTML='<img src="'+src+'" alt="Logo">';}
  var sidebarCrown=document.getElementById('sidebarCrown');
  if(sidebarCrown){sidebarCrown.innerHTML='<img src="'+src+'" alt="Logo">';}
  var chatAvatar=document.getElementById('chatBotAvatar');
  if(chatAvatar){chatAvatar.innerHTML='<img src="'+src+'" alt="Logo">';}
  updateLogoPreview();
}

function updateLogoPreview(){
  var src=getLogoSrc();
  var preview=document.getElementById('logoPreviewCurrent');
  var nameEl=document.getElementById('logoPreviewName');
  if(preview){preview.innerHTML='<img src="'+src+'" alt="Logo">';}
  if(nameEl){nameEl.textContent=getCustomLogo()?'Custom Logo':"Queen's Cup Brand Logo";}
}

function previewLogoUpload(event){
  var file=event.target.files[0];
  if(!file)return;
  if(file.size>2*1024*1024){showToast('Image must be under 2MB','error');event.target.value='';return;}
  if(!file.type.match(/^image\/(png|jpeg|svg\+xml|webp|gif)$/)){showToast('Please use PNG, JPG, SVG, or WEBP','error');event.target.value='';return;}
  var reader=new FileReader();
  reader.onload=function(e){
    var img=new Image();
    img.onload=function(){
      var canvas=document.createElement('canvas');
      var size=160;canvas.width=size;canvas.height=size;
      var ctx=canvas.getContext('2d');
      ctx.drawImage(img,0,0,size,size);
      var resized=canvas.toDataURL('image/png',0.92);
      _pendingLogo=resized;
      var newPreview=document.getElementById('logoNewPreview');
      var newThumb=document.getElementById('logoNewThumb');
      var newName=document.getElementById('logoNewFileName');
      var newSize=document.getElementById('logoNewFileSize');
      if(newPreview)newPreview.style.display='block';
      if(newThumb)newThumb.innerHTML='<img src="'+resized+'" style="width:100%;height:100%;object-fit:cover;border-radius:50%" alt="Preview">';
      if(newName)newName.textContent=file.name;
      if(newSize)newSize.textContent=(file.size/1024).toFixed(1)+' KB — Resized to '+size+'\u00d7'+size;
      var saveBtn=document.getElementById('logoSaveBtn');
      if(saveBtn)saveBtn.disabled=false;
    };
    img.src=e.target.result;
  };
  reader.readAsDataURL(file);
}

function saveLogo(){
  if(!_pendingLogo){showToast('Please select an image first','warning');return;}
  localStorage.setItem('qc_logo',_pendingLogo);
  _pendingLogo=null;
  var fileInput=document.getElementById('logoFileInput');if(fileInput)fileInput.value='';
  var newPreview=document.getElementById('logoNewPreview');if(newPreview)newPreview.style.display='none';
  var saveBtn=document.getElementById('logoSaveBtn');if(saveBtn)saveBtn.disabled=true;
  updateAllLogos();
  showToast('Logo updated successfully!','success');
}

function resetLogo(){
  localStorage.removeItem('qc_logo');
  _pendingLogo=null;
  var fileInput=document.getElementById('logoFileInput');if(fileInput)fileInput.value='';
  var newPreview=document.getElementById('logoNewPreview');if(newPreview)newPreview.style.display='none';
  var saveBtn=document.getElementById('logoSaveBtn');if(saveBtn)saveBtn.disabled=true;
  updateAllLogos();
  showToast('Logo reset to default','info');
}

function initLogoDragDrop(){
  var zone=document.getElementById('logoUploadZone');
  if(!zone)return;
  zone.addEventListener('dragover',function(e){e.preventDefault();zone.classList.add('dragover');});
  zone.addEventListener('dragleave',function(e){e.preventDefault();zone.classList.remove('dragover');});
  zone.addEventListener('drop',function(e){
    e.preventDefault();zone.classList.remove('dragover');
    var files=e.dataTransfer.files;
    if(files.length>0){var fi=document.getElementById('logoFileInput');fi.files=files;previewLogoUpload({target:fi});}
  });
}

/* ========== NOTIFICATIONS ========== */
var notifPanelOpen=false;

function generateNotificationsLegacy(){
  var notifs=[];var br=getBranch();
  if(isStaff()){
    orders.forEach(function(o){
      if(o.branch===br&&o.paymentStatus==='pending')
        notifs.push({id:'cp_'+o.id,type:'cash-pending',icon:'fa-money-bill-wave',msg:'Order <strong>#'+o.id+'</strong> — Cash payment pending (<strong>\u20B1'+o.total.toFixed(2)+'</strong>) from '+o.customer,time:o.time,action:'order_'+o.id});
    });
    orders.forEach(function(o){
      if(o.branch===br&&o.status==='pending')
        notifs.push({id:'np_'+o.id,type:'new-order',icon:'fa-receipt',msg:'New order <strong>#'+o.id+'</strong> from '+o.customer+' — \u20B1'+o.total.toFixed(2),time:o.time,action:'order_'+o.id});
    });
    orders.forEach(function(o){
      if(o.branch===br&&o.status==='preparing')
        notifs.push({id:'pr_'+o.id,type:'preparing',icon:'fa-blender',msg:'Order <strong>#'+o.id+'</strong> is being prepared',time:o.time,action:'order_'+o.id});
    });
    var lt=parseInt(getData('lowStockThreshold',10));
    products.forEach(function(p){
      var stk=getBranchStock(p);
      if(stk===0)notifs.push({id:'os_'+p.id+'_'+br,type:'out-of-stock',icon:'fa-times-circle',msg:'<strong>'+p.name+'</strong> is out of stock!',time:'Restock needed',action:'inventory'});
      else if(stk<=lt&&stk>0)notifs.push({id:'ls_'+p.id+'_'+br,type:'low-stock',icon:'fa-exclamation-triangle',msg:'<strong>'+p.name+'</strong> is running low ('+stk+' left)',time:'Check inventory',action:'inventory'});
    });
  }else{
    var myIds=getData('guest_orders_'+currentUser.fullName,[]);
    orders.forEach(function(o){
      if(myIds.indexOf(o.id)!==-1||o.customer===currentUser.fullName){
        if(o.status==='pending')notifs.push({id:'my_p_'+o.id,type:'new-order',icon:'fa-clock',msg:'Your order <strong>#'+o.id+'</strong> is pending',time:o.time,action:'myorders',orderId:o.id});
        else if(o.status==='preparing')notifs.push({id:'my_pr_'+o.id,type:'preparing',icon:'fa-blender',msg:'Your order <strong>#'+o.id+'</strong> is being prepared',time:o.time,action:'myorders',orderId:o.id});
        else if(o.status==='ready')notifs.push({id:'my_s_'+o.id,type:'completed',icon:'fa-concierge-bell',msg:'Your order is ready to pick up',time:o.time,action:'myorders',orderId:o.id});
        else if(o.status==='completed')notifs.push({id:'my_c_'+o.id,type:'completed',icon:'fa-check-circle',msg:'Your order <strong>#'+o.id+'</strong> is completed. Thank you!',time:o.time,action:'myorders',orderId:o.id});
        if(o.payment==='Cash'&&o.paymentStatus==='pending')notifs.push({id:'my_cp_'+o.id,type:'cash-pending',icon:'fa-money-bill-wave',msg:'Order <strong>#'+o.id+'</strong> — Please pay \u20B1'+o.total.toFixed(2)+' at the counter',time:o.time,action:'myorders'});
        else if(o.payment==='Cash'&&o.paymentStatus==='paid')notifs.push({id:'my_pd_'+o.id,type:'completed',icon:'fa-check-circle',msg:'Payment for order <strong>#'+o.id+'</strong> confirmed!',time:o.paidAt||o.time,action:'myorders'});
      }
    });
  }
  return notifs;
}

function generateNotifications(){
  var notifs=[];var br=getBranch();
  if(isStaff()){
    orders.forEach(function(o){
      if(o.branch===br&&o.paymentStatus==='pending'){var p=paymentInfo(o.payment);notifs.push({id:'cp_'+o.id,type:'cash-pending',icon:p.icon,msg:'Order <strong>#'+o.id+'</strong> - '+p.pendingLabel+' (<strong>\u20B1'+o.total.toFixed(2)+'</strong>) from '+o.customer,time:o.time,action:'order_'+o.id});}
    });
    orders.forEach(function(o){
      if(o.branch===br&&o.status==='pending')notifs.push({id:'np_'+o.id,type:'new-order',icon:'fa-receipt',msg:'New order <strong>#'+o.id+'</strong> from '+o.customer+' - \u20B1'+o.total.toFixed(2),time:o.time,action:'order_'+o.id});
    });
    orders.forEach(function(o){
      if(o.branch===br&&o.status==='preparing')notifs.push({id:'pr_'+o.id,type:'preparing',icon:'fa-blender',msg:'Order <strong>#'+o.id+'</strong> is being prepared',time:o.time,action:'order_'+o.id});
    });
    var lt=parseInt(getData('lowStockThreshold',10));
    products.forEach(function(p){
      var stk=getBranchStock(p);
      if(stk===0)notifs.push({id:'os_'+p.id+'_'+br,type:'out-of-stock',icon:'fa-times-circle',msg:'<strong>'+p.name+'</strong> is out of stock!',time:'Restock needed',action:'inventory'});
      else if(stk<=lt&&stk>0)notifs.push({id:'ls_'+p.id+'_'+br,type:'low-stock',icon:'fa-exclamation-triangle',msg:'<strong>'+p.name+'</strong> is running low ('+stk+' left)',time:'Check inventory',action:'inventory'});
    });
  }else{
    var myIds=getData('guest_orders_'+currentUser.fullName,[]);
    orders.forEach(function(o){
      if(myIds.indexOf(o.id)!==-1||o.customer===currentUser.fullName){
        if(o.status==='pending')notifs.push({id:'my_p_'+o.id,type:'new-order',icon:'fa-clock',msg:'Your order <strong>#'+o.id+'</strong> is pending',time:o.time,action:'myorders',orderId:o.id});
        else if(o.status==='preparing')notifs.push({id:'my_pr_'+o.id,type:'preparing',icon:'fa-blender',msg:'Your order <strong>#'+o.id+'</strong> is being prepared',time:o.time,action:'myorders',orderId:o.id});
        else if(o.status==='ready')notifs.push({id:'my_s_'+o.id,type:'completed',icon:'fa-concierge-bell',msg:'Your order is ready to pick up',time:o.time,action:'myorders',orderId:o.id});
        else if(o.status==='completed')notifs.push({id:'my_c_'+o.id,type:'completed',icon:'fa-check-circle',msg:'Your order <strong>#'+o.id+'</strong> is completed. Thank you!',time:o.time,action:'myorders',orderId:o.id});
        if(o.paymentStatus==='pending'){var pi=paymentInfo(o.payment);notifs.push({id:'my_cp_'+o.id,type:'cash-pending',icon:pi.icon,msg:'Order <strong>#'+o.id+'</strong> - '+pi.pendingLabel+' for \u20B1'+o.total.toFixed(2),time:o.time,action:'myorders',orderId:o.id});}
        else if(o.paymentStatus==='paid')notifs.push({id:'my_pd_'+o.id,type:'completed',icon:'fa-check-circle',msg:'Payment for order <strong>#'+o.id+'</strong> confirmed!',time:o.paidAt||o.time,action:'myorders',orderId:o.id});
      }
    });
  }
  return notifs;
}

function getUnreadCount(){var notifs=generateNotifications();var unread=0;notifs.forEach(function(n){if(notifReadIds.indexOf(n.id)===-1)unread++;});return unread;}
function updateNotifBadge(){var badge=document.getElementById('notifBadge');var count=getUnreadCount();if(badge){badge.textContent=count>0?count:'';}}

function renderNotifPanel(){
  var notifs=generateNotifications();var list=document.getElementById('notifList');
  if(notifs.length===0){list.innerHTML='<div class="notif-empty"><i class="fas fa-bell-slash"></i><p>No notifications right now</p></div>';return;}
  list.innerHTML=notifs.map(function(n){
    var isUnread=notifReadIds.indexOf(n.id)===-1;
    var progress='';
    if(isCustomerOrGuest()&&n.orderId){
      var order=orders.find(function(o){return o.id===n.orderId;});
      if(order)progress='<div class="notif-progress">'+renderCustomerProgress(order)+'</div>';
    }
    return '<div class="notif-item'+(isUnread?' unread':'')+'" onclick="handleNotifClick(\''+n.id+'\',\''+n.action+'\')"><div class="notif-icon '+n.type+'"><i class="fas '+n.icon+'"></i></div><div class="notif-body"><div class="notif-msg">'+n.msg+'</div><div class="notif-time">'+n.time+'</div>'+progress+'</div></div>';
  }).join('');
}

function handleNotifClick(notifId,action){
  if(notifReadIds.indexOf(notifId)===-1){notifReadIds.push(notifId);setData('notifReadIds',notifReadIds);}
  updateNotifBadge();
  if(action==='inventory'){closeNotifPanel();navigateTo('inventory');}
  else if(action==='myorders'){closeNotifPanel();navigateTo('orders');}
  else if(action.indexOf('order_')===0){var oid=parseInt(action.replace('order_',''));closeNotifPanel();if(currentPageId!=='orders')navigateTo('orders');setTimeout(function(){viewOrderDetail(oid);},300);}
}

function markAllNotifRead(){var notifs=generateNotifications();notifs.forEach(function(n){if(notifReadIds.indexOf(n.id)===-1)notifReadIds.push(n.id);});setData('notifReadIds',notifReadIds);renderNotifPanel();updateNotifBadge();showToast('All notifications marked as read','info');}

function toggleNotifPanel(){notifPanelOpen=!notifPanelOpen;var panel=document.getElementById('notifPanel');if(notifPanelOpen){renderNotifPanel();panel.classList.add('open');}else{panel.classList.remove('open');}}
function closeNotifPanel(){notifPanelOpen=false;var panel=document.getElementById('notifPanel');if(panel)panel.classList.remove('open');}

document.addEventListener('click',function(e){var wrapper=document.querySelector('.notif-wrapper');if(notifPanelOpen&&wrapper&&!wrapper.contains(e.target)){closeNotifPanel();}});


/* ========== CUSTOMER RESERVATIONS ==========
 *
 * Customers do not buy here, they reserve. Buying happens at the till in the
 * admin panel. So the customer checkout sends the basket to the reservation
 * API, which prices it, applies the per-cup take-out fee and hands back a
 * reference code. Payment is taken in person at the counter.
 */
var TAKEOUT_FEE_PER_CUP = {{ $takeoutFeePerCup ?? 5 }};
var RESERVE_URL = @json(url('/api/v1/reservations'));

function reservedReferences() {
  var stored = getData('my_reservations', []);
  return Array.isArray(stored) ? stored : [];
}

function rememberReference(reference) {
  var all = reservedReferences();
  if (all.indexOf(reference) === -1) all.unshift(reference);
  setData('my_reservations', all.slice(0, 40));
}

/** Cups in the basket. The surcharge is per cup, not per line. */
function cartCupCount() {
  return cart.reduce(function (n, i) { return n + i.qty; }, 0);
}

function checkoutIsTakeOut() {
  var select = document.getElementById('checkoutType');
  return !!select && select.value === 'Take Out';
}

/** Take-out surcharge for the current basket, or zero for dine in. */
function checkoutTakeoutFee() {
  if (!isCustomerOrGuest()) return 0;
  return checkoutIsTakeOut() ? TAKEOUT_FEE_PER_CUP * cartCupCount() : 0;
}

function submitReservation() {
  var button = document.getElementById('reserveConfirmBtn');
  if (button) { button.disabled = true; button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Reserving...'; }

  fetch(RESERVE_URL, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
    body: JSON.stringify({
      service_type: checkoutIsTakeOut() ? 'take_out' : 'dine_in',
      customer_name: currentUser.fullName,
      customer_email: currentUser.email || null,
      customer_contact: currentUser.contactNumber || null,
      branch: getBranch(),
      source: 'web',
      items: cart.map(function (i) {
        return { inventory_id: i.id, size: i.size === 'L' ? 'large' : 'regular', quantity: i.qty };
      })
    })
  })
    .then(function (response) {
      return response.json().then(function (payload) {
        if (!response.ok) {
          var message = payload.message || 'We could not save that reservation.';
          if (payload.errors) {
            var first = Object.keys(payload.errors)[0];
            if (first) message = payload.errors[first][0];
          }
          throw new Error(message);
        }
        return payload;
      });
    })
    .then(function (reservation) {
      rememberReference(reservation.reference);
      closeModal('checkoutModal');
      cart = [];
      renderCart();
      renderPOS();
      showReservationConfirmed(reservation);
      navigateTo('orders');
    })
    .catch(function (error) { showToast(error.message, 'error'); })
    .finally(function () {
      if (button) { button.disabled = false; button.innerHTML = '<i class="fas fa-check"></i> Confirm reservation'; }
    });
}

function showReservationConfirmed(reservation) {
  showToast('Reserved! Show ' + reservation.reference + ' at the counter.', 'success');
}

/* ---------- customer reservation tracking ---------- */

var myReservations = [];

function loadMyReservations() {
  var references = reservedReferences();
  var list = document.getElementById('customerReservationList');
  if (!list) return;

  if (!references.length) {
    myReservations = [];
    renderMyReservations();
    return;
  }

  Promise.all(references.map(function (reference) {
    return fetch(@json(url('/api/v1/reservations')) + '/' + encodeURIComponent(reference), {
      headers: { 'Accept': 'application/json' }
    })
      .then(function (r) { return r.ok ? r.json() : null; })
      .catch(function () { return null; });
  })).then(function (results) {
    myReservations = results.filter(Boolean);
    renderMyReservations();
  });
}

function reservationStatusBadge(status) {
  var map = {
    pending: ['badge-warning', 'fa-clock', 'Reservation received'],
    preparing: ['badge-info', 'fa-blender', 'Being prepared'],
    ready: ['badge-success', 'fa-bell-concierge', 'Ready for pick up'],
    completed: ['badge-success', 'fa-check', 'Picked up'],
    cancelled: ['badge-warning', 'fa-times', 'Cancelled']
  };
  var entry = map[status] || map.pending;
  return '<span class="badge ' + entry[0] + '"><i class="fas ' + entry[1] + '"></i> ' + entry[2] + '</span>';
}

/**
 * Finished reservations: picked up or cancelled. The active ones stay on
 * the reservations page so the two do not crowd each other out.
 */
function renderReservationHistory() {
  var list = document.getElementById('customerHistoryList');
  if (!list) return;

  loadMyReservations().then(function () {
    var done = myReservations.filter(function (r) {
      return r.status === 'completed' || r.status === 'cancelled';
    });

    if (!done.length) {
      list.innerHTML = '<div class="empty-state"><i class="fas fa-clock-rotate-left"></i>' +
        '<h3>Nothing here yet</h3><p>Reservations you have picked up will show up here.</p></div>';
      return;
    }

    list.innerHTML = done.map(renderReservationCard).join('');
  });
}

/** One reservation card, shared by the active list and the history page. */
function renderReservationCard(r) {
  var items = r.items.map(function (i) {
    return escapeHtml(i.quantity + '× ' + i.name + ' (' + i.size_label + ')');
  }).join('<br>');

  var fee = Number(r.takeout_fee) > 0
    ? '<div style="font-size:11px;color:var(--fg-muted)">incl. ₱' + Number(r.takeout_fee).toFixed(2) + ' take-out cups</div>'
    : '';

  return '<div class="card" style="margin-bottom:12px"><div class="card-body">' +
    '<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:10px">' +
      '<div><div style="font-size:18px;font-weight:900;color:var(--gold-light)">' + escapeHtml(r.reference) + '</div>' +
      '<div style="font-size:11px;color:var(--fg-muted)">' + (r.service_type === 'take_out' ? 'Take out' : 'Dine in') +
      ' · ' + r.cup_count + (r.cup_count === 1 ? ' cup' : ' cups') + '</div></div>' +
      reservationStatusBadge(r.status) +
    '</div>' +
    '<div style="font-size:12px;margin-bottom:8px">' + items + '</div>' + fee +
    '<div style="display:flex;justify-content:space-between;border-top:1px solid var(--border);padding-top:8px;font-weight:800">' +
      '<span>Total</span><span>₱' + Number(r.total).toFixed(2) + '</span></div>' +
    '<div style="font-size:11px;color:var(--fg-muted);margin-top:4px">' +
      (r.payment_status === 'paid' ? 'Paid at the counter' : 'Pay at the counter on pick up') + '</div>' +
  '</div></div>';
}


/**
 * Keeps the customer navigation counters honest.
 *
 * These used to be derived from the old local-storage order list, which a
 * customer no longer writes to, so Active sat at a permanent red zero and
 * History had no counter at all. Both now come from the reservations the
 * server actually returned, and a zero simply hides.
 */
function updateCustomerNavBadges() {
  if (!isCustomerOrGuest()) return;

  var active = 0;
  var past = 0;

  (myReservations || []).forEach(function (r) {
    if (r.status === 'completed' || r.status === 'cancelled') past++;
    else active++;
  });

  var setBadge = function (id, count) {
    var el = document.getElementById(id);
    if (!el) return;
    el.textContent = String(count);
    el.style.display = count > 0 ? 'inline-flex' : 'none';
  };

  setBadge('pendingOrdersBadge', active);
  setBadge('historyCountBadge', past);

  // The phone's bottom bar carries the same active count.
  var dot = document.getElementById('mobileActiveDot');
  if (dot) dot.style.display = active > 0 ? 'grid' : 'none';
  if (dot) dot.textContent = String(active);
}

function renderMyReservations() {
  updateCustomerNavBadges();
  var list = document.getElementById('customerReservationList');
  if (!list) return;

  // Finished ones live on the history page.
  var active = myReservations.filter(function (r) {
    return r.status !== 'completed' && r.status !== 'cancelled';
  });

  if (!active.length) {
    list.innerHTML = '<div class="empty-state"><i class="fas fa-receipt"></i><h3>No active reservations</h3>' +
      '<p>Reserve from the menu and your code will appear here.</p></div>';
    return;
  }

  list.innerHTML = active.map(renderReservationCard).join('');
}

/* ========== COUNT CASH PENDING ========== */
function countCashPending(){var br=getBranch();return orders.filter(function(o){return o.branch===br&&o.paymentStatus==='pending';}).length;}
function countCashPendingAmount(){var br=getBranch();return orders.filter(function(o){return o.branch===br&&o.paymentStatus==='pending';}).reduce(function(s,o){return s+o.total;},0);}

function updateCashPendingUI(){
  var c=countCashPending();var a=countCashPendingAmount();
  var topEl=document.getElementById('topCashPending');var topCount=document.getElementById('topCashPendingCount');
  if(topEl&&topCount){if(c>0&&isStaff()){topEl.style.display='inline-flex';topCount.textContent=c;}else{topEl.style.display='none';}}
  var br=getBranch();
  // Customers are counted from their reservations instead; see
  // updateCustomerNavBadges().
  if(isCustomerOrGuest()){updateCustomerNavBadges();}
  var pb=isCustomerOrGuest()?null:document.getElementById('pendingOrdersBadge');
  if(pb){
    if(isGuest()){var myIds=getData('guest_orders_'+currentUser.fullName,[]);pb.textContent=orders.filter(function(o){return (myIds.indexOf(o.id)!==-1||o.customer===currentUser.fullName)&&(o.status==='pending'||o.status==='preparing');}).length;}
    else{pb.textContent=orders.filter(function(o){return o.branch===br&&(o.status==='pending'||o.status==='preparing');}).length;}
  }
  var cpb=document.getElementById('cashPendingSidebarBadge');
  if(cpb){cpb.textContent=c;if(c>0)cpb.style.display='inline';else cpb.style.display='none';}
  var banner=document.getElementById('cashPendingBanner');var bannerTitle=document.getElementById('cashPendingBannerTitle');var bannerDesc=document.getElementById('cashPendingBannerDesc');
  if(banner&&isStaff()){if(c>0){banner.style.display='flex';bannerTitle.textContent=c+' Payment Pending Order'+(c>1?'s':'');bannerDesc.textContent='\u20B1'+a.toFixed(2)+' awaiting cash or QR confirmation.';}else{banner.style.display='none';}}
  else if(banner){banner.style.display='none';}
  updateNotifBadge();
}

/* ========== GUEST / LOGIN ========== */
/* ========== CUSTOMER ACCOUNTS ==========
 *
 * Customers register with a password and confirm their address with a code
 * before they can reserve, so every order carries a contact that reaches
 * someone. The server owns all of it; this only drives the forms.
 */
var pendingVerifyEmail = '';

function switchLoginTab(tab) {
  var forms = { signin: 'signinForm', register: 'registerForm', verify: 'verifyForm' };

  document.querySelectorAll('.login-form').forEach(function (f) { f.classList.remove('active'); });
  document.querySelectorAll('.login-tab').forEach(function (t) { t.classList.remove('active'); });

  var form = document.getElementById(forms[tab] || 'signinForm');
  if (form) form.classList.add('active');

  // Verifying is a step inside registering, so keep that tab lit.
  var tabKey = tab === 'verify' ? 'register' : tab;
  var button = document.querySelector('.login-tab[data-login-tab="' + tabKey + '"]');
  if (button) button.classList.add('active');

  var error = document.getElementById('loginError');
  if (error) error.classList.remove('show');
}

function customerPost(url, body) {
  return fetch(url, {
    method: 'POST',
    credentials: 'same-origin',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': csrfToken()
    },
    body: JSON.stringify(body)
  }).then(function (response) {
    return response.json().then(function (payload) {
      return { ok: response.ok, status: response.status, payload: payload };
    });
  });
}

/** Pulls the first message out of a Laravel validation response. */
function firstError(payload, fallback) {
  if (payload && payload.errors) {
    var key = Object.keys(payload.errors)[0];
    if (key) return payload.errors[key][0];
  }
  return (payload && payload.message) || fallback;
}

function busy(id, on, label) {
  var button = document.getElementById(id);
  if (!button) return;
  button.disabled = on;
  if (on) {
    button.dataset.label = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + label;
  } else if (button.dataset.label) {
    button.innerHTML = button.dataset.label;
  }
}

function handleCustomerRegister() {
  var name = (document.getElementById('regName').value || '').trim();
  var email = (document.getElementById('regEmail').value || '').trim();
  var contact = (document.getElementById('regContact').value || '').trim();
  var password = document.getElementById('regPassword').value || '';
  var confirm = document.getElementById('regPasswordConfirm').value || '';

  if (name.length < 2) return showLoginError('Please enter your full name.');
  if (!email) return showLoginError('Please enter your email address.');
  if (password.length < 8) return showLoginError('Your password needs at least 8 characters.');
  if (password !== confirm) return showLoginError('Those passwords do not match.');

  busy('registerBtn', true, 'Creating...');

  customerPost(@json(route('customer.register')), {
    name: name, email: email, contact_number: contact || null,
    password: password, password_confirmation: confirm
  }).then(function (result) {
    busy('registerBtn', false);

    if (!result.ok) return showLoginError(firstError(result.payload, 'We could not create that account.'));

    pendingVerifyEmail = email;
    document.getElementById('verifyBlurb').textContent =
      'We sent a 6 digit code to ' + email + '. Enter it below to finish.';
    switchLoginTab('verify');
    showToast('Check your email for the code', 'info');
  }).catch(function () {
    busy('registerBtn', false);
    showLoginError('We could not reach the server. Please try again.');
  });
}

function handleCustomerVerify() {
  var code = (document.getElementById('verifyCode').value || '').trim();
  if (code.length !== 6) return showLoginError('Enter the 6 digit code from your email.');

  busy('verifyBtn', true, 'Checking...');

  customerPost(@json(route('customer.verify')), { email: pendingVerifyEmail, code: code })
    .then(function (result) {
      busy('verifyBtn', false);

      if (!result.ok) return showLoginError(firstError(result.payload, 'That code was not accepted.'));

      startCustomerSession(result.payload.user);
    }).catch(function () {
      busy('verifyBtn', false);
      showLoginError('We could not reach the server. Please try again.');
    });
}

function handleResendCode() {
  if (!pendingVerifyEmail) return showLoginError('Enter your details again to get a new code.');

  busy('resendCodeBtn', true, 'Sending...');

  customerPost(@json(route('customer.resend')), { email: pendingVerifyEmail })
    .then(function () {
      busy('resendCodeBtn', false);
      showToast('A new code is on its way', 'success');
    }).catch(function () {
      busy('resendCodeBtn', false);
      showLoginError('We could not reach the server. Please try again.');
    });
}

function handleCustomerSignIn() {
  var email = (document.getElementById('signinEmail').value || '').trim();
  var password = document.getElementById('signinPassword').value || '';

  if (!email || !password) return showLoginError('Enter your email and password.');

  busy('signinBtn', true, 'Signing in...');

  customerPost(@json(route('customer.login')), { email: email, password: password })
    .then(function (result) {
      busy('signinBtn', false);

      // An unverified account is sent back to the code step with a fresh code.
      if (result.status === 409) {
        pendingVerifyEmail = email;
        document.getElementById('verifyBlurb').textContent =
          'Confirm your email first. We sent a new code to ' + email + '.';
        switchLoginTab('verify');
        return;
      }

      if (!result.ok) return showLoginError(firstError(result.payload, 'We could not sign you in.'));

      startCustomerSession(result.payload.user);
    }).catch(function () {
      busy('signinBtn', false);
      showLoginError('We could not reach the server. Please try again.');
    });
}

function startCustomerSession(user) {
  currentUser = {
    id: user.id,
    username: user.email,
    fullName: user.fullName,
    email: user.email,
    contactNumber: user.contactNumber || '',
    role: 'customer',
    since: new Date().toISOString().split('T')[0]
  };

  setSession(currentUser);
  enterApp();
  showToast('Welcome, ' + user.fullName.split(' ')[0] + '!', 'success');
}
function togglePw(id,btn){var inp=document.getElementById(id);var ic=btn.querySelector('i');if(inp.type==='password'){inp.type='text';ic.className='fas fa-eye-slash';}else{inp.type='password';ic.className='fas fa-eye';}}
function showLoginError(msg){var el=document.getElementById('loginError');el.textContent=msg;el.classList.add('show');setTimeout(function(){el.classList.remove('show');},4000);}

function csrfToken(){var el=document.querySelector('meta[name="csrf-token"]');return el?el.getAttribute('content'):'';}
function handleGuestEntry(){
  // Guest ordering was replaced by real accounts.
  switchLoginTab('register');
}

function handleLogin(){
  var u=document.getElementById('loginUsername').value.trim();var p=document.getElementById('loginPassword').value;
  if(!u||!p){showLoginError('Please enter username and password.');return;}
  var h=simpleHash(p);var user=null;
  for(var i=0;i<users.length;i++){if(users[i].username===u&&users[i].password===h){user=users[i];break;}}
  if(!user){showLoginError('Invalid username or password.');return;}
  currentUser=user;setSession(user);enterApp();
}

function handleLogout(){
  var staffSession=isStaff();
  currentUser=null;clearSession();cart=[];

  // Both roles end up on the public page. The customer branch used to skip
  // the server entirely, which left customer_user_id sitting in the session
  // even though the browser thought it had signed out.
  var endpoint=staffSession?@json(route('staff.logout')):@json(route('customer.logout'));

  fetch(endpoint,{method:'POST',credentials:'same-origin',headers:{'X-CSRF-TOKEN':@json(csrf_token())}})
    .finally(function(){window.location.href=@json(url('/'));});
}


/* ========== ADMIN CREATE STAFF ========== */
function createStaffAccount(){
  var fn=document.getElementById('staffName').value.trim();var email=document.getElementById('staffEmail').value.trim();
  var pw=document.getElementById('staffPassword').value;var rl=document.getElementById('staffRole').value;
  if(!fn){showToast('Please enter full name','error');return;}
  if(!email||!isValidEmail(email)){showToast('Please enter a valid email','error');return;}
  if(pw.length<6){showToast('Password must be at least 6 characters','error');return;}
  fetch('{{ url('/staff') }}',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrfToken()},body:JSON.stringify({name:fn,email:email,password:pw,role:rl})})
    .then(function(r){return r.json().then(function(j){if(!r.ok)throw j;return j;});})
    .then(function(res){
      document.getElementById('staffName').value='';document.getElementById('staffEmail').value='';document.getElementById('staffPassword').value='';
      showToast((res&&res.message)||'Staff account created','success');
    })
    .catch(function(err){
      var msg=(err&&err.message)||'Unable to create staff account.';
      if(err&&err.errors){var keys=Object.keys(err.errors);if(keys.length&&err.errors[keys[0]][0])msg=err.errors[keys[0]][0];}
      showToast(msg,'error');
    });
}

/* ========== ENTER APP ========== */
function enterApp(){
  document.getElementById('loginPage').classList.add('hidden');
  document.body.classList.toggle('customer-mobile',isCustomerOrGuest());
  document.getElementById('appLayout').style.display='flex';
  document.getElementById('chatbotContainer').style.display=isCustomerOrGuest()?'block':'none';
  var chatBtn=document.getElementById('customerChatBtn');if(chatBtn)chatBtn.style.display=isCustomerOrGuest()?'flex':'none';
  var mobileNav=document.getElementById('customerMobileNav');
  if(mobileNav)mobileNav.style.display='';
  updateInstallButton();
  updateCustomerCheckoutBar();
  var appSidebar=document.querySelector('#appLayout .sidebar');
  if(appSidebar)appSidebar.style.display='';
  var ini=currentUser.fullName.split(' ').map(function(w){return w[0];}).join('').toUpperCase().substring(0,2);
  var sidebarAvatar=document.getElementById('sidebarAvatar');
  var sidebarName=document.getElementById('sidebarName');
  if(sidebarAvatar)sidebarAvatar.textContent=ini;
  if(sidebarName)sidebarName.textContent=currentUser.fullName;
  var roleLabels={admin:'Branch Admin',cashier:'Cashier',customer:'Customer',guest:'Guest'};
  var sidebarRole=document.getElementById('sidebarRole');
  if(sidebarRole)sidebarRole.textContent=roleLabels[currentUser.role]||currentUser.role;
  buildSidebarNav();
  if(isStaff()){
    loadStaffOrders();
    // Orders keep arriving from the app and the till while this page is open.
    setInterval(function(){ if(!document.hidden&&isStaff()) loadStaffOrders(); },15000);
  }
  updateAllLogos();
  var initialPage=(window.location.hash||'').replace('#','');
  if(initialPage&&isStaff()&&STAFF_PAGE_ROUTES[initialPage]){
    window.location.replace(STAFF_PAGE_ROUTES[initialPage]);
    return;
  }
  if(initialPage&&document.getElementById('page-'+initialPage))navigateTo(initialPage);
  else if(isAdmin())navigateTo('orders');
  else if(isCashier())navigateTo('orders');
  else navigateTo('pos');
}

function buildSidebarNav(){
  var nav=document.getElementById('sidebarNav');var h='';
  if(!nav)return;
  if(isAdmin()){
    h+='<div class="nav-section"><div class="nav-section-title">Main</div><a class="nav-item" href="{{ url('/dashboard') }}"><i class="fas fa-chart-pie"></i> Dashboard</a><a class="nav-item" href="{{ url('/pos') }}"><i class="fas fa-cash-register"></i> Point of Sale</a></div>';
    h+='<div class="nav-section"><div class="nav-section-title">Management</div><a class="nav-item" href="{{ url('/orders') }}" data-current-page="orders"><i class="fas fa-receipt"></i> Orders <span class="nav-badge" id="pendingOrdersBadge">0</span> <span class="nav-badge cash-pending" id="cashPendingSidebarBadge" style="display:none">0</span></a><a class="nav-item" href="{{ url('/reservations') }}"><i class="fas fa-calendar-check"></i> Reservations</a><a class="nav-item" href="{{ url('/inventory') }}"><i class="fas fa-boxes-stacked"></i> Inventory</a></div>';
    h+='<div class="nav-section"><div class="nav-section-title">System</div><a class="nav-item" href="{{ url('/reports') }}"><i class="fas fa-chart-bar"></i> Reports</a><a class="nav-item" href="{{ url('/settings') }}"><i class="fas fa-gear"></i> Settings</a></div>';
  }else if(isCashier()){
    h+='<div class="nav-section"><div class="nav-section-title">Counter</div><a class="nav-item" href="{{ url('/pos') }}"><i class="fas fa-cash-register"></i> Point of Sale</a><a class="nav-item" href="{{ url('/orders') }}" data-current-page="orders"><i class="fas fa-receipt"></i> Orders <span class="nav-badge" id="pendingOrdersBadge">0</span> <span class="nav-badge cash-pending" id="cashPendingSidebarBadge" style="display:none">0</span></a></div>';
  }else{
    h+='<div class="nav-section"><div class="nav-section-title">Order</div><a class="nav-item" href="#pos" data-page="pos"><i class="fas fa-mug-hot"></i> Menu</a></div>';
    h+='<div class="nav-section"><div class="nav-section-title">My Reservations</div>'+
      '<a class="nav-item" href="#orders" data-page="orders"><i class="fas fa-receipt"></i> Active <span class="nav-badge" id="pendingOrdersBadge" style="display:none">0</span></a>'+
      '<a class="nav-item" href="#history" data-page="history"><i class="fas fa-clock-rotate-left"></i> History <span class="nav-badge muted" id="historyCountBadge" style="display:none">0</span></a></div>';
  }
  if(isStaff())h+='<div class="nav-section"><div class="nav-section-title">Account</div><a class="nav-item" href="{{ url('/profile') }}"><i class="fas fa-user-circle"></i> My Profile</a></div>';
  else h+='<div class="nav-section"><div class="nav-section-title">Account</div><a class="nav-item" href="#profile" data-page="profile"><i class="fas fa-user-circle"></i> My Profile</a></div>';
  nav.innerHTML=h;
  nav.querySelectorAll('.nav-item[data-page]').forEach(function(item){item.addEventListener('click',function(event){event.preventDefault();navigateTo(item.dataset.page);history.replaceState(null,'','#'+item.dataset.page);});});
}

/* ========== NAVIGATION ========== */
var currentPageId='orders';
function navigateTo(page){
  var adminOnly=['inventory','reports','settings'];
  if(isCashier()&&adminOnly.indexOf(page)!==-1){showToast('Access restricted to admins','warning');return;}
  if(isCustomerOrGuest()&&adminOnly.indexOf(page)!==-1){showToast('Access restricted to admins','warning');return;}
  if(isStaff()&&STAFF_PAGE_ROUTES[page]){window.location.href=STAFF_PAGE_ROUTES[page];return;}
  currentPageId=page;
  document.querySelectorAll('.page-section').forEach(function(p){p.classList.remove('active');});
  document.querySelectorAll('.nav-item').forEach(function(n){n.classList.remove('active');});
  var el=document.getElementById('page-'+page);if(el)el.classList.add('active');
  var nv=document.querySelector('.nav-item[data-page="'+page+'"],.nav-item[data-current-page="'+page+'"]');if(nv)nv.classList.add('active');
  document.querySelectorAll('#customerMobileNav button').forEach(function(btn){btn.classList.toggle('active',btn.dataset.page===page);});
  var titles={history:'Reservation History',pos:isStaff()?'Point of Sale':'Menu',inventory:'Inventory',orders:isStaff()?'Orders':'My Orders',reports:'Reports',settings:'Settings',profile:'My Profile'};
  var pageTitleEl=document.getElementById('pageTitle');if(pageTitleEl)pageTitleEl.textContent=titles[page]||page;
  var pageBreadcrumbEl=document.getElementById('pageBreadcrumb');if(pageBreadcrumbEl)pageBreadcrumbEl.textContent='Home / '+(titles[page]||page);
  document.getElementById('customerMenuHero').style.display=isCustomerOrGuest()?'block':'none';
  document.getElementById('adminOrderFilters').style.display=isStaff()?'flex':'none';
  document.getElementById('customerOrderHeader').style.display=isCustomerOrGuest()?'block':'none';
  document.getElementById('discountField').style.display=isStaff()?'block':'none';
  if(isCustomerOrGuest()&&document.getElementById('checkoutDiscount'))document.getElementById('checkoutDiscount').value='0';
  if(page==='pos')renderPOS();
  if(page==='inventory')renderInventory();
  if(page==='orders'){
    // Customers track reservations from the server; staff work the local queue.
    var reserving=isCustomerOrGuest();
    var staffCard=document.getElementById('staffOrdersCard');
    if(staffCard)staffCard.style.display=reserving?'none':'';
    var mine=document.getElementById('customerReservationList');
    if(mine)mine.style.display=reserving?'block':'none';
    if(reserving)loadMyReservations();else renderOrders();
  }
  if(page==='reports')renderReports();
  if(page==='settings'){renderSettings();initLogoDragDrop();}
  if(page==='profile')renderProfile();
  if(page==='history')renderReservationHistory();
  updateCustomerCheckoutBar();
  updateCashPendingUI();updateNotifBadge();
}

window.addEventListener('hashchange',function(){
  var page=(window.location.hash||'').replace('#','');
  if(page&&document.getElementById('page-'+page))navigateTo(page);
});

/* ========== TOAST / MODAL ========== */
function showToast(m,t){
  t=t||'info';var c=document.getElementById('toastContainer');
  var ic={success:'fa-check-circle',error:'fa-times-circle',info:'fa-info-circle',warning:'fa-exclamation-triangle'};
  var e=document.createElement('div');e.className='toast '+t;
  e.innerHTML='<i class="fas '+(ic[t]||'fa-info-circle')+'" style="font-size:14px"></i><span>'+m+'</span>';
  c.appendChild(e);
  setTimeout(function(){e.style.opacity='0';e.style.transform='translateX(40px)';e.style.transition='0.3s';setTimeout(function(){e.remove();},300);},3500);
}
function isStandaloneApp(){return window.matchMedia('(display-mode: standalone)').matches||window.navigator.standalone===true;}
function updateInstallButton(){
  var btn=document.getElementById('customerInstallBtn');if(!btn)return;
  var show=isCustomerOrGuest();
  btn.style.display=show?'inline-flex':'none';
  btn.classList.toggle('is-installed',show&&isStandaloneApp());
  btn.innerHTML=isStandaloneApp()?'<i class="fas fa-check"></i> Installed':'<i class="fas fa-download"></i> Download App';
}
function installCustomerApp(){
  if(isStandaloneApp()){showToast("Queen's Cup is already installed on this device.",'success');return;}
  if(deferredInstallPrompt){
    deferredInstallPrompt.prompt();
    deferredInstallPrompt.userChoice.then(function(choice){
      if(choice.outcome==='accepted')showToast("Queen's Cup app download started.",'success');
      deferredInstallPrompt=null;updateInstallButton();
    });
    return;
  }
  if(/iphone|ipad|ipod/i.test(navigator.userAgent)){showToast('Tap Share, then Add to Home Screen to download the app.','info');return;}
  showToast('Use your browser menu and choose Install app or Add to Home screen.','info');
}
function openModal(id){document.getElementById(id).classList.add('active');}
function closeModal(id){document.getElementById(id).classList.remove('active');}

/* ========== PAYMENT STATUS HELPERS ========== */
function getPaymentStatusBadge(o){
  var p=paymentInfo(o.payment);
  if(o.paymentStatus==='paid')return '<span class="badge badge-paid"><i class="fas fa-check-circle"></i> '+p.paidLabel+'</span>';
  return '<span class="badge '+(isQrPayment(o.payment)?'badge-qr-pending':'badge-cash-pending')+'"><i class="fas '+p.icon+'"></i> '+p.pendingLabel+'</span>';
}

function getStatusBadge(s){
  var m={ready:'<span class="badge badge-success"><i class="fas fa-bell-concierge"></i> Ready</span>',pending:'<span class="badge badge-warning"><i class="fas fa-clock"></i> Pending</span>',preparing:'<span class="badge badge-info"><i class="fas fa-blender"></i> Preparing</span>',serving:'<span class="badge badge-wine"><i class="fas fa-concierge-bell"></i> Serving</span>',completed:'<span class="badge badge-success"><i class="fas fa-check"></i> Completed</span>',cancelled:'<span class="badge badge-danger"><i class="fas fa-times"></i> Cancelled</span>'};return m[s]||s;
}

function renderCustomerProgress(o){
  var statuses=['pending','preparing','ready','completed'];
  var labels=['Placed','Preparing','Ready','Done'];
  var current=o.status==='cancelled'?0:Math.max(statuses.indexOf(o.status),0);
  var percent=o.status==='cancelled'?100:Math.round((current/(statuses.length-1))*100);
  var title=o.status==='cancelled'?'Cancelled':(labels[current]||'Placed');
  var steps=labels.map(function(label,i){
    var cls=o.status==='cancelled'?(i===0?'current':''):(i<current?'done':(i===current?'current':''));
    return '<span class="customer-progress-step '+cls+'">'+label+'</span>';
  }).join('');
  return '<div class="customer-progress '+(o.status==='cancelled'?'cancelled':'')+'"><div class="customer-progress-top"><span class="customer-progress-title">'+title+'</span><span class="customer-progress-percent">'+percent+'%</span></div><div class="customer-progress-track" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="'+percent+'" aria-label="Order progress"><div class="customer-progress-fill" style="width:'+percent+'%"></div></div><div class="customer-progress-steps">'+steps+'</div></div>';
}

/* ========== POS ========== */
var POS_CATEGORIES=['All','Milktea Series','Fruit Teas','Milky Fruit Jams','Lemonade','Coffee & Non-Coffee','Fruit Milk Shake','Sticky Milk Drinks'];

function renderPOS(){
  if((!Array.isArray(products)||products.length===0)&&Array.isArray(DEFAULT_PRODUCTS)&&DEFAULT_PRODUCTS.length>0){
    products=JSON.parse(JSON.stringify(DEFAULT_PRODUCTS));
    setData('products',products);
  }
  document.getElementById('posCategories').innerHTML=POS_CATEGORIES.map(function(c){return '<button class="pos-cat-btn '+(c===currentPOSCategory?'active':'')+'" onclick="setPOSCategory(\''+c+'\')">'+c+'</button>';}).join('');
  filterPOSItems();
  renderCart();
}
function setPOSCategory(c){currentPOSCategory=c;renderPOS();}
function productPriceText(pr){pr=pr||{R:0,L:0};return '\u20B1'+(pr.R||0)+(Number(pr.L)>0?' / '+pr.L:'');}
function productSizeTags(pr){pr=pr||{R:0,L:0};return '<span class="pos-size-tag">R</span>'+(Number(pr.L)>0?'<span class="pos-size-tag">L</span>':'');}
function productSizeButtons(id,pr){
  pr=pr||{R:0,L:0};
  var h='<div style="display:flex;gap:10px"><button class="btn" style="flex:1;flex-direction:column;padding:16px;height:auto;background:rgba(14,140,74,0.08);border:1px solid var(--border)" onclick="addToCart('+id+',\'R\')"><div style="font-size:10px;color:var(--fg-muted);margin-bottom:4px">REGULAR (16oz)</div><div style="font-size:18px;font-weight:700;color:var(--accent-light)">\u20B1'+pr.R+'</div></button>';
  if(Number(pr.L)>0)h+='<button class="btn" style="flex:1;flex-direction:column;padding:16px;height:auto;background:rgba(201,168,76,0.08);border:1px solid var(--gold-dark)" onclick="addToCart('+id+',\'L\')"><div style="font-size:10px;color:var(--fg-muted);margin-bottom:4px">LARGE (22oz)</div><div style="font-size:18px;font-weight:700;color:var(--gold-light)">\u20B1'+pr.L+'</div></button>';
  return h+'</div>';
}
function filterPOSItems(){
  var s=(document.getElementById('posSearch')?document.getElementById('posSearch').value:'').toLowerCase();
  var f=products;
  if(currentPOSCategory!=='All')f=f.filter(function(p){return p.category===currentPOSCategory;});
  if(s)f=f.filter(function(p){return p.name.toLowerCase().indexOf(s)!==-1||p.category.toLowerCase().indexOf(s)!==-1;});
  var cc={'Milktea Series':'rgba(201,168,76,0.15)','Fruit Teas':'rgba(14,140,74,0.15)','Milky Fruit Jams':'rgba(229,49,112,0.15)','Lemonade':'rgba(245,200,0,0.15)','Coffee & Non-Coffee':'rgba(139,90,43,0.15)','Fruit Milk Shake':'rgba(245,166,35,0.15)','Sticky Milk Drinks':'rgba(91,141,239,0.15)'};
  var ct={'Milktea Series':'var(--gold-light)','Fruit Teas':'var(--accent-light)','Milky Fruit Jams':'var(--danger)','Lemonade':'#FFD700','Coffee & Non-Coffee':'#C8956C','Fruit Milk Shake':'var(--warning)','Sticky Milk Drinks':'var(--info)'};
  function renderItem(p){var pr=p.prices||{R:0,L:0};var stk=getBranchStock(p);return '<div class="pos-item '+(stk===0?'out-of-stock':'')+'" onclick="openSizeSelector('+p.id+')">'+(p.bestSeller?'<div class="pos-item-bestseller"><i class="fas fa-crown" style="font-size:6px;margin-right:2px"></i>BEST</div>':'')+'<div class="pos-item-img" style="background:'+(cc[p.category]||'var(--border)')+';color:'+(ct[p.category]||'var(--fg)')+'">'+productVisual(p)+'</div><div class="pos-item-name">'+escapeHtml(p.name)+'</div><div class="pos-item-price">'+productPriceText(pr)+'</div><div class="pos-item-stock">'+(stk>0?stk+' left':'Out of stock')+'</div><div class="pos-item-sizes">'+productSizeTags(pr)+'</div></div>';}
  var html='';
  if(currentPOSCategory==='All'||currentPOSCategory==='Lemonade'){
    var lemonItems=f.filter(function(p){return p.category==='Lemonade';});
    var otherItems=f.filter(function(p){return p.category!=='Lemonade';});
    if(currentPOSCategory==='All'){html+=otherItems.map(renderItem).join('');}
    if(lemonItems.length>0){var subCats={};lemonItems.forEach(function(p){var sc=p.subCat||'Lemonade';if(!subCats[sc])subCats[sc]=[];subCats[sc].push(p);});var subOrder=['Crowds Favorite','Flavored Lemon','Calamansi'];subOrder.forEach(function(sc){if(!subCats[sc])return;if(currentPOSCategory==='Lemonade'){html+='<div class="pos-subcat"><i class="fas fa-lemon" style="margin-right:6px"></i>'+sc+'</div>';}html+=subCats[sc].map(renderItem).join('');});}
  }else{html=f.map(renderItem).join('');}
  if(!html){html='<div class="menu-empty"><i class="fas fa-mug-hot"></i><strong>No menu items yet</strong><span>Add products in Inventory and they will appear here.</span></div>';}
  document.getElementById('posGrid').innerHTML=html;
}

function openSizeSelector(id){
  var p=products.find(function(pr){return pr.id===id;});if(!p||getBranchStock(p)===0)return;
  var pr=p.prices||{R:0,L:0};
  var sizeBtns=productSizeButtons(id,pr);
  document.getElementById('sizeModalBody').innerHTML='<div style="text-align:center;margin-bottom:16px"><div class="pos-item-img" style="font-size:40px;margin:0 auto 8px">'+productVisual(p)+'</div><div style="font-size:16px;font-weight:700;font-family:\'Playfair Display\'">'+escapeHtml(p.name)+'</div>'+(p.bestSeller?'<div style="margin-top:4px"><span class="badge badge-bestseller"><i class="fas fa-crown" style="font-size:8px"></i> Best Seller</span></div>':'')+'<div style="font-size:11px;color:var(--fg-muted);margin-top:4px">'+escapeHtml(p.desc)+'</div></div>'+sizeBtns;
  openModal('sizeModal');
}

function addToCart(pid,sz){
  var p=products.find(function(pr){return pr.id===pid;});if(!p||getBranchStock(p)===0)return;
  var pr=p.prices||{R:0,L:0};var price=pr[sz]||pr.R||0;
  if(sz==='L'&&Number(pr.L)<=0)return;
  var key=pid+'_'+sz;var ex=cart.find(function(c){return c.key===key;});
  if(ex){if(ex.qty<getBranchStock(p))ex.qty++;else{showToast('Not enough stock','warning');closeModal('sizeModal');return;}}
  else{cart.push({key:key,id:pid,name:p.name,size:sz,price:price,qty:1,imageUrl:p.imageUrl||'',icon:p.icon||productIcon(p.category)});}
  closeModal('sizeModal');renderCart();showToast(p.name+' ('+sz+') added','success');
}
function removeFromCart(k){cart=cart.filter(function(c){return c.key!==k;});renderCart();}
function changeQty(k,d){var i=cart.find(function(c){return c.key===k;});if(!i)return;
  var p=products.find(function(pr){return pr.id===i.id;});i.qty+=d;
  if(i.qty<=0){removeFromCart(k);return;}
  if(i.qty>getBranchStock(p)){i.qty=getBranchStock(p);showToast('Not enough stock','warning');}
  renderCart();
}
function clearCart(){cart=[];renderCart();}

function updateCustomerCheckoutBar(){
  var bar=document.getElementById('customerCheckoutBar');if(!bar)return;
  var show=isCustomerOrGuest()&&currentPageId==='pos';
  bar.style.display=show?'flex':'none';
  var total=cart.reduce(function(sum,item){return sum+item.price*item.qty;},0);
  var count=cart.reduce(function(sum,item){return sum+item.qty;},0);
  var totalEl=document.getElementById('customerCheckoutTotal');
  var countEl=document.getElementById('customerCheckoutCount');
  var btn=document.getElementById('customerCheckoutBtn');
  if(totalEl)totalEl.textContent='\u20B1'+total.toFixed(2);
  if(countEl)countEl.textContent=count>0?(count+' item'+(count===1?'':'s')+' in cart'):'No items selected';
  if(btn)btn.classList.toggle('is-empty',count===0);
}

function renderCart(){
  var c=document.getElementById('cartItems');var s=document.getElementById('cartSummary');
  var cartPanel=document.querySelector('.pos-cart');
  if(cartPanel)cartPanel.classList.toggle('cart-empty',cart.length===0&&isCustomerOrGuest());
  updateCustomerCheckoutBar();
  if(cart.length===0){c.innerHTML='<div style="text-align:center;padding:36px 18px;color:var(--fg-muted)"><i class="fas fa-mug-hot" style="font-size:36px;opacity:0.3;margin-bottom:10px;display:block"></i><p>No items in cart</p></div>';s.style.display='none';return;}
  c.innerHTML=cart.map(function(i){
    var sizeLabel=i.size==='R'?'Regular (16oz)':'Large (22oz)';
    return '<div class="cart-item">'+orderItemVisual(i)+'<div class="cart-item-info"><div class="cart-item-name">'+escapeHtml(i.name)+'</div><div class="cart-item-size">'+sizeLabel+' \u2014 \u20B1'+i.price+' each</div></div><div class="cart-item-qty"><button class="cart-qty-btn" onclick="changeQty(\''+i.key+'\',-1)"><i class="fas fa-minus"></i></button><span class="cart-qty-val">'+i.qty+'</span><button class="cart-qty-btn" onclick="changeQty(\''+i.key+'\',1)"><i class="fas fa-plus"></i></button></div><button class="cart-item-remove" onclick="removeFromCart(\''+i.key+'\')"><i class="fas fa-trash"></i></button></div>';
  }).join('');
  s.style.display='block';updateCartTotals();
}

/**
 * Prices the basket for display.
 *
 * Mirrors ReservationService::quote() step for step: each line is rounded to
 * centavos before it is added up, then the take-out surcharge is added to that
 * rounded subtotal. Summing raw floats first and rounding once at the end can
 * land a centavo away from what the server charges, and the server is the one
 * that decides.
 */
function money2(value) {
  return Math.round((Number(value) || 0) * 100) / 100;
}

function quoteCart() {
  var subtotal = 0;
  var cups = 0;

  cart.forEach(function (line) {
    subtotal += money2(line.price * line.qty);
    cups += line.qty;
  });

  subtotal = money2(subtotal);

  // Staff can discount at the counter; a customer reserving cannot.
  var percent = 0;
  if (isStaff()) {
    var field = document.getElementById('checkoutDiscount');
    percent = field ? (parseFloat(field.value) || 0) : 0;
  }

  var discount = money2(subtotal * (percent / 100));
  var takeoutFee = checkoutIsTakeOut() ? money2(TAKEOUT_FEE_PER_CUP * cups) : 0;
  var total = money2(subtotal - discount + takeoutFee);

  return { subtotal: subtotal, discount: discount, takeoutFee: takeoutFee, cups: cups, total: total };
}

function updateCartTotals() {
  var q = quoteCart();
  var peso = function (value) { return '₱' + value.toFixed(2); };

  var write = function (id, text) {
    var el = document.getElementById(id);
    if (el) el.textContent = text;
  };
  var toggle = function (id, on, how) {
    var el = document.getElementById(id);
    if (el) el.style.display = on ? (how || 'flex') : 'none';
  };

  // Every field is written whether or not the cart panel happens to be on
  // screen. These used to sit behind a check on the panel's visibility, which
  // is why the checkout modal could open showing zeroes.
  write('cartSubtotal', peso(q.subtotal));
  write('cartDiscount', '-' + peso(q.discount));
  write('cartFee', peso(q.takeoutFee));
  write('cartTotal', peso(q.total));

  write('chkSubtotal', peso(q.subtotal));
  write('chkDiscount', '-' + peso(q.discount));
  write('chkFee', peso(q.takeoutFee));
  write('chkTotal', peso(q.total));

  var feeLabel = 'Take-out cups (' + q.cups + ' × ' + peso(TAKEOUT_FEE_PER_CUP) + ')';
  write('cartFeeLabel', feeLabel);
  write('chkFeeLabel', feeLabel);

  toggle('cartFeeRow', q.takeoutFee > 0);
  toggle('chkFeeRow', q.takeoutFee > 0);
  toggle('cartDiscountRow', isStaff());
  toggle('discountField', isStaff(), 'block');

  var tenderField = document.getElementById('checkoutCashTendered');
  var tendered = tenderField ? (parseFloat(tenderField.value) || 0) : 0;
  var changeRow = document.getElementById('changeRow');
  var changeCell = document.getElementById('chkChange');

  if (changeRow && changeCell) {
    if (tendered > 0) {
      changeRow.style.display = 'flex';
      var change = money2(tendered - q.total);
      changeCell.textContent = change >= 0 ? peso(change) : '-' + peso(Math.abs(change));
      changeCell.style.color = change >= 0 ? 'var(--success)' : 'var(--danger)';
    } else {
      changeRow.style.display = 'none';
    }
  }

  write('qrPaymentAmount', peso(q.total));
  updateCustomerCheckoutBar();
}


function renderQrPayment(method){
  var panel=document.getElementById('qrPaymentPanel');if(!panel)return;
  var qr=document.getElementById('qrPaymentCode');var title=document.getElementById('qrPaymentTitle');var hint=document.getElementById('qrPaymentHint');
  if(!isQrPayment(method)){panel.classList.remove('active');return;}
  var p=paymentInfo(method);panel.classList.add('active');
  if(title)title.textContent='Scan to Pay with '+p.label;
  if(hint)hint.textContent='Using '+p.qrImage+' if available. Replace this file with your real '+p.label+' merchant QR code.';
  if(qr)qr.innerHTML='<img src="'+p.qrImage+'" alt="'+p.label+' QR Code" onerror="this.parentNode.innerHTML=\'<div class=&quot;qr-placeholder&quot; title=&quot;Add '+p.qrImage+' for the real QR code&quot;></div>\'">';
}

function selectPaymentMethod(method){
  if(!PAYMENT_METHODS[method])method='Cash';
  document.getElementById('checkoutPaymentMethod').value=method;
  document.querySelectorAll('.payment-method-option').forEach(function(btn){btn.classList.toggle('active',btn.getAttribute('data-payment-method')===method);});
  var cashField=document.getElementById('cashTenderedField');if(cashField)cashField.style.display=method==='Cash'?'block':'none';
  var notice=document.getElementById('paymentNotice');
  if(notice){
    if(method==='Cash'){notice.style.color='var(--warning)';notice.style.borderColor='rgba(245,166,35,0.2)';notice.style.background='rgba(245,166,35,0.08)';notice.innerHTML='<i class="fas fa-info-circle" style="margin-right:5px"></i>Cash orders are marked <strong>Cash Pending</strong> until the cashier confirms payment.';}
    else{notice.style.color='var(--info)';notice.style.borderColor='rgba(91,141,239,0.22)';notice.style.background='rgba(91,141,239,0.08)';notice.innerHTML='<i class="fas fa-info-circle" style="margin-right:5px"></i>'+paymentInfo(method).label+' orders use QR payment and stay <strong>Payment Pending</strong> until confirmed.';}
  }
  renderQrPayment(method);updateCartTotals();
}

function checkout(){
  if(cart.length===0){showToast('Cart is empty','warning');return;}
  var reserving=isCustomerOrGuest();
  // A customer reserves and pays in person, so none of the till controls apply.
  var show=function(id,on){var el=document.getElementById(id);if(el)el.style.display=on?'':'none';};
  show('checkoutTypePickup',!reserving);
  show('takeoutFeeHint',reserving);
  var pickup=document.getElementById('checkoutTypePickup');
  if(pickup&&reserving&&document.getElementById('checkoutType').value==='Pick Up')document.getElementById('checkoutType').value='Dine In';
  var heading=document.getElementById('checkoutHeading');
  if(heading)heading.textContent=reserving?'Confirm reservation':'Confirm Order';
  var confirmBtn=document.getElementById('reserveConfirmBtn');
  if(confirmBtn)confirmBtn.innerHTML=reserving?'<i class="fas fa-check"></i> Confirm reservation':'<i class="fas fa-check"></i> Confirm';
  var hint=document.getElementById('takeoutFeeHint');
  if(hint)hint.textContent='Take out adds ₱'+TAKEOUT_FEE_PER_CUP.toFixed(0)+' per cup for the cup and lid.';
  document.getElementById('checkoutDiscount').value='0';
  var cnInput=document.getElementById('checkoutCustomerName');
  var cnField=document.getElementById('checkoutNameField');
  if(isStaff()){cnField.style.display='block';cnInput.value='';cnInput.placeholder='Walk-in';}
  else{cnField.style.display='none';cnInput.value=currentUser.fullName;}
  var ct=document.getElementById('checkoutCashTendered');if(ct)ct.value='';
  selectPaymentMethod('Cash');
  // selectPaymentMethod reveals the till fields, so hide them again for
  // a customer, who pays in person rather than here.
  show('paymentMethodField',!reserving);
  show('cashTenderedField',!reserving);
  show('qrPaymentPanel',false);
  show('paymentNotice',!reserving);
  updateCartTotals();openModal('checkoutModal');
}

function processPayment(){
  // Buying happens at the till. From the customer side this only reserves.
  if(isCustomerOrGuest()){submitReservation();return;}
  var ot=document.getElementById('checkoutType').value;
  var dp=parseFloat(document.getElementById('checkoutDiscount').value||0);
  var sub=cart.reduce(function(s,i){return s+i.price*i.qty;},0);var disc=sub*(dp/100);var tot=sub-disc;
  var paymentMethod=document.getElementById('checkoutPaymentMethod').value||'Cash';
  var ctInput=document.getElementById('checkoutCashTendered');var cashTendered=ctInput?(parseFloat(ctInput.value)||0):0;

  var orderCustomer,orderUsername='',orderEmail='',orderContact='';
  if(isStaff()){var cnVal=(document.getElementById('checkoutCustomerName').value||'').trim();orderCustomer=cnVal||'Walk-in';}
  else{orderCustomer=currentUser.fullName;orderUsername=currentUser.username||'';orderEmail=currentUser.email||'';orderContact=currentUser.contactNumber||'';}

  if(paymentMethod==='Cash'){
    if(cashTendered<tot){showToast('Insufficient cash! Need \u20B1'+(tot-cashTendered).toFixed(2)+' more.','error');return;}
    if(cashTendered<=0){showToast('Please enter cash amount.','error');return;}
  }else{cashTendered=0;}
  var changeAmt=paymentMethod==='Cash'?cashTendered-tot:0;
  cart.forEach(function(ci){
    var p=products.find(function(pr){return pr.id===ci.id;});
    if(p){setBranchStock(p,Math.max(0,getBranchStock(p)-ci.qty));setBranchSold(p,getBranchSold(p)+ci.qty);}
  });
  setData('products',products);
  var now=new Date();var info=getBranchInfo();
  var order={id:nextOrderId++,customer:orderCustomer,username:orderUsername,email:orderEmail,contactNumber:orderContact,type:ot,items:cart.map(function(i){return{id:i.id,name:i.name,size:i.size,qty:i.qty,price:i.price,imageUrl:i.imageUrl||'',icon:i.icon||'\uD83E\uDDCB'};}),subtotal:sub,discount:dp,total:tot,cashTendered:cashTendered,change:changeAmt,status:'pending',time:now.toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit'}),date:now.toLocaleDateString(),payment:paymentMethod,branch:getBranch(),paymentStatus:'pending',paidBy:'',paidAt:''};
  orders.push(order);setData('orders',orders);setData('nextOrderId',nextOrderId);
  if(isGuest()){var myOrders=getData('guest_orders_'+currentUser.fullName,[]);myOrders.push(order.id);setData('guest_orders_'+currentUser.fullName,myOrders);}
  closeModal('checkoutModal');cart=[];renderCart();renderPOS();
  showToast('Order #'+order.id+' placed! '+paymentInfo(paymentMethod).pendingLabel+' until cashier confirmation.','warning');
  updateCashPendingUI();updateNotifBadge();
  navigateTo('pos');
}

function markOrderPaid(id){
  var o=orders.find(function(or){return or.id===id;});if(!o)return;
  if(o.serverId){patchServerOrder('/'+o.serverId+'/payment',{payment_method:'cash'},'Order '+o.serverRef+' marked paid.');return;}
  if(o.paymentStatus==='paid'){showToast('Already marked as paid','info');return;}
  o.paymentStatus='paid';o.paidBy=currentUser.fullName||currentUser.username||'staff';o.paidAt=new Date().toLocaleTimeString('en-US',{hour:'2-digit',minute:'2-digit'});
  setData('orders',orders);renderOrders();updateCashPendingUI();updateNotifBadge();
  showToast('Order #'+o.id+' marked as PAID by '+o.paidBy,'success');
}

function generateReceipt(o){
  var now=new Date();var info=getBranchInfo();var src=getLogoSrc();
  var p=paymentInfo(o.payment);
  var logoHtml='<div class="receipt-logo"><img src="'+src+'" alt="Logo"></div>';
  var ih=o.items.map(function(i){return '<div class="receipt-row"><span>'+i.name+(i.size==='R'?' (16oz)':' (22oz)')+' x'+i.qty+'</span><span>\u20B1'+(i.price*i.qty).toFixed(2)+'</span></div>';}).join('');
  var dh=o.discount>0?'<div class="receipt-row"><span>Discount ('+o.discount+'%)</span><span>-\u20B1'+(o.subtotal*o.discount/100).toFixed(2)+'</span></div>':'';
  var cashRow=o.payment==='Cash'?'<div class="receipt-row"><span>Cash Tendered</span><span>\u20B1'+(o.cashTendered||0).toFixed(2)+'</span></div><div class="receipt-row"><span>Change</span><span>\u20B1'+(o.change||0).toFixed(2)+'</span></div>':'<div class="receipt-row"><span>Payment Method</span><span>'+p.label+'</span></div>';
  var payStatusRow='<div class="receipt-row" style="font-weight:'+(o.paymentStatus==='paid'?'700':'600')+';color:'+(o.paymentStatus==='paid'?'#2CB67D':'#F5A623')+'"><span>Payment</span><span>'+(o.paymentStatus==='paid'?p.paidLabel.toUpperCase():p.pendingLabel.toUpperCase())+'</span></div>';
  document.getElementById('receiptContent').innerHTML='<div class="receipt">'+logoHtml+'<h4>The Queen\'s Cup</h4><div class="receipt-sub">'+info.name+'<br>'+info.address+'<br>'+now.toLocaleDateString()+' '+now.toLocaleTimeString()+'</div><hr><div class="receipt-row"><span>Order</span><span>#'+o.id+'</span></div><div class="receipt-row"><span>Customer</span><span>'+o.customer+'</span></div><div class="receipt-row"><span>Type</span><span>'+o.type+'</span></div>'+payStatusRow+'<hr>'+ih+'<hr><div class="receipt-row"><span>Subtotal</span><span>\u20B1'+o.subtotal.toFixed(2)+'</span></div>'+dh+'<div class="receipt-row receipt-total"><span>TOTAL</span><span>\u20B1'+o.total.toFixed(2)+'</span></div>'+cashRow+'<hr><div class="receipt-sub" style="margin-top:6px">Thank you for choosing Queen\'s Cup!<br>Crowned with Flavors</div></div>';
}

function openOrderReceipt(id){
  var o=orders.find(function(or){return or.id===id;});if(!o)return;
  generateReceipt(o);openModal('receiptModal');
}

function printReceipt(){
  var c=document.getElementById('receiptContent').innerHTML;var w=window.open('','','width=400,height=600');
  w.document.write('<html><head><title>Receipt</title><style>body{font-family:monospace;padding:20px}.receipt{max-width:300px;margin:0 auto}h4{text-align:center;margin-bottom:3px}.receipt-sub{text-align:center;font-size:10px;color:#666;margin-bottom:12px}hr{border:none;border-top:1px dashed #ccc;margin:8px 0}.receipt-row{display:flex;justify-content:space-between;font-size:11px;margin:3px 0}.receipt-total{font-weight:bold;font-size:13px}.receipt-logo{text-align:center;margin-bottom:10px}.receipt-logo img{width:48px;height:48px;border-radius:50%;object-fit:cover}</style></head><body>'+c+'</body></html>');
  w.document.close();w.print();
}
function holdOrder(){if(cart.length===0){showToast('Cart is empty','warning');return;}showToast('Order held','info');clearCart();}

/* ========== INVENTORY ========== */
function renderInventory(){
  var br=getBranch();var bname=BRANCHES[br]?BRANCHES[br].name.split(',')[0]:'';
  var hdr=document.getElementById('invStockHeader');if(hdr)hdr.textContent='Stock ('+bname+')';
  var s=(document.getElementById('invSearch')?document.getElementById('invSearch').value:'').toLowerCase();
  var f=products;if(s)f=f.filter(function(p){return p.name.toLowerCase().indexOf(s)!==-1||p.category.toLowerCase().indexOf(s)!==-1;});
  var lt=parseInt(getData('lowStockThreshold',10));
  var catBadges={'Milktea Series':'badge-gold','Fruit Teas':'badge-accent','Milky Fruit Jams':'badge-danger','Lemonade':'badge-warning','Coffee & Non-Coffee':'badge-cashier','Fruit Milk Shake':'badge-warning','Sticky Milk Drinks':'badge-info'};
  document.getElementById('inventoryTable').innerHTML=f.map(function(p){
    var pr=p.prices||{R:0,L:0};var stk=getBranchStock(p);
    var st;if(stk===0)st='<span class="badge badge-danger">Out of Stock</span>';else if(stk<=lt)st='<span class="badge badge-warning">Low Stock</span>';else st='<span class="badge badge-success">In Stock</span>';
    var cb=catBadges[p.category]||'badge-accent';
    var bs=p.bestSeller?' <span class="badge badge-bestseller" style="font-size:8px;padding:1px 4px"><i class="fas fa-crown" style="font-size:6px"></i> BEST</span>':'';
    return '<tr><td><div class="item-line">'+productThumbHtml(p)+'<div><div style="font-weight:600">'+escapeHtml(p.name)+bs+'</div><div style="font-size:10px;color:var(--fg-muted)">'+escapeHtml(p.desc)+'</div></div></div></td><td><span class="badge '+cb+'">'+escapeHtml(p.category)+(p.subCat?' - '+escapeHtml(p.subCat):'')+'</span></td><td style="font-size:11px">'+productPriceText(pr)+'</td><td><span style="font-weight:700;color:'+(stk<=lt?(stk===0?'var(--danger)':'var(--warning)'):'var(--fg)')+'">'+stk+'</span></td><td>'+st+'</td><td><div style="display:flex;gap:5px"><button class="btn btn-secondary btn-sm btn-icon" onclick="editInventoryItem('+p.id+')"><i class="fas fa-pen"></i></button><button class="btn btn-secondary btn-sm btn-icon" onclick="adjustStock('+p.id+',10)"><i class="fas fa-plus"></i></button><button class="btn btn-secondary btn-sm btn-icon" onclick="deleteInventoryItem('+p.id+')" style="color:var(--danger)"><i class="fas fa-trash"></i></button></div></td></tr>';
  }).join('');
  document.getElementById('invInStock').textContent=products.filter(function(p){return getBranchStock(p)>lt}).length;
  document.getElementById('invLowStock').textContent=products.filter(function(p){return getBranchStock(p)>0&&getBranchStock(p)<=lt}).length;
  document.getElementById('invOutOfStock').textContent=products.filter(function(p){return getBranchStock(p)===0;}).length;
}
function filterInventory(){renderInventory();}
function setInventoryImagePreview(url,icon){
  document.getElementById('invImageUrl').value=url||'';
  document.getElementById('invImagePreview').innerHTML=url?'<img src="'+escapeHtml(url)+'" alt="Preview">':'<i class="fas fa-image"></i>';
  if(!url&&icon)document.getElementById('invImagePreview').textContent=icon;
}
function previewInventoryImage(event){
  var input=event.target;if(!input.files||!input.files.length)return;
  var file=input.files[0];
  if(!file.type.match(/^image\//)){showToast('Please choose an image file','error');input.value='';return;}
  if(file.size>1024*1024){showToast('Image must be under 1MB for orders storage','error');input.value='';return;}
  var reader=new FileReader();
  reader.onload=function(e){setInventoryImagePreview(e.target.result);};
  reader.readAsDataURL(file);
}
function openInventoryModal(){
  document.getElementById('editItemId').value='';document.getElementById('invName').value='';document.getElementById('invCategory').value='Milktea Series';document.getElementById('invPriceR').value='';document.getElementById('invPriceL').value='';document.getElementById('invStock').value='';document.getElementById('invDesc').value='';document.getElementById('invImageFile').value='';setInventoryImagePreview('');document.getElementById('invModalTitle').textContent='Add New Item';document.getElementById('invStockLabelBranch').textContent='('+BRANCHES[getBranch()].name.split(',')[0]+')';openModal('inventoryModal');
}
function editInventoryItem(id){
  var p=products.find(function(pr){return pr.id===id;});if(!p)return;var pr=p.prices||{R:0,L:0};
  document.getElementById('editItemId').value=p.id;document.getElementById('invName').value=p.name;document.getElementById('invCategory').value=p.category;document.getElementById('invPriceR').value=pr.R;document.getElementById('invPriceL').value=pr.L;document.getElementById('invStock').value=getBranchStock(p);document.getElementById('invDesc').value=p.desc;document.getElementById('invImageFile').value='';setInventoryImagePreview(p.imageUrl||'',p.icon);document.getElementById('invModalTitle').textContent='Edit Item';document.getElementById('invStockLabelBranch').textContent='('+BRANCHES[getBranch()].name.split(',')[0]+')';openModal('inventoryModal');
}
function saveInventoryItem(){
  var eid=document.getElementById('editItemId').value;var nm=document.getElementById('invName').value.trim();var cat=document.getElementById('invCategory').value;var pr=parseFloat(document.getElementById('invPriceR').value);var pl=parseFloat(document.getElementById('invPriceL').value);var st=parseInt(document.getElementById('invStock').value);var ds=document.getElementById('invDesc').value.trim();var img=document.getElementById('invImageUrl').value;
  if(!nm||isNaN(pr)||isNaN(pl)||isNaN(st)){showToast('Please fill all required fields','error');return;}
  if(eid){var p=products.find(function(px){return px.id===parseInt(eid);});if(p){p.name=nm;p.category=cat;p.prices={R:pr,L:pl};setBranchStock(p,st);p.desc=ds;p.imageUrl=img;}showToast('Item updated','success');}
  else{var mx=0;for(var i=0;i<products.length;i++){if(products[i].id>mx)mx=products[i].id;}var icons={'Milktea Series':'\uD83E\uDDCB','Fruit Teas':'\uD83C\uDF4B','Milky Fruit Jams':'\uD83C\uDF53','Lemonade':'\uD83C\uDF4B','Coffee & Non-Coffee':'\u2615','Fruit Milk Shake':'\uD83C\uDF53','Sticky Milk Drinks':'\uD83C\uDF4B'};var newP={id:mx+1,name:nm,category:cat,prices:{R:pr,L:pl},stock:{},sold:{},desc:ds,imageUrl:img,icon:icons[cat]||'\uD83E\uDDCB',bestSeller:false};Object.keys(BRANCHES).forEach(function(b){newP.stock[b]=st;newP.sold[b]=0;});products.push(newP);showToast('Item added','success');}
  setData('products',products);closeModal('inventoryModal');renderInventory();updateNotifBadge();
}
function deleteInventoryItem(id){var p=products.find(function(pr){return pr.id===id;});if(!p)return;if(!confirm('Delete "'+p.name+'"?'))return;products=products.filter(function(pr){return pr.id!==id;});setData('products',products);renderInventory();showToast('Item deleted','info');}
function adjustStock(id,amt){var p=products.find(function(pr){return pr.id===id;});if(!p)return;setBranchStock(p,getBranchStock(p)+amt);setData('products',products);renderInventory();updateNotifBadge();showToast('+'+amt+' stock for '+p.name,'success');}
function exportInventory(){
  var csv='Name,Category,Price R,Price L,Stock ('+getBranch()+'),Description\n';
  products.forEach(function(p){var pr=p.prices||{R:0,L:0};csv+='"'+p.name+'","'+p.category+'",'+pr.R+','+pr.L+','+getBranchStock(p)+',"'+p.desc+'"\n';});
  var b=new Blob([csv],{type:'text/csv'});var u=URL.createObjectURL(b);var a=document.createElement('a');a.href=u;a.download='queenscup_inventory_'+getBranch()+'.csv';a.click();showToast('Inventory exported','success');
}

/* ========== ORDERS ========== */
var currentOrderFilter='all';

/* ========== STAFF ORDER BOOK ==========
 *
 * Every order and sale the shop has taken, from both channels: reservations
 * placed in the app or on the web, and walk-in sales rung up at the till.
 *
 * These used to be read out of local storage, which meant each browser saw
 * only what it had itself recorded. A sale rung up on the counter machine was
 * invisible on the office one, and clearing site data wiped the book.
 */
var STAFF_ORDER_URL = @json(url('/staff/reservations'));
var staffOrdersLoaded = false;

/** Server statuses in the order they progress. */
var SERVER_STATUS_LABEL = {
  pending: 'Pending',
  preparing: 'Preparing',
  ready: 'Ready',
  completed: 'Completed',
  cancelled: 'Cancelled'
};

function mapServerOrder(row) {
  var when = row.created_at ? new Date(row.created_at) : null;

  return {
    id: row.reference,
    serverId: row.id,
    serverRef: row.reference,
    channel: row.source === 'pos' ? 'Counter' : 'Reservation',
    customer: row.customer_name || 'Walk-in',
    username: '',
    email: row.customer_email || '',
    contactNumber: row.customer_contact || '',
    type: row.service_type === 'take_out' ? 'Take Out' : 'Dine In',
    branch: row.branch,
    items: (row.items || []).map(function (line) {
      return {
        id: line.inventory_id,
        name: line.name,
        size: line.size_label === '22oz' ? 'L' : 'R',
        qty: line.quantity,
        price: Number(line.unit_price),
        imageUrl: '',
        icon: '🧋'
      };
    }),
    subtotal: Number(row.subtotal),
    takeoutFee: Number(row.takeout_fee || 0),
    discount: 0,
    total: Number(row.total),
    status: row.status,
    // The page's filters and Mark Paid button test for 'pending';
    // the server calls the same state unpaid.
    paymentStatus: row.payment_status === 'paid' ? 'paid' : 'pending',
    payment: (row.payment_method || '').toUpperCase() || 'Unpaid',
    cashTendered: 0,
    change: 0,
    date: when ? when.toLocaleDateString() : '',
    time: when ? when.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) : ''
  };
}

function loadStaffOrders() {
  if (!isStaff()) return Promise.resolve();

  return fetch(STAFF_ORDER_URL, {
    headers: { 'Accept': 'application/json' },
    credentials: 'same-origin'
  })
    .then(function (response) {
      if (!response.ok) throw new Error('Could not load the order book.');
      return response.json();
    })
    .then(function (payload) {
      orders = (payload.data || []).map(mapServerOrder);
      staffOrdersLoaded = true;
      if (currentPageId === 'orders') renderOrders();
      updateCashPendingUI();
    })
    .catch(function (error) {
      if (!staffOrdersLoaded) showToast(error.message, 'error');
    });
}

/** PATCHes the server, then refreshes the book from it. */
function patchServerOrder(path, body, success) {
  return fetch(STAFF_ORDER_URL + path, {
    method: 'PATCH',
    credentials: 'same-origin',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': csrfToken()
    },
    body: JSON.stringify(body)
  })
    .then(function (response) {
      return response.json().then(function (payload) {
        if (!response.ok) {
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
    .then(function () {
      showToast(success, 'success');
      return loadStaffOrders();
    })
    .catch(function (error) { showToast(error.message, 'error'); });
}

function renderOrders(){
  var br=getBranch();var f=orders;
  if(isGuest()){var myIds=getData('guest_orders_'+currentUser.fullName,[]);f=f.filter(function(o){return myIds.indexOf(o.id)!==-1||o.customer===currentUser.fullName;});}
  else if(isCustomer()){f=f.filter(function(o){return o.username===currentUser.username;});}
  // Staff see the whole book: both channels, both branches.
  var s=(document.getElementById('orderSearch')?document.getElementById('orderSearch').value:'').toLowerCase();
  if(currentOrderFilter!=='all'&&currentOrderFilter!=='cash_pending')f=f.filter(function(o){return o.status===currentOrderFilter;});
  if(currentOrderFilter==='cash_pending')f=f.filter(function(o){return o.paymentStatus==='pending';});
  if(s)f=f.filter(function(o){return o.customer.toLowerCase().indexOf(s)!==-1||String(o.id).indexOf(s)!==-1;});
  var showActions=isStaff();
  var thCols=['Order'];if(showActions){thCols.push('Channel');thCols.push('Customer');}thCols.push('Items');thCols.push('Total');thCols.push('Payment');thCols.push('Status');thCols.push('Time');if(showActions)thCols.push('Actions');
  document.getElementById('ordersThead').innerHTML='<tr>'+thCols.map(function(c){return '<th>'+c+'</th>';}).join('')+'</tr>';
  document.getElementById('ordersTable').innerHTML=f.slice().sort(function(a,b){return Number(a.id||0)-Number(b.id||0);}).map(function(o){
    var is=o.items.map(function(i){return '<div class="item-line" style="margin-bottom:5px">'+orderItemVisual(i)+'<span>'+escapeHtml(i.name)+(i.size==='R'?' (16oz)':' (22oz)')+' x'+i.qty+'</span></div>';}).join('');
    var ah='<button class="btn btn-secondary btn-sm btn-icon" onclick="viewOrderDetail('+o.id+')" title="View"><i class="fas fa-eye"></i></button><button class="btn btn-secondary btn-sm btn-icon" onclick="openOrderReceipt('+o.id+')" title="Receipt"><i class="fas fa-receipt"></i></button>';
    if(showActions){
      if(o.status==='pending')ah+='<button class="btn btn-transparent-info btn-sm btn-icon" onclick="updateOrderStatus('+o.id+',\'preparing\')" title="Prepare"><i class="fas fa-blender"></i></button>';
      if(o.status==='preparing')ah+='<button class="btn btn-sm btn-icon" style="background:var(--accent);color:#fff" onclick="updateOrderStatus('+o.id+',\'serving\')" title="Serve"><i class="fas fa-concierge-bell"></i></button>';
      if(o.status==='ready')ah+='<button class="btn btn-success btn-sm btn-icon" onclick="updateOrderStatus('+o.id+',\'completed\')" title="Complete"><i class="fas fa-check"></i></button>';
      if(o.status==='pending')ah+='<button class="btn btn-danger btn-sm btn-icon" onclick="updateOrderStatus('+o.id+',\'cancelled\')" title="Cancel"><i class="fas fa-times"></i></button>';
      if(o.paymentStatus==='pending')ah+='<button class="btn btn-gold btn-sm btn-icon" onclick="markOrderPaid('+o.id+')" title="Mark as Paid"><i class="fas '+paymentInfo(o.payment).icon+'"></i></button>';
    }
    var statusCell=showActions?getStatusBadge(o.status):renderCustomerProgress(o);
    var tdCols=['<td style="font-weight:700">'+escapeHtml(String(o.id))+'</td>'];if(showActions){tdCols.push('<td><span class="badge '+(o.channel==='Counter'?'badge-gold':'badge-info')+'">'+escapeHtml(o.channel||'Reservation')+'</span></td>');tdCols.push('<td>'+escapeHtml(o.customer)+'</td>');}tdCols.push('<td style="font-size:11px">'+is+'</td>');tdCols.push('<td style="font-weight:700;color:var(--gold-light)">\u20B1'+o.total.toFixed(2)+'</td>');tdCols.push('<td>'+getPaymentStatusBadge(o)+'</td>');tdCols.push('<td>'+statusCell+'</td>');tdCols.push('<td style="color:var(--fg-muted)">'+o.time+'</td>');if(showActions)tdCols.push('<td><div style="display:flex;gap:4px">'+ah+'</div></td>');
    return '<tr>'+tdCols.join('')+'</tr>';
  }).join('');
  updateCashPendingUI();
}
function filterOrders(f,btn){currentOrderFilter=f||'all';document.querySelectorAll('.order-filter').forEach(function(b){b.classList.remove('active');});if(btn)btn.classList.add('active');renderOrders();}
function updateOrderStatus(id,s){var o=orders.find(function(or){return or.id===id;});if(!o)return;
  if(o.serverId){patchServerOrder('/'+o.serverId+'/status',{status:s},'Order '+o.serverRef+' is now '+(SERVER_STATUS_LABEL[s]||s)+'.');return;}
  o.status=s;setData('orders',orders);renderOrders();updateNotifBadge();var m={preparing:'Being prepared',serving:'Ready for pick up',completed:'Completed!',cancelled:'Cancelled'};showToast(m[s]||'Updated',s==='cancelled'?'warning':'success');}

function viewOrderDetail(id){
  var o=orders.find(function(or){return or.id===id;});if(!o)return;var showActions=isStaff();
  var ss=['pending','preparing','serving','completed'];var sl=['Pending','Preparing','Serving','Completed'];var si=ss.indexOf(o.status);var tl='';
  if(showActions&&o.status!=='cancelled'){tl='<div class="order-timeline" style="margin-bottom:20px">'+ss.map(function(s,i){var c=i<si?'done':i===si?'current':'';return '<div class="timeline-step '+c+'"><div class="timeline-dot '+c+'">'+(i<si?'<i class="fas fa-check"></i>':(i+1))+'</div><div class="timeline-label">'+sl[i]+'</div></div>';}).join('')+'</div>';}
  if(!showActions){tl='<div style="margin-bottom:20px">'+renderCustomerProgress(o)+'</div>';}
  var ih=o.items.map(function(i){return '<div style="display:flex;justify-content:space-between;align-items:center;gap:10px;padding:6px 0;border-bottom:1px solid var(--border);font-size:12px"><div class="item-line">'+orderItemVisual(i)+'<span>'+escapeHtml(i.name)+(i.size==='R'?' (16oz)':' (22oz)')+' <span style="color:var(--fg-muted)">x'+i.qty+'</span></span></div><span style="font-weight:600">\u20B1'+(i.price*i.qty).toFixed(2)+'</span></div>';}).join('');
  var dh=o.discount>0?'<div style="display:flex;justify-content:space-between;padding:5px 0;font-size:12px;color:var(--success)"><span>Discount ('+o.discount+'%)</span><span>-\u20B1'+(o.subtotal*o.discount/100).toFixed(2)+'</span></div>':'';
  var brInfo=BRANCHES[o.branch]||BRANCHES.kotapark;var p=paymentInfo(o.payment);
  var cashInfo=o.payment==='Cash'?'<div style="display:flex;justify-content:space-between;padding:5px 0;font-size:12px"><span>Cash Tendered</span><span>\u20B1'+(o.cashTendered||0).toFixed(2)+'</span></div><div style="display:flex;justify-content:space-between;padding:5px 0;font-size:12px;color:var(--success);font-weight:700"><span>Change</span><span>\u20B1'+(o.change||0).toFixed(2)+'</span></div>':'<div style="display:flex;justify-content:space-between;padding:5px 0;font-size:12px"><span>QR Payment</span><span>'+p.label+'</span></div>';
  var payStatusHtml='<div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;font-size:12px;border-top:1px solid var(--border);margin-top:8px"><span style="font-weight:700">Payment Status</span>'+getPaymentStatusBadge(o)+'</div>';
  if(o.paymentStatus==='paid'&&o.paidBy){payStatusHtml+='<div style="font-size:10px;color:var(--fg-muted);text-align:right;margin-bottom:6px">Confirmed by '+o.paidBy+' at '+o.paidAt+'</div>';}
  var contactHtml='<div class="grid-2" style="gap:10px;margin-bottom:14px"><div style="background:rgba(255,255,255,0.92);padding:10px;border-radius:var(--radius-sm)"><div style="font-size:9px;color:var(--fg-muted);text-transform:uppercase;margin-bottom:3px">Email</div><div style="font-weight:600;font-size:13px">'+escapeHtml(o.email||'Not provided')+'</div></div><div style="background:rgba(255,255,255,0.92);padding:10px;border-radius:var(--radius-sm)"><div style="font-size:9px;color:var(--fg-muted);text-transform:uppercase;margin-bottom:3px">Contact Number</div><div style="font-weight:600;font-size:13px">'+escapeHtml(o.contactNumber||'Not provided')+'</div></div></div>';
  document.getElementById('orderDetailContent').innerHTML='<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px"><span style="font-size:22px;font-weight:900;font-family:\'Playfair Display\'">#'+o.id+'</span>'+getStatusBadge(o.status)+'</div>'+tl+'<div class="grid-2" style="gap:10px;margin-bottom:14px"><div style="background:rgba(255,255,255,0.92);padding:10px;border-radius:var(--radius-sm)"><div style="font-size:9px;color:var(--fg-muted);text-transform:uppercase;margin-bottom:3px">Customer</div><div style="font-weight:600;font-size:13px">'+escapeHtml(o.customer)+'</div></div><div style="background:rgba(255,255,255,0.92);padding:10px;border-radius:var(--radius-sm)"><div style="font-size:9px;color:var(--fg-muted);text-transform:uppercase;margin-bottom:3px">Branch</div><div style="font-weight:600;font-size:13px">'+brInfo.name+'</div></div></div>'+contactHtml+'<div class="grid-2" style="gap:10px;margin-bottom:14px"><div style="background:rgba(255,255,255,0.92);padding:10px;border-radius:var(--radius-sm)"><div style="font-size:9px;color:var(--fg-muted);text-transform:uppercase;margin-bottom:3px">Type / Payment</div><div style="font-weight:600;font-size:13px">'+o.type+' \u2014 '+p.label+'</div></div><div style="background:rgba(255,255,255,0.92);padding:10px;border-radius:var(--radius-sm)"><div style="font-size:9px;color:var(--fg-muted);text-transform:uppercase;margin-bottom:3px">Time</div><div style="font-weight:600;font-size:13px">'+o.time+'</div></div></div><div style="background:rgba(255,255,255,0.92);padding:12px;border-radius:var(--radius-sm)"><div style="font-size:9px;color:var(--fg-muted);text-transform:uppercase;margin-bottom:8px">Items</div>'+ih+dh+'<div style="display:flex;justify-content:space-between;padding:8px 0;margin-top:4px"><span style="font-weight:700">Total</span><span style="font-weight:900;color:var(--gold-light);font-size:15px">\u20B1'+o.total.toFixed(2)+'</span></div>'+cashInfo+payStatusHtml+'</div>';
  var fb='';
  if(showActions){
    fb+='<button class="btn btn-secondary btn-sm" onclick="openOrderReceipt('+o.id+')"><i class="fas fa-receipt"></i> Receipt</button>';
    if(o.paymentStatus==='pending')fb+='<button class="btn btn-gold btn-sm" onclick="markOrderPaid('+o.id+');closeModal(\'orderDetailModal\');renderOrders()"><i class="fas '+p.icon+'"></i> Mark Paid</button>';
    if(o.status!=='completed'&&o.status!=='cancelled'){
      if(o.status==='pending')fb+='<button class="btn btn-sm" style="background:var(--info);color:#fff" onclick="updateOrderStatus('+o.id+',\'preparing\');closeModal(\'orderDetailModal\');renderOrders()"><i class="fas fa-blender"></i> Prepare</button>';
      if(o.status==='preparing')fb+='<button class="btn btn-sm" style="background:var(--accent);color:#fff" onclick="updateOrderStatus('+o.id+',\'serving\');closeModal(\'orderDetailModal\');renderOrders()"><i class="fas fa-concierge-bell"></i> Serve</button>';
      if(o.status==='ready')fb+='<button class="btn btn-success btn-sm" onclick="updateOrderStatus('+o.id+',\'completed\');closeModal(\'orderDetailModal\');renderOrders()"><i class="fas fa-check"></i> Complete</button>';
      if(o.status==='pending')fb+='<button class="btn btn-danger btn-sm" onclick="updateOrderStatus('+o.id+',\'cancelled\');closeModal(\'orderDetailModal\');renderOrders()"><i class="fas fa-times"></i> Cancel</button>';
    }
  }
  fb+='<button class="btn btn-secondary btn-sm" onclick="closeModal(\'orderDetailModal\')">Close</button>';
  document.getElementById('orderDetailFooter').innerHTML=fb;openModal('orderDetailModal');
}

/* ========== REPORTS ========== */
var revChart=null,catChart=null;
function renderReports(){
  var br=getBranch();var brOrders=orders.filter(function(o){return o.branch===br&&o.status!=='cancelled'&&o.paymentStatus==='paid';});
  var totalRev=brOrders.reduce(function(s,o){return s+o.total;},0);var totalOrders=brOrders.length;var avgOrder=totalOrders>0?totalRev/totalOrders:0;var itemsSold=brOrders.reduce(function(s,o){return s+o.items.reduce(function(ss,i){return ss+i.qty;},0);},0);
  document.getElementById('rptRevenue').textContent='\u20B1'+totalRev.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g,',');document.getElementById('rptAvgSales').textContent='\u20B1'+avgOrder.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g,',');document.getElementById('rptOrders').textContent=totalOrders;document.getElementById('rptItemsSold').textContent=itemsSold;
  var rc=document.getElementById('revenueChart');if(rc){if(revChart)revChart.destroy();revChart=new Chart(rc,{type:'bar',data:{labels:['Jan','Feb','Mar','Apr','May','Jun'],datasets:[{label:'Revenue (\u20B1)',data:[14200,15800,17200,16400,17800,18640],backgroundColor:['rgba(14,140,74,0.3)','rgba(14,140,74,0.3)','rgba(14,140,74,0.3)','rgba(14,140,74,0.3)','rgba(14,140,74,0.3)','#12864e'],borderColor:'#12864e',borderWidth:1,borderRadius:6}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{grid:{display:false},ticks:{color:'#5f7f6b',font:{size:10}}},y:{grid:{color:'rgba(26,53,40,0.5)'},ticks:{color:'#5f7f6b',font:{size:10},callback:function(v){return '\u20B1'+(v/1000)+'k';}}}}}});}
  var cc=document.getElementById('categoryChart');if(cc){if(catChart)catChart.destroy();var cd={};products.forEach(function(p){cd[p.category]=(cd[p.category]||0)+getBranchSold(p)*(p.prices.R||55);});catChart=new Chart(cc,{type:'doughnut',data:{labels:Object.keys(cd),datasets:[{data:Object.values(cd),backgroundColor:['#12864e','#2f9e62','#E53170','#FFD700','#C8956C','#F5A623','#5B8DEF'],borderWidth:0,hoverOffset:8}]},options:{responsive:true,maintainAspectRatio:false,cutout:'65%',plugins:{legend:{position:'bottom',labels:{color:'#5f7f6b',padding:14,font:{size:11}}}}}});}
}

/* ========== SETTINGS ========== */
function renderSettings(){
  updateLogoPreview();
  var newPreview=document.getElementById('logoNewPreview');if(newPreview)newPreview.style.display='none';
  var saveBtn=document.getElementById('logoSaveBtn');if(saveBtn)saveBtn.disabled=true;
  _pendingLogo=null;
  var fi=document.getElementById('logoFileInput');if(fi)fi.value='';
}
function saveSettings(){setData('lowStockThreshold',document.getElementById('lowStockThreshold').value);showToast('Settings saved','success');updateNotifBadge();}
function saveBranchInfo(){var b=document.getElementById('settingsBranch').value;document.getElementById('branchSelect').value=b;onBranchChange();showToast('Branch info saved','success');}

/* ========== PROFILE ========== */
function renderProfile(){
  if(!currentUser)return;
  var ini=currentUser.fullName.split(' ').map(function(w){return w[0];}).join('').toUpperCase().substring(0,2);
  document.getElementById('profileAvatar').textContent=ini;document.getElementById('profileName').textContent=currentUser.fullName;
  var roleLabels={admin:'Branch Admin',cashier:'Cashier',customer:'Customer',guest:'Guest'};
  document.getElementById('profileRole').textContent=roleLabels[currentUser.role]||currentUser.role;
  document.getElementById('profileFullName').value=currentUser.fullName;
  document.getElementById('profileEmail').value=currentUser.email||'';
  document.getElementById('profileContact').value=currentUser.contactNumber||'';
  document.getElementById('profileUsername').value=currentUser.username||'(Guest \u2014 no username)';
  document.getElementById('profileSince').value=currentUser.since||new Date().toISOString().split('T')[0];
  var pwCard=document.getElementById('profilePasswordCard');if(pwCard)pwCard.style.display=isGuest()?'none':'block';
  var unField=document.getElementById('profileUsername');if(unField)unField.disabled=true;
}
function updateProfile(){var n=document.getElementById('profileFullName').value.trim();var email=document.getElementById('profileEmail').value.trim();var contact=document.getElementById('profileContact').value.trim();if(!n){showToast('Name cannot be empty','error');return;}if(email&&!isValidEmail(email)){showToast('Enter a valid email address','error');return;}if(contact&&!isValidContact(contact)){showToast('Enter a valid contact number','error');return;}currentUser.fullName=n;currentUser.email=email;currentUser.contactNumber=contact;if(!currentUser.isGuest){var u=users.find(function(usr){return usr.id===currentUser.id;});if(u){u.fullName=n;u.email=email;u.contactNumber=contact;}setData('users',users);}setSession(currentUser);var ini=n.split(' ').map(function(w){return w[0];}).join('').toUpperCase().substring(0,2);var sidebarAvatar=document.getElementById('sidebarAvatar');var sidebarName=document.getElementById('sidebarName');if(sidebarAvatar)sidebarAvatar.textContent=ini;if(sidebarName)sidebarName.textContent=n;renderProfile();showToast('Profile updated','success');}
function changePassword(){
  if(isGuest()){showToast('Guest accounts do not have passwords','warning');return;}
  var c=document.getElementById('pwCurrent').value;var n=document.getElementById('pwNew').value;var cf=document.getElementById('pwConfirm').value;
  if(!c||!n||!cf){showToast('Fill all fields','error');return;}
  if(simpleHash(c)!==currentUser.password){showToast('Current password incorrect','error');return;}
  if(n.length<6){showToast('Min 6 characters','error');return;}
  if(n!==cf){showToast('Passwords do not match','error');return;}
  currentUser.password=simpleHash(n);var u=users.find(function(usr){return usr.id===currentUser.id;});if(u)u.password=currentUser.password;
  setData('users',users);document.getElementById('pwCurrent').value='';document.getElementById('pwNew').value='';document.getElementById('pwConfirm').value='';
  showToast('Password changed','success');
}

/* ========== CHATBOT ========== */
var chatOpen=false;
/* ========== ASSISTANT ==========
 *
 * Replies come from the server, which knows the live menu and this
 * customer's own reservations. A signed-in customer's conversation is
 * stored against their account, so it is waiting for them on any device.
 * The landing page runs the same assistant through public/js/queens-chat.js.
 */
var chatLoaded = false;
var chatBusy = false;

function toggleChatbot(){
  chatOpen=!chatOpen;
  document.getElementById('chatWindow').classList.toggle('open',chatOpen);
  if(chatOpen&&!chatLoaded)loadChatHistory();
}

function loadChatHistory(){
  chatLoaded=true;
  fetch(@json(url('/chat')),{headers:{Accept:'application/json'},credentials:'same-origin'})
    .then(function(r){return r.ok?r.json():{data:[]};})
    .then(function(payload){
      var messages=payload.data||[];
      messages.forEach(function(m){
        if(m.author==='customer')addUserMessage(m.body);
        else addBotMessage(m.body,m.quick_replies);
      });
      if(!messages.length){
        var who=(currentUser&&currentUser.fullName)?currentUser.fullName.split(' ')[0]:'there';
        addBotMessage('Hello, '+escapeHtml(who)+"! \uD83D\uDC51 How can I help you today?",
          ['See the menu','How do I reserve?','My reservations','Opening hours']);
      }
    })
    .catch(function(){
      addBotMessage('I could not load our chat just now. Please try again in a moment.',[]);
    });
}

function addBotMessage(t,qr){
  qr=Array.isArray(qr)?qr:[];
  var c=document.getElementById('chatMessages');
  var m=document.createElement('div');
  m.className='chat-msg bot';
  var q='';
  if(qr.length){
    q='<div class="quick-replies">'+qr.map(function(r){
      return '<button class="quick-reply" onclick="handleQuickReply(\''+String(r).replace(/'/g,"\\'")+'\')">'+escapeHtml(r)+'</button>';
    }).join('')+'</div>';
  }
  // Bot copy is composed server side and may carry simple markup.
  m.innerHTML=t+q;
  c.appendChild(m);c.scrollTop=c.scrollHeight;
}

function addUserMessage(t){
  var c=document.getElementById('chatMessages');
  var m=document.createElement('div');
  m.className='chat-msg user';
  m.textContent=t;
  c.appendChild(m);c.scrollTop=c.scrollHeight;
}

function sendChat(){
  var i=document.getElementById('chatInput');
  var t=i.value.trim();
  if(!t)return;
  i.value='';
  askAssistant(t);
}

function handleQuickReply(t){askAssistant(t);}

function askAssistant(text){
  if(chatBusy)return;
  chatBusy=true;
  addUserMessage(text);

  var c=document.getElementById('chatMessages');
  var typing=document.createElement('div');
  typing.className='chat-msg bot';
  typing.textContent='typing\u2026';
  c.appendChild(typing);c.scrollTop=c.scrollHeight;

  fetch(@json(url('/chat')),{
    method:'POST',
    credentials:'same-origin',
    headers:{'Content-Type':'application/json',Accept:'application/json','X-CSRF-TOKEN':csrfToken()},
    body:JSON.stringify({message:text})
  })
    .then(function(r){return r.json();})
    .then(function(payload){
      typing.remove();
      addBotMessage(payload.body||'Sorry, I did not catch that.',payload.quick_replies||[]);
    })
    .catch(function(){
      typing.remove();
      addBotMessage('I could not reach the shop just now. Please try again shortly.',[]);
    })
    .finally(function(){chatBusy=false;});
}


/* ========== INIT ========== */
function init(){
  updateAllLogos();
  var session=AUTHENTICATED_STAFF||getSession();
  if(session){
    var sessionUser=userFromSession(session);
    if(sessionUser){currentUser=sessionUser;setSession(sessionUser);enterApp();return;}
  }
  document.getElementById('loginPage').classList.remove('hidden');
  document.getElementById('appLayout').style.display='none';
  document.getElementById('chatbotContainer').style.display='none';
  var chatBtn=document.getElementById('customerChatBtn');if(chatBtn)chatBtn.style.display='none';
  var onEnter=function(id,fn){var el=document.getElementById(id);if(el)el.addEventListener('keypress',function(e){if(e.key==='Enter')fn();});};
  onEnter('loginPassword',handleLogin);
  onEnter('loginUsername',handleLogin);
  onEnter('signinEmail',handleCustomerSignIn);
  onEnter('signinPassword',handleCustomerSignIn);
  onEnter('regPasswordConfirm',handleCustomerRegister);
  onEnter('verifyCode',handleCustomerVerify);
  onEnter('verifyCode',handleCustomerVerify);
  // Guest OTP was replaced by customer accounts.
  updateInstallButton();
}
init();
window.addEventListener('beforeinstallprompt',function(e){
  e.preventDefault();
  deferredInstallPrompt=e;
  updateInstallButton();
});
window.addEventListener('appinstalled',function(){
  deferredInstallPrompt=null;
  updateInstallButton();
  showToast("Queen's Cup app installed.",'success');
});
if ('serviceWorker' in navigator) {
  window.addEventListener('load', function () {
    navigator.serviceWorker.register('{{ asset('sw.js') }}').catch(function () {});
  });
}
</script>
</body>
</html>

