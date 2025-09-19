<?php

namespace App\Http\Controllers;

use App\Models\Devotional;
use App\Models\Comment;
use App\Models\Tag;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        // Simple "Verse of the Day" pool (rotate by day-of-year)
        $verses = [
            ['ref' => 'Psalm 23:1',      'text' => 'The LORD is my shepherd; I shall not want.'],
            ['ref' => 'Philippians 4:6', 'text' => 'Do not be anxious about anything, but in everything by prayer and supplication with thanksgiving let your requests be made known to God.'],
            ['ref' => 'Proverbs 3:5',    'text' => 'Trust in the LORD with all your heart, and do not lean on your own understanding.'],
            ['ref' => 'Isaiah 41:10',    'text' => 'Fear not, for I am with you; be not dismayed, for I am your God.'],
            ['ref' => 'John 14:27',      'text' => 'Peace I leave with you; my peace I give to you.'],
        ];
        $votd = $verses[ now()->dayOfYear % count($verses) ];

        $featured = Devotional::with(['author','tags'])
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->first();

        $latest = Devotional::with(['author','tags'])
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        $myDrafts = $user
            ? Devotional::where('user_id', $user->id)
                ->whereIn('status', ['draft','scheduled'])
                ->orderByDesc('updated_at')
                ->limit(3)
                ->get()
            : collect();

        $recentComments = Comment::with(['author','devotional.author'])
            ->whereHas('devotional', fn($q) => $q->where('status','published'))
            ->latest()
            ->limit(4)
            ->get();

        $topTags = Tag::withCount('devotionals')
            ->orderByDesc('devotionals_count')
            ->limit(12)
            ->get();

        return view('home', compact('votd','featured','latest','myDrafts','recentComments','topTags'));
    }
}
