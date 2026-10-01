<?php
namespace App\Http\Controllers;
use App\Models\{ActivityLog,User};use Illuminate\Http\{Request,RedirectResponse};use Illuminate\Support\Facades\Hash;use Illuminate\View\View;
class UserAccountController extends Controller {
 public function index():View{$users=User::with('student')->latest()->paginate(15);return view('users.index',compact('users'));}
 public function update(Request $r,User $user):RedirectResponse{
  $data=$r->validate(['status'=>'required|in:active,inactive']);
  if($user->is($r->user())&&$data['status']==='inactive'){
   throw \Illuminate\Validation\ValidationException::withMessages(['status'=>'You cannot deactivate your own account.']);
  }
  if($data['status']==='active'&&(($user->student&&$user->student->status!=='active')||($user->instructor&&$user->instructor->status!=='active'))){
   throw \Illuminate\Validation\ValidationException::withMessages(['status'=>'Restore the student or instructor record before activating this account.']);
  }
  $user->update($data);
  ActivityLog::record('account.status_updated',"{$r->user()->name} changed the account status of {$user->email} to {$data['status']}.");
  return back()->with('status','Account status updated.');
 }
 public function resetStudentPassword(Request $r,User $user):RedirectResponse{abort_unless($user->role==='student'&&$user->student,422);$user->update(['password'=>Hash::make(\App\Models\Student::defaultPasswordFor($user->student->student_number))]);ActivityLog::record('student.password_reset',"{$r->user()->name} reset the password of {$user->student->student_number} to the BCC default.");return back()->with('status','Student password reset to BCC plus the last four digits of the student number.');}
}
