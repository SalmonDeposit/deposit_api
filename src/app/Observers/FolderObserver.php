<?php

namespace App\Observers;

use App\Models\Folder;
use Illuminate\Support\Facades\Auth;

class FolderObserver
{
    public function creating(Folder $folder)
    {
        $folder->user_id = Auth::id();
    }
}
