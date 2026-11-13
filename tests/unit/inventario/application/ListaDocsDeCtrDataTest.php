<?php

declare(strict_types=1);

namespace Tests\unit\inventario\application;

use PHPUnit\Framework\TestCase;
use src\inventario\application\ListaDocsDeCtrData;
use src\inventario\domain\contracts\ColeccionRepositoryInterface;
use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\entity\Documento;
use src\inventario\domain\entity\Lugar;
use src\inventario\domain\entity\TipoDoc;
use src\shared\security\HashB;
use Tests\support\DescribeCriteria;

/**
 * Documentos de un centro/lugar para trasladar ({@see ListaDocsDeCtrData}): orden por lugar (antes el
 * lugar salía siempre vacío), rango de números (antes no salía), documentos con un lugar inexistente
 * (se muestran), cápsula de traslado por fila y nada si no se indica centro ni lugar (antes, todos).
 */
final class ListaDocsDeCtrDataTest extends TestCase
{
    /** @var list<list<string>> */
    private array $consultas = [];

    private static function doc(int $id, int $tipo, ?int $lugar, ?int $ini = null): Documento
    {
        $d = new Documento();
        $d->setId_doc($id);
        $d->setId_tipo_doc($tipo);
        $d->setId_ubi(1);
        $d->setId_lugar($lugar);
        $d->setNum_ini($ini);
        return $d;
    }

    private function uc(): ListaDocsDeCtrData
    {
        $docs = $this->createMock(DocumentoRepositoryInterface::class);
        $docs->method('matching')->willReturnCallback(function ($c): array {
            $this->consultas[] = DescribeCriteria::filtros($c);
            return [self::doc(1, 1, 21), self::doc(2, 1, 20, 1990), self::doc(3, 9, 20), self::doc(4, 1, 99)];
        });
        $tipos = $this->createMock(TipoDocRepositoryInterface::class);
        $tipos->method('findById')->willReturnCallback(static function (int $id): ?TipoDoc {
            if ($id !== 1) {
                return null;
            }
            $t = new TipoDoc();
            $t->setId_tipo_doc(1);
            $t->setSigla('C');
            $t->setNom_doc('Crónicas');
            return $t;
        });
        $lugares = $this->createMock(LugarRepositoryInterface::class);
        $lugares->method('findById')->willReturnCallback(static function (int $id): ?Lugar {
            if ($id === 99) {
                return null;
            }
            $l = new Lugar();
            $l->setId_lugar($id);
            $l->setId_ubi(1);
            $l->setNom_lugar($id === 20 ? 'Biblioteca' : 'Despacho');
            return $l;
        });

        return new ListaDocsDeCtrData($docs, $tipos, $this->createMock(ColeccionRepositoryInterface::class), $lugares);
    }

    public function test_documentos_del_centro_ordenados_por_lugar(): void
    {
        $r = $this->uc()->execute(7, 0);

        $this->assertSame([['id_ubi eq 7']], $this->consultas);
        $this->assertSame(['C Crónicas', 'C Crónicas 1990-Actual', 'C Crónicas'], array_map(static fn (array $f): string => $f[1]['valor'], $r['a_valores']));
        $this->assertSame(['id_doc' => 4], HashB::open($r['a_valores'][0]['sel'], 'traslado_doc_guardar'), 'primero el de lugar inexistente (vacío), luego Biblioteca y Despacho');
        $this->assertSame(['id_doc' => 2], HashB::open($r['a_valores'][1]['sel'], 'traslado_doc_guardar'));
    }

    public function test_por_lugar_y_sin_parametros(): void
    {
        $uc = $this->uc();
        $uc->execute(7, 20);
        $this->assertSame([['id_ubi eq 7', 'id_lugar eq 20']], $this->consultas);

        $this->assertSame(['a_valores' => []], $uc->execute(0, 0));
        $this->assertCount(1, $this->consultas, 'sin centro ni lugar no consulta');
    }
}
