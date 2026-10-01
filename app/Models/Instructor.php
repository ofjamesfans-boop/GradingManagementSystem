<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Instructor extends Model {
 protected $fillable=['user_id','employee_number','first_name','middle_name','last_name','email','status'];
 public function user(){return $this->belongsTo(User::class);} public function classAssignments(){return $this->hasMany(ClassAssignment::class);} public function getFullNameAttribute(){return trim("{$this->last_name}, {$this->first_name} {$this->middle_name}");}
}
