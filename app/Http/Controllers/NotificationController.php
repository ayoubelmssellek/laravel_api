<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
class NotificationController extends Controller
{
       public function index()
    {
        $notifications = Notification::latest()->get();
        return response()->json($notifications);
    }

}
