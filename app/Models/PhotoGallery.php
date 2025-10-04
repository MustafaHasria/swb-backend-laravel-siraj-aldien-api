<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhotoGallery extends Model
{
    protected $table = 'st_photo_gallery';
    protected $primaryKey = 'gallery_id';
    public $timestamps = false;

    protected $fillable = [
        'gallery_cat_id',
        'gallery_title',
        'gallery_ts',
        'gallery_summary',
        'gallery_des',
        'gallery_pic',
        'gallery_pic_pos',
        'gallery_visitor',
        'gallery_is_new',
        'gallery_priority',
        'gallery_active_vote',
        'gallery_active_hint',
        'gallery_active',
        'gallery_date',
        'gallery_pic_active',
        'gallery_last_gallery',
        'gallery_publisher_id',
        'gallery_source',
        'gallery_source_url',
        'gallery_youtube_id',
        'gallery_file',
        'gallery_user_add_hint_nsup'
    ];

    protected $casts = [
        'gallery_is_new' => 'boolean',
        'gallery_active_vote' => 'boolean',
        'gallery_active_hint' => 'boolean',
        'gallery_active' => 'boolean',
        'gallery_pic_active' => 'boolean',
        'gallery_last_gallery' => 'boolean',
        'gallery_user_add_hint_nsup' => 'boolean',
        'gallery_date' => 'date'
    ];

    // Scope for active galleries
    public function scopeActive($query)
    {
        return $query->where('gallery_active', 1);
    }

    // Scope for new galleries
    public function scopeNew($query)
    {
        return $query->where('gallery_is_new', 1);
    }

    // Scope for priority galleries
    public function scopePriority($query)
    {
        return $query->where('gallery_priority', 1);
    }

    // Relationship with category
    public function category()
    {
        return $this->belongsTo(PhotoGalleryCategory::class, 'gallery_cat_id', 'cat_id');
    }

    // Relationship with captions
    public function captions()
    {
        return $this->hasMany(PhotoGalleryCaption::class, 'cap_ref_id', 'gallery_id');
    }

    // Relationship with votes
    public function votes()
    {
        return $this->hasMany(PhotoGalleryVote::class, 'vote_ref_id', 'gallery_id');
    }
}
