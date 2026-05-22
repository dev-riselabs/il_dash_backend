<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

// Default per-user notification channel (Laravel notifications).
Broadcast::channel('App.Models.User.{id}', function (User $user, int $id) {
    return $user->id === $id;
});

// Public read-only channels: programme, resolutions, sentiment, session-scoped feeds.
// These are declared as plain Channel() in the events so no auth callback is required.

// Private channels - require authenticated user with appropriate permission.
Broadcast::channel('alerts', function (User $user) {
    return $user->hasAnyPermission(['command.view', 'incidents.view', 'reports.view']);
});

Broadcast::channel('incidents', function (User $user) {
    return $user->hasAnyPermission(['incidents.view', 'command.view']);
});

Broadcast::channel('deals', function (User $user) {
    return $user->hasPermissionTo('deals.view');
});
