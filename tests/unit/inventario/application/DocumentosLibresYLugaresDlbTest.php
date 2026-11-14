<?php

declare(strict_types=1);

namespace Tests\unit\inventario\application;

use PHPUnit\Framework\TestCase;
use src\configuracion\domain\contracts\ConfigSchemaRepositoryInterface;
use src\configuracion\domain\entity\ConfigSchema;
use src\inventario\application\DocumentosLibresData;
use src\inventario\application\LugaresDeDlbData;
use src\inventario\application\services\AlmacenInventarioService;
use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\inventario\domain\contracts\WhereisRepositoryInterface;
use src\inventario\domain\entity\Documento;
use src\inventario\domain\entity\Egm;
use src\inventario\domain\entity\Equipaje;
use src\inventario\domain\entity\Lugar;
use src\inventario\domain\entity\TipoDoc;
use src\inventario\domain\entity\UbiInventario;
use src\inventario\domain\entity\Whereis;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\support\DescribeCriteria;

/**
 * {@see DocumentosLibresData} (documentos del almacén para un equipaje) y {@see LugaresDeDlbData}
 * (lugares de la dl por centro, para asignar un tipo de documento o imprimir el inventario).
 */
final class DocumentosLibresYLugaresDlbTest extends TestCase
{
    private static function lugar(int $id, string $nom): Lugar
    {
        $l = new Lugar();
        $l->setId_lugar($id);
        $l->setId_ubi(1);
        $l->setNom_lugar($nom);
        return $l;
    }

    /** Almacén según el parámetro de configuración (null: sin definir). */
    private function almacen(?string $valor): AlmacenInventarioService
    {
        $config = $this->createMock(ConfigSchemaRepositoryInterface::class);
        $config->method('findById')->willReturnCallback(static function (string $parametro) use ($valor): ?ConfigSchema {
            if ($valor === null || $parametro !== AlmacenInventarioService::PARAMETRO) {
                return null;
            }
            $c = new ConfigSchema();
            $c->setParametro($parametro);
            $c->setValor($valor);
            return $c;
        });

        return new AlmacenInventarioService($config);
    }

    public function test_documentos_del_almacen_de_un_tipo(): void
    {
        $equipajes = $this->createMock(EquipajeRepositoryInterface::class);
        $equipajes->method('findById')->willReturnCallback(static fn (int $id): ?Equipaje => $id === 5 ? new Equipaje() : null);
        $consulta = [];
        $docs = $this->createMock(DocumentoRepositoryInterface::class);
        $docs->method('matching')->willReturnCallback(static function ($c) use (&$consulta): array {
            $consulta = DescribeCriteria::filtros($c);
            return array_map(static function (array $x): Documento {
                $d = new Documento();
                $d->setId_doc($x[0]);
                $d->setId_tipo_doc(3);
                $d->setId_ubi(40);
                $d->setId_lugar($x[1]);
                $d->setIdentificador($x[2]);
                return $d;
            }, [[11, 20, 'a'], [12, 66, 'b'], [13, 20, null]]);
        });
        $lugares = $this->createMock(LugarRepositoryInterface::class);
        $lugares->method('findById')->willReturnCallback(static fn (int $id): ?Lugar => $id === 20 ? self::lugar(20, 'Estante 1') : null);
        $uc = new DocumentosLibresData($equipajes, $docs, $lugares, $this->almacen('77'), $this->createMock(EgmRepositoryInterface::class), $this->createMock(WhereisRepositoryInterface::class));

        $r = $uc->execute(5, 3);

        $this->assertSame(['id_tipo_doc eq 3', 'id_ubi eq 77'], $consulta, 'el almacén es el del parámetro de configuración');
        $this->assertSame([1 => [11, 'Estante 1', 'a'], 3 => [13, 'Estante 1', null]], $r['a_valores'], 'sin el de lugar inexistente');
        $this->assertSame([], $uc->execute(6, 3), 'equipaje inexistente');
    }

