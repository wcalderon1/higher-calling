<?php

namespace App\Http\Controllers;

use App\Models\Devotional;
use App\Models\Comment;
use App\Models\Tag;
use App\Models\User;
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
        $votd = $verses[now()->dayOfYear % count($verses)];

        // Featured devotional
        $featured = Devotional::with(['author','tags'])
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->first();

        // Latest devotionals
        $latest = Devotional::with(['author','tags'])
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        // User drafts
        $myDrafts = $user
            ? Devotional::where('user_id', $user->id)
                ->whereIn('status', ['draft','scheduled'])
                ->orderByDesc('updated_at')
                ->limit(3)
                ->get()
            : collect();

        // Recent comments
        $recentComments = Comment::with(['author','devotional.author'])
            ->whereHas('devotional', fn($q) => $q->where('status','published'))
            ->latest()
            ->limit(4)
            ->get();

        // Top tags overall
        $topTags = Tag::withCount('devotionals')
            ->orderByDesc('devotionals_count')
            ->limit(12)
            ->get();

        // 🔥 NEW: Popular Tags (last 60 days)
        $popularTags = cache()->remember('popular_tags_60d', 1800, function () {
            return Tag::query()
                ->withCount(['devotionals as recent_published_count' => function ($q) {
                    $q->where('status', 'published')
                      ->where('published_at', '>=', now()->subDays(60));
                }])
                ->orderByDesc('recent_published_count')
                ->take(10)
                ->get();
        });

        // 🔥 NEW: Recent Authors (active in last 60 days)
        $recentAuthors = cache()->remember('recent_authors_60d', 1800, function () {
            return User::query()
                ->withCount(['devotionals as recent_published_count' => function ($q) {
                    $q->where('status', 'published')
                      ->where('published_at', '>=', now()->subDays(60));
                }])
                ->whereHas('devotionals', function ($q) {
                    $q->where('status', 'published')
                      ->where('published_at', '>=', now()->subDays(60));
                })
                ->orderByDesc('recent_published_count')
                ->take(6)
                ->get(['id','name','display_name','avatar_path','bio']);
        });

        return view('home', compact(
            'votd',
            'featured',
            'latest',
            'myDrafts',
            'recentComments',
            'topTags',
            'popularTags',
            'recentAuthors'
        ));
    }
}
