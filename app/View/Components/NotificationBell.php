<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Services\NotificationService;

class NotificationBell extends Component
{
    public $unreadCount;
    public $notifications;
    public $user;

    public function __construct($user = null)
    {
        $this->user = $user ?? auth()->user();
        
        if ($this->user) {
            $this->notifications = NotificationService::getUnreadNotifications($this->user);
            $this->unreadCount = $this->notifications->count();
        } else {
            $this->notifications = collect([]);
            $this->unreadCount = 0;
        }
    }

    public function render()
    {
        return view('components.notification-bell');
    }
}
