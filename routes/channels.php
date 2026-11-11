<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('App.Models.Dashboard.Admin.{id}',function ($adminUser, $id) {
    return (int) $adminUser->id === (int) $id;
},['guards' => ['admin']]);

Broadcast::channel('faqs_sent', function ($admin) {
    return $admin->can('contact');
}, ['guards' => ['admin']]);
