<?php

declare(strict_types=1);

namespace Tests\unit\dbextern\domain;

use PHPUnit\Framework\TestCase;
use src\dbextern\domain\entity\PersonaBDU;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Fecha de nacimiento de la BDU: se guarda como `j/m/Y` y se lee igual con
 * cualquier idioma de interfaz (antes se leía con el formato del idioma: en
 * inglés cambiaba día y mes, o desbordaba a otro año).
 */
final class PersonaBDUFechaNacimientoTest extends TestCase
{
    private mixed $sesion = null;

    protected function setUp(): void
    {
        $this->sesion = $_SESSION['session_auth'] ?? null;
    }

    protected function tearDown(): void
    {
        if ($this->sesion === null) {
            unset($_SESSION['session_auth']);
        } else {
            $_SESSION['session_auth'] = $this->sesion;
        }
    }

    public function test_la_fecha_de_nacimiento_no_depende_del_idioma(): void
    {
        foreach (['es_ES.UTF-8', 'en_US.UTF-8', 'ca_ES.UTF-8'] as $idioma) {
            $_SESSION['session_auth'] = ['idioma' => $idioma];
            $p = new PersonaBDU();
            $p->setFecha_Naci('1980-04-25');
            $this->assertSame('25/04/1980', $p->getFecha_Naci(), $idioma);
            $this->assertSame('1980-04-25', $p->getFechaNacimientoVo()?->format('Y-m-d'), $idioma);

            $p->setFecha_Naci(new DateTimeLocal('1975-11-03'));
            $this->assertSame('1975-11-03', $p->getFechaNacimientoVo()?->format('Y-m-d'), "$idioma, desde objeto");
        }
    }

    public function test_vacia_o_invalida(): void
    {
        $p = new PersonaBDU();
        $p->setFecha_Naci('');
        $this->assertNull($p->getFechaNacimientoVo());
        $p->setFecha_Naci(null);
        $this->assertNull($p->getFechaNacimientoVo());
    }

    public function test_fecha_de_cambio_de_ficha_en_iso_como_viene_de_la_bdu(): void
    {
        $p = new PersonaBDU();
        $p->setFecha_c_fic('2023-07-01');
        $this->assertSame('2023-07-01', $p->getFecha_c_fic()?->format('Y-m-d'));
        $p->setFecha_c_fic('2023-02-30');
        $this->assertNull($p->getFecha_c_fic());
    }
}
