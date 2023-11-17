<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\ShortUrl;
use Illuminate\Http\Request;
use App\Exports\ShortLinksExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\DataProcessorService;
use App\Services\User\ShortUrlService;

class ShortLinksController extends Controller
{
    protected $shortUrlService;
    protected $dataProcessorService;

    public function __construct(ShortUrlService $shortUrlService, DataProcessorService $dataProcessorService)
    {
        $this->shortUrlService = $shortUrlService;
        $this->dataProcessorService = $dataProcessorService;
    }
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
        $export = $request->input('export');
        $query = ShortUrl::with('user:id,name')
            ->select('short_urls.id', 'url', 'short_url_link', 'short_code', 'clicks', 'status', 'expired_at', 'short_urls.created_at', 'user_id');

        if ($url) {
            $query = $this->dataProcessorService->filterByUrl($query, $url);
        }

        if ($name) {
            $query = $this->dataProcessorService->joinUsersTable($query)
                ->filterByUserName($query, $name);
        }

        if ($sort_by === 'name') {
            $query = $this->dataProcessorService->joinUsersTable($query);
        } else {
            $query = $this->dataProcessorService->sort($query, $sort_by, $sort_order);
        }

        if ($export === 'csv') {
            return Excel::download(new ShortLinksExport($query->get()), 'data_shorts.csv');
        }

        $shortUrls = $this->dataProcessorService->paginate($query, $perPage);

        return response()->json($shortUrls);
    }

    public function getQRCode($id)
    {
        $shortUrl = $this->shortUrlService->findShortUrl($id);
        $qrcode = $shortUrl->qrcode;
        return response()->json(['qrcode' => $qrcode]);
    }
    public function updateShortURL(Request $request, $id)
    {
        $shortUrl = $this->shortUrlService->findShortUrl($id);
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
        $shortUrl = $this->shortUrlService->findShortUrl($id);
        $shortUrl->delete();
        return response()->json(['message' => 'Đã xóa short Url thành công'], 200);
    }
}
