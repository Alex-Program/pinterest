<?php


namespace App\Http\Controllers\helpers;


class Pagination
{
    public int $offset;
    public int $limit;
    public int $page;

    public function __construct($page, $limit, $defaultLimit, $maxLimit) {
        if (empty($limit) || $limit > $maxLimit) $limit = $defaultLimit;
        if (empty($page)) $page = 1;
        $this->page = $page;
        $this->limit = $limit;
        $this->offset = ($page - 1) * $limit;
    }

}
