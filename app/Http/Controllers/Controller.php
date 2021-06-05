<?php

namespace App\Http\Controllers;

use App\Http\Controllers\helpers\Pagination;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected int $limit = 50;
    protected int $maxLimit = 100;

    protected function returnError(string $data): array {
        return ['result' => 0, 'data' => $data];
    }

    protected function returnData(mixed $data): array {
        return ['result' => 1, 'data' => $data];
    }

    protected function preparePagination(Request $request): Pagination {
        return new Pagination($request->get('page'), $request->get('limit'), $this->limit, $this->maxLimit);
    }

    protected function paginate(Builder $data, Request $request): Collection {
        $pagination = $this->preparePagination($request);

        return $data->skip($pagination->offset)->limit($pagination->limit)->get();
    }

    protected function prepareDefaultArgs(Request $request): array {
        $args = [];

        if ($request->has('from_id')) $args[] = ['id', '>=', $request->get('from_id')];
        if ($request->has('to_id')) $args[] = ['id', '<=', $request->get('to_id')];

        return $args;
    }

}
