<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cliente extends Model
{
    use HasFactory;

    protected $table      = 'clientes';
    protected $primaryKey = 'id_cliente';

    protected $fillable = [
        'nombre',
        'apellido',
        'tipo_documento',
        'documento',
        'pais',
        'telefono',
    ];

    // ── Relaciones ──────────────────────────────────────────
    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'id_cliente', 'id_cliente');
    }

    // ── Accesores ───────────────────────────────────────────
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellido}";
    }
}