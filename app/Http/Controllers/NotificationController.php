<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;


class NotificationController extends Controller
{
    public function index()
    {
        return response()->json(Notification::where('read', false)->get());
    }

    public function markAsRead()
    {
        Notification::where('read', false)->update(['read' => true]);
        return response()->json(['message' => 'Notificaciones marcadas como leídas']);
    }
}
