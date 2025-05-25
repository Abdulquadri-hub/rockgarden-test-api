<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnouncementAttachment extends Model
{
    use HasFactory;
    
     protected $fillable = [
        'announcement_id',
        'file_name',
        'file_path',
        'file_size',
        'mime_type'
    ];

    public function announcement()
    {
        return $this->belongsTo(Announcement::class);
    }
}
