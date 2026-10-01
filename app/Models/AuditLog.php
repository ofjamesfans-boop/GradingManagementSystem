<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = ['user_id', 'user_name', 'user_role', 'action', 'student_id', 'student_no', 'description'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function student(): BelongsTo { return $this->belongsTo(Student::class); }

    public static function record(string $action, ?Student $student, string $description): self
    {
        $user = auth()->user();

        return self::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'System',
            'user_role' => $user?->role ?? 'system',
            'action' => $action,
            'student_id' => $student?->id,
            'student_no' => $student?->student_no,
            'description' => $description,
        ]);
    }
}
