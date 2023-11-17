<?php

namespace App\Services;

class DataProcessorService
{
    public function joinUsersTable($query)
    {
        return $query->join('users', 'short_urls.user_id', '=', 'users.id');
    }
    public function filterByUserName($query, $name)
    {
        return $query->where('users.name', 'like', '%' . $name . '%');
    }
    public function filterByUrl($query, $url)
    {
        return $query->where('url', 'like', '%' . $url . '%');
    }
    public function filterByName($query, $name)
    {
        return  $query->where('name', 'like', '%' . $name . '%');
    }

    public function sort($query, $sort_by, $sort_order)
    {
        return $query->orderBy($sort_by, $sort_order);
    }
    public function paginate($query, $perPage)
    {
        return $query->paginate($perPage);
    }
}
