<?php

namespace App\Http\Controllers\User;

use App\Models\ShortUrl;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateShortURL;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ShortUrlController extends Controller
{
    public function createShortURL(CreateShortURL $request)
    {
        $user = Auth::user();
        $userId = $user->id;
        $shortCode = Str::random(4);
        $shortUrlLink = url($shortCode);
        $qrCode = QrCode::size(200)->generate($shortUrlLink);
        $expiredAt = now()->addDays(5);
        $url = $request->input('url');
        $shortUrl = ShortUrl::create([
            'url' => $url,
            'short_code' => $shortCode,
            'short_url_link' => $shortUrlLink,
            'expired_at' => $expiredAt,
            'user_id' => $userId,
            'qrcode' => $qrCode,
        ]);
        return response()->json([
            'url' => $shortUrl->url,
            'short_url_link' => $shortUrl->short_url_link,
            'clicks' => $shortUrl->clicks,
            'status' => $shortUrl->status,
            'created_at' => $shortUrl->created_at,
            'expired_at' => $shortUrl->expired_at,
            'qrcode' => $qrCode,
            'user_id' => $userId,
        ]);
    }

    public function redirectToURL($shortCode)
    {
        $shortUrl = ShortUrl::where('short_code', $shortCode)->first();

        if (!$shortUrl) {
            return response()->json(['error' => 'Short URL không tìm thấy'], 404);
        }
        if ($shortUrl->expired_at && now() > $shortUrl->expired_at) {
            return view('errors.expired_code');
        }

        $shortUrl->increment('clicks');
        return redirect($shortUrl->url);
    }
}
