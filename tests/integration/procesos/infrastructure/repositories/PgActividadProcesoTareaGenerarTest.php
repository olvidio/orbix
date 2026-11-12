<?php

declare(strict_types=1);

namespace Tests\integration\procesos\infrastructure\persistence\postgresql;

use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;
use Tests\factories\actividades\ActividadAllFactory;
use Tests\myTest;

/**
 * Generar y borrar el proceso de una actividad (sv y sf). El repositorio es un
 * servicio compartido en la petición: al acabar debe seguir apuntando a la
 * tabla de la propia sección. La actividad no se graba (se pasa la entidad);
 * las filas de proceso creadas se borran.
 */
final class PgActividadProcesoTareaGenerarTest extends myTest
{
    private ActividadProcesoTareaRepositoryInterface $repo;
    private int $idActiv;

    public function setUp(): void
    {
        parent::setUp();
        $this->repo = $GLOBALS['container']->get(ActividadProcesoTareaRepositoryInterface::class);
        $this->idActiv = 9900000 + random_int(1000, 9999);
    }

    public function tearDown(): void
    {
        $this->repo->borrarProceso($this->idActiv);
        $this->repo->setNomTabla('a_actividad_proceso_sv');
        parent::tearDown();
    }

    public function test_generar_el_proceso_de_sv_y_sf_y_seguir_en_la_tabla_de_sv(): void
    {
        $this->assertSame('a_actividad_proceso_sv', $this->repo->getNomTabla());
        $actividad = (new ActividadAllFactory())->createSimple($this->idActiv); // tipo 271000: proceso 22 (sv) y 28 (sf)

        $primera = $this->repo->generarProceso((string) $this->idActiv, null, false, $actividad);

        $this->assertSame('a_actividad_proceso_sv', $this->repo->getNomTabla(), 'repone la tabla de la sección');
        $this->assertIsInt($primera);
        $estado = $this->repo->getListaFaseEstado($this->idActiv);
        $tareasSv = $GLOBALS['container']->get(TareaProcesoRepositoryInterface::class)->getFasesProceso(22);
        $this->assertCount(count($tareasSv), $estado, 'las fases del proceso de sv');
        // Actividad anterior a hoy (mayo 2024) en status actual: conserva el status,
        // así que salen completadas las fases de status <= actual.
        $this->assertContains(true, $estado);
        $this->assertContains(false, $estado);
        $this->assertContains($primera, $this->repo->getFasesCompletadas($this->idActiv));

        $this->repo->setNomTabla('a_actividad_proceso_sf');
        $this->assertNotSame([], $this->repo->getListaFaseEstado($this->idActiv), 'también el de sf');
        $this->repo->setNomTabla('a_actividad_proceso_sv');

        $this->assertSame($primera, $this->repo->generarProceso((string) $this->idActiv, null, false, $actividad), 'sin forzar, no lo regenera');
    }

    public function test_borrar_el_proceso_sigue_en_la_tabla_de_sv(): void
    {
        $actividad = (new ActividadAllFactory())->createSimple($this->idActiv);
        $this->repo->generarProceso((string) $this->idActiv, null, false, $actividad);

        $this->repo->borrarProceso($this->idActiv);

        $this->assertSame('a_actividad_proceso_sv', $this->repo->getNomTabla());
        $this->assertSame([], $this->repo->getListaFaseEstado($this->idActiv));
    }

    public function test_actividad_inexistente_avisa_sin_generar(): void
    {
        $this->assertTrue($this->repo->generarProceso('999999981'));
        $this->assertNotSame([], $this->repo->consumirAvisosGenerarProceso());
        $this->assertSame([], $this->repo->consumirAvisosGenerarProceso(), 'los avisos se vacían al leerlos');
    }
}
