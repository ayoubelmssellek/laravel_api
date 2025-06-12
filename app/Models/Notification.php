<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Model\User;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'user_id',
        'message',
        'reference_id', // This is the new field added to store the reference ID
        'is_read',
    ];

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
}