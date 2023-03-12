<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Acess\Authorize;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * @return Response
     */
    public function index(Request $request): Response
    {
        return inertia('Admin/Home');
    }
}
