@extends('layouts.app')
@section('content')
<div class="top"><div class="page-heading"><span class="heading-mark"></span><div><h1>{{ $assignment->subject->subject_code }} Gradebook</h1><span class="muted">{{ $assignment->subject->subject_name }} / {{ $assignment->section->section_name }} / {{ $assignment->schoolYear->school_year }} {{ $assignment->schoolYear->semester }}</span></div></div><a class="btn" href="{{ route('classes.index') }}"><i data-lucide="arrow-left"></i>Back</a></div>
@if($errors->any())<div class="notice" style="color:#b42318;background:#fff1ef;border-color:#f2c8c1">{{ $errors->first() }}</div>@endif
@php
    $roster = $assignment->enrollments->filter(fn ($enrollment) => $enrollment->status === 'active' && $enrollment->student?->status === 'active');
    $draftCount = $roster->filter(fn ($enrollment) => $enrollment->grade?->status === 'draft')->count();
    $editableCount = $roster->filter(fn ($enrollment) => ! $enrollment->grade || $enrollment->grade->status === 'draft')->count();
@endphp
@if($roster->contains(fn ($enrollment) => $enrollment->grade?->status === 'draft' && collect(['prelim','midterm','final'])->contains(fn ($component) => (float) $enrollment->grade->$component > 5)))
<div class="notice">Old-scale draft scores need new 1.00-5.00 values before saving.</div>
@endif
<div class="grid gradebook-stats" style="margin-bottom:18px">
<div class="card stat" style="min-height:100px"><div class="stat-head"><span class="stat-label">Enrolled Students</span><span class="icon-box icon-green"><i data-lucide="users"></i></span></div><b>{{ $roster->count() }}</b></div>
<div class="card stat" style="min-height:100px"><div class="stat-head"><span class="stat-label">Draft Grades</span><span class="icon-box icon-gold"><i data-lucide="file-pen-line"></i></span></div><b>{{ $draftCount }}</b></div>
<div class="card stat" style="min-height:100px"><div class="stat-head"><span class="stat-label">Submitted / Released</span><span class="icon-box icon-blue"><i data-lucide="send"></i></span></div><b>{{ $roster->count()-$editableCount }}</b></div>
</div>
@if($canEditGrades)
<form id="gradebook-form" method="post" action="{{ route('classes.gradebook.save',$assignment) }}">@csrf @method('put')</form>
@endif
<div class="card data-card"><div class="table-wrap"><table class="sheet-table gradebook-table" style="min-width:940px"><thead><tr><th>Student No.</th><th>Student</th><th class="sheet-number">Prelim</th><th class="sheet-number">Midterm</th><th class="sheet-number">Final</th><th class="sheet-number">Computed</th><th>Status</th></tr></thead><tbody>
@forelse($roster as $enrollment)
@php
    $grade = $enrollment->grade;
    $editable = $canEditGrades && (! $grade || $grade->status === 'draft');
@endphp
<tr>
<td><span class="row-title">{{ $enrollment->student->student_number }}</span><span class="row-subtitle gradebook-mobile-name">{{ $enrollment->student->full_name }}</span></td><td>{{ $enrollment->student->full_name }}</td>
@foreach(['prelim','midterm','final'] as $component)
<td class="sheet-number">@if($editable)<select form="gradebook-form" data-gradebook-input data-row="{{ $enrollment->id }}" name="grades[{{ $enrollment->id }}][{{ $component }}]" aria-label="{{ ucfirst($component) }} grade for {{ $enrollment->student->full_name }}" style="width:100px"><option value="">-</option>@foreach(\App\Models\Grade::scoreOptions() as $score)<option value="{{ $score }}" @selected((string) old("grades.{$enrollment->id}.{$component}",$grade?->$component)===$score)>{{ $score }}</option>@endforeach</select>@else{{ $grade?->$component ?? '-' }}@endif</td>
@endforeach
<td class="sheet-number"><strong data-row-average="{{ $enrollment->id }}">{{ $grade?->final_grade ?? '-' }}</strong></td>
<td><span @class(['pill','warn'=>$grade?->status==='draft','info'=>$grade?->status==='submitted','neutral'=>!$grade])>{{ ucfirst($grade?->status ?? 'Not encoded') }}</span></td>
</tr>
@empty<tr><td colspan="7" class="empty-state"><i data-lucide="users"></i><strong>No enrolled students found.</strong></td></tr>@endforelse
</tbody></table></div></div>
@if($canEditGrades && $roster->isNotEmpty())
<div class="actions" style="justify-content:flex-end;margin-top:16px">
<button class="btn primary" form="gradebook-form" @disabled($editableCount===0)><i data-lucide="save"></i>Save Drafts</button>
<form method="post" action="{{ route('classes.gradebook.submit',$assignment) }}" data-confirm-title="Submit class grades" data-confirm="Submit complete draft grades for {{ $assignment->subject->subject_code }} / {{ $assignment->section->section_name }}? Submitted grades can only be reopened by an administrator.">@csrf<button class="btn" data-submit-class-grades @disabled($draftCount===0)><i data-lucide="send"></i>Submit Class Grades</button></form>
</div>
@endif
@if(! $canEditGrades && auth()->user()->isInstructor())<div class="notice" style="margin-top:16px">This class is read-only because its academic period has ended.</div>@endif
@endsection
