<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class UserPlan extends Model
{
    protected $fillable = ['user_id','plan_id','start_date','status'];
    protected $casts = ['start_date' => 'date'];

    public function user() { return $this->belongsTo(User::class); }
    public function plan() { return $this->belongsTo(Plan::class); }
    public function entries() { return $this->hasMany(UserPlanEntry::class); }

    public function progressPercent(): int {
        $total = $this->plan->length_days ?? 0;
        $done  = $this->entries()->whereNotNull('completed_at')->count();
        return $total ? (int) floor(($done / $total) * 100) : 0;
    }

    public function currentDayNumber(): int {
        if (!$this->start_date) return 1;
        $diff = $this->start_date->diffInDays(Carbon::today()) + 1;
        return max(1, min($diff, $this->plan->length_days));
    }
}
