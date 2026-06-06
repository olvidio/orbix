<?php

declare(strict_types=1);

namespace Tests\unit\zonassacd\domain;

use PHPUnit\Framework\TestCase;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\zonassacd\domain\InfoZona;

final class InfoZonaTest extends TestCase
{
    public function test_configuracion_por_defecto(): void
    {
        $zonaRepo = $this->createStub(ZonaRepositoryInterface::class);
        $info = new InfoZona($zonaRepo);

        $this->assertSame('src\\zonassacd\\domain\\entity\\Zona', $info->getClase());
        $this->assertSame('getZonas', $info->getMetodoGestor());
        $this->assertSame(ZonaRepositoryInterface::class, $info->getRepositoryInterface());
        $this->assertSame('', $info->getTxtExplicacion());
        $this->assertNotSame('', $info->getTxtTitulo());
        $this->assertNotSame('', $info->getTxtEliminar());
        $this->assertNotSame('', $info->getTxtBuscar());
    }

    public function test_getColeccion_sin_k_buscar_pide_todas_ordenadas(): void
    {
        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->expects($this->once())
            ->method('getZonas')
            ->with(['_ordre' => 'orden'], [])
            ->willReturn(['zona-a', 'zona-b']);

        $info = new InfoZona($zonaRepo);
        $info->setK_buscar('');

        $this->assertSame(['zona-a', 'zona-b'], $info->getColeccion());
    }

    public function test_getColeccion_con_k_buscar_usa_filtro_sin_acentos(): void
    {
        $zonaRepo = $this->createMock(ZonaRepositoryInterface::class);
        $zonaRepo->expects($this->once())
            ->method('getZonas')
            ->with(['nom' => 'nord'], ['nom' => 'sin_acentos'])
            ->willReturn(['zona-nord']);

        $info = new InfoZona($zonaRepo);
        $info->setK_buscar('nord');

        $this->assertSame(['zona-nord'], $info->getColeccion());
    }
}
