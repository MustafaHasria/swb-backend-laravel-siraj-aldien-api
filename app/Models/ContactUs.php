<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUs extends Model
{
    use HasFactory;

    protected $table = 'st_contact_us';
    protected $primaryKey = 'contact_us_id';
    public $timestamps = false;

    protected $fillable = [
        'contact_us_sender_name',
        'contact_us_sender_email',
        'contact_us_sender_phone',
        'contact_us_sender_country',
        'contact_us_sender_message',
        'contact_us_sender_message_reply',
        'contact_us_date',
        'contact_us_lan',
        'contact_us_active'
    ];

    protected $casts = [
        'contact_us_date' => 'datetime',
        'contact_us_active' => 'boolean'
    ];

    /**
     * Scope to get only active contact messages
     */
    public function scopeActive($query)
    {
        return $query->where('contact_us_active', 1);
    }

    /**
     * Scope to filter by language
     */
    public function scopeLanguage($query, $language)
    {
        return $query->where('contact_us_lan', $language);
    }
}
