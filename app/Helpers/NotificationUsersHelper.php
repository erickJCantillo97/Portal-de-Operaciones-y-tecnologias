<?php

use App\Models\NotificationUser;
use Barryvdh\Debugbar\Facades\Debugbar;
use Carbon\Carbon;

function addNotification($title = 'COTECMAR', $type = 'info', $message = null, $model = 'notification')
{
    try {
        NotificationUser::create([
            'user_id' => auth()->user()->id,
            'model' => $model,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'date' => Carbon::now()
        ]);
    } catch (Exception $e) {
        Debugbar::addThrowable($e);
    }
}
