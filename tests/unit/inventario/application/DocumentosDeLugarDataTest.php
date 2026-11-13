<?php

declare(strict_types=1);

namespace Tests\unit\inventario\application;

use PHPUnit\Framework\TestCase;
use src\inventario\application\DocumentosDeLugarData;
use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\entity\Documento;
use src\inventario\domain\entity\Lugar;
use src\inventario\domain\entity\TipoDoc;
use src\shared\security\HashB;

/** Documentos de un lugar usado como maleta ({@see DocumentosDeLugarData}): filas marcadas y cápsulas según los ids que llegan. */
final class DocumentosDeLugarDataTest extends TestCase
{
    private function uc(): DocumentosDeLugarData
    {
        $docs = $this->createMock(DocumentoRepositoryInterface::class);
        $docs->method('matching')->willReturnCallback(static function (): array {
            return array_map(static function (array $x): Documento {
                $d = new Documento();
                $d->setId_doc($x[0]);
                $d->setId_tipo_doc($x[1]);
                $d->setId_ubi(1);
                $d->setIdentificador($x[2]);
                $d->setNum_reg($x[3]);
                return $d;
            }, [[11, 1, 'a', 5], [12, 9, null, null], [13, 1, null, 7]]);
        });
        $lugares = $this->createMock(LugarRepositoryInterface::class);
        $lugares->method('findById')->willReturnCallback(static function (int $id): ?Lugar {
            if ($id !== 20) {
                return null;
            }
            $l = new Lugar();
            $l->setId_lugar(20);
            $l->setId_ubi(1);
            $l->setNom_lugar('Maleta azul');
            return $l;
        });
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

        return new DocumentosDeLugarData($docs, $lugares, $tipos);
    }

    public function test_documentos_marcados_y_las_tres_capsulas(): void
    {
        $r = $this->uc()->execute(20, 2, 5, 900);

        $this->assertSame('Maleta azul', $r['nombre_valija']);
        $this->assertSame([1, 3], array_keys($r['a_valores']), 'el de tipo inexistente deja su hueco, como antes');
        $this->assertSame(['sel' => ['id' => 11, 'select' => 'checked'], 1 => 'B Carta', 2 => 'a', 3 => 5], $r['a_valores'][1]);
        $this->assertSame(['id_grupo' => 2, 'id_equipaje' => 5, 'id_lugar' => 20], HashB::open($r['ctx_update_grupo'], 'equipajes_update_grupo'));
        $this->assertSame(['id_item_egm' => 900], HashB::open($r['ctx_add'], 'equipajes_add_doc'));
        $this->assertSame(['id_grupo' => 2, 'id_equipaje' => 5], HashB::open($r['ctx_eliminar_grupo'], 'equipajes_eliminar_grupo'));
    }

    public function test_sin_ids_no_hay_capsulas_y_lugar_inexistente_no_lista(): void
    {
        $r = $this->uc()->execute(20, 0, 0, 0);
        $this->assertSame(['', '', ''], [$r['ctx_update_grupo'], $r['ctx_add'], $r['ctx_eliminar_grupo']]);
        $this->assertCount(2, $r['a_valores']);

        $r = $this->uc()->execute(66, 2, 5, 0);
        $this->assertSame([[], ''], [$r['a_valores'], $r['nombre_valija']]);
        $this->assertNotSame('', $r['ctx_update_grupo']);
    }
}
