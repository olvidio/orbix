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
        $this->assertFalse(UbiPermisos::puedeModificar('CentroDl'));
        $this->assertFalse(UbiPermisos::puedeModificar('CentroEx'));
    }

    public function test_sin_permiso_scdl_no_puede_modificar(): void
    {
        $_SESSION['oPerm'] = $this->fakePerm(false);
        $this->assertFalse(UbiPermisos::puedeModificar('CentroDl', $this->fakeUbi('cr')));
        $this->assertFalse(UbiPermisos::puedeModificar('CentroEx'));
    }

    public function test_objeto_Ex_con_permiso_scdl_siempre_puede(): void
    {
        $_SESSION['oPerm'] = $this->fakePerm(true);
        $this->assertTrue(UbiPermisos::puedeModificar('CentroEx'));
        $this->assertTrue(UbiPermisos::puedeModificar('CasaEx', null));
    }

    public function test_objeto_Dl_exige_ubi_y_misma_delegacion(): void
    {
        $_SESSION['oPerm'] = $this->fakePerm(true);
        $miDelef = ConfigGlobal::mi_delef();

        $this->assertFalse(
            UbiPermisos::puedeModificar('CentroDl', null),
            'sin ubi no puede'
        );
        $this->assertTrue(
            UbiPermisos::puedeModificar('CentroDl', $this->fakeUbi($miDelef)),
            'misma delegación sí'
        );
        $this->assertFalse(
            UbiPermisos::puedeModificar('CentroDl', $this->fakeUbi($miDelef . 'XX')),
            'otra delegación no'
        );
    }

    public function test_objeto_desconocido_no_permite(): void
    {
        $_SESSION['oPerm'] = $this->fakePerm(true);
        $this->assertFalse(UbiPermisos::puedeModificar('Desconocido'));
        $this->assertFalse(UbiPermisos::puedeModificar(''));
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
