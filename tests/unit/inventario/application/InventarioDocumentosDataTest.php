<?php

declare(strict_types=1);

namespace Tests\unit\inventario\application;

use PHPUnit\Framework\TestCase;
use src\inventario\application\InventarioDocumentosData;
use src\inventario\domain\contracts\ColeccionRepositoryInterface;
use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\inventario\domain\entity\Coleccion;
use src\inventario\domain\entity\Documento;
use src\inventario\domain\entity\Lugar;
use src\inventario\domain\entity\TipoDoc;
use src\inventario\domain\entity\UbiInventario;
use Tests\support\DescribeCriteria;

/**
 * Inventario de documentos para imprimir ({@see InventarioDocumentosData}), por centro y por lugar de
 * la dl: nombre compuesto del documento, columnas de cada listado, orden (lugar, bajo llave primero,
 * colección, tipo) y documentos cuyo tipo, colección o lugar no existe.
 */
final class InventarioDocumentosDataTest extends TestCase
{
    /** @var array<int, list<Documento>> por id_ubi o id_lugar */
    private array $docs = [];
    /** @var list<list<string>> */
    private array $consultas = [];
    private int $consultasTipo = 0;

    private static function doc(int $tipo, ?int $lugar = null, array $extra = []): Documento
    {
        $d = new Documento();
        $d->setId_doc(random_int(1, 99999));
        $d->setId_tipo_doc($tipo);
        $d->setId_ubi(1);
        $d->setId_lugar($lugar);
        $d->setNum_reg($extra['reg'] ?? null);
        $d->setNum_ejemplares($extra['ej'] ?? null);
        $d->setNum_ini($extra['ini'] ?? null);
        $d->setNum_fin($extra['fin'] ?? null);
        $d->setObserv($extra['observ'] ?? null);
        $d->setObserv_ctr($extra['observ_ctr'] ?? null);
        $d->setIdentificador($extra['id'] ?? null);
        return $d;
    }

    private function uc(): InventarioDocumentosData
    {
        $ubis = $this->createMock(UbiInventarioRepositoryInterface::class);
        $ubis->method('findById')->willReturnCallback(static function (int $id): ?UbiInventario {
            if ($id > 10) {
                return null;
            }
            $u = new UbiInventario();
            $u->setId_ubi($id);
            $u->setNom_ubi("Centro $id");
            return $u;
        });
        $docs = $this->createMock(DocumentoRepositoryInterface::class);
        $docs->method('matching')->willReturnCallback(function ($c): array {
            $f = DescribeCriteria::filtros($c);
            $this->consultas[] = $f;
            return $this->docs[(int) preg_replace('/\D/', '', $f[0])] ?? [];
        });
        $tipos = $this->createMock(TipoDocRepositoryInterface::class);
        $tipos->method('findById')->willReturnCallback(function (int $id): ?TipoDoc {
            $this->consultasTipo++;
            $datos = [
                1 => ['B', 'Carta', true, 7],
                2 => ['A', 'Boletín', false, 7],
                3 => ['C', null, null, null],
                4 => ['D', 'Sin colección', false, 99],
            ][$id] ?? null;
            if ($datos === null) {
                return null;
            }
            $t = new TipoDoc();
            $t->setId_tipo_doc($id);
            $t->setSigla($datos[0]);
            $t->setNom_doc($datos[1]);
            $t->setBajo_llave($datos[2]);
            $t->setId_coleccion($datos[3]);
            return $t;
        });
        $colecciones = $this->createMock(ColeccionRepositoryInterface::class);
        $colecciones->method('findById')->willReturnCallback(static function (int $id): ?Coleccion {
            if ($id !== 7) {
                return null;
            }
            $c = new Coleccion();
            $c->setId_coleccion(7);
            $c->setNom_coleccion('Cartas');
            return $c;
        });
        $lugares = $this->createMock(LugarRepositoryInterface::class);
        $lugares->method('findById')->willReturnCallback(static function (int $id): ?Lugar {
            if ($id > 50) {
                return null;
            }
            $l = new Lugar();
            $l->setId_lugar($id);
            $l->setId_ubi($id === 30 ? 99 : 1);
            $l->setNom_lugar(['20' => 'Armario', '21' => 'Archivo', '22' => 'Despacho', '30' => 'De otro centro'][(string) $id] ?? "Lugar $id");
            return $l;
        });

        return new InventarioDocumentosData($ubis, $docs, $tipos, $colecciones, $lugares);
    }