    /** Duda 13: antes se ofrecían también los documentos que ya iban en un equipaje de las mismas fechas. */
    public function test_descarta_los_documentos_de_este_equipaje_y_de_los_de_fechas_que_se_cruzan(): void
    {
        $equipaje = new Equipaje();
        $equipaje->setId_equipaje(5);
        $equipaje->setF_ini(new DateTimeLocal('2026-07-01'));
        $equipaje->setF_fin(new DateTimeLocal('2026-07-10'));
        $equipajes = $this->createMock(EquipajeRepositoryInterface::class);
        $equipajes->method('findById')->willReturn($equipaje);
        $equipajes->expects($this->once())->method('getEquipajesCoincidentes')->with('2026-07-01', '2026-07-10')->willReturn([5, 6]);
        $egms = $this->createMock(EgmRepositoryInterface::class);
        $egms->method('matching')->willReturnCallback(function ($c): array {
            $this->assertSame(['id_equipaje in [5,6]'], DescribeCriteria::filtros($c));
            return array_map(static function (int $id): Egm {
                $e = new Egm();
                $e->setId_item($id);
                return $e;
            }, [900, 901]);
        });
        $whereis = $this->createMock(WhereisRepositoryInterface::class);
        $whereis->method('matching')->willReturnCallback(function ($c): array {
            $this->assertSame(['id_item_egm in [900,901]'], DescribeCriteria::filtros($c));
            $w = new Whereis();
            $w->setId_doc(12);
            return [$w];
        });
        $docs = $this->createMock(DocumentoRepositoryInterface::class);
        $docs->method('matching')->willReturnCallback(static fn (): array => array_map(static function (int $id): Documento {
            $d = new Documento();
            $d->setId_doc($id);
            $d->setId_tipo_doc(3);
            $d->setId_ubi(77);
            $d->setId_lugar(20);
            return $d;
        }, [11, 12, 13]));
        $lugares = $this->createMock(LugarRepositoryInterface::class);
        $lugares->method('findById')->willReturn(self::lugar(20, 'Estante 1'));

        $r = (new DocumentosLibresData($equipajes, $docs, $lugares, $this->almacen('77'), $egms, $whereis))->execute(5, 3);

        $this->assertSame([11, 13], array_column($r['a_valores'], 0), 'el 12 ya va en una maleta');
    }

    public function test_sin_almacen_definido_avisa_y_no_busca_documentos(): void
    {
        $equipajes = $this->createMock(EquipajeRepositoryInterface::class);
        $equipajes->method('findById')->willReturn(new Equipaje());
        $docs = $this->createMock(DocumentoRepositoryInterface::class);
        $docs->expects($this->never())->method('matching');
        $uc = new DocumentosLibresData($equipajes, $docs, $this->createMock(LugarRepositoryInterface::class), $this->almacen(null), $this->createMock(EgmRepositoryInterface::class), $this->createMock(WhereisRepositoryInterface::class));

        $r = $uc->execute(5, 3);

        $this->assertSame(['aviso_txt'], array_keys($r));
        $this->assertNotSame('', $r['aviso_txt']);
    }

    private function lugaresDlb(): LugaresDeDlbData
    {
        $tipos = $this->createMock(TipoDocRepositoryInterface::class);
        $tipos->method('findById')->willReturnCallback(static function (int $id): ?TipoDoc {
            if ($id !== 1) {
                return null;
            }
            $t = new TipoDoc();
            $t->setId_tipo_doc(1);
            $t->setSigla('B');
            $t->setNom_doc('Carta');
            return $t;
        });
        $ubis = $this->createMock(UbiInventarioRepositoryInterface::class);
        $ubis->method('getUbisInventarioLugar')->willReturnCallback(static function (bool $conLugares): array {
            $ubi = static function (int $id, string $nom): UbiInventario {
                $u = new UbiInventario();
                $u->setId_ubi($id);
                $u->setNom_ubi($nom);
                return $u;
            };
            return $conLugares ? [$ubi(40, 'Almacén')] : [$ubi(2, 'Burgos'), $ubi(1, 'Ávila'), $ubi(3, 'Albacete')];
        });
        $lugares = $this->createMock(LugarRepositoryInterface::class);
        $lugares->method('matching')->willReturnCallback(function ($c): array {
            $this->assertSame(['id_ubi eq 40'], DescribeCriteria::filtros($c));
            $this->assertSame(['nom_lugar asc'], DescribeCriteria::orden($c));
            return [self::lugar(20, 'Estante 1'), self::lugar(21, 'Estante 2')];
        });

        return new LugaresDeDlbData($tipos, $ubis, $lugares);
    }

    public function test_lugares_por_centro_para_asignar_un_tipo(): void
    {
        $r = $this->lugaresDlb()->execute(1, false);

        $this->assertSame('B (Carta)', $r['nombreDoc']);
        $this->assertSame([40 => 'Almacén'], $r['aGrupos']);
        $this->assertSame([40 => [1 => ['sel' => 20, 1 => 'Estante 1'], 2 => ['sel' => 21, 1 => 'Estante 2']]], $r['a_valores']);
        $this->assertSame([], $this->lugaresDlb()->execute(2, false), 'tipo inexistente');
    }

    public function test_lugares_para_imprimir_el_inventario(): void
    {
        $r = $this->lugaresDlb()->execute(0, true);

        $this->assertSame('Para imprimir inventario', $r['nombreDoc']);
        $this->assertSame(['id' => 20, 'select' => ''], $r['a_valores'][40][1]['sel']);
    }

    public function test_centros_sin_lugares_ordenados_sin_acentos(): void
    {
        $r = $this->lugaresDlb()->centros(1, false);

        $this->assertSame('B (Carta)', $r['nombreDoc']);
        $this->assertSame([['sel' => 3, 1 => 'Albacete'], ['sel' => 1, 1 => 'Ávila'], ['sel' => 2, 1 => 'Burgos']], $r['a_valores']);
        $this->assertSame('Para imprimir inventario', $this->lugaresDlb()->centros(0, true)['nombreDoc']);
        $this->assertSame([], $this->lugaresDlb()->centros(2, false));
    }
}
