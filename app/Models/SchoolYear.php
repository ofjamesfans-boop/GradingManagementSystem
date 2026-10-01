<?php
namespace App\Models;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
class SchoolYear extends Model {
 protected $fillable=['school_year','semester','status'];
 public function classAssignments(){return $this->hasMany(ClassAssignment::class);}

 public static function periodFor(CarbonInterface $date):array {
  $month=$date->month;
  $startYear=$month>=8?$date->year:$date->year-1;
  $semester=$month>=8?'1st Semester':($month<=5?'2nd Semester':'Summer');
  return ['school_year'=>$startYear.'-'.($startYear+1),'semester'=>$semester];
 }

 public static function activateCurrent(CarbonInterface $date):self {
  $period=self::periodFor($date);
  $record=self::firstOrCreate($period,$period+['status'=>'inactive']);
  if($record->status!=='active'){
   self::whereKeyNot($record->id)->where('status','active')->update(['status'=>'inactive']);
   $record->update(['status'=>'active']);
  }
  return $record->refresh();
 }
}
