<?php

declare(strict_types=1);

namespace Tests\unit\zonassacd\application\services;

use PHPUnit\Framework\TestCase;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\ubis\domain\criteria\CentroDlField;
use src\ubis\domain\criteria\CentroEllasField;
use src\zonassacd\application\services\CentrosDeZona;
use src\zonassacd\domain\contracts\ZonaCtrRepositoryInterface;

final class CentrosDeZonaTest extends TestCase
{
    public function test_id_ubis_de_zona_cero_o_negativo_sin_consultar(): void
    {
        $repo = $this->createMock(ZonaCtrRepositoryInterface::class);
        $repo->expects($this->never())->method('idUbisDeZona');

        $svc = new CentrosDeZona($repo);
        $this->assertSame([], $svc->idUbisDeZona(0));
        $this->assertSame([], $svc->idUbisDeZona(-1));
    }

    public function test_id_ubis_de_zona_delega_en_el_repositorio(): void
    {
        $repo = $this->createMock(ZonaCtrRepositoryInterface::class);
        $repo->expects($this->once())->method('idUbisDeZona')->with(9)->willReturn([1042, 2005]);

        $this->assertSame([1042, 2005], (new CentrosDeZona($repo))->idUbisDeZona(9));
    }

    public function test_sin_zona_entre_candidatos(): void
    {
        $repo = $this->createMock(ZonaCtrRepositoryInterface::class);
        $repo->method('mapaZonaPorCentro')->with([1042, 2005, 3001])->willReturn([1042 => 9]);

        $svc = new CentrosDeZona($repo);
        $this->assertSame([2005, 3001], $svc->idUbisSinZonaEntre([1042, 2005, 3001]));
    }

    public function test_criterio_activos_in_vacio_es_null(): void
    {
        $this->assertNull(CentrosDeZona::criterioActivosIn([], CentroDlField::class));
    }

    public function test_criterio_activos_in_filtra_activos_en_lista_ordenados_por_nombre(): void
    {
        $esperado = Criteria::create()
            ->where(Filter::eq(CentroEllasField::Active, true))
            ->andWhere(Filter::in(CentroEllasField::IdUbi, [1042, 2005]))
            ->orderBy(CentroEllasField::NombreUbi);

        $this->assertEquals($esperado, CentrosDeZona::criterioActivosIn([1042, 2005], CentroEllasField::class));
    }
}
