<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contabilidad extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contabilidad';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fecha',
        'motivo',
        'valor',
        'usuario_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha' => 'date',
        'valor' => 'decimal:2',
    ];

    /**
     * Get the user that owns the contabilidad record.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
