<?php

declare(strict_types=1);

namespace Tests\unit\actividadescentro\domain\value_objects;

use src\actividadescentro\domain\value_objects\CentroEncargadoPk;
use Tests\myTest;

final class CentroEncargadoPkTest extends myTest
{
    public function test_from_array_and_getters(): void
    {
        $pk = CentroEncargadoPk::fromArray(['id_activ' => 10, 'id_ubi' => -2]);
        $this->assertSame(10, $pk->IdActiv());
        $this->assertSame(-2, $pk->IdUbi());
    }

    public function test_equals_and_to_string(): void
    {
        $a = new CentroEncargadoPk(1, 2);
        $b = new CentroEncargadoPk(1, 2);
        $this->assertTrue($a->equals($b));
        $this->assertSame('1:2', (string) $a);
    }
}
