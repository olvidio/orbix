<?php

namespace Tests\unit\actividadtarifas\application;

use PHPUnit\Framework\TestCase;
use src\actividadtarifas\application\services\TipoTarifaDropdown;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;

final class TipoTarifaDropdownTest extends TestCase
{
    public function test_devuelve_array_del_repo(): void
    {
        $map = [1 => 'A', 2 => 'B'];
        $repo = $this->createMock(TipoTarifaRepositoryInterface::class);
        $repo->expects($this->once())->method('getArrayTipoTarifas')->with('')->willReturn($map);

        $this->assertSame($map, (new TipoTarifaDropdown($repo))->opciones(0));
    }

    public function test_filtra_por_sfsv(): void
    {
        $repo = $this->createMock(TipoTarifaRepositoryInterface::class);
        $repo->expects($this->once())->method('getArrayTipoTarifas')->with(2)->willReturn([]);

        $this->assertSame([], (new TipoTarifaDropdown($repo))->opciones(2));
    }
}
