<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LikeController extends Controller
{

    public function index(Request $request): array {

        $sql = 'SELECT `id`, `user_id`, `image_id`';
        if ($request->get('image', '0') == '1') $sql .= ', `name`, `images`.`time`, `src`';
        $sql .= ' FROM `likes`';

        if ($request->get('image', '0') == '1') {
            $sql .= ' JOIN (SELECT `id` as `i_id`, `name`, `time`, `src` FROM `images`) as `images`
              ON `likes`.`image_id` = `images`.`i_id`';
        }
        $sql .= ' WHERE 1';

        $args = $this->prepareDefaultArgs($request);
        $prepared = $this->prepareSql($args);

        $sql .= $prepared->sql;

        $this->prepareOrder($request);
        $this->prepareLimit($request);
        $sql .= ' ORDER BY ' . $this->orderBy . ' ' . $this->order . ' LIMIT ' . $this->limit;

        $data = DB::select($sql, $prepared->bind);
        return $this->returnData($data);
    }

    public function like(Request $request): array {
        $validator = Validator::make($request->all(), [
            'image_id' => 'bail|required|integer|min:1'
        ]);
        if ($validator->fails()) return $this->returnError('');

        $data = DB::table('images')->where('id', '=', $request->get('image_id'))->first();
        if (!$data) return $this->returnError('invalid');

        $user = UserController::auth();
        $data = DB::table('likes')->select(['image_id', 'time'])->where([
            ['user_id', '=', $user->id],
            ['image_id', '=', $request->get('image_id')]
        ])->first();

        if ($data) $arr = (array)$data;
        else {
            $time = time();
            DB::table('likes')->insert([
                'user_id' => $user->id,
                'image_id' => $request->get('image_id'),
                'time' => $time
            ]);
            $arr = ['image_id' => $request->get('image_id'), 'time' => $time];
        }

        return $this->returnData($arr);
    }

    public function dislike(Request $request): array {
        $validator = Validator::make($request->all(), [
            'image_id' => 'bail|required|integer|min:1'
        ]);
        if ($validator->fails()) return $this->returnError('');

        $data = DB::table('images')->where('id', '=', $request->get('image_id'))->first();
        if (!$data) return $this->returnError('invalid');

        $user = UserController::auth();

        DB::table('likes')->where([
            ['user_id', '=', $user->id],
            ['image_id', '=', $request->get('image_id')]
        ])->delete();

        return $this->returnData('');
    }

}
