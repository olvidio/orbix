<?php

declare(strict_types=1);

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\application\ListaSrCsvListado;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\permisos\domain\XPermisos;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CasaRepositoryInterface;
use src\ubis\domain\entity\Casa;
use src\ubis\domain\entity\CentroDl;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use src\usuarios\domain\entity\Preferencia;
use Tests\myTest;
use Tests\support\DescribeCriteria;
use Tests\support\SustituyeServiciosDelContenedor;

/**
 * Listado de actividades de sr: guarda la búsqueda como preferencia, filtra por
 * estado, tipo y periodo, junta las de las casas compartidas sin repetir y monta
 * las filas (lugar, centros, tipo).
 */
final class ListaSrCsvListadoTest extends myTest
{
    use SustituyeServiciosDelContenedor;

    private PreferenciaRepositoryInterface&MockObject $prefs;
    private ActividadRepositoryInterface&MockObject $actividades;
    private CentroEncargadoRepositoryInterface&MockObject $encargados;
    private CasaRepositoryInterface&MockObject $casas;
    /** @var array<string, mixed> */
    private array $anterior = [];
    /** @var list<list<string>> */
    private array $consultas = [];

    public function setUp(): void
    {
        parent::setUp();
        foreach (['oPerm', 'session_auth'] as $k) {
            $this->anterior[$k] = $_SESSION[$k] ?? null;
        }
        $this->permisos([], false);
        $this->prefs = $this->createMock(PreferenciaRepositoryInterface::class);
        $this->actividades = $this->createMock(ActividadRepositoryInterface::class);
        $this->encargados = $this->createMock(CentroEncargadoRepositoryInterface::class);
        $this->encargados->method('getCentrosEncargadosActividad')->willReturn([]);
        $this->casas = $this->createMock(CasaRepositoryInterface::class);
        $tipos = $this->createMock(TipoDeActividadRepositoryInterface::class);
        $tipos->method('getNom_tipoPosibles')->willReturn(['tipo_nom' => ['000' => '(sin especificar)', '001' => 'retiro'], 'nom_tipo' => []]);
        $this->sustituirServicio(TipoDeActividadRepositoryInterface::class, $tipos);
    }

    public function tearDown(): void
    {
        $this->reponerServicios();
        foreach ($this->anterior as $k => $v) {
            if ($v === null) {
                unset($_SESSION[$k]);
            } else {
                $_SESSION[$k] = $v;
            }
        }
        parent::tearDown();
    }

    /** @param list<string> $oficinas */
    private function permisos(array $oficinas, bool $admin): void
    {
        $perm = $this->createMock(XPermisos::class);
        $perm->method('have_perm_oficina')->willReturnCallback(fn (string $p): bool => in_array($p, $oficinas, true));
        $perm->method('only_perm')->willReturnCallback(fn (string $p): bool => $admin && $p === 'admin_sv');
        $_SESSION['oPerm'] = $perm;
    }

    private function uc(): ListaSrCsvListado
    {
        return new ListaSrCsvListado($this->prefs, $this->actividades, $this->encargados, $this->casas);
    }

    private function actividad(int $id, int $tipo = 171000, ?int $ubi = 10200): ActividadAll
    {
        $a = new ActividadAll();
        $a->setId_activ($id);
        $a->setId_tipo_activ($tipo);
        $a->setNom_activ("crt sr $id");
        $a->setStatus(2);
        $a->setF_ini(new DateTimeLocal('2027-03-05'));
        $a->setF_fin(new DateTimeLocal('2027-03-08'));
        $a->setId_ubi($ubi);
        return $a;
    }

    /** @param callable(list<string>): list<ActividadAll> $fn */
    private function actividadesPor(callable $fn): void
    {
        $this->actividades->method('matching')->willReturnCallback(function ($c) use ($fn): array {
            $f = DescribeCriteria::filtros($c);
            $this->consultas[] = $f;
            return $fn($f);
        });
    }

    public function test_guarda_la_busqueda_como_preferencia_nueva(): void
    {
        $this->actividadesPor(fn (): array => []);
        $this->prefs->method('findById')->willReturn(null);
        $this->prefs->expects($this->once())->method('Guardar')->willReturnCallback(function (Preferencia $p): bool {
            $this->assertSame('busqueda_activ_sr', $p->getTipoVo()->value());
            $this->assertSame(['status' => '["2"]', 'periodo' => 'curso_ca', 'tipo_activ' => '["1"]', 'ubis_compartidos' => '[]'], json_decode($p->getPreferenciaAsString(), true));
            return true;
        });
        $out = $this->uc()->ejecutar(['status' => ['2'], 'c_activ' => ['1']]);
        $this->assertSame('', $out['pref_error']);
    }

