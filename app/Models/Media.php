<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Article;
use App\Models\User;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';

    protected $fillable = [
        'article_id',
        'type',
        'file_path',
        'caption',
        'order',
        'created_by',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
