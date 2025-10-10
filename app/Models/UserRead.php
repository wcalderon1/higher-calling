<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRead extends Model
{
    protected $fillable = ['user_id', 'devotional_id', 'read_on'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function devotional(): BelongsTo
    {
        return $this->belongsTo(Devotional::class);
    }
}
