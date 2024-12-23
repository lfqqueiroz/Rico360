<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    use HasFactory;

    // Defines which fields can be mass assigned
    protected $fillable = [
        'callSid',
        'duration',
        'status',
        'from_user_id',
        'to_user_id',
    ];

    // Format call duration from seconds to MM:SS format
    public function getFormattedDurationAttribute()
    {
        if (!$this->duration) return 'N/A';
        
        // Calculate minutes and seconds
        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;
        
        // Return formatted time string
        if ($minutes > 0) {
            return sprintf("%02d:%02d", $minutes, $seconds);
        }
        
        return sprintf("00:%02d", $seconds);
    }

    // Generic relationship to user table
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship to contact (used for historical purposes)
    public function contact()
    {
        return $this->belongsTo(User::class, 'id');
    }

    // Relationship to the user who initiated the call
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    // Relationship to the user who received the call
    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

}
