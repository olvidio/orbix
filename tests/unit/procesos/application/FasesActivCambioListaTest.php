<?php

declare(strict_types=1);

namespace Tests\unit\procesos\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\procesos\application\FasesActivCambioLista;
use src\procesos\application\ProcesoActividadService;
use src\procesos\domain\contracts\ActividadProcesoTareaRepositoryInterface;
use src\procesos\domain\contracts\TareaProcesoRepositoryInterface;

/**
 * Test unitario del camino de retorno temprano del caso de uso
 * FasesActivCambioLista (id_fase_nueva vacío).
 */
final class FasesActivCambioListaTest extends TestCase
{
    private function useCase(): FasesActivCambioLista
    {
        return new FasesActivCambioLista(
            $this->createMock(ActividadDlRepositoryInterface::class),
            $this->createMock(ActividadRepositoryInterface::class),
            $this->createMock(TipoDeActividadRepositoryInterface::class),
            $this->createMock(TareaProcesoRepositoryInterface::class),
            $this->createMock(ActividadProcesoTareaRepositoryInterface::class),
            $this->createMock(ProcesoActividadService::class),
        );
    }

    public function test_sin_id_fase_nueva_devuelve_mensaje_error(): void
    {
        $data = $this->useCase()->execute([]);
        $this->assertIsArray($data);
        $this->assertSame(_('Debe poner la fase nueva'), $data['error']);
    }

    public function test_id_fase_nueva_vacia_devuelve_mensaje_error(): void
    {
        $data = $this->useCase()->execute([
            'id_fase_nueva' => '',
            'accion' => 'marcar',
        ]);
        $this->assertIsArray($data);
        $this->assertSame(_('Debe poner la fase nueva'), $data['error']);
        $this->assertSame('marcar', $data['accion']);
    }
}
