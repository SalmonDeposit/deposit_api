<?php

namespace App\Observers;

use App\Models\Profile;
use Illuminate\Support\Facades\Auth;

class ProfileObserver
{
    public function creating(Profile $profile)
    {
        $profile->user_id = Auth::id();
    }
}
