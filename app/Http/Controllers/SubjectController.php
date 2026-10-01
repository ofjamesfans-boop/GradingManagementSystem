<?php
namespace App\Http\Controllers;
use App\Models\{ActivityLog,Subject};use Illuminate\Http\{Request,RedirectResponse};use Illuminate\Validation\Rule;use Illuminate\View\View;
class SubjectController extends Controller {
 public function index(Request $r):View{$subjects=Subject::when($r->user()->isInstructor(),fn($q)=>$q->whereHas('classAssignments.instructor',fn($i)=>$i->where('user_id',$r->user()->id)))->when($r->user()->isStudent(),fn($q)=>$q->whereHas('classAssignments.enrollments.student',fn($s)=>$s->where('user_id',$r->user()->id)))->paginate(12);return view('subjects.index',compact('subjects'));}
 public function create():View{return view('subjects.form',['subject'=>new Subject]);}
 public function store(Request $r):RedirectResponse{$s=Subject::create($this->validated($r));ActivityLog::record('subject.created',"{$r->user()->name} created subject {$s->subject_code}.");return redirect()->route('subjects.index')->with('status','Subject created.');}
 public function show(Request $r,Subject $subject):View{$assignments=$subject->classAssignments()->with('instructor','section','schoolYear');if($r->user()->isInstructor())$assignments->whereHas('instructor',fn($q)=>$q->where('user_id',$r->user()->id));if($r->user()->isStudent())$assignments->whereHas('enrollments.student',fn($q)=>$q->where('user_id',$r->user()->id));abort_if(!$r->user()->isAdmin()&&!$assignments->exists(),403);$subject->setRelation('classAssignments',$assignments->get());return view('subjects.show',compact('subject'));}
 public function edit(Subject $subject):View{return view('subjects.form',compact('subject'));}
 public function update(Request $r,Subject $subject):RedirectResponse{$subject->update($this->validated($r,$subject));ActivityLog::record('subject.updated',"{$r->user()->name} updated subject {$subject->subject_code}.");return redirect()->route('subjects.index')->with('status','Subject updated.');}
 public function archive(Request $r,Subject $subject):RedirectResponse{$subject->update(['status'=>'archived']);ActivityLog::record('subject.archived',"{$r->user()->name} archived subject {$subject->subject_code}.");return back()->with('status','Subject archived.');}
 private function validated(Request $r,?Subject $s=null):array{return $r->validate(['subject_code'=>['required',Rule::unique('subjects')->ignore($s)],'subject_name'=>'required|max:150','units'=>'required|numeric|min:0|max:12']);}
}
