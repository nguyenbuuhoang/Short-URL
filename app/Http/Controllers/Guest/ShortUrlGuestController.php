<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateShortURL;
use App\Services\User\ShortUrlService;

class ShortUrlGuestController extends Controller
{
    protected $shortUrlService;

    public function __construct(ShortUrlService $shortUrlService)
    {
        $this->shortUrlService = $shortUrlService;
    }

    public function createShortURL(CreateShortURL $request)
    {
        $url = $request->input('url');
        $shortUrl = $this->shortUrlService->createShort($url);

        return response()->json([
            'url' => $shortUrl->url,
            'short_url_link' => $shortUrl->short_url_link,
            'created_at' => $shortUrl->created_at,
            'expired_at' => $shortUrl->expired_at,
        ]);
    }
}
