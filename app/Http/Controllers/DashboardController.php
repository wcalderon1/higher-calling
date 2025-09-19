<?php

namespace App\Http\Controllers;

use App\Models\Devotional;
use App\Models\Comment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        // Quick stats for your posts
        $stats = [
            'total'     => Devotional::where('user_id', $user->id)->count(),
            'published' => Devotional::where('user_id', $user->id)->where('status', 'published')->count(),
            'draft'     => Devotional::where('user_id', $user->id)->where('status', 'draft')->count(),
            'scheduled' => Devotional::where('user_id', $user->id)->where('status', 'scheduled')->count(),
        ];

        // Recently edited/created by you
        $recentMine = Devotional::where('user_id', $user->id)
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get(['id','slug','title','status','published_at','updated_at']);

        // Drafts or scheduled that are due
        $needsAttention = Devotional::where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('status', 'draft')
                  ->orWhere(function ($q) {
                      $q->where('status', 'scheduled')->where('published_at', '<=', now());
                  });
            })
            ->orderBy('published_at')
            ->limit(5)
            ->get(['id','slug','title','status','published_at']);

        // Latest comments left on YOUR posts
        $recentComments = Comment::with(['author','devotional:id,slug,title,user_id'])
            ->whereHas('devotional', fn($q) => $q->where('user_id', $user->id))
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact('stats','recentMine','needsAttention','recentComments'));
    }
}
