<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Subject extends Model { protected $fillable=['subject_code','subject_name','units','status']; public function classAssignments(){return $this->hasMany(ClassAssignment::class);} }
