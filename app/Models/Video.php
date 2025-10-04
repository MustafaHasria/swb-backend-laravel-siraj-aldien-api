<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $table = 'st_video';
    protected $primaryKey = 'video_id';
    public $timestamps = false;

    protected $fillable = [
        'video_cat_id',
        'video_title',
        'video_ts',
        'video_summary',
        'video_des',
        'video_pic',
        'video_pic_pos',
        'video_visitor',
        'video_is_new',
        'video_priority',
        'video_active_vote',
        'video_active_hint',
        'video_active',
        'video_date',
        'video_pic_active',
        'video_last_video',
        'video_publisher_id',
        'video_source',
        'video_source_url',
        'video_youtube_id',
        'video_file',
        'video_user_add_hint_nsup'
    ];

    protected $casts = [
        'video_is_new' => 'boolean',
        'video_active_vote' => 'boolean',
        'video_active_hint' => 'boolean',
        'video_active' => 'boolean',
        'video_pic_active' => 'boolean',
        'video_last_video' => 'boolean',
        'video_user_add_hint_nsup' => 'boolean',
        'video_date' => 'date'
    ];

    // Scope for active videos
    public function scopeActive($query)
    {
        return $query->where('video_active', 1);
    }

    // Scope for new videos
    public function scopeNew($query)
    {
        return $query->where('video_is_new', 1);
    }

    // Scope for priority videos
    public function scopePriority($query)
    {
        return $query->where('video_priority', 1);
    }

    // Relationship with category
    public function category()
    {
        return $this->belongsTo(VideoCategory::class, 'video_cat_id', 'cat_id');
    }

    // Relationship with captions
    public function captions()
    {
        return $this->hasMany(VideoCaption::class, 'cap_ref_id', 'video_id');
    }

    // Relationship with votes
    public function votes()
    {
        return $this->hasMany(VideoVote::class, 'vote_ref_id', 'video_id');
    }
}

