<?php

namespace App\Exports;

use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ShortUrlsExport implements FromCollection, WithHeadings
{
    use Exportable;

    protected $shortUrls;

    public function __construct($shortUrls)
    {
        $this->shortUrls = $shortUrls;
    }

    public function collection()
    {
        return $this->shortUrls->map(function ($shortUrl) {
            return [
                'id' => $shortUrl->id,
                'url' => $shortUrl->url,
                'short_code' => $shortUrl->short_code,
                'clicks' => $shortUrl->clicks,
                'created_at' => Carbon::parse($shortUrl->created_at)->format('m/d/Y H:i'),
                'expired_at' => Carbon::parse($shortUrl->expired_at)->format('m/d/Y H:i'),
                'status' => $shortUrl->status,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Link Short',
            'Short code',
            'Clicks',
            'Created_at',
            'Expired At',
            'Trạng thái',
        ];
    }
}
