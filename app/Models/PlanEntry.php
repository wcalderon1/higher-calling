<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanEntry extends Model
{
    protected $fillable = ['plan_id','day_number','devotional_id','title','scripture_ref'];

    public function plan() { return $this->belongsTo(Plan::class); }
    public function devotional() { return $this->belongsTo(Devotional::class); }
}
