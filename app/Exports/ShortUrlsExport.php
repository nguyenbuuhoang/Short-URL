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
        $shortUrls = $this->shortUrls;
        return $shortUrls->map(function($shortUrls) {
            return [
                $shortUrls->id,
                $shortUrls->url,
                $shortUrls->short_code,
                $shortUrls->clicks,
                Carbon::parse($shortUrls->created_at)->format('m/d/Y H:i'),
                Carbon::parse($shortUrls->expired_at)->format('m/d/Y H:i'),
                $shortUrls->status,

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
            'Trạng thái'
        ];
    }
}
