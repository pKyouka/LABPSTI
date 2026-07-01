<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait RequiresAdmin
{
    protected function ensureAdmin(): void
    {
        if (!Auth::user()?->isAdmin()) {
            abort(403);
        }
    }
}