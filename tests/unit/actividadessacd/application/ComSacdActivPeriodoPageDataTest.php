<?php

namespace Tests\unit\actividadessacd\application;

use PHPUnit\Framework\TestCase;
use src\actividadessacd\application\ComSacdActivPeriodoPageData;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use src\usuarios\domain\entity\Usuario;

/**
 * {@see ComSacdActivPeriodoPageData} solo consulta usuario + roles para
 * decidir si el sacd de paso puede editar textos de comunicacion.
 */
final class ComSacdActivPeriodoPageDataTest extends TestCase
{
    private mixed $previousContainer;
    private array $previousSession;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousContainer = $GLOBALS['container'] ?? null;
        $this->previousSession = $_SESSION ?? [];
        $_SESSION['session_auth'] = [
            'id_usuario' => 501,
            'esquema' => 'H-dlv',
            'sfsv' => 1,
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

    public function test_perm_mod_txt_true_si_usuario_no_es_p_sacd(): void {
        $oUsuario = new Usuario();
        $oUsuario->setId_usuario(501);
        $oUsuario->setId_role(2);

        $usuarioRepo = $this->createMock(UsuarioRepositoryInterface::class);
        $usuarioRepo->method('findById')->with(501)->willReturn($oUsuario);

        $roleRepo = $this->createMock(RoleRepositoryInterface::class);
        $roleRepo->method('getArrayRoles')->willReturn([2 => 'admin', 9 => 'p-sacd']);

        $out = (new \src\actividadessacd\application\ComSacdActivPeriodoPageData($usuarioRepo, $roleRepo))->execute();
        $this->assertSame(['perm_mod_txt' => true], $out);
    }

    public function test_perm_mod_txt_false_si_rol_es_p_sacd(): void {
        $oUsuario = new Usuario();
        $oUsuario->setId_usuario(501);
        $oUsuario->setId_role(9);

        $usuarioRepo = $this->createMock(UsuarioRepositoryInterface::class);
        $usuarioRepo->method('findById')->with(501)->willReturn($oUsuario);

        $roleRepo = $this->createMock(RoleRepositoryInterface::class);
        $roleRepo->method('getArrayRoles')->willReturn([9 => 'p-sacd']);

        $out = (new \src\actividadessacd\application\ComSacdActivPeriodoPageData($usuarioRepo, $roleRepo))->execute();
        $this->assertSame(['perm_mod_txt' => false], $out);
    }

    public function test_perm_mod_txt_true_si_usuario_no_encontrado(): void {
        $usuarioRepo = $this->createMock(UsuarioRepositoryInterface::class);
        $usuarioRepo->method('findById')->willReturn(null);

        $roleRepo = $this->createMock(RoleRepositoryInterface::class);
        $roleRepo->expects($this->never())->method('getArrayRoles');

        $out = (new \src\actividadessacd\application\ComSacdActivPeriodoPageData($usuarioRepo, $roleRepo))->execute();
        $this->assertSame(['perm_mod_txt' => true], $out);
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
