<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use PHPUnit\Framework\TestCase;
use src\misas\application\PlanDeMisasPantallaData;
use src\misas\application\support\IdNomJefeResolver;
use src\shared\security\HashB;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

final class PlanDeMisasPantallaDataTest extends TestCase
{
    protected function setUp(): void
    {
        session_id('test-session-misas-plantilla');
    }

    private function createUseCase(): PlanDeMisasPantallaData
    {
        $resolver = $this->createMock(IdNomJefeResolver::class);
        $resolver->method('resolve')->willReturn(['id_nom_jefe' => null, 'error' => '']);

        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->method('getArrayZonas')->willReturn([1 => 'Zona A']);

        $prefRepo = $this->createMock(PreferenciaRepositoryInterface::class);
        $prefRepo->method('getPreferencias')->willReturn([]);

        return new PlanDeMisasPantallaData($zonaRepo, $prefRepo, $resolver);
    }

    public function test_modificar_plantilla_emite_ctx_importar_y_anadir(): void
    {
        $data = $this->createUseCase()->getData('modificar_plantilla');

        $this->assertSame([], HashB::open($data['ctx_importar'], 'importar_plantilla_data'));
        $this->assertSame([], HashB::open($data['ctx_anadir'], 'anadir_ctr_tarea'));
    }

    public function test_preparar_emite_ctx_crear(): void
    {
        $data = $this->createUseCase()->getData('preparar');

        $this->assertSame([], HashB::open($data['ctx_crear'], 'crear_nuevo_periodo_data'));
        $this->assertArrayNotHasKey('ctx_importar', $data);
    }

    public function test_ver_no_emite_capsulas_de_mutacion(): void
    {
        $data = $this->createUseCase()->getData('ver');

        $this->assertArrayNotHasKey('ctx_importar', $data);
        $this->assertArrayNotHasKey('ctx_anadir', $data);
        $this->assertArrayNotHasKey('ctx_crear', $data);
    }
}
