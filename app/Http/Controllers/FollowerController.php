<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FollowerController extends Controller
{
    protected array $selectFields = ['user_id', 'follower_id'];

    public function index(Request $request): array {
//        $validator = Validator::make($request->all(), [
//            'user_id' => 'bail|required|integer|min:1'
//        ]);
//        if ($validator->fails()) return $this->returnError('');
        if (!$request->has('user_id') && !$request->has('follower_id')) return $this->returnError('');

        $args = $this->prepareDefaultArgs($request);

        if ($request->has('user_id')) {
            $data = DB::table('users')
                ->select('id')
                ->where('id', '=', $request->get('user_id'))
                ->first();
            if (!$data) return $this->returnError('invalid');
            $args[] = ['user_id', '=', $data->id];
        }
        if ($request->has('follower_id')) {
            $data = DB::table('users')
                ->select('id')
                ->where('id', '=', $request->get('follower_id'))
                ->first();
            if (!$data) return $this->returnError('invalid');
            $args[] = ['follower_id', '=', $data->id];
        }

        if ($request->has('user_id') && $request->has('follower_id')) {
            $data = DB::table('followers')
                ->select($this->selectFields)
                ->where([
                    ['user_id', '=', $request->get('user_id')],
                    ['follower_id', '=', $request->get('follower_id')]
                ])->first();
            $arr = [];
            if ($data) $arr['is_follow'] = 1;
            else $arr['is_follow'] = 0;

            return $this->returnData($arr);
        }

        if ($request->get('count', 0) == 1) {
            $count = DB::table('followers')->where($args)->count();
            return $this->returnData(['count' => $count]);
        }

        $sql = 'SELECT `user_id`, `follower_id`, `name`, `avatar`
FROM `followers`
         ';
        if ($request->get('user', 0) == 1) {
            $onField = $request->has('user_id') ? 'follower_id' : 'user_id';
            $sql .= ' JOIN (SELECT `id` as `u_id`, `name`, `avatar` FROM `users`) as `users`
              ON `followers`.`' . $onField . '` = `users`.`u_id`';
        }
        $sql .= ' WHERE 1';
        $bind = [];
        foreach ($args as $arg) {
            $sql .= ' AND `' . $arg[0] . '` ' . $arg[1] . ' ?';
            $bind[] = $arg[2];
        }
        $sql .= ' LIMIT ' . $this->limit;
        $data = DB::select($sql, $bind);

        return $this->returnData($data);
    }

    public function follow(Request $request): array {
        $validator = Validator::make($request->all(), [
            'user_id' => 'bail|required|integer|min:1'
        ]);
        if ($validator->fails()) return $this->returnError('');

        $data = DB::table('users')->where('id', '=', $request->get('user_id'))->first();
        if (!$data) return $this->returnError('invalid');

        $user = UserController::auth();

        $arr = [];

        $data = DB::table('followers')->where([
            ['user_id', '=', $request->get('user_id')],
            ['follower_id', '=', $user->id]
        ])->first();
        if ($data) {
            $arr['time'] = $data->time;
            $arr['id'] = $data->id;
            $arr['user_id'] = $data->user_id;
            $arr['follower_id'] = $data->follower_id;
        } else {
            $arr['time'] = time();
            $arr['user_id'] = $request->get('user_id');
            $arr['follower_id'] = $user->id;
            $id = DB::table('followers')->insertGetId($arr);
            $arr['id'] = $id;
        }

        return $this->returnData($arr);
    }

    public function unfollow(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'bail|required|string|min:1'
        ]);
        if ($validator->fails()) return $this->returnError('');

        $data = DB::table('users')->where('id', '=', $request->get('user_id'))->first();
        if (!$data) return $this->returnError('invalid');

        $user = UserController::auth();

        DB::table('followers')->where([
            ['user_id', '=', $request->get('user_id')],
            ['follower_id', '=', $user->id]
        ])->delete();

        return $this->returnData('');
    }

}
