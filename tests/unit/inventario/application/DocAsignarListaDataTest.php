<?php

declare(strict_types=1);

namespace Tests\unit\inventario\application;

use PHPUnit\Framework\TestCase;
use src\inventario\application\DocAsignarListaData;
use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\inventario\domain\entity\Documento;
use src\inventario\domain\entity\Lugar;
use src\inventario\domain\entity\TipoDoc;
use src\inventario\domain\entity\UbiInventario;
use src\shared\security\HashB;
use Tests\support\DescribeCriteria;

/**
 * Filas para asignar un tipo de documento a centros o lugares ({@see DocAsignarListaData}): número
 * siguiente de los tipos numerados (sin contar los documentos sin número: antes un nulo dejaba el
 * «último» en 0), ejemplares de los no numerados, y filas neutras sin HTML.
 */
final class DocAsignarListaDataTest extends TestCase
{
    /** @var list<list<string>> */
    private array $consultas = [];

    private function uc(): DocAsignarListaData
    {
        $tipos = $this->createMock(TipoDocRepositoryInterface::class);
        $tipos->method('findById')->willReturnCallback(static function (int $id): ?TipoDoc {
            if ($id > 2) {
                return null;
            }
            $t = new TipoDoc();
            $t->setId_tipo_doc($id);
            $t->setSigla('B');
            $t->setNom_doc($id === 1 ? 'Carta' : null);
            $t->setNumerado($id === 1);
            return $t;
        });
        $docs = $this->createMock(DocumentoRepositoryInterface::class);
        $docs->method('matching')->willReturnCallback(function ($c): array {
            $f = DescribeCriteria::filtros($c);
            $this->consultas[] = $f;
            $d = new Documento();
            $d->setId_doc(1);
            $d->setId_tipo_doc(1);
            $d->setId_ubi(6);
            if (in_array('num_reg is_not_null', $f, true)) {
                $d->setNum_reg(904);
                return [$d];
            }
            if (in_array('id_ubi eq 6', $f, true)) {
                $d->setNum_ejemplares(3);
                return [$d];
            }
            return [];
        });
        $ubi = static function (int $id): UbiInventario {
            $u = new UbiInventario();
            $u->setId_ubi($id);
            $u->setNom_ubi("Centro $id");
            return $u;
        };
        $ubis = $this->createMock(UbiInventarioRepositoryInterface::class);
        $ubis->method('matching')->willReturnCallback(static fn (): array => [$ubi(5), $ubi(6), $ubi(7)]);
        $ubis->method('findById')->willReturnCallback(static fn (int $id): ?UbiInventario => $id === 99 ? null : $ubi($id));
        $lugares = $this->createMock(LugarRepositoryInterface::class);
        $lugares->method('findById')->willReturnCallback(static function (int $id): ?Lugar {
            if ($id === 66) {
                return null;
            }
            $l = new Lugar();
            $l->setId_lugar($id);
            $l->setId_ubi($id === 21 ? 99 : 6);
            $l->setNom_lugar("Lugar $id");
            return $l;
        });

        return new DocAsignarListaData($tipos, $docs, $ubis, $lugares);
    }

    public function test_tipo_numerado_propone_los_numeros_siguientes_al_mayor_con_numero(): void
    {
        $r = $this->uc()->paraCentros(1, ['7', '6']);

        $this->assertSame([['id_tipo_doc eq 1', 'num_reg is_not_null']], $this->consultas);
        $this->assertSame([
            ['id' => 6, 'nombre' => 'Centro 6', 'num' => 905],
            ['id' => 7, 'nombre' => 'Centro 7', 'num' => 906],
        ], $r['filas'], 'en el orden de los centros, no en el de la selección');
        $this->assertSame(['B (Carta)', true], [$r['nombreDoc'], $r['isNumerado']]);
        $this->assertSame(['id_tipo_doc' => 1], HashB::open($r['ctx_guardar'], 'doc_asignar_ctr_guardar'));
    }

    public function test_tipo_no_numerado_propone_los_ejemplares_que_ya_tiene_el_centro_o_uno(): void
    {
        $r = $this->uc()->paraCentros(2, [5, 6]);

        $this->assertSame([['id' => 5, 'nombre' => 'Centro 5', 'num' => 1], ['id' => 6, 'nombre' => 'Centro 6', 'num' => 3]], $r['filas']);
        $this->assertSame(['B', false], [$r['nombreDoc'], $r['isNumerado']]);
    }

    public function test_para_lugares_usa_el_id_del_lugar_y_salta_los_que_no_existen(): void
    {
        $r = $this->uc()->paraLugares(1, [20, 66, 21, 'x', 22]);

        $this->assertSame([
            ['id' => 20, 'nombre' => 'Centro 6 - Lugar 20', 'num' => 905],
            ['id' => 22, 'nombre' => 'Centro 6 - Lugar 22', 'num' => 906],
        ], $r['filas']);
        $this->assertSame(['id_tipo_doc' => 1], HashB::open($r['ctx_guardar'], 'doc_asignar_dlb_guardar'));
        $this->assertSame([], $this->uc()->paraLugares(3, [20]), 'tipo inexistente');
        $this->assertSame([], $this->uc()->paraCentros(3, [6]));
    }
}
