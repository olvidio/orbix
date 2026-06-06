<?php

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\TestCase;
use src\actividades\application\ListaCentrosActivDatos;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

final class ListaCentrosActivDatosTest extends TestCase
{
    public function test_sin_centros_solo_estilo(): void
    {
        $centroDl = $this->createMock(CentroDlRepositoryInterface::class);
        $centroDl->method('getCentros')->willReturn([]);

        $encRepo = $this->createMock(CentroEncargadoRepositoryInterface::class);

        $out = (new ListaCentrosActivDatos($centroDl, $encRepo))->ejecutar([
            'id_ctr_num' => 0,
            'id_ctr' => [],
            'periodo' => 'actual',
        ]);

        $this->assertArrayHasKey('html', $out);
        $this->assertStringContainsString('.responsable', $out['html']);
        $this->assertStringNotContainsString('<h3>', $out['html']);
    }
}
