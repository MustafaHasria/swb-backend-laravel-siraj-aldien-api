<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhotoGalleryVote extends Model
{
    protected $table = 'st_photo_gallery_vote';
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

    // Relationship with gallery
    public function gallery()
    {
        return $this->belongsTo(PhotoGallery::class, 'vote_ref_id', 'photo_gallery_id');
    }
}

