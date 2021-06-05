<?php


namespace App\Http\Controllers;


use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class FrontendController extends Controller
{

    public function index(Request $request): Factory|View|Application {

        return view('index');
    }

    public function user(Request $request): Factory|View|Application {
        
        return view('user');
    }

    public function album(Request $request): Factory|View|Application {
        return view('album');
    }

}
