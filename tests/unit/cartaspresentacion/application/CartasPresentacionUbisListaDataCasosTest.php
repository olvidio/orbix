<?php

declare(strict_types=1);

namespace Tests\unit\cartaspresentacion\application;

use src\cartaspresentacion\application\CartasPresentacionUbisListaData;
use src\cartaspresentacion\domain\contracts\CartaPresentacionDlRepositoryInterface;
use src\cartaspresentacion\domain\entity\CartaPresentacion;
use src\shared\security\HashB;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroExRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroDlRepositoryInterface;
use src\ubis\domain\contracts\DireccionCentroExRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroDireccionRepositoryInterface;
use src\ubis\domain\contracts\RelacionCentroExDireccionRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\CentroEx;
use src\ubis\domain\entity\Direccion;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/**
 * Lista de centros para marcar cartas de presentación ({@see CartasPresentacionUbisListaData}):
 * centros de la dl por población (anulados marcados, sede, carta sí/no con cápsula para quitarla,
 * orden por nombre) y cr/dl de fuera con una fila por dirección.
 */
final class CartasPresentacionUbisListaDataCasosTest extends myTest
{
    private const DIR_A = 100021;
    private const DIR_B = 100022;

    /** @var array<int, Direccion> */
    private array $direcciones = [];

    private function direccion(int $id, string $calle, string $poblacion, string $sede = ''): Direccion
    {
        $d = new Direccion();
        $d->setId_direccion($id);
        $d->setDireccion($calle);
        $d->setPoblacion($poblacion);
        $d->setC_p('08001');
        $d->setNom_sede($sede);
        return $this->direcciones[$id] = $d;
    }

    /** @param list<int> $conCarta claves «id_ubi#id_direccion» con carta */
    private function cartas(array $conCarta): CartaPresentacionDlRepositoryInterface
    {
        $repo = $this->createMock(CartaPresentacionDlRepositoryInterface::class);
        $repo->method('findById')->willReturnCallback(static fn (int $u, int $d): ?CartaPresentacion => in_array("$u#$d", $conCarta, true) ? new CartaPresentacion() : null);
        return $repo;
    }

    public function test_centros_de_la_dl_de_una_poblacion(): void
    {
        $dirDl = $this->createMock(DireccionCentroDlRepositoryInterface::class);
        $dirDl->method('matching')->willReturnCallback(function ($c): array {
            $this->assertSame(['poblacion matches_unaccented Sabadell'], DescribeCriteria::filtros($c));
            return [$this->direccion(self::DIR_A, 'C/ Mayor 1', 'Sabadell', 'Sede nord'), $this->direccion(self::DIR_B, 'C/ Sol 2', 'Sabadell')];
        });
        $rel = $this->createMock(RelacionCentroDireccionRepositoryInterface::class);
        $rel->method('getUbisPorDireccion')->willReturnCallback(static fn (int $d): array => $d === self::DIR_A ? [['id_ubi' => 1001], ['id_ubi' => 'x']] : [['id_ubi' => 1002], ['id_ubi' => 1999]]);
        $centros = $this->createMock(CentroDlRepositoryInterface::class);
        $centros->method('findById')->willReturnCallback(static function (int $id): ?CentroDl {
            if ($id === 1999) {
                return null;
            }
            $c = new CentroDl();
            $c->setId_ubi($id);
            $c->setNombre_ubi($id === 1001 ? 'Zeta' : 'Alfa');
            $c->setActive($id === 1001);
            return $c;
        });

        $uc = new CartasPresentacionUbisListaData($dirDl, $centros, $rel, $this->cartas(['1001#' . self::DIR_A]),
            $this->createMock(CentroExRepositoryInterface::class), $this->createMock(RelacionCentroExDireccionRepositoryInterface::class), $this->createMock(DireccionCentroExRepositoryInterface::class));
        $r = $uc->execute(['tipo_lista' => 'get_dl', 'poblacion_sel' => 'Sabadell']);

        $this->assertSame('get_dl', $r['tipo_lista']);
        $this->assertCount(2, $r['a_valores']);
        [$primera, $segunda] = array_values($r['a_valores']);
        $this->assertSame('ANULADO Alfa', $primera[2]['valor'], 'ordenado por nombre; los inactivos, marcados');
        $this->assertSame('no', $primera[3]);
        $this->assertSame('Zeta (Sede nord)', $segunda[2]['valor']);
        $this->assertSame('fnjs_modificar(' . self::DIR_A . ',1001)', $segunda[1]['script']);
        $this->assertStringStartsWith('si, ', $segunda[3]['valor']);
        $this->assertSame(1, preg_match('/fnjs_eliminar_cp\("(.+)"\)/', $segunda[3]['script3'], $m));
        $this->assertSame(['id_ubi' => 1001, 'id_direccion' => self::DIR_A], HashB::open($m[1], 'carta_presentacion_eliminar'));
        $this->assertStringContainsString('C/ Mayor 1', $segunda[4]);
        $this->assertCount(4, $r['a_cabeceras']);
    }

    /** Regresión: el contador de filas iba por centro y la segunda dirección pisaba a la primera. */
    public function test_cr_y_dl_de_fuera_una_fila_por_direccion(): void
    {
        $centros = $this->createMock(CentroExRepositoryInterface::class);
        $centros->method('matching')->willReturnCallback(function ($c): array {
            $this->assertSame(['tipo_ctr matches cr|dl', 'active eq true'], DescribeCriteria::filtros($c));
            $a = new CentroEx();
            $a->setId_ubi(-30001);
            $a->setNombre_ubi('Comisión X');
            $b = new CentroEx();
            $b->setId_ubi(-30002);
            $b->setNombre_ubi('Delegación Y');
            return [$a, $b];
        });
        $rel = $this->createMock(RelacionCentroExDireccionRepositoryInterface::class);
        $rel->method('getDireccionesPorUbi')->willReturnCallback(static fn (int $u): array|false => $u === -30001
            ? [['id_direccion' => self::DIR_A], ['id_direccion' => self::DIR_B], ['id_direccion' => 'x']]
            : false);
        $dirEx = $this->createMock(DireccionCentroExRepositoryInterface::class);
        $this->direccion(self::DIR_A, 'Via Roma 1', 'Roma');
        $this->direccion(self::DIR_B, 'Via Milano 2', 'Roma', 'Sede 2');
        $dirEx->method('findById')->willReturnCallback(fn (int $id): ?Direccion => $this->direcciones[$id] ?? null);

        $uc = new CartasPresentacionUbisListaData($this->createMock(DireccionCentroDlRepositoryInterface::class), $this->createMock(CentroDlRepositoryInterface::class),
            $this->createMock(RelacionCentroDireccionRepositoryInterface::class), $this->cartas(['-30001#' . self::DIR_B]), $centros, $rel, $dirEx);
        $r = $uc->execute(['tipo_lista' => 'get_r']);

        $this->assertSame('get_r', $r['tipo_lista']);
        $this->assertSame(['Comisión X', 'Comisión X (Sede 2)'], array_column(array_column(array_values($r['a_valores']), 2), 'valor'));
        $this->assertSame('no', array_values($r['a_valores'])[0][3]);
        $this->assertStringStartsWith('si, ', array_values($r['a_valores'])[1][3]['valor']);
        $this->assertSame('Direccion', $r['a_cabeceras'][3]['name']);
    }
}
