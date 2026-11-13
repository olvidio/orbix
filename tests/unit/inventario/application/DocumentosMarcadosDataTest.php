<?php

declare(strict_types=1);

namespace Tests\unit\inventario\application;

use PHPUnit\Framework\TestCase;
use src\inventario\application\DocumentosMarcadosData;
use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\inventario\domain\entity\Documento;
use src\inventario\domain\entity\Lugar;
use src\inventario\domain\entity\TipoDoc;
use src\inventario\domain\entity\UbiInventario;
use src\shared\domain\value_objects\DateTimeLocal;
use Tests\support\DescribeCriteria;

/** Documentos perdidos / en búsqueda ({@see DocumentosMarcadosData}): columnas, orden por centro sin acentos y saltos. */
final class DocumentosMarcadosDataTest extends TestCase
{
    /** @var list<list<string>> */
    private array $consultas = [];

    private function uc(): DocumentosMarcadosData
    {
        $docs = $this->createMock(DocumentoRepositoryInterface::class);
        $docs->method('matching')->willReturnCallback(function ($c): array {
            $this->consultas[] = DescribeCriteria::filtros($c);
            $lista = [];
            foreach ([[1, 2, null, 12], [2, 1, 20, 7], [9, 1, null, null], [3, 9, null, null], [4, 1, 66, null]] as [$ubi, $tipo, $lugar, $reg]) {
                $d = new Documento();
                $d->setId_doc(count($lista) + 1);
                $d->setId_ubi($ubi);
                $d->setId_tipo_doc($tipo);
                $d->setId_lugar($lugar);
                $d->setNum_reg($reg);
                $d->setF_perdido(new DateTimeLocal('2026-03-01'));
                $lista[] = $d;
            }
            return $lista;
        });
        $tipos = $this->createMock(TipoDocRepositoryInterface::class);
        $tipos->method('findById')->willReturnCallback(static function (int $id): ?TipoDoc {
            if ($id === 9) {
                return null;
            }
            $t = new TipoDoc();
            $t->setId_tipo_doc($id);
            $t->setSigla($id === 1 ? 'B' : 'C');
            $t->setNom_doc($id === 1 ? 'Carta' : null);
            return $t;
        });
        $ubis = $this->createMock(UbiInventarioRepositoryInterface::class);
        $ubis->method('findById')->willReturnCallback(static function (int $id): ?UbiInventario {
            if ($id === 9) {
                return null;
            }
            $u = new UbiInventario();
            $u->setId_ubi($id);
            $u->setNom_ubi([1 => 'Ávila', 2 => 'Albacete', 3 => 'Burgos', 4 => 'Cuenca'][$id]);
            return $u;
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

        return new DocumentosMarcadosData($docs, $tipos, $ubis, $lugares);
    }

    public function test_perdidos_con_fecha_y_orden_por_centro_sin_acentos(): void
    {
        $r = $this->uc()->perdidos();

        $this->assertSame([['perdido eq true']], $this->consultas);
        $this->assertSame([
            [1 => 'Albacete --> Archivo', 2 => 'B (Carta)', 3 => 7, 4 => '1/3/2026'],
            [1 => 'Ávila', 2 => 'C', 3 => 12, 4 => '1/3/2026'],
        ], $r['a_valores'], 'sin el de centro, tipo o lugar inexistente');
    }

    public function test_en_busqueda_sin_fecha(): void
    {
        $r = $this->uc()->enBusqueda();

        $this->assertSame([['en_busqueda eq true']], $this->consultas);
        $this->assertSame([1, 2, 3], array_keys($r['a_valores'][0]));
    }
}
