<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function recepcionista_no_puede_entrar_a_admin()
    {
        $user = User::factory()->create([
            'role' => 'recepcionista'
        ]);

        $response = $this->actingAs($user)
            ->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_si_puede_entrar_a_admin()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $response = $this->actingAs($user)
            ->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_puede_entrar_a_rutas_de_recepcionista()
    {
        $user = User::factory()->create([
            'role' => 'admin'
        ]);

        $response = $this->actingAs($user)
            ->get('/recepcionista/dashboard');

        $response->assertStatus(200);
    }

    /** @test */
    public function usuario_no_autenticado_no_puede_entrar()
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }
}