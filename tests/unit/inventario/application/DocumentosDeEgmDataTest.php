<?php

declare(strict_types=1);

namespace Tests\unit\inventario\application;

use PHPUnit\Framework\TestCase;
use src\inventario\application\DocumentosDeEgmData;
use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\WhereisRepositoryInterface;
use src\inventario\domain\entity\Documento;
use src\inventario\domain\entity\Egm;
use src\inventario\domain\entity\Lugar;
use src\inventario\domain\entity\TipoDoc;
use src\inventario\domain\entity\Whereis;
use src\shared\security\HashB;

/** Documentos de una maleta ({@see DocumentosDeEgmData}): orden por tipo, cápsulas y documentos con lugar inexistente. */
final class DocumentosDeEgmDataTest extends TestCase
{
    private function uc(): DocumentosDeEgmData
    {
        $egms = $this->createMock(EgmRepositoryInterface::class);
        $egms->method('findById')->willReturnCallback(static function (int $id): ?Egm {
            if ($id !== 900) {
                return null;
            }
            $m = new Egm();
            $m->setId_item(900);
            $m->setId_equipaje(5);
            $m->setId_grupo(2);
            $m->setId_lugar(20);
            return $m;
        });
        $lugares = $this->createMock(LugarRepositoryInterface::class);
        $lugares->method('findById')->willReturnCallback(static function (int $id): ?Lugar {
            $l = new Lugar();
            $l->setId_lugar($id);
            $l->setId_ubi(1);
            $l->setNom_lugar('Maleta azul');
            return $l;
        });
        $whereis = $this->createMock(WhereisRepositoryInterface::class);
        $whereis->method('matching')->willReturnCallback(static function (): array {
            return array_map(static function (int $doc): Whereis {
                $w = new Whereis();
                $w->setId_item_whereis($doc + 100);
                $w->setId_doc($doc);
                $w->setId_item_egm(900);
                return $w;
            }, [1, 2, 3, 4]);
        });
        $docs = $this->createMock(DocumentoRepositoryInterface::class);
        $docs->method('findById')->willReturnCallback(static function (int $id): ?Documento {
            if ($id === 4) {
                return null;
            }
            $d = new Documento();
            $d->setId_doc($id);
            $d->setId_tipo_doc($id === 3 ? 9 : $id);
            $d->setId_ubi(1);
            $d->setId_lugar($id === 2 ? 66 : 20); // el 2 tiene un lugar que ya no existe
            $d->setIdentificador("i$id");
            return $d;
        });
        $tipos = $this->createMock(TipoDocRepositoryInterface::class);
        $tipos->method('findById')->willReturnCallback(static function (int $id): ?TipoDoc {
            if ($id === 9) {
                return null;
            }
            $t = new TipoDoc();
            $t->setId_tipo_doc($id);
            $t->setSigla($id === 1 ? 'C' : 'B');
            $t->setNom_doc('Doc');
            return $t;
        });

        return new DocumentosDeEgmData($egms, $lugares, $whereis, $docs, $tipos);
    }

    public function test_documentos_de_la_maleta_y_capsulas(): void
    {
        $r = $this->uc()->execute(900);

        $this->assertSame('Maleta azul', $r['nombre_valija']);
        $this->assertSame([['sel' => 102, 1 => 'B Doc', 2 => 'i2'], ['sel' => 101, 1 => 'C Doc', 2 => 'i1']], $r['a_valores']);
        $this->assertSame(['id_item_egm' => 900, 'id_grupo' => 2, 'id_equipaje' => 5], HashB::open($r['ctx_del'], 'equipajes_del_doc'));
        $this->assertSame(['id_item_egm' => 900], HashB::open($r['ctx_add'], 'equipajes_add_doc'));
        $this->assertSame(['id_grupo' => 2, 'id_equipaje' => 5], HashB::open($r['ctx_eliminar_grupo'], 'equipajes_eliminar_grupo'));
    }

    public function test_maleta_inexistente(): void
    {
        $this->assertSame([], $this->uc()->execute(1));
    }
}
