<?php

namespace App\Services\User;

use App\Models\ShortUrl;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ShortUrlService
{
    //Tạo Link Short
    public function createShort($url)
    {
        $user = Auth::user();
        $shortCode = Str::random(4);
        $shortUrlLink = url($shortCode);
        $expiredAt = now()->addMinutes(30);
        $userId = null;
        $qrCode = null;

        if ($user) {
            $shortUrlLink = str_replace(['http://', 'https://'], '', $shortUrlLink);
            $expiredAt = now()->addDays(5);
            $userId = $user->id;
            $qrCode = QrCode::size(200)->generate($shortUrlLink);
        }

        return ShortUrl::create([
            'url' => $url,
            'short_code' => $shortCode,
            'short_url_link' => $shortUrlLink,
            'expired_at' => $expiredAt,
            'user_id' => $userId,
            'qrcode' => $qrCode,
        ]);
    }
    public function getByShortCode($shortCode)
    {
        return ShortUrl::where('short_code', $shortCode)->first();
    }
    public function getByShortUserId($userId)
    {
        return ShortUrl::where('user_id', $userId);
    }
    public function getTotalsByUserId($userId)
    {
        return ShortUrl::where('user_id', $userId)
            ->selectRaw('COUNT(*) as totalShortLinks, SUM(clicks) as totalClicks')
            ->first();
    }

    //Tìm Id
    public function findShortUrl($id)
    {
        return ShortUrl::find($id);
    }
    //Kiểm tra Id đăng nhập
    public function hasPermission($shortUrl)
    {
        if (!Auth::check() || $shortUrl->user_id !== Auth::user()->id) {
            return false;
        }
        return true;
    }
    //Update link shorts
    public function updateShortUrl($shortUrl, $shortCode, $status)
    {
        $shortUrl->update([
            'short_code' => $shortCode,
            'short_url_link' => str_replace(['http://', 'https://'], '', url($shortCode)),
            'status' => $status,
        ]);
        return $shortUrl->only(['url', 'short_url_link', 'status']);
    }
}
