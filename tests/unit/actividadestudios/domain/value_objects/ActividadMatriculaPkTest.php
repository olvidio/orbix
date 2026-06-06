<?php

declare(strict_types=1);

namespace Tests\unit\actividadestudios\domain\value_objects;

use src\actividadestudios\domain\value_objects\ActividadMatriculaPk;
use Tests\myTest;

final class ActividadMatriculaPkTest extends myTest
{
    public function test_from_array_with_integers(): void
    {
        $pk = ActividadMatriculaPk::fromArray([
            'id_activ' => 5,
            'id_nom' => 99,
            'id_asignatura' => 7,
        ]);
        $this->assertSame(5, $pk->idActiv());
        $this->assertSame(99, $pk->idNom());
        $this->assertSame(7, $pk->idAsignatura());
    }

    public function test_to_string(): void
    {
        $a = new ActividadMatriculaPk(1, 2, 3);
        $this->assertSame('3:2:1', (string) $a);
    }
}
