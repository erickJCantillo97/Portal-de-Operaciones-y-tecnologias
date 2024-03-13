<?php

use App\Models\NotificationUser;
use Barryvdh\Debugbar\Facades\Debugbar;
use Carbon\Carbon;

function addNotification($title = 'COTECMAR', $type = 'info', $message = null)
{
    try {
        NotificationUser::create([
            'user_id' => auth()->user()->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'date' => Carbon::now()
        ]);
    } catch (Exception $e) {
        Debugbar::addThrowable($e);
    }
}
