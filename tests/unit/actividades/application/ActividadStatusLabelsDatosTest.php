<?php

declare(strict_types=1);

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\TestCase;
use src\actividades\application\ActividadStatusLabelsDatos;
use src\actividades\domain\value_objects\StatusId;

final class ActividadStatusLabelsDatosTest extends TestCase
{
    public function test_contrato_de_claves(): void
    {
        $out = (new ActividadStatusLabelsDatos())->execute(false);

        $this->assertSame(['id_to_label'], array_keys($out));
        $this->assertSame(StatusId::getArrayStatus(false), $out['id_to_label']);
    }

    public function test_with_all_cambia_mapa(): void
    {
        $sin = (new ActividadStatusLabelsDatos())->execute(false);
        $con = (new ActividadStatusLabelsDatos())->execute(true);

        $this->assertNotSame($sin['id_to_label'], $con['id_to_label']);
        $this->assertSame(StatusId::getArrayStatus(true), $con['id_to_label']);
    }
}
