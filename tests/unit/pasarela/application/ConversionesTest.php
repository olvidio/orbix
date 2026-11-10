<?php

declare(strict_types=1);

namespace Tests\unit\pasarela\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\entity\TipoDeActividad;
use src\pasarela\application\Conversiones;
use src\pasarela\application\ExportarQueActividadTipoHtml;
use src\pasarela\domain\Activacion;
use src\pasarela\domain\ContribucionNoDuerme;
use src\pasarela\domain\ContribucionReserva;
use src\pasarela\domain\Nombre;
use src\permisos\domain\MenuDlPermissionBits;
use src\permisos\domain\XPermisos;
use Tests\myTest;

/** Valores derivados por tipo de actividad (defaults + excepciones) y selector de tipo de «exportar qué». */
final class ConversionesTest extends myTest
{
    private Activacion&MockObject $activacion;
    private ContribucionNoDuerme&MockObject $noDuerme;
    private ContribucionReserva&MockObject $reserva;
    private Nombre&MockObject $nombre;
    private TipoDeActividadRepositoryInterface&MockObject $tipos;
    private mixed $permAnterior;
    private mixed $sesionAnterior;

    public function setUp(): void
    {
        parent::setUp();
        $this->permAnterior = $_SESSION['oPerm'] ?? null;
        $this->sesionAnterior = $_SESSION['session_auth'] ?? null;
        unset($_SESSION['oPerm']);
        $_SESSION['session_auth'] = ['sfsv' => 1];
        $this->activacion = $this->createMock(Activacion::class);
        $this->noDuerme = $this->createMock(ContribucionNoDuerme::class);
        $this->reserva = $this->createMock(ContribucionReserva::class);
        $this->nombre = $this->createMock(Nombre::class);
        $this->tipos = $this->createMock(TipoDeActividadRepositoryInterface::class);
    }

    public function tearDown(): void
    {
        foreach (['oPerm' => $this->permAnterior, 'session_auth' => $this->sesionAnterior] as $k => $v) {
            if ($v === null) {
                unset($_SESSION[$k]);
            } else {
                $_SESSION[$k] = $v;
            }
        }
        parent::tearDown();
    }

    /** @param list<int> $ids */
    private function conv(array $ids = [131001, 132001]): Conversiones
    {
        $lista = array_map(function (int $id): TipoDeActividad {
            $t = $this->createMock(TipoDeActividad::class);
            $t->method('getId_tipo_activ')->willReturn($id);

            return $t;
        }, $ids);
        $this->tipos->method('matching')->willReturn($lista);

        return new Conversiones($this->tipos, $this->activacion, $this->noDuerme, $this->reserva, $this->nombre);
    }

    public function test_cada_tipo_recibe_el_defecto_y_las_excepciones_lo_sustituyen(): void
    {
        $this->noDuerme->method('getDefault')->willReturn(30);
        $this->noDuerme->method('getExcepciones')->willReturn([132001 => 0]);
        $this->reserva->method('getDefault')->willReturn(50);
        $this->reserva->method('getExcepciones')->willReturn([131001 => 100]);
        $c = $this->conv();

        $this->assertSame([131001 => 30, 132001 => 0], $c->getArrayContribucionNoDuerme());
        $this->assertSame([131001 => 100, 132001 => 50], $c->getArrayContribucionReserva());
    }

    public function test_sin_defecto_las_contribuciones_son_cero_y_la_activacion_vacia(): void
    {
        $this->noDuerme->method('getDefault')->willReturn(null);
        $this->noDuerme->method('getExcepciones')->willReturn([]);
        $this->activacion->method('getDefault')->willReturn(null);
        $this->activacion->method('getExcepciones')->willReturn([]);
        $c = $this->conv();

        $this->assertSame([131001 => 0, 132001 => 0], $c->getArrayContribucionNoDuerme());
        $this->assertSame([131001 => '', 132001 => ''], $c->getArrayActivacion());
    }

    public function test_la_activacion_mezcla_defecto_y_excepciones_con_texto_o_numero(): void
    {
        $this->activacion->method('getDefault')->willReturn('auto');
        $this->activacion->method('getExcepciones')->willReturn([131001 => 2]);

        $this->assertSame([131001 => 2, 132001 => 'auto'], $this->conv()->getArrayActivacion());
    }

    public function test_una_excepcion_de_un_tipo_que_no_esta_en_el_catalogo_tambien_sale(): void
    {
        $this->reserva->method('getDefault')->willReturn(10);
        $this->reserva->method('getExcepciones')->willReturn([999999 => 5]);

        $this->assertSame([131001 => 10, 132001 => 10, 999999 => 5], $this->conv()->getArrayContribucionReserva());
    }

    public function test_el_nombre_parte_del_de_pasarela_de_cada_tipo_y_lo_pisan_las_excepciones(): void
    {
        $this->nombre->method('getExcepciones')->willReturn([131001 => 'Mi nombre']);

        $r = $this->conv([131001, 132001])->getArrayNombre();

        $this->assertSame('Mi nombre', $r[131001]);
        $this->assertIsString($r[132001]);
        $this->assertNotSame('', $r[132001]);
    }

    public function test_perfil_y_tipo_se_calculan_para_todos_los_tipos(): void
    {
        $c = $this->conv([131001, 132001]);

        $perfil = $c->getArrayPerfil();
        $tipo = $c->getArrayTipo();

        $this->assertSame([131001, 132001], array_keys($perfil));
        $this->assertContainsOnly('string', $perfil);
        $this->assertSame([131001, 132001], array_keys($tipo));
        foreach ($tipo as $t) {
            $this->assertContains($t, ['curso retiro', 'convivencia']);
        }
    }

    public function test_el_catalogo_se_lee_una_sola_vez_si_no_esta_vacio(): void
    {
        $t = $this->createMock(TipoDeActividad::class);
        $t->method('getId_tipo_activ')->willReturn(131001);
        $this->tipos->expects($this->once())->method('matching')->willReturn([$t]);
        $this->activacion->method('getDefault')->willReturn(null);
        $this->activacion->method('getExcepciones')->willReturn([]);
        $this->noDuerme->method('getDefault')->willReturn(null);
        $this->noDuerme->method('getExcepciones')->willReturn([]);
        $c = new Conversiones($this->tipos, $this->activacion, $this->noDuerme, $this->reserva, $this->nombre);

        $c->getArrayActivacion();
        $c->getArrayContribucionNoDuerme();
        $c->getArrayPerfil();
        $this->assertSame([131001], array_keys($c->getArrayTipo()));
    }

    // ---- ExportarQueActividadTipoHtml ----------------------------------------------------------------------

    public function test_el_selector_de_exportar_devuelve_html_y_con_permiso_de_calendario_ofrece_mas(): void
    {
        $uc = new ExportarQueActividadTipoHtml();

        $normal = $uc->execute(['id_tipo_activ' => '131001']);
        $p = $this->createMock(XPermisos::class);
        $p->method('have_perm_oficina')->willReturnCallback(fn (string $o): bool => $o === MenuDlPermissionBits::CALENDARIO);
        $_SESSION['oPerm'] = $p;
        $jefe = $uc->execute(['id_tipo_activ' => '131001']);

        $this->assertSame(['html'], array_keys($normal));
        $this->assertNotSame('', $normal['html']);
        $this->assertIsString($jefe['html']);
    }

    public function test_el_selector_tolera_una_entrada_vacia(): void
    {
        $this->assertNotSame('', (new ExportarQueActividadTipoHtml())->execute([])['html']);
    }
}
