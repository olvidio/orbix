<?php

declare(strict_types=1);

namespace Tests\integration\personas\config;

use src\personas\application\PersonaEliminar;
use Tests\myTest;

/**
 * El flag provisional `ORBIX_PERSONA_DE_PASO_BORRAR_SIN_COMPROBAR` de `.env` llega al caso de uso
 * (ver «Comprobación de datos al borrar una persona de paso» en `docs/dev/backlog.md`).
 */
final class PersonaEliminarWiringTest extends myTest
{
    private const FLAG = 'ORBIX_PERSONA_DE_PASO_BORRAR_SIN_COMPROBAR';

    protected function tearDown(): void
    {
        unset($_ENV[self::FLAG]);
        parent::tearDown();
    }

    public function test_sin_el_flag_se_comprueban_los_datos(): void
    {
        unset($_ENV[self::FLAG]);

        $this->assertTrue($this->comprueba());
    }

    public function test_con_el_flag_a_1_no_se_comprueban(): void
    {
        $_ENV[self::FLAG] = '1';

        $this->assertFalse($this->comprueba());
    }

    private function comprueba(): bool
    {
        $useCase = $GLOBALS['container']->make(PersonaEliminar::class);
        $this->assertInstanceOf(PersonaEliminar::class, $useCase);

        return (bool) (new \ReflectionProperty(PersonaEliminar::class, 'comprobarDatosDePaso'))->getValue($useCase);
    }
}