    public function test_actualiza_la_preferencia_existente_y_avisa_si_no_se_guarda(): void
    {
        $this->actividadesPor(fn (): array => []);
        $existente = new Preferencia();
        $existente->setId_usuario(1);
        $existente->setTipoVo('busqueda_activ_sr');
        $this->prefs->method('findById')->willReturn($existente);
        $this->prefs->expects($this->once())->method('Guardar')->with($this->identicalTo($existente))->willReturn(false);
        $this->prefs->method('getErrorTxt')->willReturn('fallo');
        $out = $this->uc()->ejecutar(['periodo' => 'tot_any']);
        $this->assertSame("hay un error, no se ha guardado la preferencia\nfallo", $out['pref_error']);
        $this->assertSame('tot_any', json_decode($existente->getPreferenciaAsString(), true)['periodo']);
    }

    public function test_filtros_de_estado_tipo_y_periodo_en_sv(): void
    {
        $this->actividadesPor(fn (): array => []);
        $this->uc()->ejecutar(['status' => ['2', '3'], 'c_activ' => ['1', '3'], 'periodo' => 'otro', 'empiezamin' => '1/3/2027', 'empiezamax' => '31/3/2027', 'dl_org' => 'dlb']);
        $this->assertSame(['status matches [23]', 'f_ini between [2027-03-01,2027-03-31]', 'dl_org eq dlb', 'id_tipo_activ matches ^17[13]'], $this->consultas[0]);

        $this->uc()->ejecutar(['status' => ['2'], 'c_activ' => ['1'], 'periodo' => 'desdeHoy']);
        $this->assertSame('status matches 2', $this->consultas[1][0]);
        $this->assertStringStartsWith('f_fin between', $this->consultas[1][1]);
        $this->assertSame('id_tipo_activ matches ^171', end($this->consultas[1]));

        $this->uc()->ejecutar([]);
        $this->assertSame('status matches .', $this->consultas[2][0]);
        $this->assertSame('id_tipo_activ matches ^17.', end($this->consultas[2]));
    }

    public function test_en_sf_los_tipos_de_sr_de_sf(): void
    {
        $_SESSION['session_auth']['sfsv'] = 2;
        $this->actividadesPor(fn (): array => []);
        $this->uc()->ejecutar([]);
        $this->assertSame('id_tipo_activ matches ^2[789].', end($this->consultas[0]));
    }

    public function test_junta_las_de_las_casas_compartidas_sin_repetir(): void
    {
        $this->actividadesPor(fn (array $f): array => str_starts_with((string) end($f), 'id_ubi in')
            ? [$this->actividad(10), $this->actividad(20, 112021)]
            : [$this->actividad(10)]);
        $out = $this->uc()->ejecutar(['id_cdc' => ['10200', '10201']]);
        $this->assertSame('id_ubi in [10200,10201]', end($this->consultas[1]));
        $this->assertSame(['crt sr 10', 'crt sr 20'], array_column($out['a_valores'], 7));
    }

    public function test_fila_con_lugar_centros_y_tipo(): void
    {
        $this->actividadesPor(fn (): array => [$this->actividad(10)]);
        $casa = new Casa();
        $casa->setId_ubi(10200);
        $casa->setNombre_ubi('Casa Uno');
        $this->casas->method('findById')->with(10200)->willReturn($casa);
        $this->encargados = $this->createMock(CentroEncargadoRepositoryInterface::class);
        $c1 = new CentroDl();
        $c1->setNombre_ubi('Ctr A');
        $c2 = new CentroDl();
        $c2->setNombre_ubi('Ctr B');
        $this->encargados->method('getCentrosEncargadosActividad')->with(10)->willReturn([$c1, $c2]);

        $out = $this->uc()->ejecutar([]);

        $fila = $out['a_valores'][1];
        $this->assertSame(2, $fila[1]);
        $this->assertSame((new DateTimeLocal('2027-03-05'))->getFromLocal(), $fila[2]);
        $this->assertSame(['crt sr 10', 'sr', 'crt', '(sin especificar)', 'Casa Uno', 'Ctr A, Ctr B'], [$fila[7], $fila[8], $fila[9], $fila[10], $fila[11], $fila[12]]);
        $this->assertStringContainsString('crt sr 10', $out['html_tabla']);
        $this->assertSame('Listado de actividades', $out['titulo']);
    }

    public function test_sg_vcsd_o_des_no_ven_sin_especificar_salvo_los_administradores(): void
    {
        $this->actividadesPor(fn (): array => [$this->actividad(10)]);
        $this->permisos(['sg'], false);
        $this->assertSame('', $this->uc()->ejecutar([])['a_valores'][1][10]);
        // Antes se preguntaba por la oficina «admin», que no existe: los administradores tampoco lo veían.
        $this->permisos(['sg'], true);
        $this->assertSame('(sin especificar)', $this->uc()->ejecutar([])['a_valores'][1][10]);
    }

    public function test_la_consulta_con_regex_sobre_el_estado_funciona_contra_el_repositorio_real(): void
    {
        $real = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
        $uc = new ListaSrCsvListado($this->prefs, $real, $this->encargados, $this->casas);
        $out = $uc->ejecutar(['status' => ['2', '3'], 'periodo' => 'otro', 'empiezamin' => '1/1/2091', 'empiezamax' => '2/1/2091']);
        $this->assertSame([], $out['a_valores']);
    }
}
