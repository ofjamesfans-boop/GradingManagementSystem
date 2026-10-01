<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ReportController
{
    public function index(): View
    {
        $records = Grade::with('enrollment.student', 'enrollment.classAssignment.subject', 'enrollment.classAssignment.section')
            ->where('status', 'released')
            ->latest()
            ->paginate(20);

        return view('reports.index', compact('records'));
    }

    public function pdf(): Response
    {
        $records = Grade::with('enrollment.student', 'enrollment.classAssignment.subject', 'enrollment.classAssignment.section')
            ->where('status', 'released')
            ->latest()
            ->get();

        return Pdf::loadView('reports.pdf', compact('records'))
            ->setPaper('a4', 'landscape')
            ->download('gradeflow-released-grades-'.now()->format('Y-m-d').'.pdf');
    }
}
