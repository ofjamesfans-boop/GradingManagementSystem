<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Section extends Model { protected $fillable=['section_name','year_level','program','status']; public function students(){return $this->hasMany(Student::class);} public function classAssignments(){return $this->hasMany(ClassAssignment::class);} public function scopeActive($q){return $q->where('status','active');} }
