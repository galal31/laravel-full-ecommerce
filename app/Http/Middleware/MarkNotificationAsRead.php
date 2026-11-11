<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MarkNotificationAsRead
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // الادمن مسجل الدخول و فيه notification_id في الرابط
        if (auth('admin')->check() && $request->has('notification_id')) {
            $notificationId = $request->query('notification_id');
            $notification = auth('admin')->user()->notifications()->find($notificationId);
            if ($notification) {
                $notification->markAsRead();
            }
        }

        return $next($request);
    }
}
