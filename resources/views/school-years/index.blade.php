@extends('layouts.app')
@section('content')
<div class="top"><div class="page-heading"><span class="heading-mark"></span><div><h1>School Years</h1><span class="muted">Academic period follows the calendar automatically</span></div></div></div>

<section class="card" style="margin-bottom:18px">
    <div class="form-section-head"><span class="icon-box icon-green"><i data-lucide="calendar-check"></i></span><div><h2>Current Academic Period</h2><span>Automatically detected from today's date</span></div></div>
    <div style="display:flex;align-items:center;justify-content:space-between;gap:18px;flex-wrap:wrap">
        <div><strong style="display:block;font-size:24px">{{ $currentPeriod->school_year }}</strong><span class="muted">{{ $currentPeriod->semester }}</span></div>
        <span class="pill">Active</span>
    </div>
    <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin-top:20px">
        <div style="padding:12px;border-radius:7px;background:var(--green-soft)"><strong style="font-size:12px">1st Semester</strong><span class="row-subtitle">August - December</span></div>
        <div style="padding:12px;border-radius:7px;background:var(--blue-soft)"><strong style="font-size:12px">2nd Semester</strong><span class="row-subtitle">January - May</span></div>
        <div style="padding:12px;border-radius:7px;background:var(--gold-soft)"><strong style="font-size:12px">Summer</strong><span class="row-subtitle">June - July</span></div>
    </div>
</section>

<div class="card data-card"><div class="table-wrap"><table><thead><tr><th>School Year</th><th>Semester</th><th>Status</th></tr></thead><tbody>
@forelse($years as $year)
<tr><td><span class="row-title">{{ $year->school_year }}</span></td><td>{{ $year->semester }}</td><td><span @class(['pill','neutral'=>$year->status!=='active'])>{{ ucfirst($year->status) }}</span></td></tr>
@empty
<tr><td colspan="3" class="empty-state"><i data-lucide="calendar-range"></i><strong>No academic periods found.</strong></td></tr>
@endforelse
</tbody></table></div></div>
<div class="pagination">{{ $years->links() }}</div>
@endsection
