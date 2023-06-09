<?php

declare(strict_types=1);

namespace App\Models;

use App\Scopes\DeletedScope;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;
    use Uuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id', 'folder_id', 'name', 'type', 'storage_link', 'size', 'deleted'
    ];

    protected static function booted()
    {
        parent::boot();
        static::addGlobalScope(new DeletedScope);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    public function anonymize(): void
    {
        $this->update([
            'name' => 'DELETED',
            'type' => 'DELETED',
            'storage_link' => 'DELETED',
            'size' => '0',
            'deleted' => 1
        ]);
    }
}
