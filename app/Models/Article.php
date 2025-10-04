<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'st_article';
    protected $primaryKey = 'article_id';
    public $timestamps = false;

    protected $fillable = [
        'article_cat_id',
        'article_title',
        'article_ts',
        'article_summary',
        'article_des',
        'article_pic',
        'article_pic_pos',
        'article_visitor',
        'article_is_new',
        'article_priority',
        'article_active_vote',
        'article_active_hint',
        'article_active',
        'article_date',
        'article_pic_active',
        'article_last_article',
        'article_publisher_id',
        'article_source',
        'article_source_url',
        'article_youtube_id',
        'article_file',
        'article_user_add_hint_nsup'
    ];

    protected $casts = [
        'article_is_new' => 'boolean',
        'article_active_vote' => 'boolean',
        'article_active_hint' => 'boolean',
        'article_active' => 'boolean',
        'article_pic_active' => 'boolean',
        'article_last_article' => 'boolean',
        'article_user_add_hint_nsup' => 'boolean',
        'article_date' => 'date'
    ];

    // Scope for active articles
    public function scopeActive($query)
    {
        return $query->where('article_active', 1);
    }

    // Scope for new articles
    public function scopeNew($query)
    {
        return $query->where('article_is_new', 1);
    }

    // Scope for priority articles
    public function scopePriority($query)
    {
        return $query->where('article_priority', 1);
    }

    // Relationship with category
    public function category()
    {
        return $this->belongsTo(ArticleCategory::class, 'article_cat_id', 'cat_id');
    }

    // Relationship with captions
    public function captions()
    {
        return $this->hasMany(ArticleCaption::class, 'cap_ref_id', 'article_id');
    }

    // Relationship with votes
    public function votes()
    {
        return $this->hasMany(ArticleVote::class, 'vote_ref_id', 'article_id');
    }
}

