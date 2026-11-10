<?php

declare(strict_types=1);

namespace Tests\unit\frontend\actividades;

use frontend\actividades\helpers\ActividadesPermSupport;
use src\permisos\domain\MenuDlPermissionBits;
use src\permisos\domain\PermDl;
use Tests\myTest;

final class ActividadesPermSupportTest extends myTest
{
    private mixed $oPermAnterior = null;
    private mixed $oConfigAnterior = null;
    private mixed $sfsvAnterior = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->oPermAnterior = $_SESSION['oPerm'] ?? null;
        $this->oConfigAnterior = $_SESSION['oConfig'] ?? null;
        $this->sfsvAnterior = $_SESSION['session_auth']['sfsv'] ?? null;
        // Sin configuración de calendario: nadie es «jefe de calendario» por esa vía.
        unset($_SESSION['oConfig']);
    }

    public function tearDown(): void
    {
        $this->restaurar('oPerm', $this->oPermAnterior);
        $this->restaurar('oConfig', $this->oConfigAnterior);
        $_SESSION['session_auth']['sfsv'] = $this->sfsvAnterior;
        parent::tearDown();
    }

    private function restaurar(string $clave, mixed $valor): void
    {
        if ($valor === null) {
            unset($_SESSION[$clave]);
        } else {
            $_SESSION[$clave] = $valor;
        }
    }

    private function conAcceso(int $acceso, int $sfsv): void
    {
        $perm = new PermDl();
        $perm->setAccion($acceso);
        $_SESSION['oPerm'] = $perm;
        $_SESSION['session_auth']['sfsv'] = $sfsv;
    }

    public function test_el_administrador_sf_es_jefe_de_tipo_de_actividad_en_sf(): void
    {
        $this->conAcceso(MenuDlPermissionBits::map()[MenuDlPermissionBits::ADMIN_SF], 2);

        $this->assertTrue(ActividadesPermSupport::permJefeTipoActiv());
    }

    /**
     * Antes bastaba con compartir algún bit con admin_sf para ser «jefe» en sf.
     *
     * @dataProvider noAdministradoresProvider
     */
    public function test_un_usuario_sf_que_no_es_administrador_no_es_jefe(int $acceso): void
    {
        $this->conAcceso($acceso, 2);

        $this->assertFalse(ActividadesPermSupport::permJefeTipoActiv());
    }

    /**
     * @return iterable<string, array{int}>
     */
    public static function noAdministradoresProvider(): iterable
    {
        $bits = MenuDlPermissionBits::map();
        yield 'usuario de agd' => [$bits['agd']];
        yield 'usuario de sg y ctr' => [$bits['sg'] | $bits['ctr']];
        yield 'admin_sv no es admin_sf' => [$bits['admin_sv']];
        yield 'sin permisos' => [0];
    }

    public function test_en_sv_manda_des_o_vcsd_y_no_el_administrador_sf(): void
    {
        $bits = MenuDlPermissionBits::map();

        $this->conAcceso($bits['des'], 1);
        $this->assertTrue(ActividadesPermSupport::permJefeTipoActiv());

        $this->conAcceso($bits['vcsd'], 1);
        $this->assertTrue(ActividadesPermSupport::permJefeTipoActiv());

        $this->conAcceso($bits['agd'], 1);
        $this->assertFalse(ActividadesPermSupport::permJefeTipoActiv());

        $this->conAcceso($bits['admin_sf'], 1);
        $this->assertFalse(ActividadesPermSupport::permJefeTipoActiv(), 'el admin_sf solo cuenta en sesión sf');
    }
}
