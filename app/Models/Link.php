<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $table = 'st_links';
    protected $primaryKey = 'links_id';
    public $timestamps = false;

    protected $fillable = [
        'links_menus',
        'links_title',
        'links_url',
        'links_lan',
        'links_target',
        'links_priority',
        'links_date',
        'links_active'
    ];

    protected $casts = [
        'links_active' => 'boolean',
        'links_date' => 'date'
    ];

    // Scope for active links
    public function scopeActive($query)
    {
        return $query->where('links_active', 1);
    }

    // Relationship with menu
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'links_menus', 'menus_id');
    }
}

