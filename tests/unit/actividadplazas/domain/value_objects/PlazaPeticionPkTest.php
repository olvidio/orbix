<?php

declare(strict_types=1);

namespace Tests\unit\actividadplazas\domain\value_objects;

use src\actividadplazas\domain\value_objects\PlazaPeticionPk;
use Tests\myTest;

final class PlazaPeticionPkTest extends myTest
{
    public function test_from_array_and_getters(): void
    {
        $pk = PlazaPeticionPk::fromArray(['id_activ' => 3, 'id_nom' => 100]);
        $this->assertSame(3, $pk->idActiv());
        $this->assertSame(100, $pk->idNom());
    }

    public function test_equals_and_to_string(): void
    {
        $a = new PlazaPeticionPk(-1, 2);
        $b = new PlazaPeticionPk(-1, 2);
        $this->assertTrue($a->equals($b));
        $this->assertSame('-1:2', (string) $a);
    }
}
