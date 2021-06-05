<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AlbumController extends Controller
{
    public function index(Request $request): ?array {
        if ($request->has('user_id')) $userId = $request->get('user_id');
        else {
            $user = UserController::auth();
            if (!$user) {
                abort(403);
                return null;
            }
            $userId = $user->id;
        }

        $data = DB::table('albums')->where('user_id', '=', $userId)->get();
        return $this->returnData($data);
    }

    public function show(Request $request): array {
        $validator = Validator::make($request->all(), [
            'id' => 'bail|required|integer|min:1'
        ]);
        if ($validator->fails()) return $this->returnError('');

        $data = DB::table('albums')->where('id', '=', $request->get('id'))->first();

        return $this->returnData($data);
    }

    public function add(Request $request): array {
        $validator = Validator::make($request->all(), [
            'name' => 'bail|string|filled'
        ]);
        if ($validator->fails()) return $this->returnError('');

        $user = UserController::auth();

        $arr = [
            'name' => $request->get('name'),
            'user_id' => $user->id
        ];

        $arr['avatar'] = '';
        if ($request->has('image')) {
            $arr['avatar'] = ImageController::loadImage($request->file('image'), ImageController::ALBUM_PHOTOS);
        }

        $arr['time'] = time();

        $albumId = DB::table('albums')->insertGetId($arr);

        return $this->returnData([
            'id' => $albumId,
            'time' => $arr['time'],
            'name' => $arr['name'],
            'avatar' => $arr['avatar']
        ]);
    }


}
