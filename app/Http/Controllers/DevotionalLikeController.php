<?php

namespace App\Http\Controllers;

use App\Models\Devotional;
use Illuminate\Http\Request;

class DevotionalLikeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function toggle(Devotional $devotional)
    {
        $user = auth()->user();

        if ($devotional->likes()->where('user_id', $user->id)->exists()) {
            // If already liked, remove like
            $devotional->likes()->detach($user->id);
        } else {
            // If not liked yet, add like
            $devotional->likes()->attach($user->id);
        }

        return back();
    }
}
