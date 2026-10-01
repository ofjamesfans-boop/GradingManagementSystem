@extends('layouts.app')
@section('content')
<div class="top">
    <div><h1>Reports</h1><span class="muted">Released academic records</span></div>
    <div class="actions">
        <button class="btn" type="button" onclick="window.print()"><i data-lucide="printer"></i>Print page</button>
        <a class="btn primary" href="{{ route('reports.pdf') }}" download><i data-lucide="file-down"></i>Download PDF</a>
    </div>
</div>
<div class="card" style="padding:0;overflow:hidden">
    <div class="table-wrap"><table class="sheet-table" style="min-width:680px">
        <thead><tr><th>Student</th><th>Subject</th><th>Section</th><th>Final Grade</th><th>Remarks</th></tr></thead>
        <tbody>@forelse($records as $grade)<tr>
            <td>{{ $grade->enrollment->student->student_number }} - {{ $grade->enrollment->student->full_name }}</td>
            <td>{{ $grade->enrollment->classAssignment->subject->subject_code }}</td>
            <td>{{ $grade->enrollment->classAssignment->section->section_name }}</td>
            <td>{{ number_format((float) $grade->final_grade, 2) }}</td>
            <td>{{ $grade->remarks }}</td>
        </tr>@empty<tr><td colspan="5" class="empty-state">No released grades found.</td></tr>@endforelse</tbody>
    </table></div>
</div>
<div class="pagination">{{ $records->links() }}</div>
@endsection
