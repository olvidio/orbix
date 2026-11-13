<?php

declare(strict_types=1);

namespace Tests\unit\inventario\application;

use PHPUnit\Framework\TestCase;
use src\inventario\application\DocumentosPorTipoData;
use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\inventario\domain\entity\Documento;
use src\inventario\domain\entity\Lugar;
use src\inventario\domain\entity\TipoDoc;
use src\inventario\domain\entity\UbiInventario;
use Tests\support\DescribeCriteria;

/** Centros que tienen / no tienen un tipo de documento ({@see DocumentosPorTipoData}). */
final class DocumentosPorTipoDataTest extends TestCase
{
    private function uc(): DocumentosPorTipoData
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
        $ubi = static function (int $id): UbiInventario {
            $u = new UbiInventario();
            $u->setId_ubi($id);
            $u->setNom_ubi([1 => 'Ávila', 2 => 'Albacete', 3 => 'Burgos'][$id]);
            return $u;
        };
        $ubis = $this->createMock(UbiInventarioRepositoryInterface::class);
        $ubis->method('findById')->willReturnCallback(static fn (int $id): ?UbiInventario => $id <= 3 ? $ubi($id) : null);
        $ubis->method('matching')->willReturnCallback(static fn (): array => [$ubi(1), $ubi(2), $ubi(3)]);
        $docs = $this->createMock(DocumentoRepositoryInterface::class);
        $docs->method('matching')->willReturnCallback(static function ($c): array {
            $f = DescribeCriteria::filtros($c);
            $lista = [];
            foreach ([[1, null, 5], [2, 20, 6], [9, null, 7], [2, 66, 8]] as [$u, $l, $reg]) {
                if (isset($f[1]) && $f[1] !== "id_ubi eq $u") {
                    continue;
                }
                $d = new Documento();
                $d->setId_doc($reg);
                $d->setId_tipo_doc(1);
                $d->setId_ubi($u);
                $d->setId_lugar($l);
                $d->setNum_reg($reg);
                $lista[] = $d;
            }
            return $lista;
        });
        $lugares = $this->createMock(LugarRepositoryInterface::class);
        $lugares->method('findById')->willReturnCallback(static function (int $id): ?Lugar {
            if ($id !== 20) {
                return null;
            }
            $l = new Lugar();
            $l->setId_lugar(20);
            $l->setId_ubi(2);
            $l->setNom_lugar('Archivo');
            return $l;
        });

        return new DocumentosPorTipoData($tipos, $docs, $ubis, $lugares);
    }

    public function test_centros_que_tienen_el_documento(): void
    {
        $r = $this->uc()->asignados(1);

        $this->assertSame('B (Carta)', $r['nombreDoc']);
        $this->assertSame([[1 => 'Albacete --> Archivo', 2 => 6], [1 => 'Ávila', 2 => 5]], $r['a_valores']);
        $this->assertCount(2, $r['a_cabeceras']);
    }

    public function test_centros_que_no_lo_tienen_y_tipo_inexistente(): void
    {
        $this->assertSame([[1 => 'Burgos']], $this->uc()->noAsignados(1)['a_valores']);
        $this->assertSame([], $this->uc()->asignados(2));
        $this->assertSame([], $this->uc()->noAsignados(2));
    }
}
