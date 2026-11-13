<?php

declare(strict_types=1);

namespace Tests\unit\inventario\application;

use PHPUnit\Framework\TestCase;
use src\inventario\application\EquipajeDocCasaData;
use src\inventario\domain\contracts\ColeccionRepositoryInterface;
use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\inventario\domain\entity\Coleccion;
use src\inventario\domain\entity\Documento;
use src\inventario\domain\entity\Equipaje;
use src\inventario\domain\entity\Lugar;
use src\inventario\domain\entity\TipoDoc;
use src\inventario\domain\entity\UbiInventario;

/**
 * Documentos de la casa de un equipaje ({@see EquipajeDocCasaData}): orden (lugar; con identificador y
 * sin agrupar, cartas, el resto, sin colección), nombre con ejemplares y observaciones, equipaje sin casa
 * (antes excepción) y tipo con una colección inexistente (antes aviso de PHP).
 */
final class EquipajeDocCasaDataTest extends TestCase
{
    private ?int $ubiActiv = 3001;
    /** @var list<Documento> */
    private array $docs = [];

    private function doc(int $tipo, ?string $ident = null, ?int $ej = null, ?string $observ = null, ?int $lugar = null): void
    {
        $d = new Documento();
        $d->setId_doc(count($this->docs) + 1);
        $d->setId_tipo_doc($tipo);
        $d->setId_ubi(1);
        $d->setIdentificador($ident);
        $d->setNum_ejemplares($ej);
        $d->setObserv($observ);
        $d->setId_lugar($lugar);
        $this->docs[] = $d;
    }

    /** @return array<string, mixed> */
    private function ejecutar(int $idEquipaje = 5): array
    {
        $equipajes = $this->createMock(EquipajeRepositoryInterface::class);
        $equipajes->method('findById')->willReturnCallback(function (int $id): ?Equipaje {
            if ($id !== 5) {
                return null;
            }
            $e = new Equipaje();
            $e->setId_equipaje(5);
            $e->setLugar('Casa de convivencias');
            $e->setId_ubi_activ($this->ubiActiv);
            return $e;
        });
        $ubis = $this->createMock(UbiInventarioRepositoryInterface::class);
        $ubis->method('matching')->willReturnCallback(static function (): array {
            $u = new UbiInventario();
            $u->setId_ubi(1);
            $u->setNom_ubi('Casa X');
            return [$u];
        });
        $colecciones = $this->createMock(ColeccionRepositoryInterface::class);
        $colecciones->method('matching')->willReturnCallback(static function (): array {
            return array_map(static function (array $c): Coleccion {
                $o = new Coleccion();
                $o->setId_coleccion($c[0]);
                $o->setNom_coleccion($c[1]);
                $o->setAgrupar($c[2]);
                return $o;
            }, [[7, 'Cartas', true], [8, 'Libros', false]]);
        });
        $docs = $this->createMock(DocumentoRepositoryInterface::class);
        $docs->method('matching')->willReturnCallback(fn (): array => $this->docs);
        $tipos = $this->createMock(TipoDocRepositoryInterface::class);
        $tipos->method('findById')->willReturnCallback(static function (int $id): ?TipoDoc {
            $cols = [1 => 7, 2 => 8, 3 => null, 4 => 99];
            if (!array_key_exists($id, $cols)) {
                return null;
            }
            $col = $cols[$id];
            $t = new TipoDoc();
            $t->setId_tipo_doc($id);
            $t->setSigla("S$id");
            $t->setNom_doc("Doc $id");
            $t->setId_coleccion($col);
            return $t;
        });
        $lugares = $this->createMock(LugarRepositoryInterface::class);
        $lugares->method('findById')->willReturnCallback(static function (int $id): ?Lugar {
            if ($id !== 20) {
                return null;
            }
            $l = new Lugar();
            $l->setId_lugar(20);
            $l->setId_ubi(1);
            $l->setNom_lugar('Armario');
            return $l;
        });

        return (new EquipajeDocCasaData($equipajes, $ubis, $colecciones, $docs, $tipos, $lugares))->execute($idEquipaje);
    }

    public function test_documentos_de_la_casa_en_orden(): void
    {
        $this->doc(3);                         // sin colección
        $this->doc(1, null, 3, 'vol. 1');      // carta, con ejemplares y observaciones
        $this->doc(2, 'L-1');                  // libro con identificador
        $this->doc(2);                         // libro sin identificador
        $this->doc(4);                         // colección inexistente
        $this->doc(9);                         // tipo inexistente
        $this->doc(2, null, null, null, 66);   // lugar inexistente

        $r = $this->ejecutar();

        $this->assertSame(['Casa X', 1], [$r['nombre_ubi'], $r['id_ubi']]);
        $this->assertSame([
            'S3 Doc 3 ',
            'S2 Doc 2 ',
            'S4 Doc 4 ',
            '3 ejemplares de S1 Doc 1 vol. 1',
            'S2 Doc 2 ',
        ], array_column($r['a_valores'], 'nombre'), 'orden descendente: sin colección (4), resto (3), cartas (2), con identificador (1)');
        $carta = $r['a_valores'][3];
        $this->assertSame([3, 7, true], [$carta['ejemplares'], $carta['coleccion'], $carta['carta']]);
        $this->assertSame('L-1', $r['a_valores'][4]['identificador']);
    }

    public function test_equipaje_sin_casa_o_inexistente(): void
    {
        $this->ubiActiv = null;
        $this->assertSame(['a_valores' => [], 'nombre_ubi' => 'Casa de convivencias', 'id_ubi' => null], $this->ejecutar());
        $this->assertSame([], $this->ejecutar(6));
    }
}
