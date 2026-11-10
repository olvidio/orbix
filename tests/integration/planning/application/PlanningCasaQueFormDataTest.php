<?php

namespace Tests\integration\planning\application;

use src\permisos\domain\MenuDlPermissionBits;
use src\permisos\domain\PermDl;
use src\planning\application\PlanningCasaQueFormData;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use Tests\myTest;

/**
 * Filtro de casas según el rol (cdc = casa) y los permisos del usuario. Usa los roles reales de la BD.
 */
class PlanningCasaQueFormDataTest extends myTest
{
    private mixed $authAnterior;
    private mixed $permAnterior;

    private int $idRoleCdc = 0;
    private int $idRoleNoPau = 0;

    public function setUp(): void
    {
        parent::setUp();
        $this->authAnterior = $_SESSION['session_auth'] ?? null;
        $this->permAnterior = $_SESSION['oPerm'] ?? null;

        $repo = $GLOBALS['container']->get(RoleRepositoryInterface::class);
        $pau = $repo->getArrayRolesPau();
        $this->idRoleCdc = (int) (array_search('cdc', $pau, true) ?: 0);
        foreach (array_keys($repo->getArrayRoles()) as $id) {
            if (!isset($pau[$id])) {
                $this->idRoleNoPau = (int) $id;
                break;
            }
        }
        if ($this->idRoleCdc === 0 || $this->idRoleNoPau === 0) {
            $this->markTestSkipped('La BD de pruebas no tiene un rol de casa (cdc) y otro que no sea de pau.');
        }
    }

    public function tearDown(): void
    {
        $_SESSION['session_auth'] = $this->authAnterior;
        if ($this->permAnterior === null) {
            unset($_SESSION['oPerm']);
        } else {
            $_SESSION['oPerm'] = $this->permAnterior;
        }
        parent::tearDown();
    }

    private function usuario(int $idRole, string $idPau = ''): object
    {
        return new class($idRole, $idPau) {
            public function __construct(private int $idRole, private string $idPau)
            {
            }

            public function getId_role(): int
            {
                return $this->idRole;
            }

            public function getCsvIdPauAsString(): string
            {
                return $this->idPau;
            }
        };
    }

    private function sesion(object $usuario, int $sfsv, int $permisos): void
    {
        $auth = is_array($_SESSION['session_auth'] ?? null) ? $_SESSION['session_auth'] : [];
        $auth['MiUsuario'] = $usuario;
        $auth['sfsv'] = $sfsv;
        $_SESSION['session_auth'] = $auth;
        $perm = new PermDl();
        $perm->setAccion($permisos);
        $_SESSION['oPerm'] = $perm;
    }

    private function ejecutar(): array
    {
        return (new PlanningCasaQueFormData())->execute();
    }

    public function test_sin_usuario_en_sesion_lanza_excepcion(): void
    {
        $auth = is_array($_SESSION['session_auth'] ?? null) ? $_SESSION['session_auth'] : [];
        unset($auth['MiUsuario']);
        $_SESSION['session_auth'] = $auth;

        $this->expectException(\RuntimeException::class);
        $this->ejecutar();
    }

    public function test_un_usuario_de_casa_solo_ve_sus_casas(): void
    {
        $this->sesion($this->usuario($this->idRoleCdc, '12,34,56'), 1, 0);

        $r = $this->ejecutar();

        $this->assertSame('casa', $r['modo_casas']);
        $this->assertSame(['active' => true, 'id_ubi_in' => [12, 34, 56]], $r['filtro']);
    }

    public function test_las_casas_del_usuario_se_depuran(): void
    {
        $this->sesion($this->usuario($this->idRoleCdc, '12, abc,0,-5, 34,,'), 1, 0);

        $this->assertSame([12, 34], $this->ejecutar()['filtro']['id_ubi_in']);
    }

    public function test_un_usuario_de_casa_sin_casas_asignadas_no_ve_ninguna(): void
    {
        $this->sesion($this->usuario($this->idRoleCdc, ''), 1, 0);

        $r = $this->ejecutar();

        $this->assertSame('casa', $r['modo_casas']);
        $this->assertSame([], $r['filtro']['id_ubi_in'], 'lista vacía: debe interpretarse como «ninguna», no como «todas»');
    }

    public function test_el_rol_de_casa_manda_aunque_tenga_permisos_de_des(): void
    {
        $this->sesion($this->usuario($this->idRoleCdc, '12'), 1, MenuDlPermissionBits::map()[MenuDlPermissionBits::DES]);

        $this->assertSame('casa', $this->ejecutar()['modo_casas']);
    }

    /**
     * @dataProvider oficinasDeTodasLasCasasProvider
     */
    public function test_des_y_vcsd_ven_todas_las_casas(string $oficina): void
    {
        $this->sesion($this->usuario($this->idRoleNoPau), 1, MenuDlPermissionBits::map()[$oficina]);

        $r = $this->ejecutar();

        $this->assertSame('all', $r['modo_casas']);
        $this->assertSame(['active' => true], $r['filtro']);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function oficinasDeTodasLasCasasProvider(): iterable
    {
        yield 'des' => [MenuDlPermissionBits::DES];
        yield 'vcsd' => [MenuDlPermissionBits::VCSD];
    }

    public function test_sin_permisos_de_oficina_se_filtra_por_la_seccion_sv(): void
    {
        $this->sesion($this->usuario($this->idRoleNoPau), 1, MenuDlPermissionBits::map()[MenuDlPermissionBits::AGD]);

        $r = $this->ejecutar();

        $this->assertSame('sv', $r['modo_casas']);
        $this->assertSame(['active' => true, 'sv' => true], $r['filtro']);
    }

    public function test_sin_permisos_de_oficina_se_filtra_por_la_seccion_sf(): void
    {
        $this->sesion($this->usuario($this->idRoleNoPau), 2, 0);

        $r = $this->ejecutar();

        $this->assertSame('sf', $r['modo_casas']);
        $this->assertSame(['active' => true, 'sf' => true], $r['filtro']);
    }

    public function test_des_tiene_prioridad_sobre_la_seccion(): void
    {
        $this->sesion($this->usuario($this->idRoleNoPau), 2, MenuDlPermissionBits::map()[MenuDlPermissionBits::DES]);

        $this->assertSame('all', $this->ejecutar()['modo_casas']);
    }

    public function test_sin_objeto_de_permisos_se_filtra_solo_por_seccion(): void
    {
        $this->sesion($this->usuario($this->idRoleNoPau), 1, 0);
        unset($_SESSION['oPerm']);

        $this->assertSame('sv', $this->ejecutar()['modo_casas']);
    }
}
