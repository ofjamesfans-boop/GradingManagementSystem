@extends('layouts.app')
@section('content')
<div @class(['login-shell', 'student-login' => $portal === 'student', 'faculty-login' => $portal === 'faculty'])>
@if($portal !== 'student')
<header class="login-masthead"><img src="{{ asset('images/bcc-seal.png') }}" alt="BCC seal"><span><strong>Binalatongan Community College</strong><small>GradeFlow | Student Grading Management System</small></span></header>
@endif
<section class="login-brand"><img class="login-campus" src="{{ asset('images/bcc-campus.webp') }}" alt="Binalatongan Community College campus"><span class="login-shade"></span><span class="login-ring"></span><div class="brand"><img class="brand-logo login-logo" src="{{ asset('images/bcc-seal.png') }}" alt="BCC seal"><span class="brand-copy"><span class="brand-title">GradeFlow</span><span class="brand-subtitle">Binalatongan Community College</span></span></div><div class="login-copy">@if($portal !== 'student')<span class="login-place"><i data-lucide="map-pin"></i> BCC Campus</span>@else<span class="gold-line"></span>@endif<h1>Student Grading<br>Management</h1><p>Simple, organized, and accurate academic records.</p></div><small>BCC &copy; {{ date('Y') }}</small></section>
<section class="login-form-wrap"><form method="post" action="{{ route($route) }}" class="card login-card">@csrf
@if($portal !== 'student')
<div class="login-form-heading"><img src="{{ asset('images/bcc-seal.png') }}" alt=""><div><span>{{ $title }}</span><h1>Sign in to continue</h1></div></div>
@else
<h1>{{ $title }}</h1><p class="muted">Sign in to GradeFlow</p>
@endif
<div class="grid login-fields"><label>Email address<input name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>@error('email')<span class="error">{{ $message }}</span>@enderror</label><label>Password<span class="login-password-field"><input name="password" type="password" autocomplete="current-password" required><button type="button" class="login-password-toggle" aria-label="Show password" aria-pressed="false" title="Show password"><i data-lucide="eye"></i></button></span>@error('password')<span class="error">{{ $message }}</span>@enderror</label><button class="btn primary" type="submit">Sign in</button></div></form></section>
</div>
<style>.login-shell{min-height:100vh;display:grid;grid-template-columns:minmax(300px,.95fr) minmax(420px,1.05fr);background:#fff}.login-brand{position:relative;display:flex;flex-direction:column;justify-content:space-between;min-height:100vh;padding:42px;color:#fff;background:#055a35;overflow:hidden}.login-campus{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:center}.login-shade{position:absolute;inset:0;background:linear-gradient(180deg,rgba(3,54,31,.72),rgba(3,54,31,.9))}.login-ring{position:absolute;width:420px;height:420px;right:-190px;bottom:-180px;border:70px solid rgba(242,183,5,.13);border-radius:50%}.login-brand>.brand{z-index:1}.login-logo{width:72px;height:72px;flex-basis:72px}.login-copy{position:relative;z-index:1;max-width:430px}.gold-line{display:inline-block;width:42px;height:5px;border-radius:5px;background:#f2b705}.login-copy h1{margin-top:20px;font-size:clamp(34px,4vw,54px);line-height:1.08;color:#fff;text-shadow:0 2px 16px rgba(0,0,0,.2)}.login-copy p{max-width:360px;margin:16px 0 0;color:#e1eee7;line-height:1.7}.login-brand small{position:relative;z-index:1;color:#d1e5da}.login-form-wrap{display:grid;place-items:center;padding:32px;background:#f7faf8}.login-card{width:min(430px,100%);padding:34px;box-shadow:0 18px 50px rgba(17,59,40,.1)}@media(max-width:760px){.login-shell{grid-template-columns:1fr}.login-brand{display:none}.login-form-wrap{min-height:100vh;padding:20px}.login-card{padding:26px}}
.student-login{grid-template-columns:minmax(0,1fr);grid-template-rows:190px minmax(0,1fr)}.student-login .login-brand{display:flex;min-height:0;height:190px;padding:18px clamp(24px,5vw,72px)}.student-login .login-campus{object-position:center 42%}.student-login .login-shade{background:linear-gradient(90deg,rgba(3,54,31,.88),rgba(3,54,31,.38))}.student-login .login-ring,.student-login .login-brand small{display:none}.student-login .login-logo{width:46px;height:46px;flex-basis:46px}.student-login .login-copy{max-width:none}.student-login .login-copy h1{margin:7px 0 0;font-size:25px;line-height:1.1}.student-login .login-copy p{margin-top:4px;font-size:11px}.student-login .login-form-wrap{min-height:calc(100vh - 190px);place-items:start center;padding:clamp(34px,7vh,72px) 20px 28px}.student-login .login-card{max-width:430px}
@media(max-width:760px){.student-login{grid-template-rows:160px minmax(0,1fr)}.student-login .login-brand{height:160px;padding:13px 20px}.student-login .login-logo{width:38px;height:38px;flex-basis:38px}.student-login .brand-title{font-size:16px!important}.student-login .brand-subtitle{display:block;font-size:9px}.student-login .login-copy h1{font-size:20px}.student-login .login-copy p{display:none}.student-login .login-form-wrap{min-height:calc(100vh - 160px);padding:18px 20px 24px}}</style>
<style>
.login-shell:not(.student-login){grid-template-columns:minmax(360px,43%) minmax(0,1fr)}
.login-shell:not(.student-login) .login-brand{padding:36px clamp(30px,4vw,60px)}
.login-shell:not(.student-login) .login-campus{object-position:center 38%}
.login-shell:not(.student-login) .login-shade{background:linear-gradient(180deg,rgba(3,54,31,.55),rgba(3,54,31,.82))}
.login-shell:not(.student-login) .login-copy h1{font-size:40px;line-height:1.14}
.login-shell:not(.student-login) .login-copy p{font-size:14px}
.login-shell:not(.student-login) .login-card{border-top:3px solid var(--gold)}
.login-shell:not(.student-login) .login-card h1{font-size:23px;line-height:1.25}
.login-shell:not(.student-login) .login-card>p{margin:6px 0 24px;font-size:12px}
.student-login .login-card h1{font-size:26px}
.student-login .login-card>p{margin:7px 0 24px;font-size:13px}
.login-fields{gap:16px}
.login-fields input{min-height:46px}
.login-fields button[type=submit]{min-height:46px;margin-top:4px}
@media(max-width:760px){
  .login-shell:not(.student-login){grid-template-columns:1fr;grid-template-rows:140px minmax(0,1fr)}
  .login-shell:not(.student-login) .login-brand{display:flex;min-height:0;height:140px;padding:18px 20px}
  .login-shell:not(.student-login) .login-brand .brand{align-self:flex-start}
  .login-shell:not(.student-login) .login-logo{width:46px;height:46px;flex-basis:46px}
  .login-shell:not(.student-login) .brand-title{font-size:18px!important}
  .login-shell:not(.student-login) .brand-subtitle{display:block;font-size:9px}
  .login-shell:not(.student-login) .login-copy,.login-shell:not(.student-login) .login-brand small,.login-shell:not(.student-login) .login-ring{display:none}
  .login-shell:not(.student-login) .login-form-wrap{min-height:calc(100dvh - 140px);place-items:start center;padding:clamp(34px,7vh,70px) 20px 28px}
  .login-shell:not(.student-login) .login-card{width:min(430px,100%);padding:0;border:0;box-shadow:none;background:transparent}
  .login-shell:not(.student-login) .login-card h1{font-size:22px}
}
</style>
<style>
.login-password-field{position:relative;display:block}
.login-password-field input{width:100%;padding-right:48px}
.login-password-field input::-ms-reveal,.login-password-field input::-ms-clear{display:none}
.login-password-toggle{position:absolute;top:50%;right:6px;transform:translateY(-50%);width:36px;height:36px;min-height:0;margin:0;padding:0;display:grid;place-items:center;border:0;border-radius:6px;background:transparent;color:var(--muted);line-height:1;cursor:pointer}
.login-password-toggle:hover,.login-password-toggle:focus-visible{color:var(--green);background:var(--green-soft)}
.login-password-toggle svg{width:18px;height:18px}
.login-shell:not(.student-login){position:relative;isolation:isolate;min-height:100dvh;grid-template-columns:minmax(0,1.2fr) minmax(390px,.86fr);grid-template-rows:76px minmax(0,1fr);align-items:center;padding:0 clamp(24px,5vw,86px) clamp(28px,5vh,64px);gap:0;background:#153f30 url('{{ asset('images/bcc-campus.webp') }}') center/cover no-repeat}
.login-shell:not(.student-login):before{content:"";position:absolute;inset:0;z-index:-1;background:linear-gradient(110deg,rgba(6,37,28,.68),rgba(14,38,37,.74))}
.login-masthead{grid-column:1/-1;align-self:stretch;display:flex;align-items:center;gap:11px;margin:0 calc(-1 * clamp(24px,5vw,86px));padding:0 clamp(24px,5vw,86px);background:rgba(255,255,255,.13);border-bottom:1px solid rgba(255,255,255,.17);backdrop-filter:blur(9px);color:#fff}
.login-masthead img{width:43px;height:43px;object-fit:contain}
.login-masthead strong,.login-masthead small{display:block}
.login-masthead strong{font-size:16px;line-height:1.2}
.login-masthead small{margin-top:2px;font-size:11px;color:#e4eee7}
.login-shell:not(.student-login) .login-brand,.login-shell:not(.student-login) .login-form-wrap{height:min(690px,calc(100dvh - 155px));min-height:540px}
.login-shell:not(.student-login) .login-brand{padding:36px 38px;border-radius:10px 0 0 10px;background:#114632}
.login-shell:not(.student-login) .login-campus{object-position:center}
.login-shell:not(.student-login) .login-shade{background:linear-gradient(180deg,rgba(6,50,35,.56),rgba(5,43,34,.82))}
.login-shell:not(.student-login) .login-ring{display:none}
.login-shell:not(.student-login) .login-brand>.brand{display:none}
.login-shell:not(.student-login) .login-copy{max-width:550px;margin:auto 0}
.login-place{display:inline-flex;align-items:center;gap:7px;padding:9px 13px;border:1px solid rgba(255,255,255,.28);border-radius:100px;background:rgba(255,255,255,.13);font-size:12px;font-weight:700;backdrop-filter:blur(6px)}
.login-place svg{width:15px;height:15px;color:var(--gold)}
.login-shell:not(.student-login) .login-copy h1{margin:24px 0 0;font-size:clamp(30px,3.4vw,46px);line-height:1.13}
.login-shell:not(.student-login) .login-copy p{max-width:400px;font-size:14px}
.login-shell:not(.student-login) .login-form-wrap{display:grid;place-items:center;padding:40px clamp(25px,4vw,64px);border-radius:0 10px 10px 0;background:#fff}
.login-shell:not(.student-login) .login-card{width:min(440px,100%);padding:0;border:0;border-radius:0;background:transparent;box-shadow:none}
.login-form-heading{display:flex;align-items:center;gap:15px;margin-bottom:34px}
.login-form-heading img{width:57px;height:57px;object-fit:contain}
.login-form-heading span{display:block;color:var(--green);font-size:11px;font-weight:800;text-transform:uppercase}
.login-form-heading h1{margin:4px 0 0;color:var(--ink);font-size:26px;line-height:1.2}
.login-shell:not(.student-login) .login-fields{gap:18px}
.login-shell:not(.student-login) .login-fields label{gap:7px;font-size:12px;font-weight:700}
.login-shell:not(.student-login) .login-fields input{min-height:49px;background:#f8faf9}
.login-shell:not(.student-login) .login-fields input:focus{background:#fff}
.login-shell:not(.student-login) .login-fields button[type=submit]{min-height:49px;margin-top:5px}
@media(max-width:760px){
 .login-shell:not(.student-login){grid-template-columns:minmax(0,1fr);grid-template-rows:64px 170px auto;align-content:start;align-items:stretch;padding:0 16px 24px}
 .login-masthead{margin:0 -16px;padding:0 16px}
 .login-masthead img{width:36px;height:36px}
 .login-masthead strong{font-size:12px}
 .login-masthead small{font-size:9px}
 .login-shell:not(.student-login) .login-brand{display:flex;height:auto;min-height:0;padding:20px;border-radius:8px 8px 0 0}
 .login-shell:not(.student-login) .login-copy{display:block;margin:auto 0}
 .login-shell:not(.student-login) .login-copy h1{margin:12px 0 0;font-size:clamp(22px,5vw,30px)}
 .login-shell:not(.student-login) .login-copy p,.login-shell:not(.student-login) .login-brand small{display:none}
 .login-place{padding:6px 9px;font-size:10px}
 .login-shell:not(.student-login) .login-form-wrap{height:auto;min-height:0;padding:28px 23px 34px;border-radius:0 0 8px 8px}
 .login-form-heading{margin-bottom:25px}
 .login-form-heading img{width:44px;height:44px}
 .login-form-heading h1{font-size:21px}
}
@media(max-width:360px){.login-masthead small{font-size:8px}.login-shell:not(.student-login) .login-form-wrap{padding:24px 18px 28px}}
</style>
<script>
document.querySelectorAll('.login-password-toggle').forEach(button => button.addEventListener('click', () => {
    const input = button.previousElementSibling;
    const visible = input.type === 'password';
    input.type = visible ? 'text' : 'password';
    button.setAttribute('aria-label', visible ? 'Hide password' : 'Show password');
    button.setAttribute('title', visible ? 'Hide password' : 'Show password');
    button.setAttribute('aria-pressed', String(visible));
    button.querySelector('svg, i')?.remove();
    button.insertAdjacentHTML('afterbegin', `<i data-lucide="${visible ? 'eye-off' : 'eye'}"></i>`);
    window.lucide?.createIcons();
}));
</script>
@endsection
