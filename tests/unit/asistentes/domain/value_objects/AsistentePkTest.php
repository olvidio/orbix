<?php

declare(strict_types=1);

namespace Tests\unit\asistentes\domain\value_objects;

use src\asistentes\domain\value_objects\AsistentePk;
use Tests\myTest;

final class AsistentePkTest extends myTest
{
    public function test_from_array_and_getters(): void
    {
        $pk = AsistentePk::fromArray(['id_activ' => 7, 'id_nom' => 200]);
        $this->assertSame(7, $pk->idActiv());
        $this->assertSame(200, $pk->idNom());
    }

    public function test_equals_and_to_string(): void
    {
        $a = new AsistentePk(1, 2);
        $b = new AsistentePk(1, 2);
        $this->assertTrue($a->equals($b));
        $this->assertSame('1:2', (string) $a);
    }
}
