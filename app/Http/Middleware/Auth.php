<?php


namespace App\Http\Middleware;


use App\Http\Controllers\UserController;

class Auth
{

    public function handle($request, \Closure $next, $role = '') {

        $user = UserController::auth();
        $success = true;
        if ($role === "guest" && $user) $success = false;
        else if (($role === "auth" || empty($role)) && !$user) $success = false;

        if (!$success) {
            abort(403);
            return false;
        }

        return $next($request);
    }

}
