<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeScore extends Model
{
    protected $fillable = ['grade_record_id', 'grading_component_id', 'score'];

    protected $casts = [
        'score' => 'decimal:2',
    ];

    public function record(): BelongsTo
    {
        return $this->belongsTo(GradeRecord::class, 'grade_record_id');
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(GradingComponent::class, 'grading_component_id');
    }
}
