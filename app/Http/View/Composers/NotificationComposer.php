<?php

namespace App\Http\View\Composers;

use App\Services\NotificationService;
use Illuminate\View\View;

class NotificationComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        if (auth()->check()) {
            $notifications = NotificationService::getUnreadNotifications(auth()->user());
            $unreadCount = $notifications->count();
            
            $view->with([
                'notifications' => $notifications,
                'unreadCount' => $unreadCount,
            ]);
        } else {
            $view->with([
                'notifications' => collect([]),
                'unreadCount' => 0,
            ]);
        }
    }
}
