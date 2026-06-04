<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $search = request('search');
    $author = request('author');

    $articles = Article::with('user')
        ->when($search, function ($query) use ($search) {
            $query->where('title', 'like', "%{$search}%");
        })
        ->when($author, function ($query) use ($author) {
            $query->where('user_id', $author);
        })
        ->get();

    $users = User::all();

    return view('articles.index', compact('articles', 'users'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    $users = User::all();

    return view('articles.create', compact('users'));
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'status'  => 'required|in:draft,published',
        ]);

        $slug = $this->generateUniqueSlug($request->title);

        Article::create([
            'user_id' => auth()->id(),
            'title'   => $request->title,
            'slug'    => $slug,
            'content' => $request->content,
            'status'  => $request->status,
        ]);

        return redirect('/dashboard')->with('success', 'Artikel berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $article = Article::with('user')->findOrFail($id);

        if ($article->status === 'draft' && auth()->id() !== $article->user_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $article = Article::findOrFail($id);
        $users = User::all();

        return view('articles.edit', compact('article', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'status'  => 'required|in:draft,published',
        ]);

        $article = Article::findOrFail($id);

        $status = $article->status;
        if ($status !== 'published') {
            $status = $request->status;
        }

        // Generate unique slug, excluding the current article
        $slug = $this->generateUniqueSlug($request->title, $id);

        $article->update([
            'user_id' => $request->user_id ?? $article->user_id,
            'title'   => $request->title,
            'slug'    => $slug,
            'content' => $request->content,
            'status'  => $status,
        ]);

        return redirect('/dashboard')->with('success', 'Artikel berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $article = Article::findOrFail($id);

        $article->delete();

        return redirect('/dashboard');
    }
    /**
     * Dashboard became the main activity adding articles in there.
     */
    public function dashboard()
    {
        $search = request('search');
        $author = request('author');
        $sort = request('sort', 'latest_created');

        $articles = Article::with('user')
            ->where(function ($query) {
                $query->where('status', 'published')
                      ->orWhere('user_id', auth()->id());
            })
            ->when($search, function ($query) use ($search) {
                $query->where('title', 'like', "%$search%");
            })
            ->when($author, function ($query) use ($author) {
                $query->where('user_id', $author);
            })
            ->when($sort === 'latest_created', function ($query) {
                $query->orderBy('created_at', 'desc');
            })
            ->when($sort === 'latest_updated', function ($query) {
                $query->orderBy('updated_at', 'desc');
            })
            ->when($sort === 'title_asc', function ($query) {
                $query->orderBy('title', 'asc');
            })
            ->when($sort === 'title_desc', function ($query) {
                $query->orderBy('title', 'desc');
            })
            ->unless(in_array($sort, ['latest_created', 'latest_updated', 'title_asc', 'title_desc']), function ($query) {
                $query->orderBy('created_at', 'desc');
            })
            ->paginate(6);

        $users = User::all();

        return view('dashboard', compact('articles', 'users'));
    }

    /**
     * Display a listing of the draft resources.
     */
    public function drafts()
    {
        $search = request('search');
        $sort = request('sort', 'latest_created');

        $articles = Article::with('user')
            ->where('user_id', auth()->id())
            ->where('status', 'draft')
            ->when($search, function ($query) use ($search) {
                $query->where('title', 'like', "%$search%");
            })
            ->when($sort === 'latest_created', function ($query) {
                $query->orderBy('created_at', 'desc');
            })
            ->when($sort === 'latest_updated', function ($query) {
                $query->orderBy('updated_at', 'desc');
            })
            ->when($sort === 'title_asc', function ($query) {
                $query->orderBy('title', 'asc');
            })
            ->when($sort === 'title_desc', function ($query) {
                $query->orderBy('title', 'desc');
            })
            ->unless(in_array($sort, ['latest_created', 'latest_updated', 'title_asc', 'title_desc']), function ($query) {
                $query->orderBy('created_at', 'desc');
            })
            ->paginate(6);

        $users = User::all();
        $isDraftsPage = true;

        return view('dashboard', compact('articles', 'users', 'isDraftsPage'));
    }
    /**
     * Publish a draft article.
     */
    public function publish(string $id)
    {
        $article = Article::findOrFail($id);

        if (auth()->id() !== $article->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $article->status = 'published';
        $article->save();

        return redirect()->back()->with('success', 'Artikel berhasil diterbitkan!');
    }

    /**
     * Generate a unique slug, optionally excluding a given article ID.
     */
    private function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $count = 1;

        while (true) {
            $query = Article::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            if (!$query->exists()) {
                break;
            }
            $slug = $baseSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
