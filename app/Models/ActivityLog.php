<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ActivityLog extends Model {
    public $timestamps = false;
    protected $fillable = ['user_id','action','description','created_at'];
    protected $casts = ['created_at'=>'datetime'];
    public function user(){ return $this->belongsTo(User::class); }
    public static function record(string $action,string $description): self { return self::create(['user_id'=>auth()->id(),'action'=>$action,'description'=>$description,'created_at'=>now()]); }
}
