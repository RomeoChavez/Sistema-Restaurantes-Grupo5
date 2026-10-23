<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Plato;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RestauranteWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_pagina_de_inicio_carga_correctamente_con_platos(): void
    {
        $categoria = Categoria::factory()->create(['nombre' => 'Entradas']);
        Plato::factory()->for($categoria)->create([
            'nombre' => 'Salteña',
            'precio' => 8.00,
            'disponible' => true,
        ]);
        Mesa::factory()->count(5)->create(['disponible' => true, 'estado' => 'libre']);

        $respuesta = $this->get('/');

        $respuesta->assertStatus(200);
        $respuesta->assertSee('La Casona Chapaca');
        $respuesta->assertSee('Salteña');
        $respuesta->assertSee('Entradas');
        $respuesta->assertSee('5 de 5 Mesas Libres');
    }

    public function test_login_y_registro_son_accesibles_para_invitados(): void
    {
        $this->get('/login')->assertStatus(200)->assertSee('Iniciar Sesión');
        $this->get('/registro')->assertStatus(200)->assertSee('Crear Cuenta');
    }

    public function test_invitado_no_puede_acceder_a_admin_ni_cocina(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->get('/cocina')->assertRedirect('/login');
    }

    public function test_administrador_puede_acceder_a_panel_admin_y_cocina(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin')->assertStatus(200)->assertSee('Administración General');
        $this->actingAs($admin)->get('/cocina')->assertStatus(200)->assertSee('Comandas de Cocina');
    }

    public function test_mesero_puede_acceder_a_cocina_pero_no_a_admin(): void
    {
        $mesero = User::factory()->mesero()->create();

        $this->actingAs($mesero)->get('/cocina')->assertStatus(200)->assertSee('Comandas de Cocina');
        $this->actingAs($mesero)->get('/admin')->assertStatus(403);
    }

    public function test_cliente_puede_reservar_mesa_y_crear_pedido(): void
    {
        $usuario = User::factory()->create(['rol' => User::ROL_USUARIO]);
        $mesa = Mesa::factory()->create(['estado' => Mesa::ESTADO_LIBRE, 'disponible' => true]);
        $categoria = Categoria::factory()->create(['nombre' => 'Platos Fuertes']);
        $plato = Plato::factory()->for($categoria)->create(['nombre' => 'Silpancho', 'precio' => 40.00, 'disponible' => true]);

        $respuesta = $this->actingAs($usuario)->post('/reservar', [
            'mesa_id' => $mesa->id,
            'platos' => [
                $plato->id => [
                    'id' => $plato->id,
                    'cantidad' => 2,
                ],
            ],
        ]);

        $respuesta->assertRedirect('/mis-pedidos');
        $this->assertDatabaseHas('mesas', [
            'id' => $mesa->id,
            'estado' => Mesa::ESTADO_RESERVADO,
            'disponible' => false,
        ]);
        $this->assertDatabaseHas('pedidos', [
            'mesa_id' => $mesa->id,
            'estado' => Pedido::ESTADO_PENDIENTE,
        ]);
        $this->assertDatabaseHas('detalle_pedidos', [
            'plato_id' => $plato->id,
            'cantidad' => 2,
            'precio_unitario' => 40.00,
        ]);
    }

    public function test_admin_puede_crear_nuevo_plato_y_alternar_disponibilidad(): void
    {
        $admin = User::factory()->admin()->create();
        $categoria = Categoria::factory()->create();

        $respuestaCrear = $this->actingAs($admin)->post('/admin/platos', [
            'categoria_id' => $categoria->id,
            'nombre' => 'Sajta Especial',
            'precio' => 38.00,
            'descripcion' => 'Plato tradicional tarijeño',
            'disponible' => 1,
        ]);

        $respuestaCrear->assertRedirect('/admin');
        $plato = Plato::where('nombre', 'Sajta Especial')->firstOrFail();
        $this->assertTrue($plato->disponible);

        $respuestaToggle = $this->actingAs($admin)->post('/admin/platos/' . $plato->id . '/toggle');
        $respuestaToggle->assertSessionHas('success');
        $this->assertFalse($plato->fresh()->disponible);
    }

    public function test_cocina_y_mesero_pueden_avanzar_pedido_y_liberar_mesa(): void
    {
        $mesero = User::factory()->mesero()->create();
        $cliente = Cliente::factory()->create();
        $mesa = Mesa::factory()->create(['estado' => Mesa::ESTADO_RESERVADO, 'disponible' => false]);
        $pedido = Pedido::factory()->create([
            'cliente_id' => $cliente->id,
            'mesa_id' => $mesa->id,
            'estado' => Pedido::ESTADO_PENDIENTE,
        ]);

        $this->actingAs($mesero)->post('/cocina/pedidos/' . $pedido->id . '/estado', [
            'estado' => Pedido::ESTADO_PREPARANDO,
        ]);
        $this->assertEquals(Mesa::ESTADO_OCUPADO, $mesa->fresh()->estado);

        $this->actingAs($mesero)->post('/cocina/pedidos/' . $pedido->id . '/estado', [
            'estado' => Pedido::ESTADO_LISTO,
        ]);
        $this->assertEquals(Pedido::ESTADO_LISTO, $pedido->fresh()->estado);

        $this->actingAs($mesero)->post('/cocina/pedidos/' . $pedido->id . '/estado', [
            'estado' => Pedido::ESTADO_SERVIDO,
        ]);
        $this->assertEquals(Mesa::ESTADO_OCUPADO, $mesa->fresh()->estado);

        $this->actingAs($mesero)->post('/cocina/pedidos/' . $pedido->id . '/estado', [
            'estado' => Pedido::ESTADO_PAGADO,
        ]);
        $this->assertEquals(Pedido::ESTADO_PAGADO, $pedido->fresh()->estado);
        $this->assertEquals(Mesa::ESTADO_LIBRE, $mesa->fresh()->estado);
        $this->assertTrue($mesa->fresh()->disponible);
    }
}
