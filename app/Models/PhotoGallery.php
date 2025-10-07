<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhotoGallery extends Model
{
    protected $table = 'st_photo_gallery';
    protected $primaryKey = 'photo_gallery_id';
    public $timestamps = false;

    protected $fillable = [
        'photo_gallery_cat_id',
        'photo_gallery_title',
        'photo_gallery_summary',
        'photo_gallery_pic',
        'photo_gallery_visitor',
        'photo_gallery_is_new',
        'photo_gallery_publisher_id',
        'photo_gallery_active_vote',
        'photo_gallery_active_hint',
        'photo_gallery_active',
        'photo_gallery_date',
        'photo_gallery_source',
        'photo_gallery_source_url'
    ];

    protected $casts = [
        'photo_gallery_is_new' => 'boolean',
        'photo_gallery_active_vote' => 'boolean',
        'photo_gallery_active_hint' => 'boolean',
        'photo_gallery_active' => 'boolean',
        'photo_gallery_date' => 'date'
    ];

    // Scope for active galleries
    public function scopeActive($query)
    {
        return $query->where('photo_gallery_active', 1);
    }

    // Scope for new galleries
    public function scopeNew($query)
    {
        return $query->where('photo_gallery_is_new', 1);
    }

    // Relationship with category
    public function category()
    {
        return $this->belongsTo(PhotoGalleryCategory::class, 'photo_gallery_cat_id', 'cat_id');
    }

    // Relationship with captions
    public function captions()
    {
        return $this->hasMany(PhotoGalleryCaption::class, 'cap_ref_id', 'photo_gallery_id');
    }

    // Relationship with votes
    public function votes()
    {
        return $this->hasMany(PhotoGalleryVote::class, 'vote_ref_id', 'photo_gallery_id');
    }
}
