<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sound extends Model
{
    protected $table = 'st_sound';
    protected $primaryKey = 'sound_id';
    public $timestamps = false;

    protected $fillable = [
        'sound_cat_id',
        'sound_title',
        'sound_ts',
        'sound_summary',
        'sound_des',
        'sound_pic',
        'sound_pic_pos',
        'sound_visitor',
        'sound_is_new',
        'sound_priority',
        'sound_active_vote',
        'sound_active_hint',
        'sound_active',
        'sound_date',
        'sound_pic_active',
        'sound_last_sound',
        'sound_publisher_id',
        'sound_source',
        'sound_source_url',
        'sound_youtube_id',
        'sound_file',
        'sound_user_add_hint_nsup'
    ];

    protected $casts = [
        'sound_is_new' => 'boolean',
        'sound_active_vote' => 'boolean',
        'sound_active_hint' => 'boolean',
        'sound_active' => 'boolean',
        'sound_pic_active' => 'boolean',
        'sound_last_sound' => 'boolean',
        'sound_user_add_hint_nsup' => 'boolean',
        'sound_date' => 'date'
    ];

    // Scope for active sounds
    public function scopeActive($query)
    {
        return $query->where('sound_active', 1);
    }

    // Scope for new sounds
    public function scopeNew($query)
    {
        return $query->where('sound_is_new', 1);
    }

    // Scope for priority sounds
    public function scopePriority($query)
    {
        return $query->where('sound_priority', 1);
    }

    // Relationship with category
    public function category()
    {
        return $this->belongsTo(SoundCategory::class, 'sound_cat_id', 'cat_id');
    }

    // Relationship with captions
    public function captions()
    {
        return $this->hasMany(SoundCaption::class, 'cap_ref_id', 'sound_id');
    }

    // Relationship with votes
    public function votes()
    {
        return $this->hasMany(SoundVote::class, 'vote_ref_id', 'sound_id');
    }
}

