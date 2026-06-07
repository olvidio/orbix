<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\TestCase;
use src\ubis\application\CentrosGetPlazasData;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

final class CentrosGetPlazasDataTest extends TestCase
{
    public function test_mapea_centros_activos(): void
    {
        $c1 = $this->createMock(\src\ubis\domain\entity\CentroDl::class);
        $c1->method('getId_ubi')->willReturn(5);
        $c1->method('getNombre_ubi')->willReturn('Centro Norte');
        $c1->method('getNum_habit_indiv')->willReturn(2);
        $c1->method('getPlazas')->willReturn(10);
        $c1->method('isSede')->willReturn(true);

        $c2 = $this->createMock(\src\ubis\domain\entity\CentroDl::class);
        $c2->method('getId_ubi')->willReturn(6);
        $c2->method('getNombre_ubi')->willReturn('Centro Sur');
        $c2->method('getNum_habit_indiv')->willReturn(null);
        $c2->method('getPlazas')->willReturn(4);
        $c2->method('isSede')->willReturn(false);

        $repo = $this->createMock(CentroDlRepositoryInterface::class);
        $repo->expects($this->once())
            ->method('getCentros')
            ->with(['active' => 't', '_ordre' => 'nombre_ubi'])
            ->willReturn([$c1, $c2]);

        $useCase = new CentrosGetPlazasData($repo);
        $out = $useCase->execute();

        $this->assertCount(4, $out['a_cabeceras']);
        $this->assertSame(
            ['script' => 'fnjs_modificar(5,"plazas")', 'valor' => 'Centro Norte'],
            $out['a_valores'][1][1]
        );
        $this->assertSame(2, $out['a_valores'][1][2]);
        $this->assertSame(10, $out['a_valores'][1][3]);
        $this->assertSame(_('si'), $out['a_valores'][1][4]);
        $this->assertSame(_('no'), $out['a_valores'][2][4]);
    }
}
