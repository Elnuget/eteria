<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatWeb extends Model
{
    use HasFactory;

    protected $table = 'chat_webs';

    protected $fillable = [
        'chat_id',
        'contacto_web_id',
        'mensaje',
        'tipo',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the contactoWeb that owns the ChatWeb message.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contactoWeb(): BelongsTo
    {
        return $this->belongsTo(ContactoWeb::class, 'contacto_web_id');
    }
}
