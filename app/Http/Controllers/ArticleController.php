<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource (Public Endpoint).
     */
    public function index()
    {
        $search = request('search');
        $author = request('author');
        $sort = request('sort', 'latest_created');

        $articles = Article::with('user')
            ->where('status', 'published')
            ->when($search, function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%");
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

        // Slug is automatically handled by the saving event in the Article model fallback
        // or generated here if we want a custom one:
        $slug = $request->slug ? $this->generateUniqueSlug($request->slug) : $this->generateUniqueSlug($request->title);

        $status = $request->status;
        $published_at = ($status === 'published') ? now() : null;

        Article::create([
            'user_id' => auth()->id(), // user_id is automatically from logged-in user
            'title'   => $request->title,
            'slug'    => $slug,
            'content' => $request->content,
            'status'  => $status,
            'published_at' => $published_at,
        ]);

        return redirect('/dashboard')->with('success', 'Artikel berhasil ditambahkan!');
    }

    /**
     * Display the specified resource (Legacy/Internal auth fallback).
     */
    public function show(string $id)
    {
        return $this->showPublic($id);
    }

    /**
     * Display the specified resource (Public detail view by slug or id).
     */
    public function showPublic(string $slug)
    {
        $article = Article::with('user')
            ->where('slug', $slug)
            ->orWhere('id', $slug)
            ->firstOrFail();

        if ($article->status === 'draft') {
            if (!auth()->check() || (auth()->user()->role !== 'superadmin' && auth()->id() !== $article->user_id)) {
                abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk melihat draft ini.');
            }
        }

        return view('articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $article = Article::findOrFail($id);

        if (auth()->user()->role !== 'superadmin' && auth()->id() !== $article->user_id) {
            abort(403, 'Akses Ditolak: Anda hanya dapat mengubah artikel milik sendiri.');
        }

        $users = User::all();

        return view('articles.edit', compact('article', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $article = Article::findOrFail($id);

        if (auth()->user()->role !== 'superadmin' && auth()->id() !== $article->user_id) {
            abort(403, 'Akses Ditolak: Anda hanya dapat memperbarui artikel milik sendiri.');
        }

        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'status'  => 'required|in:draft,published',
        ]);

        $status = $article->status;
        $published_at = $article->published_at;

        if ($status !== 'published') {
            $status = $request->status;
            if ($status === 'published') {
                $published_at = now();
            }
        }

        // user_id tidak pernah diubah saat update — penulis artikel terkunci
        $userId = $article->user_id;

        // Generate unique slug, excluding the current article
        $slug = $request->slug ? $this->generateUniqueSlug($request->slug, $id) : $this->generateUniqueSlug($request->title, $id);

        $article->update([
            'user_id' => $userId,
            'title'   => $request->title,
            'slug'    => $slug,
            'content' => $request->content,
            'status'  => $status,
            'published_at' => $published_at,
        ]);

        return redirect('/dashboard')->with('success', 'Artikel berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $article = Article::findOrFail($id);

        if (auth()->user()->role !== 'superadmin' && auth()->id() !== $article->user_id) {
            abort(403, 'Akses Ditolak: Anda hanya dapat menghapus artikel milik sendiri.');
        }

        $article->delete();

        return redirect('/dashboard')->with('success', 'Artikel berhasil dihapus!');
    }

    /**
     * Dashboard view.
     */
    public function dashboard()
    {
        $search = request('search');
        $author = request('author');
        $sort = request('sort', 'latest_created');

        // Superadmin sees all articles, User sees all published articles + their own drafts
        $articles = Article::with('user')
            ->where(function ($query) {
                if (auth()->user()->role === 'superadmin') {
                    // Superadmin can see everything
                    $query->whereRaw('1=1');
                } else {
                    // Regular user sees published ones OR their own
                    $query->where('status', 'published')
                          ->orWhere('user_id', auth()->id());
                }
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

        // Drafts are always owner-specific (or superadmin sees all draft articles if requested, but regular users see only their own)
        $articles = Article::with('user')
            ->where('status', 'draft')
            ->where(function ($query) {
                if (auth()->user()->role !== 'superadmin') {
                    $query->where('user_id', auth()->id());
                }
            })
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

        if (auth()->user()->role !== 'superadmin' && auth()->id() !== $article->user_id) {
            abort(403, 'Akses Ditolak: Anda hanya dapat menerbitkan artikel milik sendiri.');
        }

        $article->status = 'published';
        $article->published_at = now();
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
