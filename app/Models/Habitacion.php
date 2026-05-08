<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Habitacion extends Model
{
    use HasFactory;

    protected $table      = 'habitaciones';
    protected $primaryKey = 'id_habitacion';

    protected $fillable = [
        'numero',
        'piso',
        'estado',
        'id_tipo_habitacion',
    ];

    // ── Relaciones ──────────────────────────────────────────
    public function tipo()
    {
        return $this->belongsTo(TipoHabitacion::class, 'id_tipo_habitacion', 'id_tipo');
    }

    public function detallesReserva()
    {
        return $this->hasMany(DetalleReserva::class, 'id_habitacion', 'id_habitacion');
    }

    public function reservas()
    {
        return $this->belongsToMany(
            Reserva::class,
            'detalle_reservas',
            'id_habitacion',
            'id_reserva'
        )->withPivot('precio_aplicado', 'estado');
    }

    // ── Scopes ──────────────────────────────────────────────
    public function scopeDisponible($query)
    {
        return $query->where('estado', 'disponible');
    }

    public function scopePorPiso($query, int $piso)
    {
        return $query->where('piso', $piso);
    }

    // ── Helpers ─────────────────────────────────────────────
    public function estaDisponible(): bool
    {
        return $this->estado === 'disponible';
    }
}