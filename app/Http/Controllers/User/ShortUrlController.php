<?php

namespace App\Http\Controllers\User;

use App\Models\ShortUrl;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\ShortUrlsExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
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
    public function getShortURLsByUserId(Request $request, $userId, $perPage = 4)
    {
        $query = ShortUrl::where('user_id', $userId);

        $shortLink = $request->input('url');
        if (!empty($shortLink)) {
            $query->where('url', 'like', '%' . $shortLink . '%');
        }
        $sort_by = $request->input('sort_by', 'id');
        $sort_order = $request->input('sort_order', 'asc');
        $query->orderBy($sort_by, $sort_order);

        if ($request->has('export') && $request->input('export') === 'csv') {
            $shortUrls = $query->get();
            return Excel::download(new ShortUrlsExport($shortUrls), 'data_short_urls.csv');
        }

        $shortUrls = $query->paginate($perPage);
        return response()->json([
            'shortUrls' => $shortUrls,
        ], 200);
    }
    public function getTotalsByUserId($userId)
    {
        $totals = ShortUrl::where('user_id', $userId)
            ->selectRaw('COUNT(*) as totalShortLinks, SUM(clicks) as totalClicks')
            ->first();

        return response()->json([
            'totalShortLinks' => $totals->totalShortLinks,
            'totalClicks' => $totals->totalClicks,
        ], 200);
    }
    public function updateShortCode(Request $request, $id)
    {
        $shortUrl = ShortUrl::findOrFail($id);

        if (!Auth::check() || $shortUrl->user_id !== Auth::user()->id) {
            return response()->json(['error' => 'Không có quyền chỉnh sửa'], 403);
        }

        $shortCode = $request->input('short_code');
        $status = $request->input('status');

        $shortUrl->update([
            'short_code' => $shortCode,
            'short_url_link' => str_replace(['http://', 'https://'], '', url($shortCode)),
            'status' => $status,
        ]);

        return response()->json(
            $shortUrl->only([
                'url',
                'short_url_link',
                'status',
            ])
        );
    }

    public function deleteShortURL($id)
    {
        $shortUrl = ShortUrl::find($id);
        if (!$shortUrl) {
            return response()->json(['error' => 'URL không tìm thấy'], 404);
        }
        if (!Auth::check() || $shortUrl->user_id !== Auth::user()->id) {
            return response()->json(['error' => 'Không có quyền chỉnh sửa'], 403);
        }
        $shortUrl->delete();
        return response()->json(['message' => 'URL đã được xóa thành công']);
    }
}
