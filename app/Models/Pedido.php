<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    use HasFactory;

    public const ESTADO_PENDIENTE = 'pendiente';
    public const ESTADO_PREPARANDO = 'preparando';
    public const ESTADO_SERVIDO = 'servido';
    public const ESTADO_PAGADO = 'pagado';
    public const ESTADO_CANCELADO = 'cancelado';

    public const ESTADOS = [
        self::ESTADO_PENDIENTE,
        self::ESTADO_PREPARANDO,
        self::ESTADO_SERVIDO,
        self::ESTADO_PAGADO,
        self::ESTADO_CANCELADO,
    ];

    protected $fillable = ['cliente_id', 'mesa_id', 'mesero_id', 'estado', 'fecha_hora'];

    protected $casts = [
        'fecha_hora' => 'datetime',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function mesa(): BelongsTo
    {
        return $this->belongsTo(Mesa::class);
    }

    public function mesero(): BelongsTo
    {
        return $this->belongsTo(Mesero::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetallePedido::class);
    }

    public function platos(): BelongsToMany
    {
        return $this->belongsToMany(Plato::class, 'detalle_pedidos', 'pedido_id', 'plato_id')
            ->withPivot('cantidad', 'precio_unitario')
            ->withTimestamps();
    }

    public function total(): float
    {
        return $this->detalles->sum(fn (DetallePedido $detalle) => $detalle->subtotal());
    }

    public function estaCerrado(): bool
    {
        return in_array($this->estado, [self::ESTADO_PAGADO, self::ESTADO_CANCELADO]);
    }
}