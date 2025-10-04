<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleVote extends Model
{
    protected $table = 'st_article_vote';
    protected $primaryKey = 'vote_id';
    public $timestamps = false;

    protected $fillable = [
        'vote_ref_id',
        'vote_user_id',
        'vote_date',
        'vote_name',
        'vote_email',
        'vote_text',
        'vote_rate',
        'vote_active'
    ];

    protected $casts = [
        'vote_active' => 'boolean',
        'vote_date' => 'date'
    ];

    // Scope for active votes
    public function scopeActive($query)
    {
        return $query->where('vote_active', 1);
    }

    // Relationship with article
    public function article()
    {
        return $this->belongsTo(Article::class, 'vote_ref_id', 'article_id');
    }
}

