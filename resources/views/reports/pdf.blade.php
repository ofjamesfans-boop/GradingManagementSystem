<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>GradeFlow Released Grades</title>
    <style>
        @page { margin: 24px 28px 32px; }
        body { font-family: DejaVu Sans, sans-serif; color: #123c2c; font-size: 9px; }
        .brand { border-bottom: 2px solid #087443; padding-bottom: 12px; margin-bottom: 15px; }
        .brand h1 { font-size: 18px; margin: 0 0 3px; }
        .brand p { margin: 0; color: #587064; }
        .meta { margin-bottom: 12px; color: #587064; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th { background: #eaf3ed; color: #185d3b; text-align: left; text-transform: uppercase; font-size: 8px; }
        th, td { padding: 7px 8px; border: 1px solid #d8e4dc; vertical-align: top; overflow-wrap: break-word; }
        tr { page-break-inside: avoid; }
        th:nth-child(1) { width: 37%; }
        th:nth-child(2) { width: 19%; }
        th:nth-child(3) { width: 16%; }
        th:nth-child(4) { width: 14%; }
        th:nth-child(5) { width: 14%; }
        .footer { position: fixed; bottom: -22px; right: 0; color: #587064; font-size: 8px; }
    </style>
</head>
<body>
    <div class="brand"><h1>GradeFlow</h1><p>Binalatongan Community College</p></div>
    <div class="meta"><strong>Released Grade Report</strong> &nbsp; | &nbsp; Generated {{ now()->format('F j, Y g:i A') }} &nbsp; | &nbsp; {{ $records->count() }} record{{ $records->count() === 1 ? '' : 's' }}</div>
    <table>
        <thead><tr><th>Student</th><th>Subject</th><th>Section</th><th>Final Grade</th><th>Remarks</th></tr></thead>
        <tbody>
            @forelse($records as $grade)
                <tr>
                    <td>{{ $grade->enrollment->student->student_number }} - {{ $grade->enrollment->student->full_name }}</td>
                    <td>{{ $grade->enrollment->classAssignment->subject->subject_code }} - {{ $grade->enrollment->classAssignment->subject->subject_name }}</td>
                    <td>{{ $grade->enrollment->classAssignment->section->section_name }}</td>
                    <td>{{ number_format((float) $grade->final_grade, 2) }}</td>
                    <td>{{ $grade->remarks }}</td>
                </tr>
            @empty
                <tr><td colspan="5">No released grades found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="footer">GradeFlow | Released academic records</div>
</body>
</html>
