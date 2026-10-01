@extends('layouts.app')
@section('content')
<style>
.admin-grade-filters{grid-template-columns:minmax(170px,2fr) repeat(4,minmax(120px,1fr)) auto}
.grade-instructor{margin-bottom:22px;border:1px solid var(--line);border-radius:8px;background:#fff;box-shadow:var(--shadow);overflow:hidden}
.grade-instructor-head{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:18px 20px;background:#f8faf9}
.grade-instructor-name{display:flex;align-items:center;gap:12px;min-width:0}
.grade-instructor-name h2{margin:0;font-size:16px}
.grade-instructor-name span{display:block;margin-top:3px;color:var(--muted);font-size:11px}
.grade-instructor-head>.muted{font-size:12px;white-space:nowrap}
.grade-class{border-top:1px solid var(--line)}
.grade-class summary{padding:16px 20px;display:grid;grid-template-columns:minmax(0,1.5fr) minmax(90px,.6fr) minmax(180px,1fr) auto 18px;align-items:center;gap:15px;cursor:pointer;list-style:none}
.grade-class summary::-webkit-details-marker{display:none}
.grade-class summary:hover{background:#f8fbf9}
.grade-class summary strong{display:block;font-size:14px}
.grade-class summary small{display:block;margin-top:2px;color:var(--muted);font-size:11px}
.grade-class summary>span{color:var(--muted);font-size:12px}
.grade-class summary .pill{color:var(--green)}
.grade-class summary svg{width:18px;height:18px;color:var(--muted);transition:transform .15s}
.grade-class[open] summary svg{transform:rotate(180deg)}
.grade-class .table-wrap{border-top:1px solid var(--line)}
.grade-class table{min-width:820px}
.grade-class th{background:#fbfcfb}
.grade-class td{height:54px}
@media(max-width:1150px){.admin-grade-filters{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:760px){.admin-grade-filters{grid-template-columns:1fr}.grade-instructor-head{align-items:flex-start;flex-direction:column}.grade-class summary{grid-template-columns:minmax(0,1fr) 18px}.grade-class summary>span{display:none}.grade-class-mobile-section{display:inline}.grade-class summary svg{grid-column:2;grid-row:1}}
@media(min-width:761px){.grade-class-mobile-section{display:none}}
</style>
<div class="top"><div class="page-heading"><span class="heading-mark"></span><div><h1>Grade Management</h1><span class="muted">Grades by instructor and class</span></div></div><div class="actions"><a class="btn" download href="{{ route('exports.grades',request()->query()) }}"><i data-lucide="download"></i>Export CSV</a><a class="btn primary" href="{{ route('grades.create') }}"><i data-lucide="square-pen"></i>Grade Entry</a></div></div>

<form class="card filter-bar admin-grade-filters" method="get">
<label>Search<input name="search" value="{{ request('search') }}" placeholder="Student name or number"></label>
<label>Status<select name="status"><option value="">All statuses</option>@foreach(['draft','submitted','released'] as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ ucfirst($status) }}</option>@endforeach</select></label>
<label>Subject<select name="subject_id"><option value="">All subjects</option>@foreach($subjects as $subject)<option value="{{ $subject->id }}" @selected((int)request('subject_id')===$subject->id)>{{ $subject->subject_code }}</option>@endforeach</select></label>
<label>Section<select name="section_id"><option value="">All sections</option>@foreach($sections as $section)<option value="{{ $section->id }}" @selected((int)request('section_id')===$section->id)>{{ $section->section_name }}</option>@endforeach</select></label>
<label>Academic Period<select name="school_year_id"><option value="">All periods</option>@foreach($schoolYears as $year)<option value="{{ $year->id }}" @selected((int)request('school_year_id')===$year->id)>{{ $year->school_year }} / {{ $year->semester }}</option>@endforeach</select></label>
<button class="btn primary"><i data-lucide="filter"></i>Apply</button>
</form>

@forelse($instructors as $instructor)
@php
    $classes = $gradeGroups->get($instructor->id, collect());
    $gradeCount = $classes->sum(fn ($grades) => $grades->count());
@endphp
<section class="grade-instructor">
    <div class="grade-instructor-head">
        <div class="grade-instructor-name"><span class="icon-box icon-green"><i data-lucide="presentation"></i></span><div><h2>{{ $instructor->full_name }}</h2><span>{{ $instructor->employee_number }}</span></div></div>
        <span class="muted">{{ $classes->count() }} class{{ $classes->count()===1?'':'es' }} / {{ $gradeCount }} grade{{ $gradeCount===1?'':'s' }}</span>
    </div>
    @foreach($classes as $classGrades)
    @php
        $assignment = $classGrades->first()->enrollment->classAssignment;
        $pendingCount = $classGrades->where('status','submitted')->count();
    @endphp
    <details class="grade-class" open>
        <summary>
            <div><strong>{{ $assignment->subject->subject_code }} / {{ $assignment->subject->subject_name }}</strong><small>{{ $classGrades->count() }} grade record{{ $classGrades->count()===1?'':'s' }}<span class="grade-class-mobile-section"> / Section {{ $assignment->section->section_name }}</span></small></div>
            <span>{{ $assignment->section->section_name }}</span>
            <span>{{ $assignment->schoolYear->school_year }} / {{ $assignment->schoolYear->semester }}</span>
            @if($pendingCount)<span class="pill info">{{ $pendingCount }} to release</span>@else<span class="muted">Up to date</span>@endif
            <i data-lucide="chevron-down"></i>
        </summary>
        <div class="table-wrap"><table class="sheet-table"><thead><tr><th>Student</th><th class="sheet-number">Prelim</th><th class="sheet-number">Midterm</th><th class="sheet-number">Final</th><th class="sheet-number">Computed</th><th>Status</th><th>Actions</th></tr></thead><tbody>
        @foreach($classGrades->sortBy(fn ($grade) => $grade->enrollment->student->last_name) as $grade)
        <tr>
            <td><span class="row-title">{{ $grade->enrollment->student->full_name }}</span><span class="row-subtitle">{{ $grade->enrollment->student->student_number }}</span></td>
            <td class="sheet-number">{{ $grade->prelim ?? '-' }}</td><td class="sheet-number">{{ $grade->midterm ?? '-' }}</td><td class="sheet-number">{{ $grade->final ?? '-' }}</td>
            <td class="sheet-number"><strong>{{ $grade->final_grade ?? '-' }}</strong></td>
            <td><span @class(['pill','warn'=>$grade->status==='draft','info'=>$grade->status==='submitted'])>{{ ucfirst($grade->status) }}</span></td>
            <td><div class="actions"><a class="btn icon-only" title="View grade" href="{{ route('grades.show',$grade) }}"><i data-lucide="eye"></i></a>
                @if($grade->status==='submitted')
                <form method="post" action="{{ route('grades.release',$grade) }}" data-confirm-title="Release grade" data-confirm="Release {{ $grade->enrollment->student->student_number }}'s grade in {{ $assignment->subject->subject_code }}?">@csrf @method('patch')<button class="btn primary" title="Release grade"><i data-lucide="send"></i>Release</button></form>
                @endif
                @if($grade->status!=='draft')
                <form method="post" action="{{ route('grades.reopen',$grade) }}" data-confirm-title="Reopen grade" data-confirm="Reopen {{ $grade->enrollment->student->student_number }}'s grade in {{ $assignment->subject->subject_code }} for correction?">@csrf @method('patch')<button class="btn" title="Reopen grade"><i data-lucide="rotate-ccw"></i>Reopen</button></form>
                @endif
            </div></td>
        </tr>
        @endforeach
        </tbody></table></div>
    </details>
    @endforeach
</section>
@empty
<div class="card empty-state"><i data-lucide="file-search"></i><strong>No grade records found.</strong><span>Try another filter or wait for faculty grade submissions.</span></div>
@endforelse
<div class="pagination">{{ $instructors->links() }}</div>
@endsection
