<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $table = 'st_book';
    protected $primaryKey = 'book_id';
    public $timestamps = false;

    protected $fillable = [
        'book_cat_id',
        'book_title',
        'book_ts',
        'book_summary',
        'book_des',
        'book_pic',
        'book_pic_pos',
        'book_visitor',
        'book_is_new',
        'book_priority',
        'book_active_vote',
        'book_active_hint',
        'book_active',
        'book_date',
        'book_pic_active',
        'book_last_book',
        'book_publisher_id',
        'book_source',
        'book_source_url',
        'book_youtube_id',
        'book_file',
        'book_file_ePub',
        'book_file_kfx',
        'book_user_add_hint_nsup'
    ];

    protected $casts = [
        'book_is_new' => 'boolean',
        'book_active_vote' => 'boolean',
        'book_active_hint' => 'boolean',
        'book_active' => 'boolean',
        'book_pic_active' => 'boolean',
        'book_last_book' => 'boolean',
        'book_user_add_hint_nsup' => 'boolean',
        'book_date' => 'datetime'
    ];

    // Scope for active books
    public function scopeActive($query)
    {
        return $query->where('book_active', 1);
    }

    // Scope for new books
    public function scopeNew($query)
    {
        return $query->where('book_is_new', 1);
    }

    // Scope for priority books
    public function scopePriority($query)
    {
        return $query->where('book_priority', 1);
    }

    // Relationship with category
    public function category()
    {
        return $this->belongsTo(BookCategory::class, 'book_cat_id', 'cat_id');
    }

    // Relationship with captions
    public function captions()
    {
        return $this->hasMany(BookCaption::class, 'cap_ref_id', 'book_id');
    }

    // Relationship with votes
    public function votes()
    {
        return $this->hasMany(BookVote::class, 'vote_ref_id', 'book_id');
    }
}

