<?php

declare(strict_types=1);

namespace Tests\unit\notas\application;

use PHPUnit\Framework\TestCase;
use src\notas\application\DatosActa;
use src\notas\domain\entity\PersonaNota;
use src\notas\domain\value_objects\NotaSituacion;

final class DatosActaTest extends TestCase
{
    public function test_entra_en_impresion_con_nota_numerica(): void
    {
        $nota = new PersonaNota();
        $nota->setIdSituacionVo(NotaSituacion::EXAMINADO);
        $nota->setNota_num(4.0);
        $this->assertTrue(DatosActa::entraEnImpresion($nota));
    }

    public function test_entra_en_impresion_exento_sin_numero(): void
    {
        $nota = new PersonaNota();
        $nota->setIdSituacionVo(NotaSituacion::EXENTO);
        $nota->setNota_num(null);
        $this->assertTrue(DatosActa::entraEnImpresion($nota));
    }

    public function test_no_entra_sin_nota(): void
    {
        $nota = new PersonaNota();
        $nota->setIdSituacionVo(NotaSituacion::CURSADA);
        $nota->setNota_num(null);
        $this->assertFalse(DatosActa::entraEnImpresion($nota));
    }
}
