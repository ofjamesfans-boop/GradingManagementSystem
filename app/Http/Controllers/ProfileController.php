<?php
namespace App\Http\Controllers;
use App\Models\ActivityLog;use Illuminate\Http\{Request,RedirectResponse};use Illuminate\Support\Facades\Hash;use Illuminate\View\View;
class ProfileController {
 public function __invoke(Request $r):View{$user=$r->user()->load('student.section','instructor');return view('profile.show',compact('user'));}
 public function updatePassword(Request $r):RedirectResponse{abort_unless($r->user()->isInstructor()||$r->user()->isStudent(),403);$data=$r->validate(['current_password'=>['required','current_password'],'password'=>['required','string','min:8','different:current_password','confirmed']]);$r->user()->update(['password'=>Hash::make($data['password'])]);ActivityLog::record('account.password_changed',"{$r->user()->name} changed their account password.");return back()->with('status','Password changed successfully.');}
}
