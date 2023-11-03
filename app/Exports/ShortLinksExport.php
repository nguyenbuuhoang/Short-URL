<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;

class ShortLinksExport implements FromCollection
{
    protected $shortLinks;

    public function __construct(Collection $shortLinks)
    {
        $this->shortLinks = $shortLinks;
    }

    /**
     * Create a collection containing the data you want to export to the CSV file.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->shortLinks;
    }
}
