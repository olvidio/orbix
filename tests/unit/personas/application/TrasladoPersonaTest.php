<?php

declare(strict_types=1);

namespace Tests\unit\personas\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\entity\Dossier;
use src\personas\application\services\PersonaFinderService;
use src\personas\application\support\PersonaRepositoryResolver;
use src\personas\application\TrasladoFormData;
use src\personas\application\TrasladoUpdate;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaExRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\domain\contracts\SituacionRepositoryInterface;
use src\personas\domain\contracts\TrasladoRepositoryInterface;
use src\personas\domain\entity\PersonaEx;
use src\personas\domain\entity\PersonaN;
use src\personas\domain\entity\PersonaPub;
use src\personas\domain\entity\Traslado;
use src\personas\domain\Trasladar;
use src\shared\security\HashB;
use src\ubis\application\services\DelegacionDropdown;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroRepositoryInterface;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\entity\Centro;
use src\ubis\domain\entity\CentroDl;
use Tests\myTest;

/** Traslado de centro y de delegación de una persona, y formulario que lo prepara. */
final class TrasladoPersonaTest extends myTest
{
    private mixed $sesionAnterior;

    public function setUp(): void
    {
        parent::setUp();
        $this->sesionAnterior = $_SESSION['session_auth'] ?? null;
        $_SESSION['session_auth'] = ['sfsv' => 1, 'esquema' => 'H-dlbv'];
    }

    public function tearDown(): void
    {
        if ($this->sesionAnterior === null) {
            unset($_SESSION['session_auth']);
        } else {
            $_SESSION['session_auth'] = $this->sesionAnterior;
        }
        parent::tearDown();
    }

    // ---- TrasladoUpdate ----------------------------------------------------------------------

    private PersonaNRepositoryInterface&MockObject $repoN;
    private PersonaExRepositoryInterface&MockObject $repoEx;
    private CentroRepositoryInterface&MockObject $centros;
    private TrasladoRepositoryInterface&MockObject $traslados;
    private DossierRepositoryInterface&MockObject $dossiers;
    private Trasladar&MockObject $trasladar;

    private function update(): TrasladoUpdate
    {
        $this->repoN = $this->createMock(PersonaNRepositoryInterface::class);
        $this->repoEx = $this->createMock(PersonaExRepositoryInterface::class);
        $resolver = new PersonaRepositoryResolver(
            $this->repoN,
            $this->createMock(PersonaAgdRepositoryInterface::class),
            $this->createMock(PersonaNaxRepositoryInterface::class),
            $this->createMock(PersonaSRepositoryInterface::class),
            $this->createMock(PersonaSSSCRepositoryInterface::class),
            $this->repoEx,
            $this->createMock(PersonaDlRepositoryInterface::class),
            $this->createMock(PersonaSacdRepositoryInterface::class),
        );
        $this->centros = $this->createMock(CentroRepositoryInterface::class);
        $this->traslados = $this->createMock(TrasladoRepositoryInterface::class);
        $this->traslados->method('getNewId')->willReturn(900);
        $this->dossiers = $this->createMock(DossierRepositoryInterface::class);
        $this->trasladar = $this->createMock(Trasladar::class);

        return new TrasladoUpdate($resolver, $this->centros, $this->traslados, $this->dossiers, $this->trasladar);
    }

    private function dossierAbierto(): Dossier&MockObject
    {
        $d = $this->createMock(Dossier::class);
        $d->expects($this->once())->method('abrir');

        return $d;
    }

    /**
     * @dataProvider entradasInvalidasProvider
     * @param array<string, mixed> $input
     */
    public function test_exige_persona_y_tipo_conocido(array $input): void
    {
        $uc = $this->update();
        $this->traslados->expects($this->never())->method('Guardar');

        $this->assertNotSame('', $uc->execute($input));
    }

    /**
     * @return iterable<string, array{array<string, mixed>}>
     */
    public static function entradasInvalidasProvider(): iterable
    {
        yield 'vacío' => [[]];
        yield 'sin tipo' => [['id_pau' => '10011']];
        yield 'sin persona' => [['obj_pau' => 'PersonaN']];
        yield 'tipo desconocido' => [['id_pau' => '10011', 'obj_pau' => 'PersonaX']];
    }

