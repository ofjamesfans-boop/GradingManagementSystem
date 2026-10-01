@extends('layouts.app')
@section('content')
<div class="top"><div class="page-heading"><span class="heading-mark"></span><div><h1>Assign Class</h1><span class="muted">{{ $currentPeriod->school_year }} / {{ $currentPeriod->semester }}</span></div></div><a class="btn" href="{{ route('classes.index') }}"><i data-lucide="arrow-left"></i>Back</a></div>
<form class="card form-shell" method="post" action="{{ route('classes.store') }}">
@csrf
<div class="form-section-head"><span class="icon-box icon-green"><i data-lucide="school"></i></span><div><h2>Class Assignment</h2><span>Students in the selected section will be enrolled automatically.</span></div></div>
<div class="fields">
<label>Instructor<select name="instructor_id" required><option value="">Select instructor</option>@foreach($instructors as $instructor)<option value="{{ $instructor->id }}" @selected(old('instructor_id')==$instructor->id)>{{ $instructor->full_name }}</option>@endforeach</select>@error('instructor_id')<span class="error">{{ $message }}</span>@enderror</label>
<label>Subject<select name="subject_id" required><option value="">Select subject</option>@foreach($subjects as $subject)<option value="{{ $subject->id }}" @selected(old('subject_id')==$subject->id)>{{ $subject->subject_code }} - {{ $subject->subject_name }}</option>@endforeach</select>@error('subject_id')<span class="error">{{ $message }}</span>@enderror</label>
<label>Section<select name="section_id" required><option value="">Select section</option>@foreach($sections as $section)<option value="{{ $section->id }}" @selected(old('section_id')==$section->id)>{{ $section->program }} {{ $section->year_level }} - {{ $section->section_name }}</option>@endforeach</select>@error('section_id')<span class="error">{{ $message }}</span>@enderror</label>
<label>Academic Period<input value="{{ $currentPeriod->school_year }} / {{ $currentPeriod->semester }}" disabled><span class="field-hint">Set automatically from the current calendar.</span></label>
</div>
<div class="form-footer"><a class="btn" href="{{ route('classes.index') }}">Cancel</a><button class="btn primary"><i data-lucide="plus"></i>Create Assignment</button></div>
</form>
@endsection
