<?php

declare(strict_types=1);

namespace Tests\unit\shared\infrastructure\persistence\postgresql;

use PHPUnit\Framework\TestCase;
use src\shared\infrastructure\persistence\postgresql\Condicion;

/**
 * Regresión: IN con array asociativo (p. ej. {@see NivelStgrId::getArrayNivelStgrOn()}) no debe provocar "Array to string conversion".
 */
final class CondicionInTest extends TestCase
{
    public function test_in_with_associative_int_keys_uses_keys(): void
    {
        $c = new Condicion();
        $sql = $c->getCondicion('nivel_stgr', 'IN', [1 => 'Bienio', 2 => 'Cuadrienio', 3 => 'Otro']);
        self::assertSame('nivel_stgr IN (1,2,3)', $sql);
    }

    public function test_in_with_zero_based_list_uses_values(): void
    {
        $c = new Condicion();
        $sql = $c->getCondicion('x', 'IN', [10, 20]);
        self::assertSame('x IN (10,20)', $sql);
    }

    public function test_not_in_normalizes_same_as_in(): void
    {
        $c = new Condicion();
        $sql = $c->getCondicion('nivel_stgr', 'NOT IN', [1 => 'A']);
        self::assertSame('nivel_stgr NOT IN (1)', $sql);
    }
}
