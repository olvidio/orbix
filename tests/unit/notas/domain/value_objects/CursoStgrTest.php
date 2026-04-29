<?php

declare(strict_types=1);

namespace Tests\unit\notas\domain\value_objects;

use src\actividades\domain\value_objects\NivelStgrId;
use src\notas\domain\value_objects\CursoStgr;
use Tests\myTest;

final class CursoStgrTest extends myTest
{
    public function test_rango_niveles(): void
    {
        $this->assertSame([1100, 1300], CursoStgr::BIENIO->rangoNiveles());
        $this->assertSame([2100, 2113], CursoStgr::C1->rangoNiveles());
    }

    public function test_niveles_stgr_includes_expected_constants(): void
    {
        $this->assertSame([NivelStgrId::B], CursoStgr::BIENIO->nivelesStgr());
        $this->assertSame([NivelStgrId::C1], CursoStgr::C1->nivelesStgr());
    }
}
