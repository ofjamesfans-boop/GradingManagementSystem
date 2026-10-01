@extends('layouts.app')
@section('content')
@if(auth()->user()->isStudent())
<section class="student-hub">
    <div class="student-hub-head">
        <div><span class="muted">Student workspace</span><h1>Hello, {{ auth()->user()->student?->first_name ?? auth()->user()->name }}</h1></div>
        <div class="student-identity"><div><strong>{{ auth()->user()->student?->student_number }}</strong><span class="row-subtitle">{{ auth()->user()->student?->section?->section_name ?? 'No section assigned' }}</span></div><div class="user-avatar">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div></div>
    </div>

    <div class="hub-actions">
        <a class="hub-action" href="{{ route('subjects.index') }}"><span class="icon-box icon-green"><i data-lucide="book-open"></i></span><div><strong>My Subjects</strong><span>{{ $subjectCount }} enrolled subject{{ $subjectCount===1?'':'s' }}</span></div></a>
        <a class="hub-action" href="{{ route('grades.index') }}"><span class="icon-box icon-blue"><i data-lucide="notebook-tabs"></i></span><div><strong>My Grades</strong><span>{{ $recordCount }} released record{{ $recordCount===1?'':'s' }}</span></div></a>
        <a class="hub-action" href="{{ route('profile.show') }}"><span class="icon-box icon-gold"><i data-lucide="user-round"></i></span><div><strong>My Profile</strong><span>Account and password</span></div></a>
    </div>

    <div class="class-heading"><div><h2>My Classes</h2><span class="muted" style="font-size:12px">Open a class to view its details</span></div></div>
    <div class="class-grid">
        @forelse($studentClasses as $enrollment)
        @php
            $class = $enrollment->classAssignment;
        @endphp
        <a class="class-tile" href="{{ route('subjects.show',$class->subject) }}">
            <div class="class-cover"><strong>{{ $class->subject->subject_code }}</strong><span>{{ $class->section->section_name }}</span></div>
            <div class="class-body">
                <h3>{{ $class->subject->subject_name }}</h3>
                <div class="class-meta"><i data-lucide="presentation"></i><span>{{ $class->instructor->full_name }}</span></div>
                <div class="class-meta"><i data-lucide="calendar-range"></i><span>{{ $class->schoolYear->school_year }} / {{ $class->schoolYear->semester }}</span></div>
                <div class="class-status"><span>{{ $class->subject->units }} units</span><span @class(['pill','neutral'=>!$enrollment->grade||$enrollment->grade->status!=='released'])>@if($enrollment->grade?->status==='released')Grade {{ number_format((float)$enrollment->grade->final_grade,2) }}@else Awaiting release @endif</span></div>
            </div>
        </a>
        @empty
        <div class="hub-empty"><i data-lucide="book-dashed"></i><strong style="display:block;color:var(--ink)">No classes yet</strong><span>Your enrolled classes will appear here.</span></div>
        @endforelse
    </div>
</section>
@elseif(auth()->user()->isInstructor())
<section class="faculty-workspace">
    <div class="faculty-head"><div><span class="muted">Faculty workspace</span><h1>Good day, {{ auth()->user()->instructor?->first_name ?? auth()->user()->name }}</h1></div><div class="actions"><a class="btn" href="{{ route('grades.index',['status'=>'submitted']) }}"><i data-lucide="send"></i>Submitted</a><a class="btn primary" href="{{ route('faculty.grade-entry') }}"><i data-lucide="square-pen"></i>Grade Entry</a></div></div>

    @php
        $draftCount = $statusCounts->get('draft',0);
        $submittedCount = $statusCounts->get('submitted',0);
    @endphp
    <div class="faculty-summary">
        <a href="{{ route('classes.index') }}"><span class="icon-box icon-green"><i data-lucide="school"></i></span><div><strong>{{ $instructorClasses->count() }}</strong><span>Assigned classes</span></div></a>
        <a href="{{ route('students.index') }}"><span class="icon-box icon-blue"><i data-lucide="users"></i></span><div><strong>{{ $studentCount }}</strong><span>Students across classes</span></div></a>
        <a href="{{ route('grades.index',['status'=>'draft']) }}"><span class="icon-box icon-gold"><i data-lucide="file-pen-line"></i></span><div><strong>{{ $draftCount }}</strong><span>Draft grade records</span></div></a>
    </div>

    <div class="class-heading"><div><h2>My Teaching Classes</h2><span class="muted" style="font-size:12px">Open a class roster or continue grade entry</span></div></div>
    <div class="faculty-class-grid">
        @forelse($instructorClasses as $class)
        @php
            $classGrades = $class->enrollments->pluck('grade')->filter();
            $classDrafts = $classGrades->where('status','draft')->count();
            $classSubmitted = $classGrades->where('status','submitted')->count();
            $classReleased = $classGrades->where('status','released')->count();
        @endphp
        <article class="card faculty-class">
            <div class="faculty-class-top"><div class="faculty-class-code"><span class="icon-box icon-green"><i data-lucide="book-open"></i></span><div><h3>{{ $class->subject->subject_code }}</h3><span>{{ $class->subject->subject_name }}</span></div></div><span class="pill">{{ $class->section->section_name }}</span></div>
            <div class="faculty-class-body">
                <div class="class-meta"><i data-lucide="calendar-range"></i><span>{{ $class->schoolYear->school_year }} / {{ $class->schoolYear->semester }}</span></div>
                <div class="faculty-progress"><div><strong>{{ $class->enrollments_count }}</strong><span>Students</span></div><div><strong>{{ $classDrafts }}</strong><span>Draft</span></div><div><strong>{{ $classSubmitted+$classReleased }}</strong><span>Completed</span></div></div>
                <div class="faculty-class-actions"><a class="btn primary" href="{{ route('classes.show',$class) }}"><i data-lucide="square-pen"></i>Open Gradebook</a></div>
            </div>
        </article>
        @empty
        <div class="hub-empty"><i data-lucide="school"></i><strong style="display:block;color:var(--ink)">No assigned classes</strong><span>Your assigned subjects and sections will appear here.</span></div>
        @endforelse
    </div>
