<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{

    public function index(Request $request): array {
        $validator = Validator::make($request->all(), [
            'image_id' => 'bail|required|integer|min:1'
        ]);
        if ($validator->fails()) return $this->returnError('');

        $data = DB::table('images')->where('id', '=', $request->get('image_id'))->first();
        if (!$data) return $this->returnError('invalid');

        $args = $this->prepareDefaultArgs($request);
        $args[] = ['image_id', '=', $request->get('image_id')];

        $this->prepareOrder($request);

        $sql = "SELECT `id`, `comment`, `user_id`, `name`, `avatar`
FROM `comments`
         JOIN (SELECT `name`, `avatar`, `id` as `u_id` FROM `users`) as `users`
              ON `comments`.`user_id` = `users`.`u_id`
              ";
        $prepared = $this->prepareSql($args);
        $sql .= $prepared->sql;
        $sql .= ' ORDER BY ' . $this->orderBy . ' ' . $this->order . ' LIMIT ' . $this->limit;

        $data = DB::select($sql, $prepared->bind);
        return $this->returnData($data);
    }

    public function add(Request $request): array {
        $validator = Validator::make($request->all(), [
            'image_id' => 'bail|required|integer|min:1',
            'comment' => 'bail|filled|string|min:1',
        ]);
        if ($validator->fails()) return $this->returnError('');

        $data = DB::table('images')->where('id', '=', $request->get('image_id'))->first();
        if (!$data) return $this->returnError('invalid');


        $user = UserController::auth();

        $arr = [
            'image_id' => $request->get('image_id'),
            'user_id' => $user->id,
            'comment' => $request->get('comment'),
            'time' => time()
        ];
        $commentId = DB::table('comments')->insertGetId($arr);

        return $this->returnData([
            'id' => $commentId,
            'comment' => $arr['comment'],
            'time' => $arr['time'],
            'name' => $user->name,
            'avatar' => $user->avatar
        ]);
    }

}
