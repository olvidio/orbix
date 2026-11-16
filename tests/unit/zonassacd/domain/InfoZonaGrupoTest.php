<?php

declare(strict_types=1);

namespace Tests\unit\zonassacd\domain;

use PHPUnit\Framework\TestCase;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\zonassacd\domain\contracts\ZonaGrupoRepositoryInterface;
use src\zonassacd\domain\criteria\ZonaGrupoField;
use src\zonassacd\domain\InfoZonaGrupo;

final class InfoZonaGrupoTest extends TestCase
{
    public function test_configuracion_por_defecto(): void
    {
        $zonaRepo = $this->createStub(ZonaGrupoRepositoryInterface::class);
        $info = new InfoZonaGrupo($zonaRepo);

        $this->assertSame('src\\zonassacd\\domain\\entity\\ZonaGrupo', $info->getClase());
        $this->assertSame('getZonasGrupo', $info->getMetodoGestor());
        $this->assertSame(ZonaGrupoRepositoryInterface::class, $info->getRepositoryInterface());
        $this->assertSame('', $info->getTxtExplicacion());
        $this->assertNotSame('', $info->getTxtTitulo());
        $this->assertNotSame('', $info->getTxtEliminar());
        $this->assertNotSame('', $info->getTxtBuscar());
    }

    public function test_getColeccion_sin_k_buscar_pide_todas_ordenadas(): void
    {
        $zonaRepo = $this->createMock(ZonaGrupoRepositoryInterface::class);
        $zonaRepo->expects($this->once())
            ->method('matching')
            ->with($this->equalTo(Criteria::create()->orderBy(ZonaGrupoField::Orden)))
            ->willReturn(['grupo-a', 'grupo-b']);

        $info = new InfoZonaGrupo($zonaRepo);
        $info->setK_buscar('');

        $this->assertSame(['grupo-a', 'grupo-b'], $info->getColeccion());
    }

    public function test_getColeccion_con_k_buscar_usa_filtro_sin_acentos(): void
    {
        $zonaRepo = $this->createMock(ZonaGrupoRepositoryInterface::class);
        $zonaRepo->expects($this->once())
            ->method('matching')
            ->with($this->equalTo(Criteria::create()->where(Filter::matchesUnaccented(ZonaGrupoField::NombreGrupo, 'nord'))))
            ->willReturn(['grupo-nord']);

        $info = new InfoZonaGrupo($zonaRepo);
        $info->setK_buscar('nord');

        $this->assertSame(['grupo-nord'], $info->getColeccion());
    }
}
