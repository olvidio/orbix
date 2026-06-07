<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\value_objects\NivelStgrId;
use src\notas\application\ComprobarNotasConstantsData;
use src\notas\domain\value_objects\NotaSituacion;

final class ComprobarNotasConstantsDataTest extends TestCase
{
    public function test_execute_expone_vo_esperados(): void
    {
        $out = (new ComprobarNotasConstantsData())->execute();
        $this->assertArrayHasKey('vo', $out);
        $this->assertSame(NivelStgrId::B, $out['vo']['NivelStgrId']['B']);
        $this->assertSame(NotaSituacion::NUMERICA, $out['vo']['NotaSituacion']['NUMERICA']);
    }
}