    public function test_una_persona_inexistente_avisa(): void
    {
        $uc = $this->update();
        $this->repoN->method('findById')->willReturn(null);

        $this->assertNotSame('', $uc->execute(['id_pau' => '10011', 'obj_pau' => 'PersonaN']));
    }

    public function test_sin_cambios_pedidos_solo_se_abre_el_dossier_de_traslados(): void
    {
        $uc = $this->update();
        $this->repoN->method('findById')->willReturn(new PersonaN());
        $this->traslados->expects($this->never())->method('Guardar');
        $this->trasladar->expects($this->never())->method('trasladar');
        $d = $this->dossierAbierto();
        $this->dossiers->method('findByPk')->willReturn($d);
        $this->dossiers->expects($this->once())->method('Guardar')->with($d);

        $this->assertSame('', $uc->execute(['id_pau' => '10011', 'obj_pau' => 'PersonaN']));
    }

    public function test_si_no_existe_el_dossier_de_traslados_se_crea_y_se_abre(): void
    {
        $uc = $this->update();
        $this->repoN->method('findById')->willReturn(new PersonaN());
        $this->dossiers->method('findByPk')->willReturn(null);
        $d = $this->dossierAbierto();
        $this->dossiers->expects($this->once())->method('crearDossier')->willReturn($d);

        $uc->execute(['id_pau' => '10011', 'obj_pau' => 'PersonaN']);
    }

    public function test_el_cambio_de_centro_actualiza_la_persona_y_registra_el_traslado_con_origen_y_destino(): void
    {
        $uc = $this->update();
        $persona = new PersonaN();
        $this->repoN->method('findById')->willReturn($persona);
        $this->repoN->expects($this->once())->method('Guardar')->with($persona)->willReturn(true);
        $centro = $this->createMock(Centro::class);
        $centro->method('getNombre_ubi')->willReturn('Centro Nuevo');
        $this->centros->method('findById')->with(1004)->willReturn($centro);
        $registrado = null;
        $this->traslados->expects($this->once())->method('Guardar')->willReturnCallback(function (Traslado $t) use (&$registrado): bool {
            $registrado = $t;

            return true;
        });
        $this->dossiers->method('findByPk')->willReturn($this->dossierAbierto());

        $r = $uc->execute(['id_pau' => '10011', 'obj_pau' => 'PersonaN', 'new_ctr' => '1004#x', 'f_ctr' => '5/3/2026', 'id_ctr_o' => '1003', 'ctr_o' => 'Centro Viejo']);

        $this->assertSame('', $r);
        $this->assertSame(1004, $persona->getId_ctr());
        $this->assertSame([900, 'sede', 1003, 'Centro Viejo', 1004, 'Centro Nuevo', '5/3/2026'], [
            $registrado->getId_item(), $registrado->getTipo_cmb(), $registrado->getId_ctr_origen(), $registrado->getCtr_origen(),
            $registrado->getId_ctr_destino(), $registrado->getCtr_destino(), $registrado->getF_traslado()?->getFromLocal(),
        ]);
    }

    public function test_sin_centro_de_origen_el_id_origen_queda_nulo_y_un_fallo_de_guardado_se_acumula(): void
    {
        $uc = $this->update();
        $this->repoN->method('findById')->willReturn(new PersonaN());
        $this->repoN->method('Guardar')->willReturn(false);
        $this->centros->method('findById')->willReturn(null);
        $registrado = null;
        $this->traslados->method('Guardar')->willReturnCallback(function (Traslado $t) use (&$registrado): bool {
            $registrado = $t;

            return false;
        });
        $this->dossiers->method('findByPk')->willReturn($this->dossierAbierto());

        $r = $uc->execute(['id_pau' => '10011', 'obj_pau' => 'PersonaN', 'new_ctr' => '1004', 'f_ctr' => '5/3/2026']);

        $this->assertNull($registrado->getId_ctr_origen());
        $this->assertSame(2, substr_count($r, _("hay un error, no se ha guardado")), 'persona y traslado');
    }

