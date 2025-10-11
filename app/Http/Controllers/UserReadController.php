<?php

namespace App\Http\Controllers;

use App\Models\Devotional;
use App\Models\UserRead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class UserReadController extends Controller
{
    /** Store a “read today” record for the current user. */
    public function store(Devotional $devotional): RedirectResponse
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // one record per user per calendar day
        UserRead::updateOrCreate(
            ['user_id' => Auth::id(), 'read_on' => today()],
            ['devotional_id' => $devotional->id]
        );

        return back()->with('status', 'Marked as read for today.');
    }
}
