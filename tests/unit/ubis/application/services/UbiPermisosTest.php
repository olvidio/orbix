<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application\services;

use src\permisos\domain\XPermisos;
use src\shared\config\ConfigGlobal;
use PHPUnit\Framework\TestCase;
use src\ubis\application\services\UbiPermisos;
use src\ubis\domain\entity\CentroDl;

final class UbiPermisosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        ConfigGlobal::setTest_mode(true);
        unset($_SESSION['oPerm']);
        $_SESSION['session_auth'] = [
            'esquema' => 'H-dlbv',
            'sfsv' => 1,
        ];
    }

    protected function tearDown(): void
    {
        unset($_SESSION['oPerm'], $_SESSION['session_auth']);
        parent::tearDown();
    }

    public function test_sin_oPerm_no_puede_modificar(): void
    {
        $this->assertFalse(UbiPermisos::puedeModificarPorObjeto('CentroDl', ConfigGlobal::mi_delef()));
        $this->assertFalse(UbiPermisos::puedeModificarPorObjeto('CentroEx'));
    }

    public function test_permiso_scdl_desde_iPermMenus_sin_oPerm(): void
    {
        $_SESSION['iPermMenus'] = 1 << 5;
        $this->assertTrue(UbiPermisos::puedeModificarPorObjeto('CentroDl', ConfigGlobal::mi_delef()));
        $this->assertTrue(UbiPermisos::puedeModificarPorObjeto('CentroEx'));
    }

    public function test_sin_permiso_scdl_no_puede_modificar(): void
    {
        $_SESSION['oPerm'] = $this->fakePerm(false);
        $this->assertFalse(UbiPermisos::puedeModificarPorObjeto('CentroDl', ConfigGlobal::mi_delef()));
        $this->assertFalse(UbiPermisos::puedeModificarPorObjeto('CentroEx'));
    }

    public function test_objeto_Ex_con_permiso_scdl_siempre_puede(): void
    {
        $_SESSION['oPerm'] = $this->fakePerm(true);
        $this->assertTrue(UbiPermisos::puedeModificarPorObjeto('CentroEx'));
        $this->assertTrue(UbiPermisos::puedeModificarPorObjeto('CasaEx', null));
    }

    public function test_centro_y_casa_son_solo_lectura(): void
    {
        $_SESSION['oPerm'] = $this->fakePerm(true);
        $miDelef = ConfigGlobal::mi_delef();

        $this->assertFalse(UbiPermisos::puedeModificarPorObjeto('Centro', $miDelef));
        $this->assertFalse(UbiPermisos::puedeModificarPorObjeto('Casa', $miDelef));
        $this->assertFalse(UbiPermisos::puedeModificarPorObjeto('DireccionCentro', $miDelef));
        $this->assertFalse(UbiPermisos::puedeModificarPorObjeto('DireccionCdc', $miDelef));
    }

    public function test_objeto_Dl_exige_dl_delegacion(): void
    {
        $_SESSION['oPerm'] = $this->fakePerm(true);
        $miDelef = ConfigGlobal::mi_delef();

        $this->assertFalse(UbiPermisos::puedeModificarPorObjeto('CentroDl', null));
        $this->assertTrue(UbiPermisos::puedeModificarPorObjeto('CentroDl', $miDelef));
        $this->assertTrue(UbiPermisos::puedeModificarPorObjeto('DireccionCentroDl', $miDelef));
        $this->assertFalse(UbiPermisos::puedeModificarPorObjeto('CentroDl', $miDelef . 'XX'));
        $this->assertTrue(
            UbiPermisos::puedeModificar('CentroDl', $this->fakeUbi($miDelef)),
            'puedeModificar delega en dl del ubi'
        );
    }

    public function test_dlPerteneceAMiDelegacion_acepta_variantes_con_y_sin_f(): void
    {
        $miDelef = ConfigGlobal::mi_delef();
        $miDele = ConfigGlobal::mi_dele();

        $this->assertTrue(UbiPermisos::dlPerteneceAMiDelegacion($miDelef));
        $this->assertTrue(UbiPermisos::dlPerteneceAMiDelegacion($miDele));
        if (str_ends_with($miDelef, 'f')) {
            $this->assertTrue(UbiPermisos::dlPerteneceAMiDelegacion(rtrim($miDelef, 'f')));
        }
    }

    public function test_objeto_desconocido_no_permite(): void
    {
        $_SESSION['oPerm'] = $this->fakePerm(true);
        $this->assertFalse(UbiPermisos::puedeModificarPorObjeto('Desconocido'));
        $this->assertFalse(UbiPermisos::puedeModificarPorObjeto(''));
    }

    private function fakePerm(bool $tienePermiso): XPermisos
    {
        return new class($tienePermiso) extends XPermisos {
            public function __construct(private readonly bool $value) {}
            public function have_perm_oficina(string $oficina): bool
            {
                return $oficina === 'scdl' ? $this->value : false;
            }
        };
    }

    private function fakeUbi(string $dl): CentroDl
    {
        return new class($dl) extends CentroDl {
            public function __construct(private readonly string $dlVal)
            {
            }

            public function getDl(): ?string
            {
                return $this->dlVal;
            }
        };
    }
}
