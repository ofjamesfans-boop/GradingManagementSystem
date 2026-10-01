<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Student extends Model {
 protected $fillable=['user_id','student_number','first_name','middle_name','last_name','email','section_id','status'];
 public function user(){return $this->belongsTo(User::class);} public function section(){return $this->belongsTo(Section::class);} public function enrollments(){return $this->hasMany(Enrollment::class);}
 public function getFullNameAttribute(){return trim("{$this->last_name}, {$this->first_name} {$this->middle_name}");} public function scopeActive($q){return $q->where('status','active');}
 public static function defaultPasswordFor(string $studentNumber):string{$digits=preg_replace('/\D/','',$studentNumber);return 'BCC'.substr($digits,-4);}
}
