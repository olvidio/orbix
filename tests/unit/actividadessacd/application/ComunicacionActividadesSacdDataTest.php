<?php

namespace Tests\unit\actividadessacd\application;

use PHPUnit\Framework\TestCase;
use src\actividadessacd\application\ComunicacionActividadesSacdData;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\Usuario;

/**
 * Unitarios de {@see ComunicacionActividadesSacdData::resolverContexto}
 * que necesitan mockear `UsuarioRepository` y `RoleRepository`. En
 * particular, cubren la rama `p-sacd` (usuario con ese rol se ve solo a
 * si mismo), que antes llamaba al metodo inexistente `Usuario::getCsv_id_pau()`
 * y fallaba en runtime.
 */
final class ComunicacionActividadesSacdDataTest extends TestCase
{
    private mixed $previousContainer;
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth'] = [
            'id_usuario' => 443,
            'esquema' => 'H-dlv',
            'sfsv' => 1,
            'idioma' => 'ca',
        ];
    }

    protected function tearDown(): void
    {
        if ($this->previousContainer === null) {
            unset($GLOBALS['container']);
        } else {
            $GLOBALS['container'] = $this->previousContainer;
        }
        $_SESSION = $this->previousSession;
        parent::tearDown();
    }

    public function test_resolver_contexto_rol_p_sacd_fuerza_un_sacd_con_id_pau(): void
    {
        $oUsuario = new Usuario();
        $oUsuario->setId_usuario(443);
        $oUsuario->setId_role(77);
        $oUsuario->setCsvIdPauVo('9988');

        $usuarioRepo = $this->createMock(UsuarioRepositoryInterface::class);
        $usuarioRepo->method('findById')->with(443)->willReturn($oUsuario);

        $roleRepo = $this->createMock(RoleRepositoryInterface::class);
        $roleRepo->method('getArrayRoles')->willReturn([77 => 'p-sacd']);

        $GLOBALS['container'] = $this->containerFromMap([
            UsuarioRepositoryInterface::class => $usuarioRepo,
            RoleRepositoryInterface::class => $roleRepo,
        ]);

        $ctx = ComunicacionActividadesSacdData::resolverContexto([
            'que' => '',
            'id_nom' => 0,
            'propuesta' => '',
            'periodo' => '',
            'year' => '2030',
            'empiezamin' => '',
            'empiezamax' => '',
        ]);

        $this->assertSame('un_sacd', $ctx['que']);
        $this->assertSame(9988, $ctx['id_nom']);
        $this->assertSame('2029-07-01', $ctx['inicioIso']);
        $this->assertSame('2031-06-30', $ctx['finIso']);
    }

    public function test_resolver_contexto_rol_distinto_de_p_sacd_no_fuerza_un_sacd(): void
    {
        $oUsuario = new Usuario();
        $oUsuario->setId_usuario(443);
        $oUsuario->setId_role(1);

        $usuarioRepo = $this->createMock(UsuarioRepositoryInterface::class);
        $usuarioRepo->method('findById')->with(443)->willReturn($oUsuario);

        $roleRepo = $this->createMock(RoleRepositoryInterface::class);
        $roleRepo->method('getArrayRoles')->willReturn([1 => 'admin', 77 => 'p-sacd']);

        $GLOBALS['container'] = $this->containerFromMap([
            UsuarioRepositoryInterface::class => $usuarioRepo,
            RoleRepositoryInterface::class => $roleRepo,
        ]);

        $ctx = ComunicacionActividadesSacdData::resolverContexto([
            'que' => 'nagd',
            'id_nom' => 0,
            'propuesta' => '',
            'periodo' => 'tot_any',
            'year' => '2099',
            'empiezamin' => '',
            'empiezamax' => '',
        ]);

        $this->assertSame('nagd', $ctx['que']);
        $this->assertSame(0, $ctx['id_nom']);
    }

    public function test_resolver_contexto_usuario_no_encontrado_usa_que_input(): void
    {
        $usuarioRepo = $this->createMock(UsuarioRepositoryInterface::class);
        $usuarioRepo->method('findById')->willReturn(null);

        $roleRepo = $this->createMock(RoleRepositoryInterface::class);
        $roleRepo->expects($this->never())->method('getArrayRoles');

        $GLOBALS['container'] = $this->containerFromMap([
            UsuarioRepositoryInterface::class => $usuarioRepo,
            RoleRepositoryInterface::class => $roleRepo,
        ]);

        $ctx = ComunicacionActividadesSacdData::resolverContexto([
            'que' => 'sssc',
            'id_nom' => 0,
            'propuesta' => '',
            'periodo' => 'tot_any',
            'year' => '2099',
            'empiezamin' => '',
            'empiezamax' => '',
        ]);

        $this->assertSame('sssc', $ctx['que']);
    }

    /**
     * @param array<class-string, object> $services
     */
    private function containerFromMap(array $services): object
    {
        return new class($services) {
            public function __construct(private readonly array $services) {}

            public function get(string $id): object
            {
                if (!array_key_exists($id, $this->services)) {
                    throw new \RuntimeException('Unexpected DI key: ' . $id);
                }
                return $this->services[$id];
            }
        };
    }
}
