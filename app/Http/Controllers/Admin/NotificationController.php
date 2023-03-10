<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Acess\Authorize;
use App\Http\Controllers\Controller;
use Inertia\Response;

class NotificationController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        Authorize::any('notification_index');

        return inertia('Admin/Notification/Index');
    }
}
