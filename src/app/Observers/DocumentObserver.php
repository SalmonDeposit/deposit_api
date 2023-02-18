<?php

namespace App\Observers;

use App\Models\Document;
use Illuminate\Support\Facades\Auth;

class DocumentObserver
{
    public function creating(Document $document)
    {
        $document->user_id = Auth::id();
    }
}
