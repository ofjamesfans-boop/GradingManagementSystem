<?php
namespace App\Http\Controllers;
use App\Models\SchoolYear;
use Illuminate\Http\{Request,RedirectResponse};
use Illuminate\View\View;

class SchoolYearController {
 public function index():View {
  $currentPeriod=SchoolYear::activateCurrent(now());
  $years=SchoolYear::latest()->paginate(12);
  return view('school-years.index',compact('years','currentPeriod'));
 }
 public function store(Request $r):RedirectResponse {$d=$r->validate(['school_year'=>'required','semester'=>'required|in:1st Semester,2nd Semester,Summer']);SchoolYear::firstOrCreate($d,$d+['status'=>'inactive']);return back()->with('status','School year added.');}
 public function activate(SchoolYear $schoolYear):RedirectResponse {SchoolYear::query()->update(['status'=>'inactive']);$schoolYear->update(['status'=>'active']);return back()->with('status','Active school year updated.');}
}
