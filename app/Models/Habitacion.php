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
        'motivo_mantenimiento',
        'mantenimiento_desde',
        'mantenimiento_hasta',
    ];

    protected $casts = [
        'mantenimiento_desde' => 'date',
        'mantenimiento_hasta' => 'date',
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

    public function scopeEnMantenimiento($query)
    {
        return $query->where('estado', 'mantenimiento');
    }

    // ── Helpers ─────────────────────────────────────────────
    public function estaDisponible(): bool
    {
        return $this->estado === 'disponible';
    }

    public function estaEnMantenimiento(): bool
    {
        return $this->estado === 'mantenimiento';
    }
}