<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\MatrizPriorizacion;
use App\Models\ProgramaAuditoria;
use App\Models\MunicipioColombia;

class ProgramaAuditoriaTest extends TestCase
{
    use RefreshDatabase;

    protected User $jefe_auditor;
    protected User $super_admin;
    protected MunicipioColombia $municipio;
    protected MatrizPriorizacion $matriz;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear usuarios
        $this->jefe_auditor = User::factory()->create(['role' => 'jefe_auditor']);
        $this->super_admin = User::factory()->create(['role' => 'super_administrador']);

        // Crear municipio
        $this->municipio = MunicipioColombia::factory()->create();

        // Crear matriz aprobada
        $this->matriz = MatrizPriorizacion::factory()->create([
            'municipio_id' => $this->municipio->id,
            'estado' => 'aprobado',
            'vigencia' => 2025,
        ]);
    }

    /** @test */
    public function puede_ver_listado_de_programas()
    {
        $response = $this->actingAs($this->jefe_auditor)
            ->get(route('programa-auditoria.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function puede_crear_programa_desde_matriz_aprobada()
    {
        $response = $this->actingAs($this->jefe_auditor)
            ->get(route('programa-auditoria.create'));

        $response->assertStatus(200);
    }

    /** @test */
    public function auditor_no_puede_acceder_a_crear_programa()
    {
        $auditor = User::factory()->create(['role' => 'auditor']);

        $response = $this->actingAs($auditor)
            ->get(route('programa-auditoria.create'));

        $response->assertStatus(403);
    }

    /** @test */
    public function puede_guardar_programa_con_datos_validos()
    {
        $data = [
            'nombre' => 'Programa de Auditoría 2025',
            'matriz_priorizacion_id' => $this->matriz->id,
            'fecha_inicio_programacion' => now()->addDays(7)->toDateString(),
            'fecha_fin_programacion' => now()->addMonths(12)->toDateString(),
            'numero_auditores' => 5,
            'objetivos' => 'Auditar procesos críticos',
            'alcance' => 'Todas las áreas de la entidad',
        ];

        $response = $this->actingAs($this->jefe_auditor)
            ->post(route('programa-auditoria.store'), $data);

        $response->assertRedirect(route('programa-auditoria.show', ProgramaAuditoria::first()));
        $this->assertDatabaseHas('programa_auditoria', [
            'nombre' => 'Programa de Auditoría 2025',
            'vigencia' => 2025,
        ]);
    }

    /** @test */
    public function programa_requiere_matriz_aprobada()
    {
        $matriz_en_elaboracion = MatrizPriorizacion::factory()->create([
            'estado' => 'elaboracion',
        ]);

        $data = [
            'nombre' => 'Test Programa',
            'matriz_priorizacion_id' => $matriz_en_elaboracion->id,
            'fecha_inicio_programacion' => now()->addDays(7)->toDateString(),
            'fecha_fin_programacion' => now()->addMonths(12)->toDateString(),
        ];

        $response = $this->actingAs($this->jefe_auditor)
            ->post(route('programa-auditoria.store'), $data);

        $response->assertSessionHas('error');
    }

    /** @test */
    public function puede_ver_detalle_de_programa()
    {
        $programa = ProgramaAuditoria::factory()->create([
            'matriz_priorizacion_id' => $this->matriz->id,
        ]);

        $response = $this->actingAs($this->jefe_auditor)
            ->get(route('programa-auditoria.show', $programa));

        $response->assertStatus(200);
    }

    /** @test */
    public function puede_editar_programa_en_elaboracion()
    {
        $programa = ProgramaAuditoria::factory()->create([
            'matriz_priorizacion_id' => $this->matriz->id,
            'estado' => 'elaboracion',
        ]);

        $response = $this->actingAs($this->jefe_auditor)
            ->put(route('programa-auditoria.update', $programa), [
                'nombre' => 'Nombre Actualizado',
                'fecha_inicio_programacion' => now()->addDays(5)->toDateString(),
                'fecha_fin_programacion' => now()->addMonths(11)->toDateString(),
            ]);

        $response->assertRedirect(route('programa-auditoria.show', $programa));
        $this->assertDatabaseHas('programa_auditoria', [
            'id' => $programa->id,
            'nombre' => 'Nombre Actualizado',
        ]);
    }

    /** @test */
    public function no_puede_editar_programa_aprobado()
    {
        $programa = ProgramaAuditoria::factory()->create([
            'matriz_priorizacion_id' => $this->matriz->id,
            'estado' => 'aprobado',
        ]);

        $response = $this->actingAs($this->jefe_auditor)
            ->get(route('programa-auditoria.edit', $programa));

        $response->assertStatus(302);
        $response->assertSessionHas('error');
    }

    /** @test */
    public function jefe_auditor_puede_enviar_a_aprobacion()
    {
        $programa = ProgramaAuditoria::factory()->create([
            'matriz_priorizacion_id' => $this->matriz->id,
            'estado' => 'elaboracion',
        ]);

        $response = $this->actingAs($this->jefe_auditor)
            ->patch(route('programa-auditoria.enviar', $programa));

        $response->assertRedirect();
        $this->assertDatabaseHas('programa_auditoria', [
            'id' => $programa->id,
            'estado' => 'enviado_aprobacion',
        ]);
    }

    /** @test */
    public function solo_super_admin_puede_aprobar()
    {
        $programa = ProgramaAuditoria::factory()->create([
            'matriz_priorizacion_id' => $this->matriz->id,
            'estado' => 'enviado_aprobacion',
        ]);

        // Jefe auditor intenta aprobar
        $response = $this->actingAs($this->jefe_auditor)
            ->patch(route('programa-auditoria.aprobar', $programa));

        $response->assertSessionHas('error');

        // Super admin aprueba
        $response = $this->actingAs($this->super_admin)
            ->patch(route('programa-auditoria.aprobar', $programa));

        $response->assertRedirect();
        $this->assertDatabaseHas('programa_auditoria', [
            'id' => $programa->id,
            'estado' => 'aprobado',
        ]);
    }

    /** @test */
    public function puede_eliminar_programa_en_elaboracion()
    {
        $programa = ProgramaAuditoria::factory()->create([
            'matriz_priorizacion_id' => $this->matriz->id,
            'estado' => 'elaboracion',
        ]);

        $response = $this->actingAs($this->jefe_auditor)
            ->delete(route('programa-auditoria.destroy', $programa));

        $response->assertRedirect(route('programa-auditoria.index'));
        $this->assertSoftDeleted('programa_auditoria', ['id' => $programa->id]);
    }
}
