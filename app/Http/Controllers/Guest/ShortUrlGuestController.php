<?php

namespace App\Http\Controllers\Guest;


use App\Models\ShortUrl;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateShortURL;

class ShortUrlGuestController extends Controller
{
    public function createShortURL(CreateShortURL $request)
    {
        $shortCode = Str::random(4);
        $shortUrlLink = str_replace(['http://', 'https://'], '', url($shortCode));
        $expiredAt = now()->addMinutes(30);
        $url = $request->input('url');

        $shortUrl = ShortUrl::create([
            'url' => $url,
            'short_code' => $shortCode,
            'short_url_link' => $shortUrlLink,
            'expired_at' => $expiredAt,
        ]);

        return response()->json([
            'url' => $shortUrl->url,
            'short_url_link' => $shortUrl->short_url_link,
            'created_at' => $shortUrl->created_at,
            'expired_at' => $shortUrl->expired_at,
        ]);
    }
}
