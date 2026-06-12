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
            ->where('published_at', '<=', now())
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
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'status'       => 'required|in:draft,published',
            'cover_image'  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'media'        => 'nullable|array',
            'media.*'      => 'required|file|mimes:jpeg,png,jpg,gif,webp,mp4,mov,avi,webm,m4v|max:20480',
            'published_at' => 'nullable|date',
        ]);

        $slug = $request->slug ? $this->generateUniqueSlug($request->slug) : $this->generateUniqueSlug($request->title);

        $status = $request->status;
        $published_at = null;

        if ($status === 'published') {
            $published_at = $request->filled('published_at') 
                ? \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $request->published_at, 'Asia/Jakarta') 
                : now();
        }

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('covers', 'public');
        }

        $article = Article::create([
            'user_id'      => auth()->id(),
            'title'        => $request->title,
            'slug'         => $slug,
            'content'      => $request->content,
            'status'       => $status,
            'published_at' => $published_at,
            'cover_image'  => $coverPath,
        ]);

        if ($request->hasFile('media')) {
            $order = 0;
            foreach ($request->file('media') as $file) {
                $mimeType = $file->getMimeType();
                $type = str_starts_with($mimeType, 'video/') ? 'video' : 'image';
                $path = $file->store('media', 'public');
                
                $article->media()->create([
                    'type'       => $type,
                    'file_path'  => $path,
                    'caption'    => null,
                    'order'      => $order++,
                    'created_by' => auth()->id(),
                ]);
            }
        }

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
        $article = Article::with(['user', 'media'])
            ->where('slug', $slug)
            ->orWhere('id', $slug)
            ->firstOrFail();

        $isAuthorOrAdmin = auth()->check() && (auth()->user()->role === 'superadmin' || auth()->id() === $article->user_id);
        $isPublished = $article->status === 'published' && $article->published_at && $article->published_at->isPast();

        if (!$isPublished && !$isAuthorOrAdmin) {
            abort(403, 'Akses Ditolak: Artikel ini belum diterbitkan atau masih berupa konsep.');
        }

        return view('articles.show', compact('article'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $article = Article::with('media')->findOrFail($id);

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
            'title'          => 'required|string|max:255',
            'content'        => 'required|string',
            'status'         => 'required|in:draft,published',
            'cover_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'media'          => 'nullable|array',
            'media.*'        => 'required|file|mimes:jpeg,png,jpg,gif,webp,mp4,mov,avi,webm,m4v|max:20480',
            'published_at'   => 'nullable|date',
            'media_captions' => 'nullable|array',
            'media_orders'   => 'nullable|array',
        ]);

        $status = $request->status;
        $published_at = $article->published_at;

        if ($status === 'draft') {
            $published_at = null;
        } else {
            // published
            if ($request->filled('published_at')) {
                $published_at = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $request->published_at, 'Asia/Jakarta');
            } elseif (!$published_at) {
                $published_at = now();
            }
        }

        $userId = $article->user_id;

        $coverPath = $article->cover_image;
        if ($request->hasFile('cover_image')) {
            if ($coverPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($coverPath)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($coverPath);
            }
            $coverPath = $request->file('cover_image')->store('covers', 'public');
        } elseif ($request->boolean('clear_cover')) {
            if ($coverPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($coverPath)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($coverPath);
            }
            $coverPath = null;
        }

        $slug = $request->slug ? $this->generateUniqueSlug($request->slug, $id) : $this->generateUniqueSlug($request->title, $id);

        $article->update([
            'user_id'      => $userId,
            'title'        => $request->title,
            'slug'         => $slug,
            'content'      => $request->content,
            'status'       => $status,
            'published_at' => $published_at,
            'cover_image'  => $coverPath,
        ]);

        // Process captions updates
        if ($request->has('media_captions')) {
            foreach ($request->media_captions as $mediaId => $caption) {
                $media = \App\Models\Media::find($mediaId);
                if ($media && ($media->article->user_id === auth()->id() || auth()->user()->role === 'superadmin')) {
                    $media->caption = $caption;
                    $media->save();
                }
            }
        }

        // Process existing media reordering
        if ($request->has('media_orders')) {
            foreach ($request->media_orders as $mediaId => $order) {
                $media = \App\Models\Media::find($mediaId);
                if ($media && ($media->article->user_id === auth()->id() || auth()->user()->role === 'superadmin')) {
                    $media->order = (int)$order;
                    $media->save();
                }
            }
        }

        // Process new media uploads
        if ($request->hasFile('media')) {
            $maxOrder = (int)$article->media()->max('order');
            $order = $maxOrder + 1;

            foreach ($request->file('media') as $file) {
                $mimeType = $file->getMimeType();
                $type = str_starts_with($mimeType, 'video/') ? 'video' : 'image';
                $path = $file->store('media', 'public');

                $article->media()->create([
                    'type'       => $type,
                    'file_path'  => $path,
                    'caption'    => null,
                    'order'      => $order++,
                    'created_by' => auth()->id(),
                ]);
            }
        }

        return redirect('/dashboard')->with('success', 'Artikel berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $article = Article::with('media')->findOrFail($id);

        if (auth()->user()->role !== 'superadmin' && auth()->id() !== $article->user_id) {
            abort(403, 'Akses Ditolak: Anda hanya dapat menghapus artikel milik sendiri.');
        }

        // Physically delete all associated media files
        foreach ($article->media as $media) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($media->file_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($media->file_path);
            }
        }

        // Physically delete cover image
        if ($article->cover_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($article->cover_image)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($article->cover_image);
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

        $articles = Article::with('user')
            ->where(function ($query) {
                if (auth()->user()->role === 'superadmin') {
                    $query->whereRaw('1=1');
                } else {
                    $query->where(function ($q) {
                        $q->where('status', 'published')
                          ->where('published_at', '<=', now());
                    })->orWhere('user_id', auth()->id());
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
     * Set cover image from existing media image.
     */
    public function setCover(string $articleId, string $mediaId)
    {
        $article = Article::findOrFail($articleId);
        $media = \App\Models\Media::findOrFail($mediaId);

        if (auth()->user()->role !== 'superadmin' && auth()->id() !== $article->user_id) {
            abort(403, 'Akses Ditolak: Anda hanya dapat memperbarui artikel milik sendiri.');
        }

        if ($media->article_id != $article->id || $media->type !== 'image') {
            return redirect()->back()->with('error', 'Media tidak valid atau bukan gambar.');
        }

        $article->cover_image = $media->file_path;
        $article->save();

        return redirect()->back()->with('success', 'Gambar sampul berhasil diubah dari galeri media.');
    }

    /**
     * Delete a single media record and its physical file.
     */
    public function deleteMedia(string $mediaId)
    {
        $media = \App\Models\Media::findOrFail($mediaId);
        $article = $media->article;

        if (auth()->user()->role !== 'superadmin' && auth()->id() !== $article->user_id) {
            abort(403, 'Akses Ditolak: Anda tidak dapat menghapus media ini.');
        }

        if ($article->cover_image === $media->file_path) {
            $article->cover_image = null;
            $article->save();
        }

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($media->file_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($media->file_path);
        }

        $media->delete();

        return redirect()->back()->with('success', 'Media berhasil dihapus.');
    }

    /**
     * Reorder media files via AJAX.
     */
    public function reorderMedia(Request $request, string $articleId)
    {
        $article = Article::findOrFail($articleId);

        if (auth()->user()->role !== 'superadmin' && auth()->id() !== $article->user_id) {
            return response()->json(['error' => 'Akses Ditolak.'], 403);
        }

        $request->validate([
            'order'   => 'required|array',
            'order.*' => 'integer|exists:media,id'
        ]);

        foreach ($request->order as $index => $mediaId) {
            $media = \App\Models\Media::where('article_id', $article->id)->find($mediaId);
            if ($media) {
                $media->order = $index;
                $media->save();
            }
        }

        return response()->json(['success' => true, 'message' => 'Urutan media berhasil diperbarui.']);
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
