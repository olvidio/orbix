<?php

declare(strict_types=1);

namespace Tests\unit\inventario\application;

use PHPUnit\Framework\TestCase;
use src\inventario\application\DocumentosLibresData;
use src\inventario\application\EquipajeEgmData;
use src\inventario\application\MaletasPosiblesData;
use src\inventario\domain\contracts\ColeccionRepositoryInterface;
use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\WhereisRepositoryInterface;
use src\inventario\domain\entity\Coleccion;
use src\inventario\domain\entity\Documento;
use src\inventario\domain\entity\Egm;
use src\inventario\domain\entity\Equipaje;
use src\inventario\domain\entity\Lugar;
use src\inventario\domain\entity\TipoDoc;
use src\inventario\domain\entity\Whereis;
use src\inventario\domain\ListaDocsGrupo;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\security\HashB;
use Tests\support\DescribeCriteria;

/**
 * Maletas de un equipaje con sus documentos ({@see EquipajeEgmData} + {@see ListaDocsGrupo}) y maletas
 * del almacén que se pueden añadir ({@see MaletasPosiblesData}).
 */
final class EquipajeEgmYMaletasTest extends TestCase
{
    private static function lugar(int $id, string $nom): Lugar
    {
        $l = new Lugar();
        $l->setId_lugar($id);
        $l->setId_ubi(40);
        $l->setNom_lugar($nom);
        return $l;
    }

    private static function egm(int $item, ?int $grupo, ?int $lugar): Egm
    {
        $m = new Egm();
        $m->setId_item($item);
        $m->setId_equipaje(5);
        $m->setId_grupo($grupo);
        $m->setId_lugar($lugar);
        return $m;
    }

    public function test_maletas_del_equipaje_con_sus_documentos_ordenados(): void
    {
        $egms = $this->createMock(EgmRepositoryInterface::class);
        $egms->method('matching')->willReturnCallback(static function ($c): array {
            $f = DescribeCriteria::filtros($c);
            // sin grupo: todas las maletas del equipaje; con grupo: la de ese grupo
            return count($f) === 1 ? [self::egm(900, 1, 20), self::egm(901, null, 21)] : [self::egm(900, 1, 20)];
        });
        $lugares = $this->createMock(LugarRepositoryInterface::class);
        $lugares->method('findById')->willReturnCallback(static fn (int $id): ?Lugar => $id === 99 ? null : self::lugar($id, $id === 20 ? 'Maleta A' : 'Armario'));
        $colecciones = $this->createMock(ColeccionRepositoryInterface::class);
        $colecciones->method('matching')->willReturnCallback(static function (): array {
            $c = new Coleccion();
            $c->setId_coleccion(7);
            $c->setNom_coleccion('Cartas');
            $c->setAgrupar(true);
            return [$c];
        });
        $whereis = $this->createMock(WhereisRepositoryInterface::class);
        $whereis->method('matching')->willReturnCallback(static function (): array {
            return array_map(static function (int $doc): Whereis {
                $w = new Whereis();
                $w->setId_item_whereis($doc + 100);
                $w->setId_doc($doc);
                $w->setId_item_egm(900);
                return $w;
            }, [1, 2, 3]);
        });
        $docs = $this->createMock(DocumentoRepositoryInterface::class);
        $docs->method('findById')->willReturnCallback(static function (int $id): Documento {
            $d = new Documento();
            $d->setId_doc($id);
            $d->setId_ubi(40);
            $d->setId_tipo_doc($id === 1 ? 1 : 2);   // 1: carta (colección que se agrupa); 2: sin colección
            $d->setId_lugar($id === 3 ? 21 : 20);    // el 3 viene de otro lugar
            $d->setIdentificador($id === 2 ? null : "i$id");
            $d->setNum_ejemplares($id === 2 ? 4 : null);
            return $d;
        });
        $tipos = $this->createMock(TipoDocRepositoryInterface::class);
        $tipos->method('findById')->willReturnCallback(static function (int $id): TipoDoc {
            $t = new TipoDoc();
            $t->setId_tipo_doc($id);
            $t->setSigla($id === 1 ? 'B' : 'L');
            $t->setNom_doc($id === 1 ? 'Carta' : 'Libro');
            $t->setId_coleccion($id === 1 ? 7 : null);
            return $t;
        });
        $lista = new ListaDocsGrupo($colecciones, $lugares, $tipos, $docs, $whereis, $egms);

        $r = (new EquipajeEgmData($egms, $lugares, $lista))->execute(5);

        $this->assertSame([1, 2], array_keys($r['a_egm']));
        $maleta = $r['a_egm'][1];
        $this->assertSame([1, 20, 'Maleta A', 900], [$maleta['id_grupo'], $maleta['id_lugar'], $maleta['nom_lugar'], $maleta['id_item_egm']]);
        $this->assertSame(
            ['L Libro ', '4 ejemplares de L Libro ', 'B Carta '],
            array_column($maleta['a_valores'], 'nombre'),
            'con identificador y sin agrupar, sin identificador ni colección, cartas'
        );
        $this->assertSame('de Armario: i3', $maleta['a_valores'][0]['identificador'], 'el que viene de otro lugar lo indica');
        $this->assertSame(['id_item_egm' => 900], HashB::open($maleta['ctx_add'], 'equipajes_add_doc'));
        $this->assertSame(['id_grupo' => 1, 'id_equipaje' => 5], HashB::open($maleta['ctx_eliminar_grupo'], 'equipajes_eliminar_grupo'));
        $this->assertSame(['id_grupo' => null, 'id_lugar' => 21, 'texto' => null, 'nom_lugar' => 'Armario'], $r['a_egm'][2], 'maleta sin grupo: sin documentos ni cápsulas');
    }

    public function test_maletas_del_almacen_que_no_estan_en_equipajes_coincidentes(): void
    {
        $equipajes = $this->createMock(EquipajeRepositoryInterface::class);
        $equipajes->method('findById')->willReturnCallback(static function (int $id): ?Equipaje {
            if ($id !== 5) {
                return null;
            }
            $e = new Equipaje();
            $e->setId_equipaje(5);
            $e->setF_ini(new DateTimeLocal('2026-07-01'));
            $e->setF_fin(new DateTimeLocal('2026-07-10'));
            return $e;
        });
        $equipajes->expects($this->once())->method('getEquipajesCoincidentes')->with('2026-07-01', '2026-07-10')->willReturn([5, 6]);
        $egms = $this->createMock(EgmRepositoryInterface::class);
        $egms->method('getArrayIdFromIdEquipajes')->with([5, 6], 'lugar')->willReturn(['21']);
        $egms->method('getUltimoGrupo')->with(5)->willReturn(3);
        $lugares = $this->createMock(LugarRepositoryInterface::class);
        $lugares->method('matching')->willReturnCallback(function ($c): array {
            $this->assertSame(['id_ubi eq ' . DocumentosLibresData::ID_UBI_ALMACEN], DescribeCriteria::filtros($c));
            return [self::lugar(22, 'Maleta C'), self::lugar(21, 'Maleta B'), self::lugar(20, 'Maleta A')];
        });
        $uc = new MaletasPosiblesData($equipajes, $egms, $lugares);

        $r = $uc->execute(5);

        $this->assertSame([20 => 'Maleta A', 22 => 'Maleta C', 1 => 'nuevo'], $r['a_opciones'], 'sin la ocupada, por nombre, con «nuevo»');
        $this->assertSame(4, $r['new_id_grupo']);
        $this->assertSame([], $uc->execute(6));
    }
}
