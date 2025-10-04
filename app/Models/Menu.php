<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'st_menus';
    protected $primaryKey = 'menus_id';
    public $timestamps = false;

    protected $fillable = [
        'menus_name',
        'menus_url',
        'menus_pos',
        'menus_priority',
        'menus_lan',
        'menus_date',
        'menus_active_header',
        'menus_active'
    ];

    protected $casts = [
        'menus_active_header' => 'boolean',
        'menus_active' => 'boolean',
        'menus_date' => 'date'
    ];

    // Scope for active menus
    public function scopeActive($query)
    {
        return $query->where('menus_active', 1);
    }

    // Scope for header menus
    public function scopeHeader($query)
    {
        return $query->where('menus_active_header', 1);
    }

    // Scope for specific position
    public function scopePosition($query, $position)
    {
        return $query->where('menus_pos', $position);
    }

    // Relationship with links
    public function links()
    {
        return $this->hasMany(Link::class, 'links_menus', 'menus_id');
    }
}