</section>
@else
<div class="top"><div class="page-heading"><span class="heading-mark"></span><div><h1>Admin Dashboard</h1><span class="muted">Welcome back, {{ auth()->user()->name }}</span></div></div></div>
<div class="grid stats admin-stats"><div class="card stat"><div class="stat-head"><span class="stat-label">Active Students</span><span class="icon-box icon-green"><i data-lucide="users"></i></span></div><b>{{ $studentCount }}</b></div><div class="card stat"><div class="stat-head"><span class="stat-label">Subjects</span><span class="icon-box icon-gold"><i data-lucide="book-open"></i></span></div><b>{{ $subjectCount }}</b></div><div class="card stat"><div class="stat-head"><span class="stat-label">Grade Records</span><span class="icon-box icon-blue"><i data-lucide="notebook-tabs"></i></span></div><b>{{ $recordCount }}</b></div></div>
@if($statusCounts->get('submitted', 0) > 0)
<a class="dashboard-review-link" href="{{ route('grades.index', ['status' => 'submitted']) }}">
    <span><i data-lucide="clipboard-check"></i><strong>{{ $statusCounts->get('submitted') }} submitted grade{{ $statusCounts->get('submitted') === 1 ? '' : 's' }} awaiting release</strong></span>
    <span>Review grades <i data-lucide="arrow-right"></i></span>
</a>
@endif
<div class="grid analytics"><section class="card"><div class="section-head"><div><h2>Grade Distribution</h2><span class="muted" style="font-size:12px">Current academic records</span></div><i data-lucide="chart-bar-big" class="muted"></i></div>@foreach($gradeBands as $label=>$count)<div class="chart-row"><span>{{ $label }}</span><div class="chart-track"><div @class(['chart-fill','gold'=>$loop->index===1,'blue'=>$loop->index===2,'orange'=>$loop->index===3]) style="width:{{ ($count/$chartMaximum)*100 }}%"></div></div><strong>{{ $count }}</strong></div>@endforeach</section><section class="card"><div class="section-head"><div><h2>Workflow Status</h2><span class="muted" style="font-size:12px">Draft to release progress</span></div><i data-lucide="workflow" class="muted"></i></div>@foreach($statusCounts as $status=>$count)<div class="chart-row"><span>{{ ucfirst($status) }}</span><div class="chart-track"><div @class(['chart-fill','gold'=>$status==='submitted','blue'=>$status==='released']) style="width:{{ $recordCount ? ($count/$recordCount)*100 : 0 }}%"></div></div><strong>{{ $count }}</strong></div>@endforeach</section></div>
<div class="card" style="margin-top:18px;padding:0;overflow:hidden"><div class="section-head" style="padding:20px"><div><h2>Recent Grades</h2><span class="muted" style="font-size:12px">Latest grading activity</span></div><a class="btn" href="{{ route('grades.index') }}">View All <i data-lucide="arrow-right"></i></a></div><div style="overflow-x:auto"><table><thead><tr><th>Student</th><th>Subject</th><th>Grade</th><th>Status</th></tr></thead><tbody>@forelse($records as $record)<tr><td>{{ $record->enrollment->student->full_name }}</td><td>{{ $record->enrollment->classAssignment->subject->subject_code }}</td><td><strong>{{ number_format($record->final_grade,2) }}</strong></td><td><span class="pill">{{ ucfirst($record->status) }}</span></td></tr>@empty<tr><td colspan="4" class="empty-state"><i data-lucide="inbox"></i><strong>No grade records found.</strong></td></tr>@endforelse</tbody></table></div></div>
@endif
@endsection
