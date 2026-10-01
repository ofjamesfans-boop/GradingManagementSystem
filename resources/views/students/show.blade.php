@extends('layouts.app')
@section('content')
<div class="top"><div><h1>{{ $student->full_name }}</h1><span class="muted">{{ $student->student_number }} | {{ $student->section?->section_name }}</span></div><a class="btn" href="{{ route('students.index') }}"><i data-lucide="arrow-left"></i>Back</a></div>
<div class="card data-card"><div class="table-wrap"><table><thead><tr><th>Subject</th><th>School Year</th><th>Prelim</th><th>Midterm</th><th>Final</th><th>Final Grade</th><th>Status</th></tr></thead><tbody>
@forelse($enrollments as $enrollment)
<tr><td><strong>{{ $enrollment->classAssignment->subject->subject_code }}</strong></td><td>{{ $enrollment->classAssignment->schoolYear->school_year }} / {{ $enrollment->classAssignment->schoolYear->semester }}</td><td>{{ $enrollment->grade?->prelim ?? '-' }}</td><td>{{ $enrollment->grade?->midterm ?? '-' }}</td><td>{{ $enrollment->grade?->final ?? '-' }}</td><td>{{ $enrollment->grade?->final_grade ?? '-' }}</td><td><span @class(['pill','neutral'=>!$enrollment->grade,'warn'=>$enrollment->grade?->status==='draft'])>{{ ucfirst($enrollment->grade?->status ?? 'Not encoded') }}</span></td></tr>
@empty<tr><td colspan="7" class="empty-state"><i data-lucide="book-open"></i><strong>No enrollments found.</strong></td></tr>@endforelse
</tbody></table></div></div>
@endsection
