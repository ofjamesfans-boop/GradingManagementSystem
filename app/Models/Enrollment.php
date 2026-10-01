<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Enrollment extends Model { protected $fillable=['student_id','class_assignment_id','status']; public function student(){return $this->belongsTo(Student::class);} public function classAssignment(){return $this->belongsTo(ClassAssignment::class);} public function grade(){return $this->hasOne(Grade::class);} }
