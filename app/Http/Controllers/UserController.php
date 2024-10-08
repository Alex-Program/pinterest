<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    static private $USER = null;

    static public function auth(): object|null {
        if (self::$USER != null) return self::$USER;

        $headers = getallheaders();
        $token = null;
        $id = null;
        if (!empty($headers['Token'])) $token = $headers['Token'];
        else if (!empty($_COOKIE['Token'])) $token = $_COOKIE['Token'];
        if (!empty($headers['Id'])) $id = $headers['Id'];
        else if (!empty($_COOKIE['Id'])) $id = $_COOKIE['Id'];


        if (empty($id) || empty($token)) return null;

        self::$USER = DB::table('users')->where([
            ['id', '=', $id],
            ['remember_token', '=', $token]
        ])->first();

        return self::$USER;
    }

    public function registration(Request $request): array {
        $validator = Validator::make($request->all(),
            [
                'name' => 'bail|required|string|min:1|max:128',
                'email' => 'bail|required|email|max:128',
                'password' => 'bail|required|string|min:1|max:128'
            ]
        );
        if ($validator->fails()) return $this->returnError('invalid');

        $email = mb_strtolower($request->get('email'), 'UTF-8');
        $data = DB::table('users')->where('email', '=', $email)->first();
        if ($data) return $this->returnError('exists');

        $token = Str::random(100);
        $password = Hash::make($request->get('password'));
        $userId = DB::table('users')->insertGetId([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => $password,
            'remember_token' => $token
        ]);

        return $this->returnData(['token' => $token, 'id' => $userId]);
    }

    public function login(Request $request): array {
        $validator = Validator::make($request->all(), [
            'email' => 'bail|required|email|min:1',
            'password' => 'bail|required|string|min:1'
        ]);
        if ($validator->fails()) return $this->returnError('');

        $email = mb_strtolower($request->get('email'), 'UTF-8');
        $data = DB::table('users')->where('email', '=', $email)->first();
        if (!$data) return $this->returnError('invalid');
        if (!Hash::check($request->get('password'), $data->password)) return $this->returnError('invalid');

        $token = Str::random(100);
        DB::table('users')->where('id', '=', $data->id)->update([
            'remember_token' => $token
        ]);

        return $this->returnData(['token' => $token, 'id' => $data->id]);
    }

    public function getInfo(Request $request): array {
        if ($request->has('id')) {
            $info = DB::table('users')->where('id', '=', $request->get('id'))->first();
        } else $info = self::auth();

        if (!$info) return $this->returnError('invalid');

        $arr = [
            'name' => $info->name,
            'email' => $info->email,
            'avatar' => $info->avatar
        ];

        if ($request->get('is_follow', 0) == 1) {
            $user = UserController::auth();
            if (!$user) $arr['is_follow'] = 0;
            else if ($user->id === $info->id) $arr['is_follow'] = 1;
            else {
                $data = DB::table('followers')->where([
                    ['user_id', '=', $info->id],
                    ['follower_id', '=', $user->id]
                ])->first();
                if ($data) $arr['is_follow'] = 1;
                else $arr['is_follow'] = 0;
            }
        }


        return $this->returnData($arr);
    }

    public function update(Request $request): array {
        $validator = Validator::make($request->all(), [
            'name' => 'bail|string|min:1|max:128',
            'password' => 'bail|string|min:1|max:128',
            'image' => 'bail|image'
        ]);
        if ($validator->fails()) return $this->returnError('');

        $user = self::auth();

        $arr = [];

        if ($request->has('image')) {
            $arr['avatar'] = ImageController::loadImage($request->file('image'), ImageController::USER_PHOTOS);
        }
        if ($request->has('name')) $arr['name'] = $request->get('name');
        if ($request->has('password')) $arr['password'] = Hash::make($request->get('password'));

        DB::table('users')->where('id', '=', $user->id)->update($arr);

        return $this->returnData('');
    }

    public function checkAuth(Request $request): array {
        if (self::auth()) return $this->returnData('');
        else return $this->returnError('');
    }

}
