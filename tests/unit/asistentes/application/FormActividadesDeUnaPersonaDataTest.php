<?php

declare(strict_types=1);

namespace Tests\unit\asistentes\application;

use PHPUnit\Framework\MockObject\MockObject;
use Psr\Container\ContainerInterface;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividadplazas\application\services\ResumenPlazasService;
use src\actividadplazas\domain\value_objects\PlazaId;
use src\asistentes\application\FormActividadesDeUnaPersonaData;
use src\asistentes\application\services\AsistenteActividadService;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\entity\PersonaEx;
use src\shared\config\ConfigGlobal;
use Tests\myTest;

/** Formulario del dossier «actividades de una persona»: alta de asistencia o edición de la existente. */
final class FormActividadesDeUnaPersonaDataTest extends myTest
{
    private ActividadAllRepositoryInterface&MockObject $actividadAll;
    private AsistenteActividadService&MockObject $servicio;
    private ActividadRepositoryInterface&MockObject $actividades;
    private PersonaExRepositoryInterface&MockObject $personasEx;
    private ResumenPlazasService&MockObject $resumen;
    private ContainerInterface&MockObject $container;
    private AsistenteRepositoryInterface&MockObject $repoAsistente;
    /** @var array<string, mixed> */
    private array $anterior = [];

