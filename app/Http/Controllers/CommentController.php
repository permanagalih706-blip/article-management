<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use App\Models\ModerationLog;
use App\Services\ContentModerationService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    protected $moderationService;

    public function __construct(ContentModerationService $moderationService)
    {
        $this->moderationService = $moderationService;
    }

    /**
     * Store a newly created comment.
     */
    public function store(Request $request, Article $article)
    {
        $request->validate([
            'body'      => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        // If parent_id is provided, verify it belongs to the same article
        if ($request->parent_id) {
            $parent = Comment::where('id', $request->parent_id)
                ->where('article_id', $article->id)
                ->first();

            if (!$parent) {
                return redirect()->back()->with('error', 'Komentar induk tidak valid.');
            }
        }

        // Check content against blocklist
        if (!$this->moderationService->isAllowed($request->body)) {
            $blockedWords = $this->moderationService->getBlockedWords($request->body);

            // Log the rejected comment
            ModerationLog::create([
                'user_id'         => auth()->id(),
                'action'          => 'auto_filtered',
                'reason'          => 'Mengandung kata terlarang: ' . implode(', ', $blockedWords),
                'blocked_content' => $request->body,
            ]);

            return redirect()->back()->with('error', 'Komentar Anda mengandung kata yang tidak diperbolehkan dan tidak dapat disimpan.');
        }

        Comment::create([
            'article_id' => $article->id,
            'user_id'    => auth()->id(),
            'parent_id'  => $request->parent_id,
            'body'       => $request->body,
        ]);

        return redirect()->back()->with('success', 'Komentar berhasil ditambahkan!');
    }

    /**
     * Update the specified comment.
     */
    public function update(Request $request, Comment $comment)
    {
        // Only the comment owner can edit
        if (auth()->id() !== $comment->user_id) {
            abort(403, 'Anda hanya dapat mengedit komentar milik sendiri.');
        }

        $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        // Check content against blocklist
        if (!$this->moderationService->isAllowed($request->body)) {
            $blockedWords = $this->moderationService->getBlockedWords($request->body);

            ModerationLog::create([
                'comment_id'      => $comment->id,
                'user_id'         => auth()->id(),
                'action'          => 'auto_filtered',
                'reason'          => 'Edit ditolak - mengandung kata terlarang: ' . implode(', ', $blockedWords),
                'blocked_content' => $request->body,
            ]);

            return redirect()->back()->with('error', 'Komentar Anda mengandung kata yang tidak diperbolehkan.');
        }

        $comment->update([
            'body' => $request->body,
        ]);

        return redirect()->back()->with('success', 'Komentar berhasil diperbarui!');
    }

    /**
     * Remove the specified comment (soft delete).
     */
    public function destroy(Comment $comment)
    {
        // Only the comment owner or superadmin can delete
        if (auth()->id() !== $comment->user_id && auth()->user()->role !== 'superadmin') {
            abort(403, 'Anda tidak memiliki izin untuk menghapus komentar ini.');
        }

        $comment->delete();

        return redirect()->back()->with('success', 'Komentar berhasil dihapus!');
    }
}
