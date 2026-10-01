<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradeRecord extends Model
{
    protected $fillable = ['student_id', 'subject_id', 'final_grade', 'remarks', 'recorded_at', 'status', 'finalized_at', 'finalized_by'];

    protected $casts = [
        'recorded_at' => 'date',
        'final_grade' => 'decimal:2',
        'finalized_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(GradeScore::class);
    }

    public function finalizer(): BelongsTo { return $this->belongsTo(User::class, 'finalized_by'); }

    public static function remarkFor(float $grade): string
    {
        return $grade >= 75 ? 'Passed' : 'Needs Review';
    }
}
