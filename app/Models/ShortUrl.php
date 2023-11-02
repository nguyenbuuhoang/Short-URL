<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShortUrl extends Model
{
    use HasFactory;
    protected $fillable = [
        'url',
        'short_code',
        'short_url_link',
        'clicks',
        'status',
        'expired_at',
        'created_at',
        'user_id',
        'qrcode',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
