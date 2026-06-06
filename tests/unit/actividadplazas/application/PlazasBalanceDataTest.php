<?php

namespace Tests\unit\actividadplazas\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividadplazas\application\PlazasBalanceData;
use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
use src\asistentes\application\services\AsistenteActividadService;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

/**
 * Validaciones antes de sesion `oConfig` y consultas a repos.
 */
final class PlazasBalanceDataTest extends TestCase
{
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth'] = [
            'id_usuario' => 1,
            'esquema' => 'H-dlv',
            'sfsv' => 1,
        ];
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_falta_dl(): void
    {
        $useCase = new PlazasBalanceData(
            $this->createMock(DelegacionRepositoryInterface::class),
            $this->createMock(ActividadRepositoryInterface::class),
            $this->createMock(ActividadPlazasRepositoryInterface::class),
            $this->createMock(AsistenteActividadService::class),
        );

        $out = $useCase->execute(['dl' => '', 'id_tipo_activ' => '123456']);
        $this->assertArrayHasKey('error', $out);
        $this->assertSame('', $out['dlB']);
    }

    public function test_dl_igual_a_la_propia(): void
    {
        $useCase = new PlazasBalanceData(
            $this->createMock(DelegacionRepositoryInterface::class),
            $this->createMock(ActividadRepositoryInterface::class),
            $this->createMock(ActividadPlazasRepositoryInterface::class),
            $this->createMock(AsistenteActividadService::class),
        );

        $out = $useCase->execute(['dl' => 'dl', 'id_tipo_activ' => '123456']);
        $this->assertArrayHasKey('error', $out);
        $this->assertSame('dl', $out['dlB']);
    }
}
