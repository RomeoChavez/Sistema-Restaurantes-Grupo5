<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mesa extends Model
{
    use HasFactory;

    public const ESTADO_LIBRE = 'libre';
    public const ESTADO_RESERVADO = 'reservado';
    public const ESTADO_OCUPADO = 'ocupado';

    public const ESTADOS = [
        self::ESTADO_LIBRE => 'Libre',
        self::ESTADO_RESERVADO => 'Reservado',
        self::ESTADO_OCUPADO => 'Ocupado',
    ];

    protected $fillable = ['numero', 'capacidad', 'disponible', 'estado'];

    protected $casts = [
        'disponible' => 'boolean',
    ];

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }

    public function scopeLibres(Builder $consulta): Builder
    {
        return $consulta->where('estado', self::ESTADO_LIBRE);
    }

    public function scopeReservadas(Builder $consulta): Builder
    {
        return $consulta->where('estado', self::ESTADO_RESERVADO);
    }

    public function scopeOcupadas(Builder $consulta): Builder
    {
        return $consulta->where('estado', self::ESTADO_OCUPADO);
    }

    public function esLibre(): bool
    {
        return $this->estado === self::ESTADO_LIBRE;
    }

    public function marcarReservada(): void
    {
        $this->update(['estado' => self::ESTADO_RESERVADO, 'disponible' => false]);
    }

    public function marcarOcupada(): void
    {
        $this->update(['estado' => self::ESTADO_OCUPADO, 'disponible' => false]);
    }

    public function liberar(): void
    {
        $this->update(['estado' => self::ESTADO_LIBRE, 'disponible' => true]);
    }
}