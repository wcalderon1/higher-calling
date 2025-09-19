<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['devotional_id','user_id','body'];

    public function devotional()
    {
        return $this->belongsTo(Devotional::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
