<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhotoGalleryCaption extends Model
{
    protected $table = 'st_photo_gallery_caption';
    protected $primaryKey = 'cap_id';
    public $timestamps = false;

    protected $fillable = [
        'cap_ref_id',
        'cap_user_id',
        'cap_date',
        'cap_title',
        'cap_name',
        'cap_email',
        'cap_address',
        'cap_text',
        'cap_isnew',
        'cap_active'
    ];

    protected $casts = [
        'cap_isnew' => 'boolean',
        'cap_active' => 'boolean',
        'cap_date' => 'date'
    ];

    // Scope for active captions
    public function scopeActive($query)
    {
        return $query->where('cap_active', 1);
    }

    // Scope for new captions
    public function scopeNew($query)
    {
        return $query->where('cap_isnew', 1);
    }

    // Relationship with gallery
    public function gallery()
    {
        return $this->belongsTo(PhotoGallery::class, 'cap_ref_id', 'gallery_id');
    }
}

