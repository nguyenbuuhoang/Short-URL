<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Exports\ShortUrlsExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\CreateShortURL;
use App\Services\DataProcessorService;
use App\Services\User\ShortUrlService;

class ShortUrlController extends Controller
{
    protected $shortUrlService;
    protected $dataProcessorService;

    public function __construct(ShortUrlService $shortUrlService, DataProcessorService $dataProcessorService)
    {
        $this->shortUrlService = $shortUrlService;
        $this->dataProcessorService = $dataProcessorService;
    }

    public function createShortURL(CreateShortURL $request)
    {
        $url = $request->input('url');
        $shortUrl = $this->shortUrlService->createShort($url);

        return response()->json([
            'user_id' => $shortUrl->user_id,
            'url' => $shortUrl->url,
            'short_url_link' => $shortUrl->short_url_link,
            'created_at' => $shortUrl->created_at,
            'expired_at' => $shortUrl->expired_at,
            'qrcode' => $shortUrl->qrcode,
        ]);
    }

    public function redirectToURL($shortCode)
    {
        $shortUrl = $this->shortUrlService->getByShortCode($shortCode);

        if (!$shortUrl) {
            return response()->json(['error' => 'Short URL không tìm thấy'], 404);
        }
        if ($shortUrl->expired_at && now() > $shortUrl->expired_at) {
            return view('errors.expired_code');
        }

        $shortUrl->increment('clicks');
        return redirect($shortUrl->url);
    }
    public function getShortURLsByUserId(Request $request, $userId)
    {
        $perPage = $request->input('perPage', 4);
        $url = $request->input('url');
        $sort_by = $request->input('sort_by', 'id');
        $sort_order = $request->input('sort_order', 'asc');
        $export = $request->input('export');

        $query = $this->shortUrlService->getByShortUserId($userId);
        if ($url) {
            $this->dataProcessorService->filterByUrl($query, $url);
        }
        $query = $this->dataProcessorService->sort($query, $sort_by, $sort_order);
        if ($export === 'csv') {
            return Excel::download(new ShortUrlsExport($query->get()), 'data_short_urls.csv');
        }
        $shortUrls = $this->dataProcessorService->paginate($query, $perPage);
        return response()->json([
            'shortUrls' => $shortUrls,
        ], 200);
    }
    public function getTotalsByUserId($userId)
    {
        $totals = $this->shortUrlService->getTotalsByUserId($userId);
        return response()->json($totals, 200);
    }
    public function updateShortCode(Request $request, $id)
    {
        $shortUrl = $this->shortUrlService->findShortUrl($id);

        if (!$this->shortUrlService->hasPermission($shortUrl)) {
            return response()->json(['error' => 'Không có quyền chỉnh sửa'], 403);
        }

        $shortCode = $request->input('short_code');
        $status = $request->input('status');

        $result = $this->shortUrlService->updateShortUrl($shortUrl, $shortCode, $status);

        return response()->json($result);
    }

    public function deleteShortURL($id)
    {
        $shortUrl = $this->shortUrlService->findShortUrl($id);
        if (!$shortUrl) {
            return response()->json(['error' => 'URL không tìm thấy'], 404);
        }
        if (!$this->shortUrlService->hasPermission($shortUrl)) {
            return response()->json(['error' => 'Không có quyền chỉnh sửa'], 403);
        }
        $shortUrl->delete();
        return response()->json(['message' => 'URL đã được xóa thành công']);
    }
}
