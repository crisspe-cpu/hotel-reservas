<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Reserva extends Model
{
    use HasFactory;

    protected $table      = 'reservas';
    protected $primaryKey = 'id_reserva';

    protected $fillable = [
        'id_cliente',
        'id',
        'fecha_entrada',
        'fecha_salida',
        'num_huespedes',
        'precio_total',
        'estado',
        'fecha_registro',
    ];

    protected $casts = [
        'fecha_entrada'  => 'date',
        'fecha_salida'   => 'date',
        'fecha_registro' => 'datetime',
        'precio_total'   => 'decimal:2',
        'num_huespedes'  => 'integer',
    ];

    // ── Relaciones ──────────────────────────────────────────
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id', 'id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleReserva::class, 'id_reserva', 'id_reserva');
    }

    public function habitaciones()
    {
        return $this->belongsToMany(
            Habitacion::class,
            'detalle_reservas',
            'id_reserva',
            'id_habitacion'
        )->withPivot('precio_aplicado', 'estado');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_reserva', 'id_reserva');
    }

    public function boletas()
    {
        return $this->hasMany(Boleta::class, 'id_reserva', 'id_reserva');
    }

    // ── Scopes ──────────────────────────────────────────────
    public function scopePendiente($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeConfirmada($query)
    {
        return $query->where('estado', 'confirmada');
    }

    public function scopeActivas($query)
    {
        return $query->whereIn('estado', ['pendiente', 'confirmada']);
    }

    // ── Accesores / Helpers ─────────────────────────────────
    public function getNochesAttribute(): int
    {
        return $this->fecha_entrada->diffInDays($this->fecha_salida);
    }

   public function getTotalPagadoAttribute(): float
    {
        // Suma directa de los montos asociados a la reserva
        return $this->pagos->sum('monto');
    }

    public function getSaldoPendienteAttribute(): float
    {
        return $this->precio_total - $this->total_pagado;
    }
}