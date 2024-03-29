<?php

namespace App\Models;

use App\Scopes\DeletedScope;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Folder extends Model
{
    use HasFactory;
    use Uuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id', 'folder_id', 'name', 'deleted'
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

    public function folders(): HasMany
    {
        return $this->hasMany(Folder::class)->orderBy('name');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class)->orderBy('name');
    }

    public function anonymize(): void
    {
        $this->update([
            'name' => 'DELETED',
            'deleted' => 1
        ]);
    }
}
