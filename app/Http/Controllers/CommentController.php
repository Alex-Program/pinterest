<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{

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
