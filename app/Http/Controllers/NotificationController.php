<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        
        // Check if user owns this notification
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }
        
        $notification->update(['is_read' => true, 'read_at' => now()]);
        
        return response()->json(['success' => true]);
    }
    
    public function delete($id)
    {
        $notification = Notification::findOrFail($id);
        
        // Check if user owns this notification
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }
        
        $notification->delete();
        
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        
        return response()->json(['success' => true]);
    }

    public function getUnreadCount()
    {
        if (!auth()->check()) {
            return response()->json(['count' => 0]);
        }
        
        $count = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();
        
        return response()->json(['count' => $count]);
    }
    
    public function getRecentNotifications()
    {
        if (!auth()->check()) {
            return response()->json(['notifications' => []]);
        }
        
        $notifications = Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'type' => $notification->type ?? 'general'
                ];
            });
            
        return response()->json(['notifications' => $notifications]);
    }

    public function getAdminNotifications()
    {
        $notifications = NotificationService::getNotificationsForUser(null, 20);
        
        return response()->json($notifications);
    }

    public function getAdminUnreadCount()
    {
        $count = NotificationService::getUnreadCountForUser(null);
        
        return response()->json(['count' => $count]);
    }
}