    /**
     * Hallazgo: una persona de paso (`PersonaEx`, no es `PersonaDl`) no cambia de centro, pero igualmente se
     * registra el traslado de centro en su historial.
     */
    public function test_una_persona_de_paso_registra_el_traslado_de_centro_sin_cambiar_su_centro(): void
    {
        $uc = $this->update();
        $this->repoEx->method('findById')->willReturn(new PersonaEx());
        $this->repoEx->expects($this->never())->method('Guardar');
        $this->traslados->expects($this->once())->method('Guardar')->willReturn(true);
        $this->dossiers->method('findByPk')->willReturn($this->dossierAbierto());

        $uc->execute(['id_pau' => '10011', 'obj_pau' => 'PersonaEx', 'new_ctr' => '1004', 'f_ctr' => '5/3/2026']);
    }

    public function test_el_cambio_de_dl_exige_una_situacion_valida_y_corta_antes_del_dossier(): void
    {
        $uc = $this->update();
        $this->repoN->method('findById')->willReturn(new PersonaN());
        $this->trasladar->expects($this->never())->method('trasladar');
        $this->dossiers->expects($this->never())->method('findByPk');

        $r = $uc->execute(['id_pau' => '10011', 'obj_pau' => 'PersonaN', 'new_dl' => 'dlp', 'f_dl' => '5/3/2026', 'situacion' => '']);

        $this->assertNotSame('', $r);
    }

    public function test_el_cambio_de_dl_traslada_con_origen_y_destino_con_sufijo_de_sfsv_y_acumula_el_error(): void
    {
        $uc = $this->update();
        $this->repoN->method('findById')->willReturn(new PersonaN());
        $this->trasladar->expects($this->once())->method('setId_nom')->with(10011);
        $this->trasladar->expects($this->once())->method('setReg_dl_org')->with('H-dlbv');
        $this->trasladar->expects($this->once())->method('setReg_dl_dst')->with('H-dlpv');
        $this->trasladar->expects($this->once())->method('trasladar')->willReturn(['success' => false]);
        $this->trasladar->method('getError')->willReturn('sin esquema');
        $this->dossiers->method('findByPk')->willReturn($this->dossierAbierto());

        // El desplegable manda región y dl («H-dlp», DelegacionDropdown::listaRegDele).
        $r = $uc->execute(['id_pau' => '10011', 'obj_pau' => 'PersonaN', 'new_dl' => 'H-dlp', 'f_dl' => '5/3/2026', 'situacion' => 'L', 'dl' => 'dlb']);

        $this->assertSame('sin esquema', $r);
    }

    public function test_en_sf_el_destino_no_lleva_la_f_dos_veces(): void
    {
        $_SESSION['session_auth'] = ['sfsv' => 2, 'esquema' => 'H-dlbf'];
        $uc = $this->update();
        $this->repoN->method('findById')->willReturn(new PersonaN());
        $this->trasladar->expects($this->once())->method('setReg_dl_org')->with('H-dlbf');
        // En sf el desplegable ya trae la «f» («H-dlpf»); antes quedaba «H-dlpff».
        $this->trasladar->expects($this->once())->method('setReg_dl_dst')->with('H-dlpf');
        $this->trasladar->method('trasladar')->willReturn(['success' => true]);
        $this->trasladar->method('getError')->willReturn('');
        $this->dossiers->method('findByPk')->willReturn($this->dossierAbierto());

        $this->assertSame('', $uc->execute(['id_pau' => '10011', 'obj_pau' => 'PersonaN', 'new_dl' => 'H-dlpf', 'f_dl' => '5/3/2026', 'situacion' => 'L', 'dl' => 'dlb']));
    }

    // ---- TrasladoFormData ----------------------------------------------------------------------

    private PersonaFinderService&MockObject $finder;
    private CentroDlRepositoryInterface&MockObject $centrosDl;

