<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookVote extends Model
{
    protected $table = 'st_book_vote';
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

    // Relationship with book
    public function book()
    {
        return $this->belongsTo(Book::class, 'vote_ref_id', 'book_id');
    }
}

