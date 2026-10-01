<?php
namespace App\Http\Controllers;use App\Models\ActivityLog;use Illuminate\View\View;class AuditLogController {public function __invoke():View{$logs=ActivityLog::with('user')->latest('created_at')->paginate(20);return view('audit.index',compact('logs'));}}
