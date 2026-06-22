<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentReport;
use Illuminate\Http\Request;

class CommentReportController extends Controller
{
    /**
     * Store a new report for a comment.
     */
    public function store(Request $request, Comment $comment)
    {
        $request->validate([
            'reason'        => 'required|string|max:255',
            'custom_reason' => 'nullable|string|max:255',
            'description'   => 'nullable|string|max:1000',
        ]);

        // Prevent reporting own comment
        if (auth()->id() === $comment->user_id) {
            return redirect()->back()->with('error', 'Anda tidak dapat melaporkan komentar milik sendiri.');
        }

        // Prevent duplicate reports from same user
        $existing = CommentReport::where('comment_id', $comment->id)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Anda sudah melaporkan komentar ini sebelumnya.');
        }

        $reason = $request->reason;
        if ($reason === 'other') {
            $reason = $request->filled('custom_reason') ? $request->custom_reason : 'Lainnya';
        }

        CommentReport::create([
            'comment_id'  => $comment->id,
            'user_id'     => auth()->id(),
            'reason'      => $reason,
            'description' => $request->description,
            'status'      => 'pending',
        ]);

        return redirect()->back()->with('success', 'Laporan berhasil dikirim. Terima kasih atas laporan Anda!');
    }

    /**
     * Display all reports (admin).
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $reports = CommentReport::with(['comment.user', 'comment.article', 'reporter'])
            ->when($status !== 'all', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.reports.index', compact('reports', 'status'));
    }

    /**
     * Show a specific report (admin).
     */
    public function show(CommentReport $report)
    {
        $report->load(['comment.user', 'comment.article', 'reporter', 'resolver']);

        return view('admin.reports.show', compact('report'));
    }

    /**
     * Resolve a report (admin).
     */
    public function resolve(Request $request, CommentReport $report)
    {
        $request->validate([
            'action' => 'required|in:dismiss,delete_comment,resolve',
        ]);

        $actionTaken = '';

        switch ($request->action) {
            case 'dismiss':
                $report->update([
                    'status'       => 'dismissed',
                    'resolved_by'  => auth()->id(),
                    'resolved_at'  => now(),
                    'action_taken' => 'Laporan ditolak/dismissed',
                ]);
                $actionTaken = 'Laporan berhasil ditolak (dismissed).';
                break;

            case 'delete_comment':
                if ($report->comment) {
                    $report->comment->delete(); // soft delete
                }
                $report->update([
                    'status'       => 'resolved',
                    'resolved_by'  => auth()->id(),
                    'resolved_at'  => now(),
                    'action_taken' => 'Komentar dihapus oleh admin',
                ]);
                $actionTaken = 'Komentar berhasil dihapus dan laporan diselesaikan.';
                break;

            case 'resolve':
                $report->update([
                    'status'       => 'resolved',
                    'resolved_by'  => auth()->id(),
                    'resolved_at'  => now(),
                    'action_taken' => 'Laporan diselesaikan tanpa tindakan',
                ]);
                $actionTaken = 'Laporan berhasil diselesaikan.';
                break;
        }

        return redirect()->route('admin.reports.index')->with('success', $actionTaken);
    }
}