    private function form(): TrasladoFormData
    {
        $this->finder = $this->createMock(PersonaFinderService::class);
        $this->centrosDl = $this->createMock(CentroDlRepositoryInterface::class);
        $situaciones = $this->createMock(SituacionRepositoryInterface::class);
        $situaciones->method('getArraySituaciones')->with(true)->willReturn(['L' => 'traslado']);
        $delegaciones = $this->createMock(DelegacionRepositoryInterface::class);
        $delegaciones->method('matching')->willReturn([]);

        return new TrasladoFormData($this->finder, $this->centrosDl, $situaciones, new DelegacionDropdown($delegaciones));
    }

    public function test_una_persona_inexistente_o_de_paso_devuelve_error(): void
    {
        $uc = $this->form();
        $this->finder->method('findPersonaEnGlobal')->willReturnOnConsecutiveCalls(null, new PersonaPub());

        $this->assertStringContainsString('10011', $uc->execute(['id_pau' => '10011'])['error']);
        $this->assertArrayHasKey('error', $uc->execute(['id_pau' => '10011']));
    }

    public function test_el_formulario_trae_persona_centro_actual_opciones_y_firma_con_id_y_tipo(): void
    {
        $uc = $this->form();
        $p = $this->createMock(\src\personas\domain\entity\PersonaDl::class);
        $p->method('getId_ctr')->willReturn(1003);
        $p->method('getDl')->willReturn('dlb');
        $p->method('getNombreApellidos')->willReturn('Ana Gil');
        $this->finder->method('findPersonaEnGlobal')->with(10011)->willReturn($p);
        $c = $this->createMock(CentroDl::class);
        $c->method('getNombre_ubi')->willReturn('Centro A');
        $this->centrosDl->method('findById')->with(1003)->willReturn($c);
        $this->centrosDl->method('getArrayCentros')->willReturn([1003 => 'Centro A']);

        $r = $uc->execute(['sel' => ['10011#n'], 'obj_pau' => 'PersonaN']);

        $this->assertSame(['Ana Gil', 1003, 'Centro A', 'dlb', 10011], [$r['titulo'], $r['id_ctr'], $r['nombre_ctr'], $r['dl'], $r['id_pau']]);
        $this->assertSame([1003 => 'Centro A'], $r['opciones_centros']);
        $this->assertSame(['L' => 'traslado'], $r['opciones_situacion']);
        $this->assertSame(['id_pau' => 10011, 'obj_pau' => 'PersonaN'], HashB::open($r['ctx_update'], 'traslado_update'));
        $this->assertSame((new \DateTimeImmutable('today'))->format('j/n/Y'), $r['hoy']);
    }

    public function test_una_persona_sin_centro_no_consulta_el_centro_y_la_seleccion_como_texto_vale(): void
    {
        $uc = $this->form();
        $p = $this->createMock(\src\personas\domain\entity\PersonaDl::class);
        $p->method('getId_ctr')->willReturn(null);
        $this->finder->method('findPersonaEnGlobal')->willReturn($p);
        $this->centrosDl->expects($this->never())->method('findById');
        $this->centrosDl->method('getArrayCentros')->willReturn([]);

        $r = $uc->execute(['sel' => '10012#a']);

        $this->assertSame([0, '', 10012], [$r['id_ctr'], $r['nombre_ctr'], $r['id_pau']]);
    }

    /**
     * Hallazgo: el filtro de centros usa `tipo_ctr !~ '^[(cgi)|(igl)]'`, una clase de caracteres y no una
     * alternancia: excluye cualquier tipo que empiece por `c`, `g`, `i`, `l`, `(`, `)` o `|`, no solo `cgi`/`igl`.
     */
    public function test_el_filtro_de_centros_es_una_clase_de_caracteres_y_no_una_alternancia(): void
    {
        $uc = $this->form();
        $p = $this->createMock(\src\personas\domain\entity\PersonaDl::class);
        $this->finder->method('findPersonaEnGlobal')->willReturn($p);
        $this->centrosDl->expects($this->once())->method('getArrayCentros')->with("WHERE tipo_ctr !~ '^[(cgi)|(igl)]'")->willReturn([]);

        $uc->execute(['id_pau' => '10011']);
    }
}