    public function test_por_centro_nombre_compuesto_columnas_y_orden(): void
    {
        $this->docs[1] = [
            self::doc(2, 21, ['observ' => 'o2']),
            self::doc(1, 21, ['reg' => 413, 'ej' => 3, 'observ_ctr' => 'oc1']),
            self::doc(1, 20, ['ini' => 5]),
            self::doc(3, 20, ['ini' => 5, 'fin' => 9]),
        ];

        $r = $this->uc()->porUbis([1, 'x', 77]);

        $this->assertSame([['id_ubi eq 1', 'eliminado eq false']], $this->consultas);
        $this->assertSame(['Centro 1'], array_keys($r['a_valores']));
        $nombres = array_map(static fn (array $f): string => $f[2]['valor'], $r['a_valores']['Centro 1']);
        $this->assertSame([
            '* B Carta, 413 (3 ej.)',
            'A Boletín',
            '* B Carta desde el 5-Actual',
            'C desde el 5- 9',
        ], $nombres, 'por lugar (Archivo < Armario), bajo llave primero y luego colección y tipo');
        $this->assertSame(['Archivo', 'Archivo', 'Armario', 'Armario'], $r['a_lugar']['Centro 1']);
        $this->assertSame(['clase' => 'check', 'valor' => '<input type=checkbox>'], $r['a_valores']['Centro 1'][0][1]);
        $this->assertSame(['oc1', null], [$r['a_valores']['Centro 1'][0][3], $r['a_valores']['Centro 1'][0][4]], 'observaciones del centro y generales');
        $this->assertSame([null, 'o2'], [$r['a_valores']['Centro 1'][1][3], $r['a_valores']['Centro 1'][1][4]]);
        $this->assertSame([0, 1, 2, 3], array_keys($r['a_valores']['Centro 1']), 'lista desde 0');
        $this->assertSame(3, $this->consultasTipo, 'el tipo 1 (con colección) se consulta una vez');
    }

    public function test_por_lugar_lleva_el_identificador_y_el_nombre_del_centro(): void
    {
        $this->docs[22] = [self::doc(2, 22, ['id' => 'ARX-1', 'observ' => 'o', 'observ_ctr' => 'oc'])];

        $r = $this->uc()->porLugares([22, 30, 60]);

        $this->assertSame([['id_lugar eq 22', 'eliminado eq false']], $this->consultas, 'el lugar de otro centro inexistente y el lugar inexistente se saltan');
        $fila = $r['a_valores']['Despacho'][0];
        $this->assertSame([['clase' => 'doc', 'valor' => 'ARX-1'], 'o', 'oc'], [$fila[3], $fila[4], $fila[5]]);
        $this->assertSame('Centro 1', $r['a_lugar']['Despacho']);
    }

    public function test_se_saltan_los_documentos_sin_tipo_sin_coleccion_o_sin_lugar_y_los_grupos_vacios(): void
    {
        $this->docs[1] = [self::doc(9), self::doc(4), self::doc(2, 60), self::doc(2)];
        $this->docs[2] = [self::doc(9)];

        $r = $this->uc()->porUbis([1, 2]);

        $this->assertSame(['Centro 1'], array_keys($r['a_valores']), 'el centro sin documentos válidos no sale');
        $this->assertSame(['A Boletín'], array_map(static fn (array $f): string => $f[2]['valor'], $r['a_valores']['Centro 1']));
        $this->assertSame([''], $r['a_lugar']['Centro 1'], 'documento sin lugar');
    }
}
