<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Grade extends Model
{
    protected $fillable = ['enrollment_id', 'prelim', 'midterm', 'final', 'final_grade', 'remarks', 'status', 'encoded_by'];

    protected $casts = ['prelim' => 'decimal:2', 'midterm' => 'decimal:2', 'final' => 'decimal:2', 'final_grade' => 'decimal:2'];

    public function enrollment() { return $this->belongsTo(Enrollment::class); }
    public function encoder() { return $this->belongsTo(User::class, 'encoded_by'); }

    public static function scoreOptions(): array
    {
        return array_map(fn ($quarter) => number_format($quarter / 4, 2, '.', ''), range(4, 20));
    }

    public static function compute($p, $m, $f) { return round(((float) $p + (float) $m + (float) $f) / 3, 2); }
    public static function remark($grade) { return (float) $grade <= 5 ? ((float) $grade < 3.25 ? 'Passed' : 'Failed') : ((float) $grade >= 75 ? 'Passed' : 'Failed'); }
}
