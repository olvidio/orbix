<?php

declare(strict_types=1);

namespace Tests\unit\devel\application;

use PHPUnit\Framework\TestCase;
use src\devel_db_admin\application\ComprobarPrecondicionesCrearEsquema;

final class ComprobarPrecondicionesCrearEsquemaRolesTest extends TestCase
{
    public function test_mensaje_faltan_roles_indica_paso_crear_usuarios(): void
    {
        $ref = new \ReflectionClass(ComprobarPrecondicionesCrearEsquema::class);
        $m = $ref->getMethod('mensajeFaltanRoles');
        /** @var string $msg */
        $msg = $m->invoke(new ComprobarPrecondicionesCrearEsquema(), 'B-crB', ['• comun: rol «B-crB»']);

        $this->assertStringStartsWith('Aviso:', $msg);
        $this->assertStringContainsString('1º crear usuarios', $msg);
        $this->assertStringContainsString('B-crB', $msg);
    }
}
