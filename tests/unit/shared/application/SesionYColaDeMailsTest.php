<?php

declare(strict_types=1);

namespace Tests\unit\shared\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\permisos\domain\PermDl;
use src\permisos\domain\PermisosActividadesTrue;
use src\shared\application\copias\InformeEsquemaCopia;
use src\shared\application\EnviarMailsEnCola;
use src\shared\application\HydrateMenuPermissions;
use src\shared\application\HydratePermisosActividades;
use src\shared\domain\contracts\ColaMailRepositoryInterface;
use Tests\myTest;

/** Hidratación de permisos en sesión, cola de mails sin pendientes e informe de reconciliación de copias. */
final class SesionYColaDeMailsTest extends myTest
{
    /** @var array<string, mixed> */
    private array $anterior = [];
    private const CLAVES = ['iPermMenus', 'oPerm', 'oPermActividades', 'session_perms_hydrated', 'config', 'session_auth'];

    public function setUp(): void
    {
        parent::setUp();
        foreach (self::CLAVES as $k) {
            $this->anterior[$k] = $_SESSION[$k] ?? null;
            unset($_SESSION[$k]);
        }
        $_SESSION['session_auth'] = ['id_usuario' => 55, 'sfsv' => 1];
    }

    public function tearDown(): void
    {
        foreach ($this->anterior as $k => $v) {
            if ($v === null) {
                unset($_SESSION[$k]);
            } else {
                $_SESSION[$k] = $v;
            }
        }
        parent::tearDown();
    }

    // ---- HydrateMenuPermissions ----------------------------------------------------------------------

    public function test_con_permisos_de_menu_ya_en_sesion_solo_se_recrea_el_objeto_si_falta(): void
    {
        $_SESSION['iPermMenus'] = '12';

        (new HydrateMenuPermissions())->execute();

        $this->assertInstanceOf(PermDl::class, $_SESSION['oPerm']);
        $this->assertSame('12', $_SESSION['iPermMenus'], 'no se vuelve a calcular');

        $existente = $_SESSION['oPerm'];
        (new HydrateMenuPermissions())->execute();
        $this->assertSame($existente, $_SESSION['oPerm'], 'si el objeto ya está no se sustituye');
    }

    // ---- HydratePermisosActividades ----------------------------------------------------------------------

    public function test_invalidar_la_cache_borra_las_claves_de_permisos(): void
    {
        foreach (['iPermMenus' => 1, 'oPerm' => 'x', 'oPermActividades' => 'y', HydratePermisosActividades::SESSION_HYDRATED_KEY => true] as $k => $v) {
            $_SESSION[$k] = $v;
        }

        HydratePermisosActividades::invalidateSessionCache();

        foreach (['iPermMenus', 'oPerm', 'oPermActividades', HydratePermisosActividades::SESSION_HYDRATED_KEY] as $k) {
            $this->assertArrayNotHasKey($k, $_SESSION);
        }
    }

    public function test_sin_la_app_de_procesos_los_permisos_de_actividad_son_siempre_verdaderos_y_queda_marcado(): void
    {
        $_SESSION['iPermMenus'] = '0';
        $_SESSION['config'] = [];

        (new HydratePermisosActividades())->execute();

        $this->assertInstanceOf(PermisosActividadesTrue::class, $_SESSION['oPermActividades']);
        $this->assertTrue($_SESSION[HydratePermisosActividades::SESSION_HYDRATED_KEY]);
    }

    public function test_en_peticiones_posteriores_solo_se_restaura_el_objeto_de_permisos_de_menu(): void
    {
        $_SESSION[HydratePermisosActividades::SESSION_HYDRATED_KEY] = true;
        $_SESSION['oPermActividades'] = new PermisosActividadesTrue(55);
        $_SESSION['iPermMenus'] = '7';

        (new HydratePermisosActividades())->execute();

        $this->assertInstanceOf(PermDl::class, $_SESSION['oPerm']);
    }

    public function test_si_falta_oPermActividades_se_recalcula_todo(): void
    {
        $_SESSION[HydratePermisosActividades::SESSION_HYDRATED_KEY] = true;
        $_SESSION['iPermMenus'] = '7';
        $_SESSION['config'] = [];

        (new HydratePermisosActividades())->execute();

        $this->assertInstanceOf(PermisosActividadesTrue::class, $_SESSION['oPermActividades']);
    }

    // ---- EnviarMailsEnCola ----------------------------------------------------------------------

    private function cola(): ColaMailRepositoryInterface&MockObject
    {
        $repo = $this->createMock(ColaMailRepositoryInterface::class);
        $repo->method('matching')->willReturn([]);

        return $repo;
    }

    public function test_la_purga_borra_los_mails_de_hace_mas_de_un_mes(): void
    {
        $repo = $this->cola();
        $limite = null;
        $repo->expects($this->once())->method('deleteColaMails')->willReturnCallback(function (string $iso) use (&$limite): bool {
            $limite = $iso;

            return true;
        });

        $this->assertTrue((new EnviarMailsEnCola($repo))->purge());

        $this->assertSame((new \DateTimeImmutable('-1 month'))->format('Y-m-d'), (new \DateTimeImmutable((string) $limite))->format('Y-m-d'));
    }

    public function test_sin_mails_pendientes_no_se_envia_nada_ni_se_llama_a_mail(): void
    {
        $repo = $this->cola();
        $repo->expects($this->once())->method('deleteColaMails');
        $repo->expects($this->never())->method('Guardar');

        $r = (new EnviarMailsEnCola($repo))->execute();

        $this->assertSame(['purgados' => true, 'enviados' => 0], $r);
    }

    // ---- InformeEsquemaCopia ----------------------------------------------------------------------

    public function test_un_informe_reconciliado_cuenta_cambios_si_hay_altas_cambios_o_bajas(): void
    {
        $con = InformeEsquemaCopia::reconciliado('H-dlb', 10, 9, 1, 0, 0, ['id_ubi=5']);
        $sin = InformeEsquemaCopia::reconciliado('H-dlb', 10, 10, 0, 0, 0);

        $this->assertTrue($con->tieneCambios());
        $this->assertFalse($sin->tieneCambios());
        $this->assertFalse($con->esOmitido());
        $this->assertFalse($con->tieneError());
        $this->assertSame(['esquema' => 'H-dlb', 'origen' => 10, 'destino' => 9, 'altas' => 1, 'cambios' => 0, 'bajas' => 0, 'detalle' => ['id_ubi=5'], 'omitido' => '', 'error' => ''], $con->toArray());
    }

    public function test_omitido_y_con_error_son_excluyentes_y_sin_recuentos(): void
    {
        $omitido = InformeEsquemaCopia::omitido('H-dlx', 'no existe en sv');
        $error = InformeEsquemaCopia::conError('H-dly', 'sin conexión');

        $this->assertTrue($omitido->esOmitido());
        $this->assertFalse($omitido->tieneError());
        $this->assertTrue($error->tieneError());
        $this->assertFalse($error->esOmitido());
        $this->assertFalse($omitido->tieneCambios());
        $this->assertSame([0, 0, 0, 0], [$error->origen, $error->altas, $error->cambios, $error->bajas]);
        $this->assertSame('no existe en sv', $omitido->toArray()['omitido']);
    }
}
