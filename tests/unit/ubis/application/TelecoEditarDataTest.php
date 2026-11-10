<?php

declare(strict_types=1);

namespace Tests\unit\ubis\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\permisos\domain\MenuDlPermissionBits;
use src\permisos\domain\XPermisos;
use src\ubis\application\TelecoEditarData;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;
use src\ubis\domain\contracts\TelecoCtrDlRepositoryInterface;
use src\ubis\domain\contracts\TipoTelecoRepositoryInterface;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\entity\TelecoUbi;
use Tests\myTest;
use Tests\unit\ubis\support\UbiResolverFactory;

/** Formulario de edición de un teléfono / fax / correo de un centro. */
final class TelecoEditarDataTest extends myTest
{
    private TipoTelecoRepositoryInterface&MockObject $tipos;
    private DescTelecoRepositoryInterface&MockObject $descs;
    private TelecoCtrDlRepositoryInterface&MockObject $telecos;
    private CentroDlRepositoryInterface&MockObject $centros;
    private mixed $sesionAnterior;
    private mixed $permAnterior;

    public function setUp(): void
    {
        parent::setUp();
        $this->sesionAnterior = $_SESSION['session_auth'] ?? null;
        $this->permAnterior = $_SESSION['oPerm'] ?? null;
        $_SESSION['session_auth'] = ['sfsv' => 1, 'esquema' => 'H-dlbv'];
        $this->tipos = $this->createMock(TipoTelecoRepositoryInterface::class);
        $this->tipos->method('getArrayTiposTelecoUbi')->willReturn([1 => 'telf', 3 => 'e-mail']);
        $this->descs = $this->createMock(DescTelecoRepositoryInterface::class);
        $this->telecos = $this->createMock(TelecoCtrDlRepositoryInterface::class);
        $this->centros = $this->createMock(CentroDlRepositoryInterface::class);
    }

    public function tearDown(): void
    {
        foreach (['session_auth' => $this->sesionAnterior, 'oPerm' => $this->permAnterior] as $k => $v) {
            if ($v === null) {
                unset($_SESSION[$k]);
            } else {
                $_SESSION[$k] = $v;
            }
        }
        parent::tearDown();
    }

    private function permisoScdl(bool $tiene): void
    {
        $perm = $this->createMock(XPermisos::class);
        $perm->method('have_perm_oficina')->willReturnCallback(fn (string $o): bool => $tiene && $o === MenuDlPermissionBits::SCDL);
        $_SESSION['oPerm'] = $perm;
    }

    private function uc(): TelecoEditarData
    {
        $resolver = UbiResolverFactory::resolver($this, [
            TelecoCtrDlRepositoryInterface::class => $this->telecos,
            CentroDlRepositoryInterface::class => $this->centros,
        ]);

        return new TelecoEditarData($resolver, $this->tipos, $this->descs);
    }

    private function centro(string $dl): CentroDl
    {
        $c = $this->createMock(CentroDl::class);
        $c->method('getDl')->willReturn($dl);

        return $c;
    }

    public function test_un_alta_no_carga_el_telefono_y_solo_trae_los_tipos(): void
    {
        $this->permisoScdl(true);
        $this->centros->method('findById')->willReturn($this->centro('dlb'));
        $this->telecos->expects($this->never())->method('findById');

        $r = $this->uc()->execute('CentroDl', 'nuevo', 1003, 55);

        $this->assertSame(['', '', '', ''], [$r['id_tipo_teleco'], $r['id_desc_teleco'], $r['num_teleco'], $r['observ']]);
        $this->assertSame([1 => 'telf', 3 => 'e-mail'], $r['a_tipos']);
        $this->assertSame([], $r['a_desc']);
        $this->assertSame('1,3', $r['botones']);
        $this->assertSame('dlb', $r['dl']);
        $this->assertSame(TelecoCtrDlRepositoryInterface::class, $r['obj']);
    }

    public function test_al_editar_se_cargan_los_datos_y_las_descripciones_del_tipo(): void
    {
        $this->permisoScdl(true);
        $this->centros->method('findById')->willReturn($this->centro('dlb'));
        $t = $this->createMock(TelecoUbi::class);
        $t->method('getId_desc_teleco')->willReturn(7);
        $t->method('getId_tipo_teleco')->willReturn(1);
        $t->method('getNum_teleco')->willReturn('91 000 00 00');
        $t->method('getObserv')->willReturn('recepción');
        $this->telecos->method('findById')->with(55)->willReturn($t);
        $this->descs->expects($this->once())->method('getArrayDescTelecoUbis')->with('1')->willReturn([7 => 'oficina']);

        $r = $this->uc()->execute('CentroDl', 'editar', 1003, 55);

        $this->assertSame([1, 7, '91 000 00 00', 'recepción'], [$r['id_tipo_teleco'], $r['id_desc_teleco'], $r['num_teleco'], $r['observ']]);
        $this->assertSame([7 => 'oficina'], $r['a_desc']);
    }

    public function test_si_el_telefono_no_existe_el_formulario_queda_vacio(): void
    {
        $this->permisoScdl(true);
        $this->centros->method('findById')->willReturn($this->centro('dlb'));
        $this->telecos->method('findById')->willReturn(null);

        $r = $this->uc()->execute('CentroDl', 'editar', 1003, 55);

        $this->assertSame('', $r['num_teleco']);
        $this->assertSame([], $r['a_desc']);
    }

    /**
     * @dataProvider botonesProvider
     */
    public function test_los_botones_dependen_del_permiso_y_de_si_el_centro_es_de_mi_dl(bool $permiso, string $dlCentro, string $botones): void
    {
        $this->permisoScdl($permiso);
        $this->centros->method('findById')->willReturn($this->centro($dlCentro));

        $this->assertSame($botones, $this->uc()->execute('CentroDl', 'nuevo', 1003, 0)['botones']);
    }

    /**
     * @return iterable<string, array{bool, string, string}>
     */
    public static function botonesProvider(): iterable
    {
        yield 'con permiso y mi dl' => [true, 'dlb', '1,3'];
        yield 'con permiso y otra dl' => [true, 'dlp', '0'];
        yield 'sin permiso' => [false, 'dlb', '0'];
    }

    public function test_si_el_centro_no_existe_no_hay_dl_ni_permiso_de_modificar(): void
    {
        $this->permisoScdl(true);
        $this->centros->method('findById')->willReturn(null);

        $r = $this->uc()->execute('CentroDl', 'nuevo', 1003, 0);

        $this->assertSame(['', '0'], [$r['dl'], $r['botones']]);
    }
}
