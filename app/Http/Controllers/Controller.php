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
    protected string $orderBy = 'id';
    protected string $order = 'ASC';
    protected array $orders = ['ASC', 'DESC'];
    protected array $selectFields = [];
    protected array $allFields = [];
    protected array $preparedFields = [];
    protected string $preparedFieldsString = "";

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

    protected function prepareSql($args): object {
        $sql = "";
        $bind = [];
        foreach ($args as $arg) {
            $sql .= ' AND ' . $arg[0] . ' ' . $arg[1] . ' ?';
            $bind[] = $arg[2];
        }

        return (object)['bind' => $bind, 'sql' => $sql];
    }

    protected function prepareOrder(Request $request) {
        if ($request->has('order')) {
            $order = $request->get('order');
            if (!$order) return;
            $order = (string)mb_strtoupper($order, 'UTF-8');
            if (in_array($order, $this->orders)) $this->order = $order;
        }
    }

    protected function prepareLimit(Request $request) {
        if (!$request->has('limit') || $request->get('limit') == 0) return;

        if ($request->get('limit') > $this->maxLimit) $this->limit = $this->maxLimit;
        else $this->limit = $request->get('limit');
    }

    protected function prepareFields(Request $request) {
        $this->preparedFields = array_merge($this->selectFields);

        if ($request->has('fields')) {
            $fields = json_decode($request->get('fields'));
            foreach ($fields as $field) {
                if (!in_array($field, $this->allFields) || in_array($fields, $this->selectFields)) return;
                $this->preparedFields[] = $field;
            }
        }

        $this->preparedFieldsString = implode(",", $this->preparedFields);
    }

}
