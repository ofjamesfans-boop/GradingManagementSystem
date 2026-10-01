@extends('layouts.app')
@section('content')
<div class="top">
    <div class="page-heading"><span class="heading-mark"></span><div><h1>{{ $grade->exists?'Edit Draft Grade':'Grade Entry' }}</h1><span class="muted">{{ $grade->exists?'Update grade components while this record is in draft.':'Select a class and encode the student grades.' }}</span></div></div>
    <a class="btn" href="{{ route('grades.index') }}"><i data-lucide="arrow-left"></i>Back</a>
</div>

@unless($grade->exists)
<form class="card select-card" method="get" action="{{ route('grades.create') }}">
    <div class="form-section-head"><span class="icon-box icon-green"><i data-lucide="school"></i></span><div><h2>Class Selection</h2><span>Choose an assigned class before entering grades.</span></div></div>
    <div class="fields"><label><span class="required">Assigned Class</span><select name="assignment_id" onchange="this.form.requestSubmit()" required><option value="">Select class</option>@foreach($assignments as $a)<option value="{{ $a->id }}" @selected($assignment?->id===$a->id)>{{ $a->subject->subject_code }} - {{ $a->subject->subject_name }} / {{ $a->section->section_name }}</option>@endforeach</select></label></div>
</form>
@endunless

@if($assignment)
@if($grade->exists && collect(['prelim','midterm','final'])->contains(fn ($component) => (float) $grade->$component > 5))
<div class="notice">This draft uses the old scale. Select new 1.00-5.00 values before saving.</div>
@endif
<form class="card form-shell" method="post" action="{{ $grade->exists?route('grades.update',$grade):route('grades.store') }}">
    @csrf @if($grade->exists)@method('put')@endif
    <section class="form-section">
        <div class="form-section-head"><span class="icon-box icon-blue"><i data-lucide="user-round"></i></span><div><h2>Student</h2><span>{{ $assignment->subject->subject_code }} / {{ $assignment->section->section_name }}</span></div></div>
        <label><span class="required">Student Enrollment</span><select name="enrollment_id" required @disabled($grade->exists)>@forelse($enrollments as $e)<option value="{{ $e->id }}" @selected(old('enrollment_id',$grade->enrollment_id)===$e->id)>{{ $e->student->student_number }} - {{ $e->student->full_name }}</option>@empty<option value="">No enrolled students</option>@endforelse</select>@if($grade->exists)<input type="hidden" name="enrollment_id" value="{{ $grade->enrollment_id }}">@endif @error('enrollment_id')<span class="error">{{ $message }}</span>@enderror</label>
    </section>

    <section class="form-section">
        <div class="form-section-head"><span class="icon-box icon-gold"><i data-lucide="notebook-pen"></i></span><div><h2>Grade Components</h2><span>Choose a grade from 1.00 to 5.00.</span></div></div>
        <div class="grade-grid">
            @foreach(['prelim'=>'Prelim','midterm'=>'Midterm','final'=>'Final'] as $name=>$label)
            <label><span class="required">{{ $label }}</span><select data-grade-input name="{{ $name }}" required><option value="">Select grade</option>@foreach(\App\Models\Grade::scoreOptions() as $score)<option value="{{ $score }}" @selected((string) old($name,$grade->$name)===$score)>{{ $score }}</option>@endforeach</select>@error($name)<span class="error">{{ $message }}</span>@enderror</label>
            @endforeach
        </div>
        @php
            $previewValues = [old('prelim',$grade->prelim), old('midterm',$grade->midterm), old('final',$grade->final)];
            $hasPreview = collect($previewValues)->every(fn($value) => is_numeric($value));
            $previewAverage = $hasPreview ? collect($previewValues)->average() : null;
        @endphp
        <div class="grade-preview"><div><small>Computed result</small><strong data-grade-remark>{{ $previewAverage === null ? 'Complete all grade fields' : \App\Models\Grade::remark($previewAverage) }}</strong></div><strong class="grade-score" data-grade-average>{{ $previewAverage === null ? '--' : number_format($previewAverage,2) }}</strong></div>
    </section>

    <div class="form-footer"><a class="btn" href="{{ route('grades.index') }}">Cancel</a><button class="btn primary" @disabled($enrollments->isEmpty())><i data-lucide="save"></i>Save Draft</button></div>
</form>
@endif
@endsection
