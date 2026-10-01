@extends('layouts.app')
@section('content')
<div class="top"><div class="page-heading"><span class="heading-mark"></span><div><h1>Profile</h1><span class="muted">Account information and security</span></div></div></div>

<div class="grid profile-grid">
    <section class="card">
        <div class="icon-box icon-green" style="margin-bottom:18px"><i data-lucide="user-round"></i></div>
        <div class="grid" style="gap:16px">
            <div><span class="muted">Name</span><h2 style="margin:4px 0 0">{{ $user->name }}</h2></div>
            <div><span class="muted">Email</span><br><strong>{{ $user->email }}</strong></div>
            <div><span class="muted">Role</span><br><span class="pill">{{ ucfirst($user->role) }}</span></div>
            @if($user->student)
                <div><span class="muted">Student Number</span><br><strong>{{ $user->student->student_number }}</strong></div>
                <div><span class="muted">Section</span><br><strong>{{ $user->student->section?->section_name ?? 'Unassigned' }}</strong></div>
            @elseif($user->instructor)
                <div><span class="muted">Employee Number</span><br><strong>{{ $user->instructor->employee_number }}</strong></div>
            @endif
        </div>
    </section>

    @if($user->isInstructor() || $user->isStudent())
        <details class="card password-panel" @if($errors->hasAny(['current_password','password'])) open @endif>
            <summary>
                <span class="icon-box icon-gold"><i data-lucide="key-round"></i></span>
                <span><strong>Change Password</strong><small>Update your account password</small></span>
                <i class="panel-chevron" data-lucide="chevron-down"></i>
            </summary>
            <form method="post" action="{{ route('profile.password.update') }}">
                @csrf @method('put')
                <div class="grid" style="gap:15px">
                    <label>Current Password
                        <input type="password" name="current_password" autocomplete="current-password" required>
                        @error('current_password')<span class="error">{{ $message }}</span>@enderror
                    </label>
                    <label>New Password
                        <input type="password" name="password" autocomplete="new-password" minlength="8" required>
                        @error('password')<span class="error">{{ $message }}</span>@enderror
                    </label>
                    <label>Confirm New Password
                        <input type="password" name="password_confirmation" autocomplete="new-password" minlength="8" required>
                    </label>
                    <div class="actions"><button class="btn primary"><i data-lucide="shield-check"></i>Save New Password</button></div>
                </div>
            </form>
        </details>
    @endif
</div>

<style>
.profile-grid{grid-template-columns:minmax(0,.8fr) minmax(360px,1.2fr);align-items:start}.password-panel{padding:0;overflow:hidden}.password-panel summary{min-height:76px;padding:16px 20px;display:flex;align-items:center;gap:13px;cursor:pointer;list-style:none}.password-panel summary::-webkit-details-marker{display:none}.password-panel summary:hover{background:#f8fbf9}.password-panel summary span:nth-child(2){display:grid;gap:3px;flex:1}.password-panel summary small{color:var(--muted);font-size:11px;font-weight:600}.panel-chevron{width:18px;color:var(--muted);transition:transform .2s}.password-panel[open] .panel-chevron{transform:rotate(180deg)}.password-panel form{padding:20px;border-top:1px solid var(--line)}@media(max-width:760px){.profile-grid{grid-template-columns:1fr}.profile-grid>.card{min-width:0}.profile-grid h2,.profile-grid strong{overflow-wrap:anywhere}}
</style>
@endsection
