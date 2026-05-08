<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetalleReserva extends Model
{
    use HasFactory;

    protected $table      = 'detalle_reservas';
    protected $primaryKey = 'id_detalle';
    public    $timestamps = false; // esta tabla no necesita timestamps

    protected $fillable = [
        'id_reserva',
        'id_habitacion',
        'precio_aplicado',
        'estado',
    ];

    protected $casts = [
        'precio_aplicado' => 'decimal:2',
    ];

    // ── Relaciones ──────────────────────────────────────────
    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'id_reserva', 'id_reserva');
    }

    public function habitacion()
    {
        return $this->belongsTo(Habitacion::class, 'id_habitacion', 'id_habitacion');
    }

    // ── Helpers ─────────────────────────────────────────────
    public function estaActiva(): bool
    {
        return $this->estado === 'activa';
    }
}