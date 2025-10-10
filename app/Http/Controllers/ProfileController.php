<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
    $user = $request->user();

    // fill standard Breeze fields
    $user->fill($request->only(['name','email']));

    if ($user->isDirty('email')) {
        $user->email_verified_at = null;
    }

    // fill NEW profile fields
    $user->display_name = $request->input('display_name');
    $user->bio          = strip_tags($request->input('bio') ?? '');

    // avatar upload (optional)
    if ($request->hasFile('avatar')) {
        // delete old avatar if it exists
        if ($user->avatar_path && \Storage::disk('public')->exists($user->avatar_path)) {
            \Storage::disk('public')->delete($user->avatar_path);
        }
        $path = $request->file('avatar')->store('avatars/'.$user->id, 'public'); // e.g. avatars/25/abc.webp
        $user->avatar_path = $path;
    }

    $user->save();

    return \Illuminate\Support\Facades\Redirect::route('profile.edit')
        ->with('status', 'profile-updated');
}


    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Show my own profile (redirects to the public profile page).
     */
    public function me(Request $request): RedirectResponse
    {
        return Redirect::route('profile.show', $request->user());
    }

    /**
     * Show a public profile by user id.
     */
    public function show(User $user): \Illuminate\View\View
    {
    $recentDevos = $user->devotionals()->latest()->take(3)->get();
    return view('profile.show', compact('user', 'recentDevos'));
    }

    public function followers(\App\Models\User $user): \Illuminate\View\View
{
    $followers = $user->followers()->with('followers', 'following')
        ->latest('follows.created_at')->paginate(12);
    return view('profile.followers', compact('user', 'followers'));
}

public function following(\App\Models\User $user): \Illuminate\View\View
{
    $following = $user->following()->with('followers', 'following')
        ->latest('follows.created_at')->paginate(12);
    return view('profile.following', compact('user', 'following'));
}


}
