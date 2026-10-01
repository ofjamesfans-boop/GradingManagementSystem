@extends('layouts.app')
@section('content')
<div class="top"><div class="page-heading"><span class="heading-mark"></span><div><h1>Grade Entry</h1><span class="muted">{{ $currentPeriod->school_year }} / {{ $currentPeriod->semester }}</span></div></div></div>
<div class="class-heading"><h2>Assigned Classes</h2></div>
<div class="faculty-class-grid">
@forelse($classes as $class)
<article class="card faculty-class">
    <div class="faculty-class-top"><div class="faculty-class-code"><span class="icon-box icon-green"><i data-lucide="book-open"></i></span><div><h3>{{ $class->subject->subject_code }}</h3><span>{{ $class->subject->subject_name }}</span></div></div><span class="pill">{{ $class->section->section_name }}</span></div>
    <div class="faculty-class-body">
        <div class="class-meta"><i data-lucide="calendar-range"></i><span>{{ $class->schoolYear->school_year }} / {{ $class->schoolYear->semester }}</span></div>
        <div class="faculty-progress">
            <div><strong>{{ $class->students_count }}</strong><span>Students</span></div>
            <div><strong>{{ $class->drafts_count }}</strong><span>Draft</span></div>
            <div><strong>{{ $class->submitted_count + $class->released_count }}</strong><span>Submitted / Released</span></div>
        </div>
        @if($class->students_count)
        <div class="faculty-class-actions"><a class="btn primary" href="{{ route('classes.show',$class) }}"><i data-lucide="square-pen"></i>Open Gradebook</a></div>
        @else
        <div class="faculty-class-actions"><span class="muted" style="font-size:12px">No students enrolled in this class yet. Ask the registrar to check the section assignment.</span></div>
        @endif
    </div>
</article>
@empty
<div class="hub-empty"><i data-lucide="school"></i><strong style="display:block;color:var(--ink)">No assigned classes</strong><span>Your current semester classes will appear here after the registrar assigns them.</span></div>
@endforelse
</div>
@endsection
