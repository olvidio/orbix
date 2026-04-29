<?php

declare(strict_types=1);

namespace Tests\unit\cartaspresentacion\domain\value_objects;

use src\cartaspresentacion\domain\value_objects\PresentacionPk;
use Tests\myTest;

final class PresentacionPkTest extends myTest
{
    public function test_from_array_and_getters(): void
    {
        $pk = PresentacionPk::fromArray(['id_ubi' => 500, 'id_direccion' => 30]);
        $this->assertSame(500, $pk->idUbi());
        $this->assertSame(30, $pk->idDireccion());
    }

    public function test_equals_and_to_string(): void
    {
        $a = new PresentacionPk(1, 2);
        $b = new PresentacionPk(1, 2);
        $this->assertTrue($a->equals($b));
        $this->assertSame('1:2', (string) $a);
    }
}
