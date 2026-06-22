<?php

namespace App\Http\Controllers;

use App\Models\ModerationLog;
use Illuminate\Http\Request;

class ModerationLogController extends Controller
{
    /**
     * Display all moderation logs (admin).
     */
    public function index()
    {
        $logs = ModerationLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.moderation-logs', compact('logs'));
    }
}
