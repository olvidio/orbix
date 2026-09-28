<?php

declare(strict_types=1);

namespace Tests\unit\dbextern\domain;

use PHPUnit\Framework\TestCase;
use src\dbextern\domain\NumerosTelecoListas;

final class NumerosTelecoListasTest extends TestCase
{
    public function test_parte_dos_correos_separados_por_coma(): void
    {
        $partes = NumerosTelecoListas::partes(
            'delcastillo@vilallonga-abogados.com, delcastilloubedajulian@gmail.com'
        );

        $this->assertSame([
            'delcastillo@vilallonga-abogados.com',
            'delcastilloubedajulian@gmail.com',
        ], $partes);
        foreach ($partes as $parte) {
            $this->assertLessThanOrEqual(50, mb_strlen($parte));
        }
    }

    public function test_un_solo_valor_actualiza_el_registro_existente(): void
    {
        $plan = NumerosTelecoListas::plan(
            [['num' => 'antiguo@ejemplo.com', 'de_listas' => false]],
            ['nuevo@ejemplo.com'],
        );

        $this->assertSame([
            ['op' => 'update', 'index' => 0, 'num' => 'nuevo@ejemplo.com'],
        ], $plan);
    }

    public function test_dos_correos_reutilizan_el_de_listas_y_crean_el_segundo(): void
    {
        $plan = NumerosTelecoListas::plan(
            [['num' => 'antiguo@ejemplo.com', 'de_listas' => true]],
            [
                'delcastillo@vilallonga-abogados.com',
                'delcastilloubedajulian@gmail.com',
            ],
        );

        $this->assertSame([
            ['op' => 'update', 'index' => 0, 'num' => 'delcastillo@vilallonga-abogados.com'],
            ['op' => 'create', 'index' => null, 'num' => 'delcastilloubedajulian@gmail.com'],
        ], $plan);
    }

    public function test_no_duplica_si_los_dos_registros_ya_existen(): void
    {
        $plan = NumerosTelecoListas::plan(
            [
                ['num' => 'delcastilloubedajulian@gmail.com', 'de_listas' => true],
                ['num' => 'delcastillo@vilallonga-abogados.com', 'de_listas' => true],
            ],
            [
                'delcastillo@vilallonga-abogados.com',
                'delcastilloubedajulian@gmail.com',
            ],
        );

        $this->assertSame([
            ['op' => 'update', 'index' => 1, 'num' => 'delcastillo@vilallonga-abogados.com'],
            ['op' => 'update', 'index' => 0, 'num' => 'delcastilloubedajulian@gmail.com'],
        ], $plan);
    }
}
