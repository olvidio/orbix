<?php

declare(strict_types=1);

namespace Tests\unit\pasarela\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\pasarela\application\ContribucionNoDuermeDefaultGuardar;
use src\pasarela\application\ContribucionNoDuermeExcepcionEliminar;
use src\pasarela\application\ContribucionNoDuermeExcepcionGuardar;
use src\pasarela\application\ContribucionNoDuermeLista;
use src\pasarela\application\ContribucionReservaDefaultGuardar;
use src\pasarela\application\ContribucionReservaExcepcionEliminar;
use src\pasarela\application\ContribucionReservaExcepcionGuardar;
use src\pasarela\application\ContribucionReservaLista;
use src\pasarela\domain\ContribucionNoDuerme;
use src\pasarela\domain\ContribucionReserva;
use Tests\myTest;

/**
 * Los parámetros `contribucion_no_duerme` y `contribucion_reserva` tienen casos de uso gemelos:
 * se prueban los dos con los mismos casos.
 */
final class ContribucionesTest extends myTest
{
    /** @return iterable<string, array{string}> */
    public static function parametrosProvider(): iterable
    {
        yield 'no duerme' => ['NoDuerme'];
        yield 'reserva' => ['Reserva'];
    }

    private function dominio(string $p): ContribucionNoDuerme|ContribucionReserva|MockObject
    {
        return $this->createMock($p === 'NoDuerme' ? ContribucionNoDuerme::class : ContribucionReserva::class);
    }

    private function caso(string $p, string $caso, object $dominio): object
    {
        $clase = "src\\pasarela\\application\\Contribucion{$p}{$caso}";

        return new $clase($dominio);
    }

    /** @dataProvider parametrosProvider */
    public function test_el_valor_por_defecto_exige_un_numero_entre_0_y_100(string $p): void
    {
        $d = $this->dominio($p);
        $d->expects($this->once())->method('setDefault')->with(40);
        $uc = $this->caso($p, 'DefaultGuardar', $d);

        $this->assertNotSame('', $uc->execute(''));
        $this->assertNotSame('', $uc->execute('abc'));
        $this->assertNotSame('', $uc->execute('-1'));
        $this->assertNotSame('', $uc->execute('101'));
        $this->assertSame('', $uc->execute('40'));
    }

    /** @dataProvider parametrosProvider */
    public function test_los_limites_0_y_100_son_validos_para_la_excepcion(string $p): void
    {
        $d = $this->dominio($p);
        $metodo = "addContribucion{$p}";
        $d->expects($this->exactly(2))->method($metodo)->willReturnCallback(function (): void {
        });
        $uc = $this->caso($p, 'ExcepcionGuardar', $d);

        $this->assertSame('', $uc->execute('131001', '0'));
        $this->assertSame('', $uc->execute('131001', '100'));
    }

    /** @dataProvider parametrosProvider */
    public function test_la_excepcion_valida_tipo_y_valor_antes_de_guardar(string $p): void
    {
        $d = $this->dominio($p);
        $d->expects($this->never())->method("addContribucion{$p}");
        $uc = $this->caso($p, 'ExcepcionGuardar', $d);

        $this->assertNotSame('', $uc->execute('', '10'));
        $this->assertNotSame('', $uc->execute('131001', ''));
        $this->assertNotSame('', $uc->execute('131001', 'x'));
        $this->assertNotSame('', $uc->execute('131001', '101'));
    }

    /** @dataProvider parametrosProvider */
    public function test_eliminar_exige_el_tipo_y_borra_su_excepcion(string $p): void
    {
        $d = $this->dominio($p);
        $d->expects($this->once())->method("delContribucion{$p}")->with('131001');
        $uc = $this->caso($p, 'ExcepcionEliminar', $d);

        $this->assertNotSame('', $uc->execute(''));
        $this->assertSame('', $uc->execute('131001'));
    }

    /** @dataProvider parametrosProvider */
    public function test_la_lista_devuelve_el_defecto_y_cada_excepcion_con_su_etiqueta(string $p): void
    {
        $d = $this->dominio($p);
        $d->method('getDefault')->willReturn(25);
        $d->method('getExcepciones')->willReturn(['131001' => 10, '132001' => 0]);

        $r = $this->caso($p, 'Lista', $d)->execute();

        $this->assertSame('25', $r['default']);
        $this->assertSame(['131001', '132001'], array_column($r['excepciones'], 'id_tipo_activ'));
        $this->assertSame(['10', '0'], array_column($r['excepciones'], 'valor'));
        $this->assertNotSame('', $r['excepciones'][0]['etiqueta']);
    }

    /** @dataProvider parametrosProvider */
    public function test_sin_defecto_la_lista_lo_muestra_vacio(string $p): void
    {
        $d = $this->dominio($p);
        $d->method('getDefault')->willReturn(null);
        $d->method('getExcepciones')->willReturn([]);

        $this->assertSame(['default' => '', 'excepciones' => []], $this->caso($p, 'Lista', $d)->execute());
    }
}
