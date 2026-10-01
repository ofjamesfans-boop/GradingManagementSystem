<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class MyProfileController extends Controller
{
    public function __invoke(Request $request): View
    {
        abort_unless($request->user()->isStudent() && $request->user()->student, 403);
        $student = $request->user()->student->load('gradeRecords.subject');
        return view('students.show', ['student' => $student, 'isOwnProfile' => true]);
    }
}
