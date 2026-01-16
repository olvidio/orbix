<?php

namespace Tests\unit\tablonanuncios\domain\value_objects;

use DateTimeInterface;
use src\tablonanuncios\domain\value_objects\AnuncioTAnotado;
use Tests\myTest;

class AnuncioTAnotadoTest extends myTest
{
    public function test_create_valid_anuncioTAnotado()
    {
        $anuncioTAnotado = new AnuncioTAnotado('2025-12-25', '12:30:05', null);
        $this->assertEquals('2025-12-25T12:30:05+00:00', $anuncioTAnotado->format(Datetimeinterface::W3C));
    }

}
