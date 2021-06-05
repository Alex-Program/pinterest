<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
{

    const USER_PHOTOS = 'public/images/users';
    const MAIN_PHOTOS = 'public/images/main';
    const ALBUM_PHOTOS = 'public/images/albums';

    static public function loadImage($file, $path): string {

        $name = Storage::put($path, $file);
        return basename($name);
    }

    public function index(Request $request): array {
        $user = UserController::auth();

        $args = $this->prepareDefaultArgs($request);

        if ($request->has('album_id')) {
            $args[] = ['album_id', '=', $request->get('album_id')];
//            $data = DB::table('albums')->find($request->get('album_id'));

        }
        if ($request->has('name')) $args[] = ['LOWER(name)', 'like', '%' . mb_strtolower($request->get('name', 'UTF-8')) . '%'];


        $sql = "SELECT * FROM `images` WHERE 1";
        $bind = [];
        foreach ($args as $arg) {
            $sql .= ' AND ' . $arg[0] . ' ' . $arg[1] . ' ?';
            $bind[] = $arg[2];
        }
        $sql .= ' LIMIT ' . $this->limit;

        $data = DB::select($sql, $bind);
        return $this->returnData($data);
    }

    public function add(Request $request): array {
        $validator = Validator::make($request->all(), [
            'image' => 'bail|required|file',
            'album_id' => 'bail|required|integer|min:1'
        ]);
        if ($validator->fails()) return $this->returnError('');

        $user = UserController::auth();
        $data = DB::table('albums')->where([
            ['id', '=', $request->get('album_id')],
            ['user_id', '=', $user->id]
        ])->first();
        if (!$data) return $this->returnError('invalid_album');


        $fileName = self::loadImage($request->file('image'), self::MAIN_PHOTOS);
        $name = $request->get('name', '') ?? '';
        $description = $request->get('description', '') ?? '';

        $time = time();
        $imageId = DB::table('images')->insertGetId([
            'src' => $fileName,
            'user_id' => $user->id,
            'album_id' => $request->get('album_id'),
            'description' => $description,
            'name' => $name,
            'time' => $time
        ]);

        return $this->returnData(['src' => $fileName, 'id' => $imageId, 'time' => $time, 'name' => $name]);
    }

    public function show(Request $request): array {
        $validator = Validator::make($request->all(), [
            'id' => 'bail|required|integer|min:1'
        ]);
        if ($validator->fails()) return $this->returnError('');

        $data = DB::table('images')->where('id', '=', $request->get('id'))->first();
        if (!$data) return $this->returnError('invalid');

        $data->user = DB::table('users')->select(['name', 'avatar'])->where('id', '=', $data->user_id)->first();

        if ($request->get('comments', '0') == '1') {
            $sql = "SELECT *
FROM `comments`
         JOIN (SELECT `name`, `avatar`, `id` as `u_id` FROM `users`) as `users`
              ON `comments`.`user_id` = `users`.`u_id`
WHERE `comments`.`image_id` = ?
";
            $data->comments = DB::select($sql, [$data->id]);
        }

        return $this->returnData($data);
    }

}
