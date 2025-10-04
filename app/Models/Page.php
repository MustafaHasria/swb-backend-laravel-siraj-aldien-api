<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'st_pages';
    protected $primaryKey = 'pages_id';
    public $timestamps = false;

    protected $fillable = [
        'pages_menus',
        'pages_title',
        'pages_content',
        'pages_lan',
        'pages_visitor',
        'pages_priority',
        'pages_date',
        'pages_active'
    ];

    protected $casts = [
        'pages_active' => 'boolean',
        'pages_date' => 'date'
    ];

    // Scope for active pages
    public function scopeActive($query)
    {
        return $query->where('pages_active', 1);
    }

    // Scope for priority pages
    public function scopePriority($query)
    {
        return $query->where('pages_priority', 1);
    }
}
