<?php

declare(strict_types=1);

namespace Tests\unit\usuarios\application;

use PHPUnit\Framework\TestCase;
use src\usuarios\application\usuariosRegionContactos;
use src\usuarios\domain\contracts\PermMenuRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioGrupoRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;

final class UsuariosRegionContactosTest extends TestCase
{
    public function test_parse_una_region(): void
    {
        $this->assertSame(['H-crH'], usuariosRegionContactos::parseCodigosRegion('H-crH'));
    }

    public function test_parse_varias_regiones_sin_duplicar(): void
    {
        $this->assertSame(
            ['H-crH', 'H-dlb'],
            usuariosRegionContactos::parseCodigosRegion('H-crH, H-dlb , H-crH'),
        );
    }

    public function test_parse_ignora_todos(): void
    {
        $this->assertSame([], usuariosRegionContactos::parseCodigosRegion('todos'));
        $this->assertSame(['I-crI'], usuariosRegionContactos::parseCodigosRegion('todos,I-crI'));
    }

    public function test_fusionar_conserva_region_y_no_pisa_nombres(): void
    {
        $primero = usuariosRegionContactos::fusionarContactos(
            [],
            ['Ana' => ['email' => 'a@x.test', 'cargo' => 'est']],
            'H-crH',
        );
        $out = usuariosRegionContactos::fusionarContactos(
            $primero,
            ['Ana' => ['email' => 'a2@x.test', 'cargo' => 'sm']],
            'H-dlb',
        );

        $this->assertSame('a@x.test', $out['H-crH|Ana']['email']);
        $this->assertSame('H-crH', $out['H-crH|Ana']['region']);
        $this->assertSame('a2@x.test', $out['H-dlb|Ana']['email']);
        $this->assertSame('H-dlb', $out['H-dlb|Ana']['region']);
    }

    public function test_execute_todos_sin_lista_devuelve_vacio(): void
    {
        $useCase = new usuariosRegionContactos(
            $this->createMock(UsuarioGrupoRepositoryInterface::class),
            $this->createMock(UsuarioRepositoryInterface::class),
            $this->createMock(PermMenuRepositoryInterface::class),
        );

        $out = $useCase->execute('todos');

        $this->assertSame('', $out['error']);
        $this->assertTrue($out['data']['success']);
        $this->assertSame([], $out['data']['contactos']);
    }
}
