<?php

namespace App\Http\Controllers\V1\Admin\Notification;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Notification\AdminNotificationCollection;
use App\Models\AdminNotification;
use App\ResponseTrait;
use Illuminate\Http\Request;

class AdminOrderNotificationController extends Controller
{
    //
    use ResponseTrait;
    function index(Request $request)
    {
        $per_page = $request->per_page ?? 10;
        $notifications = AdminNotification::orderBy('created_at', 'desc')->paginate($per_page);
        $notifications=new AdminNotificationCollection($notifications);
        return $this->apiSuccess('Notifications fetched successfully', $notifications);
    }
    function update(AdminNotification $notification)
    {
        $notification->is_read = true;
        $notification->save();
        return $this->apiSuccess('Notification marked as read successfully', $notification);
    }
}
