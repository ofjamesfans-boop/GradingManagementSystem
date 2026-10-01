<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('grades')->whereNotNull('final_grade')->where('final_grade', '<', 3.25)
            ->where('final_grade', '<=', 5)->update(['remarks' => 'Passed']);
        DB::table('grades')->whereNotNull('final_grade')->whereBetween('final_grade', [3.25, 5])
            ->update(['remarks' => 'Failed']);
    }

    public function down(): void
    {
        // Historical remarks cannot be reconstructed reliably.
    }
};
