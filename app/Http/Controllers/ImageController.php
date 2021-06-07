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

    protected array $selectFields = ['id', 'src', 'time', 'name'];
    protected array $allFields = ['id', 'src', 'time', 'name', 'user_id', 'album_id', 'description', 'tags'];

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

        $this->prepareFields($request);

        $sql = "SELECT $this->preparedFieldsString FROM `images` WHERE 1";
        $bind = [];

        if ($request->has('tags')) {
            $tags = json_decode($request->get('tags'));
            foreach ($tags as $tag) {
                $tag = mb_strtolower(trim($tag), 'UTF-8');
                $sql .= ' AND `tags` LIKE ?';
                $bind[] = '%' . $tag . '%';
            }

        }

        foreach ($args as $arg) {
            $sql .= ' AND ' . $arg[0] . ' ' . $arg[1] . ' ?';
            $bind[] = $arg[2];
        }
        $this->prepareOrder($request);

        $sql .= ' ORDER BY ' . $this->orderBy . ' ' . $this->order . ' LIMIT ' . $this->limit;

        $data = DB::select($sql, $bind);
        return $this->returnData($data);
    }

    public function add(Request $request): array {
        $validator = Validator::make($request->all(), [
            'image' => 'bail|required|image',
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
            $sql = "SELECT `id`, `comment`, `user_id`, `name`, `avatar`
FROM `comments`
         JOIN (SELECT `name`, `avatar`, `id` as `u_id` FROM `users`) as `users`
              ON `comments`.`user_id` = `users`.`u_id`
WHERE `comments`.`image_id` = ?
 LIMIT " . $this->limit;
            $data->comments = DB::select($sql, [$data->id]);
        }
        if ($request->get('is_liked', '0') == '1') {
            $user = UserController::auth();
            if (!$user) $data->is_liked = 0;
            else {
                $info = DB::table('likes')->where([
                    ['user_id', '=', $user->id],
                    ['image_id', '=', $data->id]
                ])->first();
                $data->is_liked = $info ? 1 : 0;
            }
        }

        return $this->returnData($data);
    }

    public function save(Request $request): array {
        $validator = Validator::make($request->all(), [
            'id' => 'bail|required|integer|min:1',
            'name' => 'bail|string|min:1',
            'description' => 'bail|nullable|string|max:1024|min:0',
            'image' => 'bail|image',
            'tags' => 'bail|nullable|string|max:1024|min:0'
        ]);
        if ($validator->fails()) return $this->returnError('');


        $data = DB::table('images')->select(['user_id'])->where('id', '=', $request->get('id'))->first();
        if (!$data) return $this->returnError('invalid');

        $user = UserController::auth();
        if ($data->user_id != $user->id) return $this->returnError('invalid');

        $arr = [];
        if ($request->has('name')) $arr['name'] = $request->get('name');
        if ($request->has('description')) $arr['description'] = $request->get('description') ?? '';
        if ($request->has('tags')) $arr['tags'] = mb_strtolower($request->get('tags') ?? '', 'UTF-8');
        if ($request->has('image')) {
            $fileName = self::loadImage($request->file('image'), self::MAIN_PHOTOS);
            $arr['src'] = $fileName;
        }

        DB::table('images')->where('id', '=', $request->get('id'))->update($arr);

        $data = DB::table('images')->select($this->selectFields)->where('id', '=', $request->get('id'))->first();

        return $this->returnData(['src' => $data->src, 'id' => $data->id, 'time' => $data->time, 'name' => $data->name]);
    }

}
