<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use PHPUnit\Framework\TestCase;
use src\misas\application\VerInicialesZonaData;
use src\misas\domain\contracts\InicialesSacdRepositoryInterface;
use src\misas\domain\entity\InicialesSacd;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\entity\PersonaSacd;
use src\shared\security\HashB;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;

final class VerInicialesZonaDataTest extends TestCase
{
    public function test_fila_incluye_ctx_update_atado_al_id_sacd(): void
    {
        $zonaSacdRepository = $this->createMock(ZonaSacdRepositoryInterface::class);
        $zonaSacdRepository->method('getIdSacdsDeZona')->with(9)->willReturn([501]);

        $personaSacd = $this->createMock(PersonaSacd::class);
        $personaSacd->method('getNombreApellidos')->willReturn('Mn. Joan');
        $personaSacdRepository = $this->createMock(PersonaSacdRepositoryInterface::class);
        $personaSacdRepository->method('findById')->with(501)->willReturn($personaSacd);

        $inicialesSacd = $this->createMock(InicialesSacd::class);
        $inicialesSacd->method('getIniciales')->willReturn('JV');
        $inicialesSacd->method('getColor')->willReturn('ff0000');
        $inicialesSacdRepository = $this->createMock(InicialesSacdRepositoryInterface::class);
        $inicialesSacdRepository->method('findById')->with(501)->willReturn($inicialesSacd);

        $useCase = new VerInicialesZonaData($zonaSacdRepository, $personaSacdRepository, $inicialesSacdRepository);
        $out = $useCase->getData(9);

        $this->assertCount(1, $out['rows']);
        $row = $out['rows'][0];
        $this->assertSame(501, $row['id_sacd']);
        $this->assertSame(
            ['id_sacd' => 501],
            HashB::open($row['ctx_update'], 'update_iniciales')
        );
    }
}
