<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ClassAssignment extends Model { protected $fillable=['instructor_id','subject_id','section_id','school_year_id','status']; public function instructor(){return $this->belongsTo(Instructor::class);} public function subject(){return $this->belongsTo(Subject::class);} public function section(){return $this->belongsTo(Section::class);} public function schoolYear(){return $this->belongsTo(SchoolYear::class);} public function enrollments(){return $this->hasMany(Enrollment::class);} }
