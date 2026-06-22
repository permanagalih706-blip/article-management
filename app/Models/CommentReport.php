<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment_id',
        'user_id',
        'reason',
        'description',
        'status',
        'resolved_by',
        'resolved_at',
        'action_taken',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function comment()
    {
        return $this->belongsTo(Comment::class)->withTrashed();
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
