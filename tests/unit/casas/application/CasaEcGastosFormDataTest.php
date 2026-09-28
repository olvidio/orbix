<?php

declare(strict_types=1);

namespace Tests\unit\casas\application;

use PHPUnit\Framework\TestCase;
use src\casas\application\CasaEcGastosFormData;
use src\casas\domain\contracts\UbiGastoRepositoryInterface;
use src\shared\security\HashB;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\entity\Casa;
use src\ubis\domain\value_objects\UbiNombreText;

final class CasaEcGastosFormDataTest extends TestCase
{
    protected function setUp(): void
    {
        session_id('test-session-casas-ec-gastos');
    }

    public function test_sin_casas_devuelve_error(): void
    {
        $useCase = new CasaEcGastosFormData(
            $this->createMock(CasaDlRepositoryInterface::class),
            $this->createMock(UbiGastoRepositoryInterface::class),
        );

        $rta = $useCase->execute(['year' => 2026, 'id_cdc' => []]);

        $this->assertFalse($rta['ok']);
        $this->assertSame([], $rta['casas']);
    }

    public function test_ok_emite_ctx_guardar_por_casa(): void
    {
        $casa = $this->createMock(Casa::class);
        $casa->method('getNombreUbiVo')->willReturn(new UbiNombreText('Casa Test'));

        $casaRepo = $this->createMock(CasaDlRepositoryInterface::class);
        $casaRepo->method('findById')->with(12)->willReturn($casa);

        $gastoRepo = $this->createMock(UbiGastoRepositoryInterface::class);
        $gastoRepo->method('getUbisGastos')->willReturn([]);

        $rta = (new CasaEcGastosFormData($casaRepo, $gastoRepo))->execute([
            'year' => 2026,
            'id_cdc' => [12],
        ]);

        $this->assertTrue($rta['ok']);
        $this->assertCount(1, $rta['casas']);
        $this->assertSame(
            ['id_ubi' => 12, 'year' => 2026],
            HashB::open($rta['casas'][0]['ctx_guardar'], 'casa_ec_gastos_guardar')
        );
    }
}
