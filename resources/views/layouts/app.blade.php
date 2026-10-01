<!doctype html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}"><title>{{ config('app.name') }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root{--ink:#183229;--muted:#6b7d76;--line:#dfe9e3;--bg:#f4f7f5;--panel:#fff;--green:#087443;--green-dark:#055a35;--green-soft:#e7f5ed;--gold:#f2b705;--gold-soft:#fff7d9;--blue:#2859c5;--blue-soft:#eaf0ff;--orange:#e56a1f;--orange-soft:#fff0e6;--shadow:0 10px 28px rgba(17,59,40,.07)}
*{box-sizing:border-box}body{margin:0;font-family:Manrope,Segoe UI,Arial,sans-serif;color:var(--ink);background:var(--bg)}button,input,select{font:inherit}a{color:inherit;text-decoration:none}
.shell{display:grid;grid-template-columns:260px minmax(0,1fr);min-height:100vh}.side{position:sticky;top:0;height:100vh;padding:24px 18px 18px;display:flex;flex-direction:column;gap:26px;color:#fff;background:var(--green-dark);overflow:hidden}.side:after{content:"";position:absolute;width:230px;height:230px;left:-85px;bottom:-115px;border:42px solid rgba(242,183,5,.09);border-radius:50%;pointer-events:none}
.brand{position:relative;display:flex;align-items:center;gap:12px;min-width:0}.brand-logo{width:56px;height:56px;flex:0 0 56px;object-fit:contain;filter:drop-shadow(0 5px 8px rgba(0,0,0,.2))}.brand-copy{min-width:0}.brand-title{display:block;font-size:18px;line-height:1.15;font-weight:800}.brand-subtitle{display:block;margin-top:4px;color:#b9d8c7;font-size:10px;line-height:1.4;font-weight:700;text-transform:uppercase}
.nav{position:relative;display:grid;gap:7px}.nav-label{padding:0 12px 7px;color:#9bc4ad;font-size:10px;font-weight:800;text-transform:uppercase}.nav a,.logout{width:100%;min-height:46px;padding:0 13px;display:flex;align-items:center;gap:12px;border:0;border-radius:7px;color:#d8ebe1;background:transparent;cursor:pointer;font-weight:700;text-align:left}.nav a:hover,.nav a.active,.logout:hover{color:#fff;background:rgba(255,255,255,.11)}.nav a.active{box-shadow:inset 3px 0 var(--gold)}.nav svg,.logout svg{width:19px;height:19px;flex:0 0 19px}.logout-form{position:relative;margin-top:auto;padding-top:14px;border-top:1px solid rgba(255,255,255,.12)}
.main{min-width:0;padding:30px clamp(20px,3vw,42px)}.top{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:22px}.page-heading{display:flex;align-items:center;gap:14px}.heading-mark{width:4px;height:42px;border-radius:4px;background:var(--gold)}h1{margin:0;font-size:28px;line-height:1.2}.muted{color:var(--muted)}.page-heading .muted{display:block;margin-top:4px;font-size:13px}.grid{display:grid;gap:16px}.stats{grid-template-columns:repeat(4,minmax(0,1fr))}.admin-stats{grid-template-columns:repeat(3,minmax(0,1fr))}.dashboard-review-link{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-top:16px;padding:13px 16px;border:1px solid #cde5d7;border-radius:7px;color:var(--green);background:var(--green-soft);font-size:12px}.dashboard-review-link:hover{border-color:var(--green);background:#dff3e8}.dashboard-review-link>span{display:flex;align-items:center;gap:9px}.dashboard-review-link svg{width:16px;height:16px;flex:none}.dashboard-review-link strong{font-size:12px}
.card{background:var(--panel);border:1px solid var(--line);border-radius:8px;padding:20px;box-shadow:var(--shadow)}.stat{min-height:132px;display:flex;flex-direction:column;justify-content:space-between}.stat-head{display:flex;align-items:center;justify-content:space-between;gap:12px}.stat-label{color:var(--muted);font-size:13px;font-weight:700}.stat b{display:block;font-size:30px;line-height:1}.icon-box{width:42px;height:42px;flex:0 0 42px;display:grid;place-items:center;border-radius:8px}.icon-box svg{width:21px;height:21px}.icon-green{color:var(--green);background:var(--green-soft)}.icon-gold{color:#a87400;background:var(--gold-soft)}.icon-blue{color:var(--blue);background:var(--blue-soft)}.icon-orange{color:var(--orange);background:var(--orange-soft)}.filter-tabs{display:flex;gap:4px;padding:4px;border:1px solid var(--line);border-radius:8px;background:#fff}.filter-tabs a{padding:8px 12px;border-radius:6px;color:var(--muted);font-size:12px;font-weight:700}.filter-tabs a.active{color:#fff;background:var(--green)}dialog{width:min(430px,calc(100% - 32px));padding:0;border:0;border-radius:8px;box-shadow:0 24px 70px rgba(9,50,31,.25)}dialog::backdrop{background:rgba(9,35,24,.55)}.modal-body{padding:24px}.modal-icon{margin-bottom:16px}.modal-body h2{margin:0 0 8px;font-size:20px}.modal-body p{margin:0;color:var(--muted);line-height:1.6}.modal-actions{display:flex;justify-content:flex-end;gap:10px;padding:15px 24px;border-top:1px solid var(--line);background:#f8faf9}
.actions{display:flex;gap:10px;flex-wrap:wrap;align-items:center}.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:40px;padding:0 14px;border:1px solid var(--line);border-radius:7px;background:#fff;color:var(--ink);cursor:pointer;font-weight:700}.btn:hover{border-color:#b9cfc2;background:#f9fcfa}.btn.primary{border-color:var(--green);color:#fff;background:var(--green)}.btn.primary:hover{border-color:var(--green-dark);background:var(--green-dark)}.btn.danger{color:#b42318}.btn svg{width:17px;height:17px}
input,select{width:100%;min-height:43px;padding:0 12px;border:1px solid var(--line);border-radius:7px;color:var(--ink);background:#fff;outline:none}input:focus,select:focus{border-color:var(--green);box-shadow:0 0 0 3px rgba(8,116,67,.12)}label{display:grid;gap:7px;font-size:13px;font-weight:700}form .fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}table{width:100%;border-collapse:collapse;background:#fff}th,td{padding:14px 16px;border-bottom:1px solid var(--line);text-align:left;vertical-align:middle}th{color:var(--muted);background:#f8faf9;font-size:11px;font-weight:800;text-transform:uppercase}tr:last-child td{border-bottom:0}tbody tr:hover{background:#fbfdfc}.pill{display:inline-flex;padding:5px 10px;border-radius:999px;color:var(--green);background:var(--green-soft);font-size:11px;font-weight:800}.pill.warn{color:#b84420;background:var(--orange-soft)}.notice{margin-bottom:16px;padding:12px 14px;border:1px solid #cbe8d7;border-radius:7px;color:var(--green);background:var(--green-soft);font-weight:700}.error{color:#b42318;font-size:12px;font-weight:600}.pagination{margin-top:14px}.report{max-width:820px}.section-head{margin-bottom:14px;display:flex;align-items:center;justify-content:space-between;gap:14px}.section-head h2{margin:0;font-size:18px}
.appbar{height:72px;margin:-30px calc(clamp(20px,3vw,42px)*-1) 28px;padding:0 clamp(20px,3vw,42px);display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--line);background:rgba(255,255,255,.92);backdrop-filter:blur(12px)}.appbar-context{display:flex;align-items:center;gap:9px;color:var(--muted);font-size:12px;font-weight:700}.appbar-context svg{width:16px}.user-chip{display:flex;align-items:center;gap:11px}.user-avatar{width:38px;height:38px;display:grid;place-items:center;border:1px solid #cce3d5;border-radius:50%;color:#fff;background:var(--green);font-size:13px;font-weight:800}.user-meta{display:grid;gap:2px;text-align:right}.user-meta strong{font-size:12px}.user-meta span{color:var(--muted);font-size:10px;font-weight:700;text-transform:uppercase}.page-content{animation:pageIn .2s ease}.card{transition:border-color .2s ease,box-shadow .2s ease,transform .2s ease}.card:hover{border-color:#cbdcd2}.stat:hover{transform:translateY(-2px);box-shadow:0 14px 34px rgba(17,59,40,.1)}.btn{transition:background .15s ease,border-color .15s ease,color .15s ease,transform .15s ease,box-shadow .15s ease}.btn:active{transform:translateY(1px)}.btn.primary{box-shadow:0 5px 13px rgba(8,116,67,.18)}th{height:46px}td{height:58px}.ajax-progress{position:fixed;z-index:9999;left:0;top:0;width:100%;height:3px;opacity:0;transform:scaleX(0);transform-origin:left;background:var(--gold);transition:transform .3s ease,opacity .2s}.ajax-progress.loading{opacity:1;transform:scaleX(.72)}.ajax-progress.done{opacity:0;transform:scaleX(1)}.toast-stack{position:fixed;z-index:10000;right:22px;bottom:22px;display:grid;gap:10px}.toast{min-width:280px;max-width:380px;padding:13px 15px;display:flex;align-items:center;gap:10px;border:1px solid #cbe8d7;border-radius:8px;color:var(--green-dark);background:#fff;box-shadow:0 16px 44px rgba(9,50,31,.18);animation:toastIn .25s ease}.toast svg{width:19px;color:var(--green)}.is-updating{opacity:.5;pointer-events:none}.empty-state{padding:46px 20px!important;text-align:center!important}.empty-state svg{width:34px;height:34px;margin-bottom:8px;color:#9bb3a7}@keyframes pageIn{from{opacity:0;transform:translateY(5px)}to{opacity:1;transform:none}}@keyframes toastIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}
.appbar-tools{display:flex;align-items:center;gap:8px}.menu-wrap{position:relative}.tool-button,.profile-button{border:0;background:transparent;cursor:pointer}.tool-button{position:relative;width:40px;height:40px;display:grid;place-items:center;border-radius:8px;color:var(--muted)}.tool-button:hover,.profile-button:hover{color:var(--green);background:var(--green-soft)}.tool-button svg{width:19px}.notification-dot{position:absolute;top:7px;right:7px;width:8px;height:8px;border:2px solid #fff;border-radius:50%;background:var(--orange)}.profile-button{padding:5px 7px;display:flex;align-items:center;gap:11px;border-radius:8px}.popup-menu{position:absolute;z-index:1000;top:calc(100% + 9px);right:0;width:280px;padding:8px;border:1px solid var(--line);border-radius:8px;background:#fff;box-shadow:0 18px 52px rgba(9,50,31,.17);opacity:0;visibility:hidden;transform:translateY(-5px);transition:.16s ease}.popup-menu.open{opacity:1;visibility:visible;transform:none}.popup-head{padding:11px 12px;border-bottom:1px solid var(--line)}.popup-head strong{display:block;font-size:13px}.popup-head span{color:var(--muted);font-size:11px}.popup-menu a,.popup-menu .menu-action{width:100%;min-height:40px;padding:0 11px;display:flex;align-items:center;gap:10px;border:0;border-radius:6px;color:var(--ink);background:transparent;font-size:12px;font-weight:700;text-align:left;cursor:pointer}.popup-menu a:hover,.popup-menu .menu-action:hover{color:var(--green);background:var(--green-soft)}.popup-menu svg{width:17px}.popup-empty{padding:16px 12px;color:var(--muted);font-size:12px;text-align:center}.analytics{grid-template-columns:1.2fr .8fr;margin-top:18px}.chart-row{display:grid;grid-template-columns:82px 1fr 38px;align-items:center;gap:10px;margin-top:14px;font-size:12px}.chart-track{height:9px;overflow:hidden;border-radius:6px;background:#edf2ef}.chart-fill{height:100%;border-radius:6px;background:var(--green)}.chart-fill.gold{background:var(--gold)}.chart-fill.blue{background:var(--blue)}.chart-fill.orange{background:var(--orange)}.filter-bar{display:grid;grid-template-columns:repeat(4,minmax(140px,1fr)) auto;gap:10px;margin-bottom:16px;padding:14px}.filter-bar label{gap:5px}.filter-bar select,.filter-bar input{min-height:38px}.filter-bar .btn{align-self:end}
@media(max-width:1020px){.stats,.admin-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.analytics{grid-template-columns:1fr}.filter-bar{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:760px){.shell{grid-template-columns:1fr}.side{position:static;width:100%;height:auto;padding:16px;gap:15px}.brand-logo{width:46px;height:46px;flex-basis:46px}.brand-subtitle,.nav-label{display:none}.nav{grid-template-columns:repeat(4,minmax(0,1fr));gap:5px}.nav a{min-height:54px;padding:7px 5px;flex-direction:column;justify-content:center;gap:4px;font-size:11px}.nav a.active{box-shadow:inset 0 -3px var(--gold)}.main{padding:22px 16px}.appbar{height:62px;margin:-22px -16px 22px;padding:0 16px}.appbar-context,.user-meta{display:none}.top{align-items:flex-start;flex-direction:column}.stats,.admin-stats,form .fields,.filter-bar{grid-template-columns:1fr}.stat{min-height:112px}.toast-stack{left:16px;right:16px;bottom:16px}.toast{min-width:0;max-width:none}}@media print{.side,.appbar,.actions,.btn,.ajax-progress,.toast-stack{display:none!important}.shell{display:block}.main{padding:0}body{background:#fff}.card{box-shadow:none}}
/* Shared professional UI refinements */
.table-wrap{overflow:auto}.data-card{padding:0;overflow:hidden}.data-card table{min-width:680px}th{position:sticky;top:0;z-index:2;white-space:nowrap}tbody tr{transition:background .15s}tbody tr:hover{background:#f7fbf8}.row-title{font-weight:800}.row-subtitle{display:block;margin-top:3px;color:var(--muted);font-size:11px}.pill{align-items:center;gap:6px}.pill:before{content:"";width:6px;height:6px;border-radius:50%;background:currentColor}.pill.info{color:var(--blue);background:var(--blue-soft)}.pill.neutral{color:#68766f;background:#edf2ef}.btn.icon-only{width:38px;min-height:38px;padding:0}.empty-state{height:240px;padding:44px 20px!important;color:var(--muted);text-align:center!important}.empty-state svg{display:block;width:38px;height:38px;margin:0 auto 12px;color:#8eaa9b}.empty-state strong{display:block;margin-bottom:4px;color:var(--ink);font-size:14px}.empty-state span{font-size:12px}.form-card{max-width:920px}.form-footer{display:flex;justify-content:flex-end;gap:10px;margin:20px -20px -20px;padding:16px 20px;border-top:1px solid var(--line);background:#fafcfb}.btn[disabled]{opacity:.65;cursor:wait}.confirm-name{color:var(--ink);font-weight:800}
@media(max-width:760px){.data-card{margin-left:-2px;margin-right:-2px}.top>.actions{width:100%}.top>.actions .btn{flex:1}.form-footer{justify-content:stretch}.form-footer .btn{flex:1}h1{font-size:24px}.actions{gap:7px}}
.form-shell{max-width:980px}.form-section{padding:0}.form-section+.form-section{margin-top:22px;padding-top:22px;border-top:1px solid var(--line)}.form-section-head{display:flex;align-items:center;gap:11px;margin-bottom:16px}.form-section-head .icon-box{width:36px;height:36px;flex-basis:36px}.form-section-head .icon-box svg{width:18px;height:18px}.form-section-head h2{margin:0;font-size:15px}.form-section-head span{display:block;margin-top:2px;color:var(--muted);font-size:11px}.field-hint{color:var(--muted);font-size:11px;font-weight:500}.grade-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}.grade-preview{display:grid;grid-template-columns:1fr auto;align-items:center;gap:20px;margin-top:18px;padding:16px;border:1px solid #cde5d7;border-radius:7px;background:var(--green-soft)}.grade-preview small{display:block;color:var(--muted);font-weight:700}.grade-preview strong{display:block;margin-top:3px;font-size:15px}.grade-score{font-size:28px!important;color:var(--green);font-weight:800}.select-card{max-width:980px;margin-bottom:16px}.select-card .fields{grid-template-columns:1fr}.required:after{content:" *";color:#b42318}@media(max-width:760px){.grade-grid{grid-template-columns:1fr}.grade-preview{grid-template-columns:1fr}.grade-score{text-align:left}}
.student-app .shell{display:block}.student-app .side{display:none}.student-app .main{max-width:1440px;margin:0 auto;padding-left:clamp(20px,4vw,56px);padding-right:clamp(20px,4vw,56px)}.student-app .appbar{margin-left:calc(clamp(20px,4vw,56px)*-1);margin-right:calc(clamp(20px,4vw,56px)*-1);padding-left:clamp(20px,4vw,56px);padding-right:clamp(20px,4vw,56px)}.student-hub-head{display:flex;align-items:end;justify-content:space-between;gap:20px;margin-bottom:28px}.student-hub-head h1{font-size:30px}.student-identity{display:flex;align-items:center;gap:12px}.student-identity .user-avatar{width:46px;height:46px}.hub-actions{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-bottom:30px}.hub-action{min-height:80px;padding:16px;display:flex;align-items:center;gap:13px;border:1px solid var(--line);border-radius:8px;background:#fff;box-shadow:var(--shadow);transition:.18s}.hub-action:hover{border-color:#a9cdb9;transform:translateY(-2px);box-shadow:0 14px 34px rgba(17,59,40,.1)}.hub-action strong{display:block;font-size:13px}.hub-action span{display:block;margin-top:3px;color:var(--muted);font-size:11px}.class-heading{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}.class-heading h2{margin:0;font-size:20px}.class-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}.class-tile{min-height:205px;overflow:hidden;border:1px solid var(--line);border-radius:8px;background:#fff;box-shadow:var(--shadow);transition:.18s}.class-tile:hover{transform:translateY(-3px);border-color:#a9cdb9;box-shadow:0 16px 36px rgba(17,59,40,.12)}.class-cover{height:72px;padding:17px 18px;color:#fff;background:#087443}.class-tile:nth-child(4n+2) .class-cover{background:#2859c5}.class-tile:nth-child(4n+3) .class-cover{background:#9a6b00}.class-tile:nth-child(4n+4) .class-cover{background:#b94d22}.class-cover strong{font-size:19px}.class-cover span{display:block;margin-top:3px;font-size:11px;opacity:.85}.class-body{padding:17px 18px}.class-body h3{margin:0 0 8px;font-size:14px}.class-meta{display:flex;align-items:center;gap:7px;margin-top:7px;color:var(--muted);font-size:11px}.class-meta svg{width:14px;height:14px}.class-status{margin-top:14px;padding-top:12px;border-top:1px solid var(--line);display:flex;justify-content:space-between;font-size:11px}.hub-empty{grid-column:1/-1;padding:54px 20px;text-align:center;border:1px dashed #bdd2c5;border-radius:8px;color:var(--muted);background:#fafcfb}.hub-empty svg{width:38px;height:38px;margin-bottom:10px}@media(max-width:900px){.class-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:760px){.student-app .main{padding:16px}.student-app .appbar{margin:-16px -16px 22px;padding:0 16px}.student-hub-head{align-items:flex-start;flex-direction:column}.hub-actions,.class-grid{grid-template-columns:1fr}.hub-action{min-height:70px}}
.faculty-head{display:flex;align-items:flex-end;justify-content:space-between;gap:18px;margin-bottom:24px}.faculty-head h1{margin-top:4px}.faculty-summary{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-bottom:26px}.faculty-summary a,.faculty-summary>div{min-height:92px;padding:16px 18px;display:flex;align-items:center;gap:13px;border:1px solid var(--line);border-radius:8px;background:#fff;box-shadow:var(--shadow)}.faculty-summary a{transition:.18s}.faculty-summary a:hover{transform:translateY(-2px);border-color:#a9cdb9}.faculty-summary strong{display:block;font-size:22px}.faculty-summary span:not(.icon-box){display:block;margin-top:3px;color:var(--muted);font-size:11px}.faculty-class-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:15px}.faculty-class{padding:0;overflow:hidden}.faculty-class-top{padding:17px 18px;display:flex;align-items:flex-start;justify-content:space-between;gap:14px;border-bottom:1px solid var(--line);background:#f8fbf9}.faculty-class-code{display:flex;align-items:center;gap:11px}.faculty-class-code h3{margin:0;font-size:16px}.faculty-class-code span{display:block;margin-top:3px;color:var(--muted);font-size:11px}.faculty-class-body{padding:16px 18px}.faculty-progress{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin:14px 0}.faculty-progress div{padding:10px;border-radius:7px;background:#f4f7f5}.faculty-progress strong{display:block;font-size:16px}.faculty-progress span{color:var(--muted);font-size:10px}.faculty-class-actions{display:flex;gap:8px;padding-top:14px;border-top:1px solid var(--line)}@media(max-width:900px){.faculty-summary{grid-template-columns:1fr}.faculty-class-grid{grid-template-columns:1fr}}@media(max-width:760px){.faculty-head{align-items:flex-start;flex-direction:column}.faculty-head .actions{width:100%}.faculty-head .btn{flex:1}}
.student-back{width:38px;height:38px;margin-bottom:12px;display:grid;place-items:center;border:1px solid var(--line);border-radius:7px;color:var(--ink);background:#fff;box-shadow:0 5px 14px rgba(17,59,40,.06);transition:.15s}.student-back:hover{color:var(--green);border-color:#a9cdb9;background:var(--green-soft)}.student-back svg{width:18px;height:18px}.gradebook-stats{grid-template-columns:repeat(3,minmax(0,1fr))}@media(max-width:760px){.gradebook-stats{grid-template-columns:1fr}.gradebook-stats .stat{min-height:100px}}
.sheet-table{width:100%;border-collapse:separate;border-spacing:0;font-variant-numeric:tabular-nums}.sheet-table th,.sheet-table td{height:46px;padding:9px 13px;border-right:1px solid #e1e9e4;border-bottom:1px solid #e1e9e4}.sheet-table th:last-child,.sheet-table td:last-child{border-right:0}.sheet-table thead th{height:42px;color:#536e60;background:#edf4ef;font-size:11px}.sheet-table tbody tr:nth-child(even){background:#f8fbf9}.sheet-table tbody tr:hover{background:#eaf5ee}.sheet-table tbody tr:last-child td{border-bottom:0}.sheet-table .sheet-number{text-align:right;white-space:nowrap}.sheet-table .sheet-result{text-align:center;white-space:nowrap}.sheet-table .sheet-action{text-align:center;width:64px}.sheet-table .row-title{font-size:13px}.sheet-table .row-subtitle{font-size:11px}.sheet-table select[data-gradebook-input]{min-height:35px;padding:0 8px;text-align:right;font-variant-numeric:tabular-nums}@media(max-width:760px){.sheet-table th,.sheet-table td{padding:8px 10px}.sheet-table .sheet-action{width:54px}}
.card table,.grade-class table{border-collapse:separate;border-spacing:0;font-size:12.5px;font-variant-numeric:tabular-nums}
.card table th,.card table td,.grade-class table th,.grade-class table td{height:44px;padding:8px 11px;border-right:1px solid #dfe8e2;border-bottom:1px solid #dfe8e2;line-height:1.4}
.card table th:last-child,.card table td:last-child,.grade-class table th:last-child,.grade-class table td:last-child{border-right:0}
.card table thead th,.grade-class table thead th{height:38px;color:#526d60;background:#eef4f0;font-size:10.5px;font-weight:800}
.card table tbody tr:nth-child(even),.grade-class table tbody tr:nth-child(even){background:#f8fbf9}
.card table tbody tr:hover,.grade-class table tbody tr:hover{background:#eaf5ee}
.card table .row-title,.grade-class table .row-title{font-size:12.5px}.card table .row-subtitle,.grade-class table .row-subtitle{font-size:10px}
.card table .btn,.grade-class table .btn{min-height:32px;padding:0 10px;font-size:11.5px}.card table .btn.icon-only,.grade-class table .btn.icon-only{width:32px;min-height:32px;padding:0}.card table .btn svg,.grade-class table .btn svg{width:15px;height:15px}
.card table select,.grade-class table select{min-height:32px;font-size:12px}.card table .pill,.grade-class table .pill{font-size:10px}
.student-filter-bar{grid-template-columns:2fr 1fr 1fr auto}
@media(max-width:1020px){.student-filter-bar{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:760px){.student-filter-bar{grid-template-columns:1fr}}
@media(max-width:760px){.card table th,.card table td,.grade-class table th,.grade-class table td{padding:7px 9px}}
@media(max-width:760px){.side{position:relative}}
.side{gap:18px;padding:22px 14px 16px;overflow-y:auto;scrollbar-width:thin;scrollbar-color:rgba(255,255,255,.25) transparent}
.side:after{display:none}
.side>.brand{padding:0 6px 18px;border-bottom:1px solid rgba(255,255,255,.14)}
.side .nav{gap:3px}
.side .nav a{min-height:43px;padding:0 12px;font-size:13px}
.side .nav a.active{background:#226d4b;box-shadow:inset 3px 0 var(--gold)}
.side .nav a:focus-visible{outline:2px solid var(--gold);outline-offset:2px}
.side .nav-label.nav-group{margin-top:8px;padding:13px 12px 5px;border-top:1px solid rgba(255,255,255,.12);color:#acd7bc}
.mobile-appbar-start,.mobile-nav-backdrop{display:none}
@media(max-width:760px){
    body:not(.student-app) .shell{display:block}
    body:not(.student-app) .side{position:fixed;z-index:1101;top:0;left:0;width:min(300px,calc(100vw - 56px));height:100dvh;padding:22px 16px;display:flex;gap:22px;overflow-y:auto;overscroll-behavior:contain;transform:translateX(-105%);transition:transform .22s ease;box-shadow:18px 0 48px rgba(0,0,0,.16)}
    body:not(.student-app).mobile-nav-open{overflow:hidden}
    body:not(.student-app).mobile-nav-open .side{transform:translateX(0)}
    body:not(.student-app) .side .brand-subtitle,body:not(.student-app) .side .nav-label{display:block}
    body:not(.student-app) .side .nav{display:grid;grid-template-columns:1fr;gap:5px}
    body:not(.student-app) .side .nav a{min-height:45px;padding:0 12px;flex-direction:row;justify-content:flex-start;gap:12px;font-size:13px}
    body:not(.student-app) .side .nav a.active{box-shadow:inset 3px 0 var(--gold)}
    body:not(.student-app) .mobile-appbar-start{display:flex;align-items:center;gap:6px;min-width:0}
    .mobile-appbar-start strong{font-size:15px}
    body:not(.student-app) .mobile-nav-backdrop{position:fixed;z-index:1100;inset:0;display:block;width:100%;border:0;background:rgba(9,35,24,.5);opacity:0;visibility:hidden;transition:opacity .22s ease,visibility .22s ease}
    body:not(.student-app).mobile-nav-open .mobile-nav-backdrop{opacity:1;visibility:visible}
    body:not(.student-app) .page-content,body:not(.student-app) .top,body:not(.student-app) .card{min-width:0}
    body:not(.student-app) .table-wrap{max-width:100%;overscroll-behavior-x:contain;-webkit-overflow-scrolling:touch}
    body:not(.student-app) .mobile-actions-table th:last-child,body:not(.student-app) .mobile-actions-table td:last-child{position:sticky;right:0;z-index:3;min-width:100px;background:#fff;border-left:1px solid var(--line);box-shadow:-5px 0 10px rgba(17,59,40,.05)}
    body:not(.student-app) .mobile-actions-table th:last-child{z-index:4;background:#f8faf9}
    body:not(.student-app) .mobile-actions-table tbody tr:hover td:last-child{background:#f7fbf8}
    body:not(.student-app) .mobile-actions-table td:last-child .actions{flex-wrap:nowrap}
    body:not(.student-app) .mobile-students-table,body:not(.student-app) .mobile-classes-table{min-width:0;table-layout:fixed}
    .mobile-students-table th:nth-child(3),.mobile-students-table td:nth-child(3),.mobile-students-table th:nth-child(4),.mobile-students-table td:nth-child(4){display:none}
    .mobile-students-table th:first-child{width:30%}.mobile-students-table th:nth-child(2){width:34%}.mobile-students-table th:last-child{width:36%}
    .mobile-students-table td:first-child .row-title{white-space:nowrap;font-size:11px}
    .mobile-students-table td:nth-child(2){overflow-wrap:anywhere}
    .mobile-students-table .student-list-email{display:none}
    .mobile-students-table td:last-child .actions{gap:4px}
    .mobile-students-table td:last-child .btn.icon-only{width:31px;min-height:34px}
    .mobile-students-table td:last-child .btn svg{width:15px}
    .mobile-student-status,.mobile-class-section,.mobile-class-period{display:block}
    .mobile-classes-table th:nth-child(2),.mobile-classes-table td:nth-child(2),.mobile-classes-table th:nth-child(3),.mobile-classes-table td:nth-child(3),.mobile-classes-table th:nth-child(4),.mobile-classes-table td:nth-child(4),.mobile-classes-table th:nth-child(5),.mobile-classes-table td:nth-child(5){display:none}
    .mobile-classes-table th:first-child{width:60%}.mobile-classes-table th:last-child{width:40%}
    .mobile-classes-table td:first-child{overflow-wrap:anywhere}
    .mobile-classes-table td:last-child .btn{padding:0 7px;gap:4px;font-size:11px;white-space:nowrap}
    .mobile-classes-table td:last-child .btn svg{width:14px}
    .gradebook-table{min-width:740px!important}
    .gradebook-table th:nth-child(2),.gradebook-table td:nth-child(2){display:none}
    .gradebook-table th:first-child,.gradebook-table td:first-child{position:sticky;left:0;z-index:3;min-width:145px;max-width:145px;background:#fff;border-right:1px solid var(--line);overflow-wrap:anywhere}
    .gradebook-table th:first-child{z-index:4;background:#f8faf9}
    .gradebook-table tbody tr:hover td:first-child{background:#f7fbf8}
    .gradebook-mobile-name{display:block}
}
@media(min-width:761px){.gradebook-mobile-name,.mobile-student-status,.mobile-class-section,.mobile-class-period{display:none}}
.grade-mobile-label{display:none}.student-grades-table{min-width:760px}
@media(max-width:760px){
    .student-app .student-subjects-table,.student-app .student-grades-table{min-width:0;table-layout:fixed}
    .student-app .student-subjects-table th:nth-child(3),.student-app .student-subjects-table td:nth-child(3),.student-app .student-subjects-table th:nth-child(4),.student-app .student-subjects-table td:nth-child(4){display:none}
    .student-app .student-subjects-table th:first-child{width:25%}.student-app .student-subjects-table th:last-child{width:48px}
    .student-app .student-grades-table colgroup,.student-app .student-grades-table .grade-component,.student-app .grade-desktop-label{display:none}
    .student-app .grade-mobile-label{display:inline}.student-app .student-grades-table th:first-child{width:40%}.student-app .student-grades-table th:nth-child(5){width:16%}.student-app .student-grades-table th:nth-child(6){width:29%}.student-app .student-grades-table th:last-child{width:15%}
    .student-app .student-grades-table .sheet-result{white-space:normal}.student-app .student-grades-table .pill{white-space:normal;justify-content:center;padding:4px 6px;line-height:1.2}
    .student-app .student-subjects-table td:nth-child(2),.student-app .student-grades-table td:first-child{overflow-wrap:anywhere}
    .student-app .top>div,.student-app .student-hub-head>div,.student-app .grade-record{min-width:0;overflow-wrap:anywhere}
    .student-app .grade-record h1{font-size:20px}.student-app .grade-record .table-wrap{max-width:100%}
    .student-app .class-status{gap:10px}.student-app .class-status .pill{text-align:center}
}
</style></head><body @class(['student-app'=>auth()->check()&&auth()->user()->isStudent()])>
@auth
<div class="shell"><aside class="side" id="primary-navigation">
<a class="brand" href="{{ route('dashboard') }}"><img class="brand-logo" src="{{ asset('images/bcc-seal.png') }}" alt="BCC seal"><span class="brand-copy"><span class="brand-title">GradeFlow</span><span class="brand-subtitle">Binalatongan Community College</span></span></a>
<nav class="nav"><span class="nav-label">Main menu</span><a @class(['active'=>request()->routeIs('dashboard')]) href="{{ route('dashboard') }}"><i data-lucide="layout-dashboard"></i><span>Dashboard</span></a>
@if(auth()->user()->isAdmin())
<span class="nav-label nav-group">Academic records</span><a @class(['active'=>request()->routeIs('students.*')]) href="{{ route('students.index') }}"><i data-lucide="users"></i><span>Students</span></a><a @class(['active'=>request()->routeIs('instructors.*')]) href="{{ route('instructors.index') }}"><i data-lucide="presentation"></i><span>Instructors</span></a><a @class(['active'=>request()->routeIs('subjects.*')]) href="{{ route('subjects.index') }}"><i data-lucide="book-open"></i><span>Subjects</span></a><a @class(['active'=>request()->routeIs('sections.*')]) href="{{ route('sections.index') }}"><i data-lucide="panels-top-left"></i><span>Sections</span></a><a @class(['active'=>request()->routeIs('classes.*')]) href="{{ route('classes.index') }}"><i data-lucide="school"></i><span>Classes</span></a><a @class(['active'=>request()->routeIs('school-years.*')]) href="{{ route('school-years.index') }}"><i data-lucide="calendar-range"></i><span>School Years</span></a><span class="nav-label nav-group">Review & access</span><a @class(['active'=>request()->routeIs('grades.*')]) href="{{ route('grades.index') }}"><i data-lucide="notebook-tabs"></i><span>Grade Management</span></a><a @class(['active'=>request()->routeIs('reports.*')]) href="{{ route('reports.index') }}"><i data-lucide="file-chart-column"></i><span>Reports</span></a><a @class(['active'=>request()->routeIs('audit.*')]) href="{{ route('audit.index') }}"><i data-lucide="history"></i><span>Activity Logs</span></a><a @class(['active'=>request()->routeIs('users.*')]) href="{{ route('users.index') }}"><i data-lucide="user-cog"></i><span>User Accounts</span></a>
@elseif(auth()->user()->isInstructor())
<span class="nav-label nav-group">Teaching</span><a @class(['active'=>request()->routeIs('classes.*')]) href="{{ route('classes.index') }}"><i data-lucide="school"></i><span>My Classes</span></a><a @class(['active'=>request()->routeIs('students.*')]) href="{{ route('students.index') }}"><i data-lucide="users"></i><span>Students</span></a><a @class(['active'=>request()->routeIs('faculty.grade-entry')]) href="{{ route('faculty.grade-entry') }}"><i data-lucide="square-pen"></i><span>Grade Entry</span></a><a @class(['active'=>request()->routeIs('grades.index')&&request('status')==='submitted']) href="{{ route('grades.index',['status'=>'submitted']) }}"><i data-lucide="send"></i><span>Submitted Grades</span></a>
@else
<a href="{{ route('subjects.index') }}"><i data-lucide="book-open"></i><span>My Subjects</span></a><a href="{{ route('grades.index') }}"><i data-lucide="notebook-tabs"></i><span>My Grades</span></a><a href="{{ route('grades.index') }}"><i data-lucide="history"></i><span>Grade History</span></a>
@endif</nav>
</aside><button class="mobile-nav-backdrop" type="button" data-close-mobile-nav aria-label="Close menu"></button><main class="main"><header class="appbar"><div class="mobile-appbar-start"><button class="tool-button mobile-menu-toggle" type="button" data-mobile-nav-toggle aria-controls="primary-navigation" aria-expanded="false" aria-label="Open menu" title="Menu"><i data-lucide="menu"></i></button><strong>GradeFlow</strong></div><div class="appbar-context"><i data-lucide="calendar-days"></i><span>{{ now()->format('l, F j, Y') }}</span></div><div class="appbar-tools"><div class="menu-wrap"><button class="tool-button" type="button" data-menu-toggle="notifications" title="Notifications"><i data-lucide="bell"></i>@if($showNotificationDot)<span class="notification-dot"></span>@endif</button><div class="popup-menu" data-menu="notifications"><div class="popup-head"><strong>Notifications</strong><span>Items needing attention</span></div>@forelse($notifications as $notification)<a href="{{ $notification['url'] }}"><i data-lucide="circle-alert"></i>{{ $notification['text'] }}</a>@empty<div class="popup-empty">You're all caught up.</div>@endforelse</div></div><div class="menu-wrap"><button class="profile-button" type="button" data-menu-toggle="profile"><div class="user-meta"><strong>{{ auth()->user()->name }}</strong><span>{{ auth()->user()->role }}</span></div><div class="user-avatar">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div><i data-lucide="chevron-down" style="width:15px"></i></button><div class="popup-menu" data-menu="profile"><div class="popup-head"><strong>{{ auth()->user()->name }}</strong><span>{{ auth()->user()->email }}</span></div><a href="{{ route('profile.show') }}"><i data-lucide="user-round"></i>View Profile</a><form method="post" action="{{ route('logout') }}">@csrf<button class="menu-action" type="submit"><i data-lucide="log-out"></i>Log out</button></form></div></div></div></header><div class="page-content">@if(session('status'))<div class="notice" data-toast-message>{{ session('status') }}</div>@endif @if($errors->any())<div class="notice" role="alert" style="color:#b42318;background:#fff4f2;border-color:#f7c6bd">Please check the highlighted fields and try again.</div>@endif @yield('content')</div></main></div>
@else @yield('content') @endauth
<div class="ajax-progress" id="ajax-progress"></div><div class="toast-stack" id="toast-stack"></div>
<dialog id="confirm-dialog"><div class="modal-body"><span class="icon-box icon-gold modal-icon"><i data-lucide="circle-alert"></i></span><h2 id="confirm-title">Confirm action</h2><p id="confirm-message">Are you sure you want to continue?</p></div><div class="modal-actions"><button class="btn" type="button" data-confirm-cancel>Cancel</button><button class="btn primary" type="button" data-confirm-accept>Confirm</button></div></dialog>
<script src="https://unpkg.com/lucide@0.468.0/dist/umd/lucide.min.js"></script>
<script>
(() => {
    const progress = document.getElementById('ajax-progress');
    const content = () => document.querySelector('.page-content');
    const icons = () => window.lucide && lucide.createIcons();
    const closeMobileNav = () => {
        document.body.classList.remove('mobile-nav-open');
        const toggle = document.querySelector('[data-mobile-nav-toggle]');
        toggle?.setAttribute('aria-expanded', 'false');
        toggle?.setAttribute('aria-label', 'Open menu');
    };
    const setupStudentBack = () => {
        @if(auth()->check() && auth()->user()->isStudent())
        if (location.pathname === '/dashboard' || document.querySelector('[data-student-back]')) return;
        const page = content();
        const top = page?.querySelector('.top');
        if (!top || !page) return;
        top.querySelector(':scope > a.btn')?.remove();
        const back = document.createElement('a');
        back.className = 'student-back'; back.href = '{{ route('dashboard') }}'; back.dataset.studentBack = '1';
        back.title = 'Back to dashboard'; back.setAttribute('aria-label', 'Back to dashboard');
        back.innerHTML = '<i data-lucide="arrow-left"></i>';
        page.insertBefore(back, top);
        @endif
    };
    const toast = message => {
        if (!message) return;
        const item = document.createElement('div'); item.className = 'toast';
        item.innerHTML = '<i data-lucide="circle-check"></i><span></span>';
        item.querySelector('span').textContent = message;
        document.getElementById('toast-stack').appendChild(item); icons();
        setTimeout(() => item.remove(), 3800);
    };
    const loading = state => {
        progress.className = 'ajax-progress ' + (state ? 'loading' : 'done');
        content()?.classList.toggle('is-updating', state);
        if (!state) setTimeout(() => progress.className = 'ajax-progress', 350);
    };
    const render = (html, url, push = true) => {
        closeMobileNav();
        const doc = new DOMParser().parseFromString(html, 'text/html');
        const next = doc.querySelector('.page-content');
        if (!next) { location.href = url; return; }
        content().innerHTML = next.innerHTML;
        const notificationButton = document.querySelector('[data-menu-toggle="notifications"]');
        const nextNotificationButton = doc.querySelector('[data-menu-toggle="notifications"]');
        const notificationMenu = document.querySelector('[data-menu="notifications"]');
        const nextNotificationMenu = doc.querySelector('[data-menu="notifications"]');
        if (notificationButton && nextNotificationButton) notificationButton.innerHTML = nextNotificationButton.innerHTML;
        if (notificationMenu && nextNotificationMenu) {
            notificationMenu.innerHTML = nextNotificationMenu.innerHTML;
            notificationMenu.classList.remove('open');
        }
        document.title = doc.title;
        document.body.className = doc.body.className;
        if (push) history.pushState({}, '', url);
        document.querySelectorAll('.nav a').forEach(link => link.classList.toggle('active', link.href === url || (url.startsWith(link.href + '/') && link.pathname !== '/')));
        setupStudentBack(); icons(); window.scrollTo({top:0,behavior:'smooth'});
        const notice = content().querySelector('[data-toast-message]');
        if (notice) { toast(notice.textContent.trim()); notice.remove(); }
    };
    const request = async (url, options = {}, push = true) => {
        loading(true);
        try {
            const response = await fetch(url, {...options, headers:{'X-Requested-With':'XMLHttpRequest','Accept':'text/html',...(options.headers||{})}});
            if (!response.ok) { toast(`Request failed (${response.status}). Please try again.`); return; }
            render(await response.text(), response.url, push);
        } catch (error) {
            toast('Unable to complete the request. Please try again.');
        } finally { loading(false); }
    };
    document.addEventListener('click', event => {
        const mobileToggle = event.target.closest('[data-mobile-nav-toggle]');
        if (mobileToggle) {
            const open = document.body.classList.toggle('mobile-nav-open');
            mobileToggle.setAttribute('aria-expanded', String(open));
            mobileToggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
            return;
        }
        if (event.target.closest('[data-close-mobile-nav]')) { closeMobileNav(); return; }
        if (event.target.closest('.side a')) closeMobileNav();
        const toggle = event.target.closest('[data-menu-toggle]');
        if (toggle) {
            event.stopPropagation();
            const menu = document.querySelector(`[data-menu="${toggle.dataset.menuToggle}"]`);
            document.querySelectorAll('.popup-menu.open').forEach(item => item !== menu && item.classList.remove('open'));
            menu?.classList.toggle('open');
            if (toggle.dataset.menuToggle === 'notifications' && menu?.classList.contains('open') && toggle.querySelector('.notification-dot')) {
                fetch('{{ route('notifications.seen') }}', {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json'},
                }).then(response => { if (response.ok) toggle.querySelector('.notification-dot')?.remove(); }).catch(() => {});
            }
            return;
        }
        if (!event.target.closest('.popup-menu')) document.querySelectorAll('.popup-menu.open').forEach(item => item.classList.remove('open'));
        const link = event.target.closest('a');
        if (link) document.querySelectorAll('.popup-menu.open').forEach(item => item.classList.remove('open'));
        if (!link || !document.querySelector('.shell') || link.origin !== location.origin || link.target || link.hasAttribute('download') || event.ctrlKey || event.metaKey || event.shiftKey) return;
        event.preventDefault(); request(link.href);
    });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && document.body.classList.contains('mobile-nav-open')) closeMobileNav();
    });
    document.addEventListener('submit', event => {
        const form = event.target;
        if (!form.closest('.main') || form.classList.contains('logout-form')) return;
        if (form.dataset.confirm && !form.dataset.confirmed) {
            event.preventDefault();
            const dialog = document.getElementById('confirm-dialog');
            document.getElementById('confirm-title').textContent = form.dataset.confirmTitle || 'Confirm action';
            document.getElementById('confirm-message').textContent = form.dataset.confirm;
            dialog._pendingForm = form;
            dialog.showModal();
            return;
        }
        event.preventDefault();
        const method = (form.method || 'get').toLowerCase();
        if (method === 'get') {
            const url = new URL(form.action || location.href); new FormData(form).forEach((value,key) => url.searchParams.set(key,value)); request(url.href); return;
        }
        const submitter = event.submitter; if (submitter) submitter.disabled = true;
        request(form.action, {method:'POST',body:new FormData(form)}).finally?.(() => { if (submitter) submitter.disabled = false; });
    });
    window.addEventListener('popstate', () => request(location.href, {}, false));
    document.addEventListener('change', event => {
        if (event.target.matches('[data-gradebook-input]')) {
            const submit = document.querySelector('[data-submit-class-grades]');
            if (submit) { submit.disabled = true; submit.title = 'Save drafts before submitting'; }
            const row = event.target.closest('tr');
            const inputs = [...row.querySelectorAll('[data-gradebook-input]')];
            const complete = inputs.every(input => input.value !== '' && Number(input.value) >= 1 && Number(input.value) <= 5);
            const average = complete ? inputs.reduce((sum, input) => sum + Number(input.value), 0) / inputs.length : null;
            row.querySelector('[data-row-average]').textContent = average === null ? '-' : average.toFixed(2);
            return;
        }
        if (!event.target.matches('[data-grade-input]')) return;
        const form = event.target.closest('form');
        const inputs = [...form.querySelectorAll('[data-grade-input]')];
        const values = inputs.map(input => Number(input.value));
        const complete = inputs.every(input => input.value !== '') && values.every(value => Number.isFinite(value) && value >= 1 && value <= 5);
        const average = complete ? values.reduce((sum, value) => sum + value, 0) / values.length : null;
        const score = form.querySelector('[data-grade-average]');
        const remark = form.querySelector('[data-grade-remark]');
        if (score) score.textContent = average === null ? '--' : average.toFixed(2);
        if (remark) remark.textContent = average === null ? 'Complete all grade fields' : (average <= 5 ? (average < 3.25 ? 'Passed' : 'Failed') : (average >= 75 ? 'Passed' : 'Failed'));
    });
    document.querySelector('[data-confirm-cancel]').addEventListener('click', () => document.getElementById('confirm-dialog').close());
    document.querySelector('[data-confirm-accept]').addEventListener('click', () => {
        const dialog = document.getElementById('confirm-dialog');
        const form = dialog._pendingForm;
        dialog.close();
        if (form) { form.dataset.confirmed = '1'; form.requestSubmit(); delete form.dataset.confirmed; }
    });
    setupStudentBack(); icons();
    const notice = document.querySelector('[data-toast-message]'); if (notice) { toast(notice.textContent.trim()); notice.remove(); }
})();
</script>
</body></html>
