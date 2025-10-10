<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;

class FollowController extends Controller
{
    public function store(User $user): RedirectResponse
    {
        $me = auth()->user();

        if ($me->id === $user->id) {
            return back()->with('error', 'You cannot follow yourself.');
        }

        // no-dup attach
        $me->following()->syncWithoutDetaching([$user->id]);

        return back()->with('success', 'You are now following '.($user->display_name ?? $user->name).'.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $me = auth()->user();
        $me->following()->detach($user->id);

        return back()->with('success', 'Unfollowed '.($user->display_name ?? $user->name).'.');
    }
}
