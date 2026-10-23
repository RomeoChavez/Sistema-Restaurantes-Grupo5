<?php

namespace App\Models;

use DomainException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Pedido extends Model
{
    use HasFactory;

    public const ESTADO_PENDIENTE = 'pendiente';
    public const ESTADO_PREPARANDO = 'preparando';
    public const ESTADO_LISTO = 'listo';
    public const ESTADO_SERVIDO = 'servido';
    public const ESTADO_PAGADO = 'pagado';
    public const ESTADO_CANCELADO = 'cancelado';

    public const ESTADOS = [
        self::ESTADO_PENDIENTE,
        self::ESTADO_PREPARANDO,
        self::ESTADO_LISTO,
        self::ESTADO_SERVIDO,
        self::ESTADO_PAGADO,
        self::ESTADO_CANCELADO,
    ];

    public const ESTADOS_ACTIVOS = [
        self::ESTADO_PENDIENTE,
        self::ESTADO_PREPARANDO,
        self::ESTADO_LISTO,
        self::ESTADO_SERVIDO,
    ];

    public const ESTADOS_COCINA = [
        self::ESTADO_PENDIENTE,
        self::ESTADO_PREPARANDO,
    ];

    public const TRANSICIONES = [
        self::ESTADO_PENDIENTE => [self::ESTADO_PREPARANDO, self::ESTADO_CANCELADO],
        self::ESTADO_PREPARANDO => [self::ESTADO_LISTO, self::ESTADO_CANCELADO],
        self::ESTADO_LISTO => [self::ESTADO_SERVIDO],
        self::ESTADO_SERVIDO => [self::ESTADO_PAGADO],
        self::ESTADO_PAGADO => [],
        self::ESTADO_CANCELADO => [],
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

    public function scopeActivos(Builder $consulta): Builder
    {
        return $consulta->whereIn('estado', self::ESTADOS_ACTIVOS);
    }

    public function scopeDeCocina(Builder $consulta): Builder
    {
        return $consulta->whereIn('estado', self::ESTADOS_COCINA)->orderBy('fecha_hora');
    }

    public function scopeSinMesero(Builder $consulta): Builder
    {
        return $consulta->whereNull('mesero_id');
    }

    public function total(): float
    {
        return $this->detalles->sum(fn (DetallePedido $detalle) => $detalle->subtotal());
    }

    public function estaCerrado(): bool
    {
        return in_array($this->estado, [self::ESTADO_PAGADO, self::ESTADO_CANCELADO], true);
    }

    public function esPendiente(): bool
    {
        return $this->estado === self::ESTADO_PENDIENTE;
    }

    public function puedePasarA(string $estadoNuevo): bool
    {
        return in_array($estadoNuevo, self::TRANSICIONES[$this->estado] ?? [], true);
    }

    public function cambiarEstado(string $estadoNuevo): void
    {
        if (! $this->puedePasarA($estadoNuevo)) {
            throw new DomainException("No se puede pasar de {$this->estado} a {$estadoNuevo}");
        }

        DB::transaction(function () use ($estadoNuevo) {
            $this->update(['estado' => $estadoNuevo]);

            if ($this->estaCerrado()) {
                $this->mesa?->liberar();
            } elseif (in_array($estadoNuevo, [self::ESTADO_PREPARANDO, self::ESTADO_SERVIDO], true)) {
                $this->mesa?->marcarOcupada();
            }
        });
    }
}