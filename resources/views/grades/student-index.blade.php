@extends('layouts.app')
@section('content')
<div class="top"><div class="page-heading"><span class="heading-mark"></span><div><h1>My Grades</h1><span class="muted">Subjects and released grades</span></div></div></div>
@php
    $terms = $enrollments->groupBy(fn ($enrollment) => $enrollment->classAssignment->schoolYear->school_year.' / '.$enrollment->classAssignment->schoolYear->semester);
@endphp
@forelse($terms as $period => $subjects)
<section style="margin-bottom:24px">
    <div class="section-head"><h2>{{ $period }}</h2><span class="muted" style="font-size:12px">{{ $subjects->count() }} subject{{ $subjects->count() === 1 ? '' : 's' }}</span></div>
    <div class="card data-card"><div class="table-wrap"><table class="sheet-table student-grades-table"><colgroup><col style="width:27%"><col style="width:12%"><col style="width:12%"><col style="width:12%"><col style="width:15%"><col style="width:15%"><col style="width:7%"></colgroup><thead><tr><th>Subject</th><th class="sheet-number grade-component">Prelim</th><th class="sheet-number grade-component">Midterm</th><th class="sheet-number grade-component">Final</th><th class="sheet-number"><span class="grade-desktop-label">Overall Grade</span><span class="grade-mobile-label">Grade</span></th><th class="sheet-result">Result</th><th class="sheet-action">View</th></tr></thead><tbody>
    @foreach($subjects as $enrollment)
        @php
            $subject = $enrollment->classAssignment->subject;
            $releasedGrade = $enrollment->grade?->status === 'released' ? $enrollment->grade : null;
        @endphp
        <tr>
            <td><span class="row-title">{{ $subject->subject_name }}</span><span class="row-subtitle">{{ $subject->subject_code }}</span></td>
            <td class="sheet-number grade-component">{{ $releasedGrade?->prelim ?? '-' }}</td>
            <td class="sheet-number grade-component">{{ $releasedGrade?->midterm ?? '-' }}</td>
            <td class="sheet-number grade-component">{{ $releasedGrade?->final ?? '-' }}</td>
            <td class="sheet-number"><strong>{{ $releasedGrade?->final_grade ?? '-' }}</strong></td>
            <td class="sheet-result"><span @class(['pill','neutral'=>!$releasedGrade,'warn'=>$releasedGrade?->remarks === 'Failed'])>{{ $releasedGrade?->remarks ?? 'Awaiting release' }}</span></td>
            <td class="sheet-action">@if($releasedGrade)<a class="btn icon-only" title="View grade details" aria-label="View grade details for {{ $subject->subject_name }}" href="{{ route('grades.show',$releasedGrade) }}"><i data-lucide="eye"></i></a>@endif</td>
        </tr>
    @endforeach
    </tbody></table></div></div>
</section>
@empty
<div class="card empty-state"><i data-lucide="book-open"></i><strong>No enrolled subjects yet.</strong><span>Your subjects and released grades will appear here.</span></div>
@endforelse
@endsection
