<?php

declare(strict_types=1);

namespace Tests\unit\inventario\application;

use PHPUnit\Framework\TestCase;
use src\inventario\application\EquipajesMovimientosData;
use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\WhereisRepositoryInterface;
use src\inventario\domain\entity\Documento;
use src\inventario\domain\entity\Egm;
use src\inventario\domain\entity\Equipaje;
use src\inventario\domain\entity\Lugar;
use src\inventario\domain\entity\TipoDoc;
use src\inventario\domain\entity\Whereis;
use Tests\support\DescribeCriteria;

/**
 * Movimientos para preparar un equipaje ({@see EquipajesMovimientosData}): qué quitar del lugar de cada
 * maleta y qué poner (y de dónde), contando ejemplares por tipo. Antes el contador y los
 * identificadores se compartían entre tipos y entre «quitar» y «poner».
 */
final class EquipajesMovimientosDataTest extends TestCase
{
    private const MALETA = 900;
    private const LUGAR = 20;

    /** @var array<int, Documento> */
    private array $docs = [];
    /** @var list<int> documentos del lugar de la maleta, por id */
    private array $delLugar = [];
    /** @var list<int> documentos que están en la maleta */
    private array $enMaleta = [];

    private function doc(int $id, int $tipo, ?int $lugar, ?string $ident = null): void
    {
        $d = new Documento();
        $d->setId_doc($id);
        $d->setId_tipo_doc($tipo);
        $d->setId_ubi(1);
        $d->setId_lugar($lugar);
        $d->setIdentificador($ident);
        $this->docs[$id] = $d;
    }

    /** @return array<string, mixed> */
    private function ejecutar(): array
    {
        $equipajes = $this->createMock(EquipajeRepositoryInterface::class);
        $equipajes->method('findById')->willReturnCallback(static function (int $id): ?Equipaje {
            if ($id !== 5) {
                return null;
            }
            $e = new Equipaje();
            $e->setId_equipaje(5);
            $e->setNom_equipaje('Convivencia');
            return $e;
        });
        $egms = $this->createMock(EgmRepositoryInterface::class);
        $egms->method('matching')->willReturnCallback(static function (): array {
            $m = new Egm();
            $m->setId_item(self::MALETA);
            $m->setId_equipaje(5);
            $m->setId_lugar(self::LUGAR);
            return [$m];
        });
        $lugares = $this->createMock(LugarRepositoryInterface::class);
        $lugares->method('findById')->willReturnCallback(static function (int $id): ?Lugar {
            $l = new Lugar();
            $l->setId_lugar($id);
            $l->setId_ubi(1);
            $l->setNom_lugar([self::LUGAR => 'Maleta A', 21 => 'Armario', 22 => 'Archivo'][$id] ?? 'otro');
            return $l;
        });
        $docs = $this->createMock(DocumentoRepositoryInterface::class);
        $docs->method('matching')->willReturnCallback(fn (): array => array_map(fn (int $id): Documento => $this->docs[$id], $this->delLugar));
        $docs->method('findById')->willReturnCallback(fn (int $id): ?Documento => $this->docs[$id] ?? null);
        $whereis = $this->createMock(WhereisRepositoryInterface::class);
        $whereis->method('matching')->willReturnCallback(function ($c): array {
            $f = DescribeCriteria::filtros($c);
            $lista = [];
            foreach ($this->enMaleta as $id) {
                if (count($f) === 2 && $f[0] !== "id_doc eq $id") {
                    continue;
                }
                $w = new Whereis();
                $w->setId_item_whereis($id);
                $w->setId_doc($id);
                $w->setId_item_egm(self::MALETA);
                $lista[] = $w;
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
            $t->setNom_doc($id === 1 ? 'Carta' : 'Boletín');
            return $t;
        });

        return (new EquipajesMovimientosData($equipajes, $egms, $whereis, $docs, $tipos, $lugares))->execute([5, 6]);
    }

    public function test_quitar_cuenta_por_tipo_aunque_los_documentos_vengan_intercalados(): void
    {
        $this->doc(1, 1, self::LUGAR, 'x1');
        $this->doc(2, 2, self::LUGAR);
        $this->doc(3, 1, self::LUGAR, 'x3');
        $this->doc(4, 2, self::LUGAR, 'y4');
        $this->doc(5, 1, self::LUGAR); // está en la maleta
        $this->doc(6, 9, self::LUGAR); // tipo inexistente
        $this->delLugar = [1, 2, 3, 4, 5, 6];
        $this->enMaleta = [5];

        $r = $this->ejecutar();

        $this->assertSame(['5' => 'Convivencia'], array_map('strval', $r['aNomEquipajes']));
        $this->assertSame([self::MALETA => 'Maleta A'], $r['aLugaresPorEgm']);
        $this->assertSame([
            1 => '2 ejemplares de B Carta (x1,x3)',
            2 => '2 ejemplares de C Boletín (y4)',
        ], $r['aCambios'][5][self::MALETA]['out']);
        $this->assertArrayNotHasKey('in', $r['aCambios'][5][self::MALETA], 'el que está en la maleta es de su lugar');
    }

    public function test_poner_indica_el_lugar_de_origen_y_separa_los_de_lugares_distintos(): void
    {
        $this->doc(11, 1, 21, 'a');
        $this->doc(12, 1, 21, 'b');
        $this->doc(13, 1, 22);
        $this->doc(14, 2, 21);
        $this->doc(15, 2, null); // sin lugar: no se mueve
        $this->enMaleta = [11, 12, 13, 14, 15];

        $poner = $this->ejecutar()['aCambios'][5][self::MALETA]['in'];

        $this->assertSame([
            '1#20#21' => '2 ejemplares de B Carta [de Armario: (a,b)]',
            '1#20#22' => 'B Carta de Archivo',
            '2#20#21' => 'C Boletín de Armario',
        ], $poner);
    }

    public function test_un_solo_ejemplar_con_identificador(): void
    {
        $this->doc(1, 1, self::LUGAR, 'x1');
        $this->delLugar = [1];

        $this->assertSame([1 => 'B Carta (x1)'], $this->ejecutar()['aCambios'][5][self::MALETA]['out']);
    }
}