    public function setUp(): void
    {
        parent::setUp();
        foreach (['config', 'session_auth'] as $k) {
            $this->anterior[$k] = $_SESSION[$k] ?? null;
            unset($_SESSION[$k]);
        }
        $_SESSION['session_auth'] = ['sfsv' => 1, 'esquema' => 'H-dlbv'];
        $this->actividadAll = $this->createMock(ActividadAllRepositoryInterface::class);
        $this->servicio = $this->createMock(AsistenteActividadService::class);
        $this->actividades = $this->createMock(ActividadRepositoryInterface::class);
        $this->personasEx = $this->createMock(PersonaExRepositoryInterface::class);
        $this->resumen = $this->createMock(ResumenPlazasService::class);
        $this->container = $this->createMock(ContainerInterface::class);
        $this->repoAsistente = $this->createMock(AsistenteRepositoryInterface::class);
        $this->container->method('get')->willReturn($this->repoAsistente);
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

    private function plazas(bool $si): void
    {
        $_SESSION['config'] = $si ? ['a_apps' => ['actividadplazas' => 4], 'app_installed' => [4]] : [];
    }

    private function uc(): FormActividadesDeUnaPersonaData
    {
        return new FormActividadesDeUnaPersonaData($this->actividadAll, $this->servicio, $this->actividades, $this->personasEx, $this->resumen, $this->container);
    }

    private function asistente(bool $propio = true, bool $falta = false, bool $estOk = true, ?string $propietario = null): Asistente&MockObject
    {
        $a = $this->createMock(Asistente::class);
        $a->method('isPropio')->willReturn($propio);
        $a->method('isFalta')->willReturn($falta);
        $a->method('isEst_ok')->willReturn($estOk);
        $a->method('getObserv')->willReturn('nota');
        $a->method('getPlaza')->willReturn((string) PlazaId::ASIGNADA);
        $a->method('getPropietario')->willReturn($propietario);

        return $a;
    }

    private function actividadExistente(): void
    {
        $a = $this->createMock(ActividadAll::class);
        $a->method('getNom_activ')->willReturn('ca invierno');
        $this->actividadAll->method('findById')->willReturn($a);
    }

    // ---- alta ----------------------------------------------------------------------

    public function test_un_alta_ofrece_las_actividades_de_otras_dl_del_tipo_de_mi_sfsv(): void
    {
        $this->actividades->expects($this->once())->method('getArrayActividadesDeTipo')
            ->with('^1', 'AND status = 2 AND dl_org != \'' . ConfigGlobal::mi_delef() . '\'')->willReturn([3001 => 'ca']);

        $r = $this->uc()->build(['id_pau' => '10011', 'obj_pau' => 'PersonaN']);

        $this->assertSame([3001 => 'ca'], $r['actividades_opciones']);
        $this->assertNull($r['actividades_onchange']);
        $this->assertSame(['AsistenteDl', '', '', 'checked', '', ''], [$r['obj'], $r['id_activ_real'], $r['nom_activ'], $r['propio_chk'], $r['falta_chk'], $r['est_chk']]);
        $this->assertSame('observ!id_activ', $r['hash_main']['campos_form']);
        $this->assertSame('nuevo', $r['hash_main']['campos_hidden']['mod']);
        $this->assertArrayNotHasKey('id_activ', $r['hash_main']['campos_hidden']);
    }

    public function test_el_filtro_de_dl_y_de_tipo_pedidos_se_aplican_y_se_escapan(): void
    {
        $this->actividades->expects($this->once())->method('getArrayActividadesDeTipo')
            ->with('^131', "AND status = 2 AND dl_org = 'd''l'")->willReturn([]);

        $this->uc()->build(['id_tipo' => '131', 'que_dl' => "d'l"]);
    }

    public function test_con_la_app_de_plazas_el_alta_trae_plazas_y_cambio_de_propietario(): void
    {
        $this->plazas(true);
        $this->actividades->method('getArrayActividadesDeTipo')->willReturn([]);

        $r = $this->uc()->build(['id_pau' => '10011']);

        $this->assertSame('fnjs_cmb_propietario()', $r['actividades_onchange']);
        $this->assertTrue($r['plazas_installed']);
        $this->assertNotSame([], $r['plaza_opciones']);
        $this->assertSame((string) PlazaId::PEDIDA, $r['plaza_selected']);
        $this->assertSame([], $r['propietario_opciones']);
        $this->assertSame('observ!plaza!propietario!id_activ', $r['hash_main']['campos_form']);
        $this->assertSame(['path' => 'src/actividadplazas/posibles_propietarios_data', 'campos_form' => 'id_activ!id_nom'], $r['ajax_propietarios']);
    }

    // ---- edición ----------------------------------------------------------------------

    public function test_editar_una_actividad_inexistente_devuelve_error(): void
    {
        $this->actividadAll->method('findById')->willReturn(null);

        $this->assertStringContainsString('3001', $this->uc()->build(['sel' => ['3001#x'], 'id_pau' => '10011'])['error']);
    }

    public function test_si_el_repositorio_no_es_valido_se_devuelve_el_error_de_la_excepcion(): void
    {
        $this->actividadExistente();
        $this->servicio->method('getRepoAsistente')->willThrowException(new \RuntimeException('dl ajena'));

        $this->assertSame(['error' => 'dl ajena'], $this->uc()->build(['sel' => ['3001#x'], 'id_pau' => '10011']));
    }

    public function test_editar_un_asistente_inexistente_devuelve_error_con_los_ids(): void
    {
        $this->actividadExistente();
        $this->servicio->method('getRepoAsistente')->willReturn(AsistenteRepositoryInterface::class);
        $this->repoAsistente->method('findById')->willReturn(null);

        $r = $this->uc()->build(['sel' => ['3001#x'], 'id_pau' => '10011']);

        $this->assertStringContainsString('10011', $r['error']);
        $this->assertStringContainsString('3001', $r['error']);
    }

    public function test_editar_carga_los_datos_del_asistente_y_la_actividad_va_oculta(): void
    {
        $this->actividadExistente();
        $this->servicio->method('getRepoAsistente')->willReturn(AsistenteRepositoryInterface::class);
        $this->repoAsistente->method('findById')->with(3001, 10011)->willReturn($this->asistente(true, true, false));

        $r = $this->uc()->build(['sel' => ['3001#x'], 'id_pau' => '10011', 'obj_pau' => 'PersonaN']);

        $this->assertSame([3001, 'ca invierno', 'checked', 'checked', '', 'nota'], [$r['id_activ_real'], $r['nom_activ'], $r['propio_chk'], $r['falta_chk'], $r['est_chk'], $r['observ']]);
        $this->assertSame((string) PlazaId::ASIGNADA, $r['plaza_selected']);
        $this->assertArrayNotHasKey('actividades_opciones', $r);
        $this->assertSame(3001, $r['hash_main']['campos_hidden']['id_activ']);
        $this->assertSame('editar', $r['hash_main']['campos_hidden']['mod']);
        $this->assertSame('observ', $r['hash_main']['campos_form']);
    }

    public function test_si_la_plaza_es_de_otra_dl_se_avisa_en_lugar_de_mostrar_el_formulario(): void
    {
        $this->plazas(true);
        $this->actividadExistente();
        $this->servicio->method('getRepoAsistente')->willReturn(AsistenteRepositoryInterface::class);
        $this->repoAsistente->method('findById')->willReturn($this->asistente(propietario: 'dlp>dlx'));

        $r = $this->uc()->build(['sel' => ['3001'], 'id_pau' => '10011', 'obj_pau' => 'PersonaN']);

        $this->assertSame(['aviso_txt'], array_keys($r));
        $this->assertStringContainsString('dlx', $r['aviso_txt']);
    }

    public function test_el_propietario_de_la_plaza_si_es_mi_dl_o_la_persona_es_de_paso_deja_editar(): void
    {
        $this->plazas(true);
        $this->actividadExistente();
        $this->servicio->method('getRepoAsistente')->willReturn(AsistenteRepositoryInterface::class);
        $mia = 'dlp>' . ConfigGlobal::mi_delef();
        $this->repoAsistente->method('findById')->willReturnOnConsecutiveCalls($this->asistente(propietario: $mia), $this->asistente(propietario: 'dlp>dlx'));
        $this->resumen->method('getPosiblesPropietariosOpciones')->willReturn(['dlp>dlb' => 'dlp']);

        $propia = $this->uc()->build(['sel' => ['3001'], 'id_pau' => '10011', 'obj_pau' => 'PersonaN']);
        $paso = $this->uc()->build(['sel' => ['3001'], 'id_pau' => '10011', 'obj_pau' => 'PersonaEx']);

        $this->assertArrayNotHasKey('aviso_txt', $propia);
        $this->assertSame(['dlp>dlb' => 'dlp'], $propia['propietario_opciones']);
        $this->assertTrue($propia['propietario_select_blanco']);
        $this->assertArrayNotHasKey('aviso_txt', $paso);
    }

    public function test_para_una_persona_de_paso_se_pasa_su_dl_a_los_propietarios_posibles(): void
    {
        $this->plazas(true);
        $this->actividadExistente();
        $this->servicio->method('getRepoAsistente')->willReturn(AsistenteRepositoryInterface::class);
        $this->repoAsistente->method('findById')->willReturn($this->asistente());
        $ex = $this->createMock(PersonaEx::class);
        $ex->method('getDl')->willReturn('dlx');
        $this->personasEx->method('findById')->with(10011)->willReturn($ex);
        $this->resumen->expects($this->once())->method('getPosiblesPropietariosOpciones')->with('dlx')->willReturn([]);

        $this->uc()->build(['sel' => ['3001'], 'id_pau' => '10011', 'obj_pau' => 'PersonaEx']);
    }
}
