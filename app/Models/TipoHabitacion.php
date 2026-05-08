<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TipoHabitacion extends Model
{
    use HasFactory;

    protected $table      = 'tipo_habitaciones';
    protected $primaryKey = 'id_tipo';
    public    $timestamps = false; // esta tabla no tiene created_at / updated_at

    protected $fillable = [
        'nombre',
        'capacidad',
        'precio_base',
        'descripcion',
    ];

    protected $casts = [
        'precio_base' => 'decimal:2',
        'capacidad'   => 'integer',
    ];

    // ── Relaciones ──────────────────────────────────────────
    public function habitaciones()
    {
        return $this->hasMany(Habitacion::class, 'id_tipo_habitacion', 'id_tipo');
    }
}