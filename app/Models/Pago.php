<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pago extends Model
{
    use HasFactory;

    protected $table      = 'pagos';
    protected $primaryKey = 'id_pago';
    public    $timestamps = false;

    protected $fillable = [
        'id_reserva',
        'monto',
        'fecha_pago',
        'metodo_pago',
    ];

    protected $casts = [
        'monto'      => 'decimal:2',
        'fecha_pago' => 'datetime',
    ];

    // ── Relaciones ──────────────────────────────────────────
    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'id_reserva', 'id_reserva');
    }
}