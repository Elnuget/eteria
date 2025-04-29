<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContactoWeb extends Model
{
    use HasFactory;

    protected $table = 'contacto_webs';

    protected $fillable = [
        'nombre',
        'email',
    ];

    /**
     * Get all of the chatMessages for the ContactoWeb
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatWeb::class, 'contacto_web_id');
    }
}
