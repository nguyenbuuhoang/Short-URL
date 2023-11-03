<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;

class UserListsExport implements FromCollection
{
    protected $users;

    public function __construct(Collection $users)
    {
        $this->users = $users;
    }

    public function collection()
    {
        $formattedData = $this->users->map(function ($user) {
            return [
                'ID' => $user->id,
                'Name' => $user->name,
                'Email' => $user->email,
                'Roles' => $user->roles->pluck('name')->implode(', '),
                'Total URLs' => $user->totalUrls->count(),
                'Created At' => $user->created_at,
                'Is Verified' => $user->is_verified,
            ];
        });

        return $formattedData;
    }
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Roles',
            'Total URLs',
            'Created At',
            'Trạng thái'
        ];
    }
}
