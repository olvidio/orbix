<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use PHPUnit\Framework\TestCase;
use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\misas\application\VerMisasZonaData;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;

final class VerMisasZonaDataTest extends TestCase
{
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth']['idioma'] = 'es_ES.UTF8';
    }

    protected function tearDown(): void
    {
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_seleccion_con_bit_2_pide_sacds_al_ZonaSacdRepository_con_metodo_real(): void
    {
        $zonaSacdRepo = $this->createMock(ZonaSacdRepositoryInterface::class);
        $zonaSacdRepo->expects($this->once())
            ->method('getIdSacdsDeZona')
            ->with(10)
            ->willReturn([]);

        $encargoRepo = $this->createMock(EncargoRepositoryInterface::class);
        $encargoRepo->expects($this->once())
            ->method('getEncargos')
            ->willReturn([]);

        $useCase = new VerMisasZonaData(
            $this->createStub(PersonaSacdRepositoryInterface::class),
            $zonaSacdRepo,
            $this->createStub(EncargoDiaRepositoryInterface::class),
            $this->createStub(EncargoHorarioRepositoryInterface::class),
            $encargoRepo,
        );

        $out = $useCase->build([
            'id_zona' => 10,
            'seleccion' => 2,
            'empiezamin' => '01/01/2026',
            'empiezamax' => '02/01/2026',
        ]);

        $this->assertSame('', $out['error']);
        $this->assertSame(10, $out['id_zona']);
        $this->assertSame(2, $out['seleccion']);
        $this->assertSame([], $out['data_cuadricula']);
    }

    public function test_sin_bit_2_no_resuelve_ZonaSacdRepository(): void
    {
        $encargoRepo = $this->createMock(EncargoRepositoryInterface::class);
        $encargoRepo->method('getEncargos')->willReturn([]);

        $useCase = new VerMisasZonaData(
            $this->createStub(PersonaSacdRepositoryInterface::class),
            $this->createStub(ZonaSacdRepositoryInterface::class),
            $this->createStub(EncargoDiaRepositoryInterface::class),
            $this->createStub(EncargoHorarioRepositoryInterface::class),
            $encargoRepo,
        );

        $out = $useCase->build([
            'id_zona' => 10,
            'seleccion' => 0,
            'empiezamin' => '01/01/2026',
            'empiezamax' => '02/01/2026',
        ]);

        $this->assertSame('', $out['error']);
        $this->assertSame([], $out['data_cuadricula']);
    }
}
