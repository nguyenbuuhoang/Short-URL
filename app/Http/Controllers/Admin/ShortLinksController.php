<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\ShortUrl;
use Illuminate\Http\Request;
use App\Exports\ShortLinksExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ShortLinksController extends Controller
{
    public function getTotal()
    {
        $totals = [
            'total_users' => User::count(),
            'total_short_url' => ShortUrl::count(),
            'total_clicks' => ShortUrl::sum('clicks'),
        ];

        return response()->json($totals);
    }

    public function getShortURL(Request $request)
    {
        $perPage = $request->input('per_page', 4);
        $sort_by = $request->input('sort_by', 'id');
        $sort_order = $request->input('sort_order', 'asc');
        $name = $request->input('name');
        $url = $request->input('url');
        $query = ShortUrl::with('user:id,name')
            ->select('short_urls.id', 'url', 'short_url_link', 'short_code', 'clicks', 'status', 'expired_at', 'short_urls.created_at', 'user_id');

        if ($name) {
            $query->whereHas('user', function ($query) use ($name) {
                $query->where('name', 'like', '%' . $name . '%');
            });
        }
        if ($url) {
            $query->where('url', 'like', '%' . $url . '%');
        }

        if ($sort_by === 'name') {
            $query->join('users', 'short_urls.user_id', '=', 'users.id')
                ->orderBy('users.name', $sort_order);
        } else {
            $query->orderBy($sort_by, $sort_order);
        }
        if ($request->has('export') && $request->input('export') === 'csv') {
            $shortLinks = $query->get();
            return Excel::download(new ShortLinksExport($shortLinks), 'data_shorts.csv');
        }
        $shortUrls = $query->paginate($perPage);

        return response()->json($shortUrls);
    }
    public function getQRCode($id)
    {
        $shortUrl = ShortUrl::findOrFail($id);
        $qrcode = $shortUrl->qrcode;
        return response()->json(['qrcode' => $qrcode]);
    }
    public function updateShortURL(Request $request, $id)
    {
        $shortUrl = ShortUrl::findOrFail($id);
        $shortCode = $request->input('short_code');
        $status = $request->input('status');
        $shortUrl->update([
            'short_code' => $shortCode,
            'short_url_link' => str_replace(['http://', 'https://'], '', url($shortCode)),
            'status' => $status,
        ]);

        return response()->json(['message' => 'Short URL đã được cập nhật thành công.']);
    }

    public function deleteShortURL($id)
    {
        $shortUrl = ShortUrl::findOrFail($id);
        $shortUrl->delete();
        return response()->json(['message' => 'Đã xóa short Url thành công'], 200);
    }
}
