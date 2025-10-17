<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPlanEntry extends Model
{
    protected $fillable = ['user_plan_id','plan_entry_id','completed_at'];
    protected $casts = ['completed_at' => 'datetime'];

    public function userPlan() { return $this->belongsTo(UserPlan::class); }
    public function planEntry() { return $this->belongsTo(PlanEntry::class); }
}
