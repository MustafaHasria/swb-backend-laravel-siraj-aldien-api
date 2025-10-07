<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhotoGalleryCategory extends Model
{
    protected $table = 'st_photo_gallery_category';
    protected $primaryKey = 'cat_id';
    public $timestamps = false;

    protected $fillable = [
        'cat_father_id',
        'cat_menus',
        'cat_title',
        'cat_note',
        'cat_pic',
        'cat_sup',
        'cat_date',
        'cat_pic_active',
        'cat_lan',
        'cat_pos',
        'cat_active',
        'cat_show_menu',
        'cat_show_main',
        'cat_agent'
    ];

    protected $casts = [
        'cat_pic_active' => 'boolean',
        'cat_active' => 'boolean',
        'cat_show_menu' => 'boolean',
        'cat_show_main' => 'boolean',
        'cat_date' => 'date'
    ];

    // Scope for active categories
    public function scopeActive($query)
    {
        return $query->where('cat_active', 1);
    }

    // Scope for main categories (no parent)
    public function scopeMain($query)
    {
        return $query->where('cat_father_id', 0);
    }

    // Self-referencing relationship for hierarchy
    public function parent()
    {
        return $this->belongsTo(PhotoGalleryCategory::class, 'cat_father_id', 'cat_id');
    }

    public function children()
    {
        return $this->hasMany(PhotoGalleryCategory::class, 'cat_father_id', 'cat_id');
    }

    // Relationship with galleries
    public function galleries()
    {
        return $this->hasMany(PhotoGallery::class, 'photo_gallery_cat_id', 'cat_id');
    }

    // Relationship with menu
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'cat_menus', 'menus_id');
    }
}
