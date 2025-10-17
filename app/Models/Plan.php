<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['title','slug','description','length_days'];

    public function entries() { return $this->hasMany(PlanEntry::class)->orderBy('day_number'); }
    public function userPlans() { return $this->hasMany(UserPlan::class); }
}
