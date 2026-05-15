<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Boleta extends Model
{
    use HasFactory;

    protected $table      = 'boletas';
    protected $primaryKey = 'id_boleta';
    public    $timestamps = false;

    protected $fillable = [
        'id_reserva',
        'id',
        'fecha_emision',
        'total',
    ];

    protected $casts = [
        'fecha_emision' => 'datetime',
        'total'         => 'decimal:2',
    ];

    // ── Relaciones ──────────────────────────────────────────
    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'id_reserva', 'id_reserva');
    }

    public function usuario()   
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }
}