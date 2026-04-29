<?php

declare(strict_types=1);

namespace Tests\unit\actividadestudios\domain\value_objects;

use src\actividadestudios\domain\value_objects\ActividadAsignaturaPk;
use Tests\myTest;

final class ActividadAsignaturaPkTest extends myTest
{
    public function test_from_array_and_getters(): void
    {
        $pk = ActividadAsignaturaPk::fromArray(['id_activ' => 8, 'id_asignatura' => 12]);
        $this->assertSame(8, $pk->IdActiv());
        $this->assertSame(12, $pk->IdAsignatura());
    }

    public function test_equals_and_to_string(): void
    {
        $a = new ActividadAsignaturaPk(1, 4);
        $b = new ActividadAsignaturaPk(1, 4);
        $this->assertTrue($a->equals($b));
        $this->assertSame('1:4', (string) $a);
    }
}
