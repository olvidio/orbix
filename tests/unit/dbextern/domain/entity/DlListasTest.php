<?php

declare(strict_types=1);

namespace Tests\unit\dbextern\domain\entity;

use src\dbextern\domain\entity\DlListas;
use Tests\myTest;

final class DlListasTest extends myTest
{
    public function test_set_and_get_campos(): void
    {
        $o = new DlListas();
        $o->setDl('dlb');
        $o->setNombre_dl('Barcelona');
        $o->setAbr_r('mad');
        $o->setNumero_dl(12);
        $o->setNumero_r(3);

        $this->assertSame('dlb', $o->getDl());
        $this->assertSame('Barcelona', $o->getNombre_dl());
        $this->assertSame('mad', $o->getAbr_r());
        $this->assertSame(12, $o->getNumero_dl());
        $this->assertSame(3, $o->getNumero_r());
    }

    public function test_from_array_hidrata_como_filas_de_la_bdu(): void
    {
        $o = DlListas::fromArray(['dl' => 'dlp', 'nombre_dl' => 'Pamplona', 'abr_r' => 'esp', 'numero_dl' => 5, 'numero_r' => 1]);

        $this->assertSame('dlp', $o->getDl());
        $this->assertSame('Pamplona', $o->getNombre_dl());
        $this->assertSame('esp', $o->getAbr_r());
        $this->assertSame(5, $o->getNumero_dl());
        $this->assertSame(1, $o->getNumero_r());
    }
}
