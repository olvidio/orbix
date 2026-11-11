<?php

declare(strict_types=1);

namespace Tests\unit\misas\application;

use PHPUnit\Framework\TestCase;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\encargossacd\domain\entity\Encargo;
use src\encargossacd\domain\entity\EncargoTipo;
use src\misas\application\DesplegableCentrosZonaData;
use src\misas\application\DesplegableEncargosData;
use src\shared\domain\criteria\Criteria;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\contracts\CentroEllosRepositoryInterface;
use src\ubis\domain\entity\CentroEllas;
use src\ubis\domain\entity\CentroEllos;
use src\zonassacd\application\services\CentrosDeZona;
use src\zonassacd\domain\contracts\ZonaCtrRepositoryInterface;
use Tests\support\DescribeCriteria;

/** Desplegables AJAX de misas: centros de una zona (sf + sv) y encargos de una zona. */
final class DesplegablesMisasDataTest extends TestCase
{
    private function centroEllas(int $id, string $nombre): CentroEllas
    {
        $c = $this->createMock(CentroEllas::class);
        $c->method('getId_ubi')->willReturn($id);
        $c->method('getNombre_ubi')->willReturn($nombre);

        return $c;
    }

    private function centroEllos(int $id, string $nombre): CentroEllos
    {
        $c = $this->createMock(CentroEllos::class);
        $c->method('getId_ubi')->willReturn($id);
        $c->method('getNombre_ubi')->willReturn($nombre);

        return $c;
    }

    /** @param list<int> $idUbis */
    private function centrosZona(array $idUbis): CentrosDeZona
    {
        $zc = $this->createMock(ZonaCtrRepositoryInterface::class);
        $zc->method('idUbisDeZona')->willReturn($idUbis);

        return new CentrosDeZona($zc);
    }

    // ---- centros de zona ----------------------------------------------------------------------

    public function test_zona_sin_centros_devuelve_listas_vacias_sin_consultar(): void
    {
        $ellas = $this->createMock(CentroEllasRepositoryInterface::class);
        $ellos = $this->createMock(CentroEllosRepositoryInterface::class);
        $ellas->expects($this->never())->method('matching');
        $ellos->expects($this->never())->method('matching');

        $r = (new DesplegableCentrosZonaData($ellas, $ellos, $this->centrosZona([])))->getData(3);

        $this->assertSame([], $r['opciones_sf']);
        $this->assertSame([], $r['opciones_sv']);
        $this->assertSame('id_ubi', $r['id']);
        $this->assertTrue($r['blanco']);
    }

    public function test_los_centros_salen_ordenados_por_nombre_como_pares_y_con_el_filtro_de_activos(): void
    {
        $criterios = [];
        $ellas = $this->createMock(CentroEllasRepositoryInterface::class);
        $ellas->method('matching')->willReturnCallback(function (Criteria $c) use (&$criterios): array {
            $criterios[] = DescribeCriteria::filtros($c);

            return [$this->centroEllas(1003, 'Zeta'), $this->centroEllas(501, 'Alfa')];
        });
        $ellos = $this->createMock(CentroEllosRepositoryInterface::class);
        $ellos->method('matching')->willReturn([$this->centroEllos(700, 'Beta')]);

        $r = (new DesplegableCentrosZonaData($ellas, $ellos, $this->centrosZona([501, 1003, 700])))->getData(3, 501);

        $this->assertSame([['501', 'Alfa'], ['1003', 'Zeta']], $r['opciones_sf']);
        $this->assertSame([['700', 'Beta']], $r['opciones_sv']);
        $this->assertSame('501', $r['selected']);
        $this->assertSame([['active eq true', 'id_ubi in [501,1003,700]']], $criterios);
    }

    /**
     * @dataProvider seleccionesVaciasProvider
     */
    public function test_sin_centro_preseleccionado_el_valor_es_vacio(?int $sel): void
    {
        $ellas = $this->createMock(CentroEllasRepositoryInterface::class);
        $ellos = $this->createMock(CentroEllosRepositoryInterface::class);

        $this->assertSame('', (new DesplegableCentrosZonaData($ellas, $ellos, $this->centrosZona([])))->getData(3, $sel)['selected']);
    }

    /**
     * @return iterable<string, array{?int}>
     */
    public static function seleccionesVaciasProvider(): iterable
    {
        yield 'null' => [null];
        yield 'cero' => [0];
    }

    // ---- encargos de zona ----------------------------------------------------------------------

    private function tipo(int $id): EncargoTipo
    {
        $t = $this->createMock(EncargoTipo::class);
        $t->method('getId_tipo_enc')->willReturn($id);

        return $t;
    }

    private function encargo(int $id, ?string $desc): Encargo
    {
        $e = $this->createMock(Encargo::class);
        $e->method('getId_enc')->willReturn($id);
        $e->method('getDesc_enc')->willReturn($desc);

        return $e;
    }

    public function test_solo_se_ofrecen_encargos_de_los_tipos_8100_o_mas_de_la_zona(): void
    {
        $tipos = $this->createMock(EncargoTipoRepositoryInterface::class);
        $tipos->method('matching')->willReturn([$this->tipo(8000), $this->tipo(8100), $this->tipo(8210)]);
        $criterio = null;
        $encargos = $this->createMock(EncargoRepositoryInterface::class);
        $encargos->method('matching')->willReturnCallback(function (Criteria $c) use (&$criterio): array {
            $criterio = DescribeCriteria::filtros($c);

            return [$this->encargo(20, 'Misa B'), $this->encargo(10, null)];
        });

        $r = (new DesplegableEncargosData($tipos, $encargos))->getData(4);

        $this->assertSame(['id_tipo_enc in [8100,8210]', 'id_zona eq 4'], $criterio);
        $this->assertSame([['20', 'Misa B'], ['10', '']], $r['opciones']);
        $this->assertSame('id_enc', $r['id']);
        $this->assertSame('', $r['selected']);
        $this->assertSame('fnjs_prepara_select_centro()', $r['action']);
    }

    public function test_el_encargo_seleccionado_va_primero_aunque_no_sea_de_la_zona(): void
    {
        $tipos = $this->createMock(EncargoTipoRepositoryInterface::class);
        $tipos->method('matching')->willReturn([$this->tipo(8100)]);
        $encargos = $this->createMock(EncargoRepositoryInterface::class);
        $encargos->method('findById')->with(99)->willReturn($this->encargo(99, 'Fuera de zona'));
        $encargos->method('matching')->willReturn([$this->encargo(20, 'Misa B')]);

        $r = (new DesplegableEncargosData($tipos, $encargos))->getData(4, 99);

        $this->assertSame([['99', 'Fuera de zona'], ['20', 'Misa B']], $r['opciones']);
        $this->assertSame('99', $r['selected']);
    }

    public function test_sin_tipos_validos_no_se_buscan_encargos(): void
    {
        $tipos = $this->createMock(EncargoTipoRepositoryInterface::class);
        $tipos->method('matching')->willReturn([$this->tipo(8000)]);
        $encargos = $this->createMock(EncargoRepositoryInterface::class);
        $encargos->expects($this->never())->method('matching');
        $encargos->method('findById')->willReturn(null);

        $r = (new DesplegableEncargosData($tipos, $encargos))->getData(4, 7);

        $this->assertSame([], $r['opciones']);
        $this->assertSame('7', $r['selected'], 'la selección se devuelve aunque el encargo no exista');
    }
}
