<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;
use Spatie\Permission\Models\Role;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Broadcast::channel('App.Models.User.{id}', function ($user, $id, $contract) {
//     return (int) $user->id === (int) $id;
// });

// Broadcast::channel('testing', function () {
//     return true;
// });

Broadcast::channel('contracts.{contractId}', function (User $user, int $contractId) {
    $role = Role::first()->name;
    return $user->hasRole($role);
});
