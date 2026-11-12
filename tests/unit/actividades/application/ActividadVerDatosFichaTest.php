<?php

declare(strict_types=1);

namespace Tests\unit\actividades\application;

use PHPUnit\Framework\MockObject\MockObject;
use src\actividades\application\ActividadVerDatos;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividades\domain\contracts\RepeticionRepositoryInterface;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\value_objects\NivelStgrId;
use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\domain\entity\RelacionTarifaTipoActividad;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\TimeLocal;
use src\shared\security\HashB;
use src\ubis\application\services\DelegacionDropdown;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\entity\Delegacion;
use src\usuarios\domain\contracts\LocalRepositoryInterface;
use Tests\myTest;
use Tests\support\DescribeCriteria;

/**
 * Datos de la ficha de una actividad: con actividad existente (manda sobre la
 * entrada), alta (repetición «no se repite», tokens), nombre del lugar,
 * desplegables, textos del tipo y tarifa inicial.
 *
 * Extiende myTest porque el lugar se busca con Ubi::newUbi.
 */
final class ActividadVerDatosFichaTest extends myTest
{
    private ActividadAllRepositoryInterface&MockObject $actividades;
    private TipoTarifaRepositoryInterface&MockObject $tarifas;
    private RepeticionRepositoryInterface&MockObject $repeticiones;
    private RelacionTarifaTipoActividadRepositoryInterface&MockObject $relaciones;
    private DelegacionRepositoryInterface&MockObject $delegaciones;

    public function setUp(): void
    {
        parent::setUp();
        $this->actividades = $this->createMock(ActividadAllRepositoryInterface::class);
        $this->tarifas = $this->createMock(TipoTarifaRepositoryInterface::class);
        $this->tarifas->method('getArrayTipoTarifas')->willReturnCallback(fn (int $s): array => $s === 2 ? [9 => 'sf A'] : [4 => 'B', 5 => 'C']);
        $this->repeticiones = $this->createMock(RepeticionRepositoryInterface::class);
        $this->repeticiones->method('getArrayRepeticion')->willReturn([11 => 'cada año', 10 => 'No se repite ']);
        $this->relaciones = $this->createMock(RelacionTarifaTipoActividadRepositoryInterface::class);
        $this->delegaciones = $this->createMock(DelegacionRepositoryInterface::class);
        $this->delegaciones->method('matching')->willReturn([$this->delegacion('dlb', 'Barcelona'), $this->delegacion('dlp', 'Pamplona')]);
    }

    private function delegacion(string $dl, string $nombre): Delegacion
    {
        $d = new Delegacion();
        $d->setDl($dl);
        $d->setNombre_dl($nombre);
        $d->setRegion('H');
        return $d;
    }

    private function uc(): ActividadVerDatos
    {
        $locales = $this->createMock(LocalRepositoryInterface::class);
        $locales->method('getArrayLocales')->willReturn(['ca_ES' => 'català']);
        return new ActividadVerDatos($this->actividades, $this->tarifas, $locales, $this->repeticiones, $this->relaciones, new DelegacionDropdown($this->delegaciones));
    }

    private function actividad(): ActividadAll
    {
        $a = new ActividadAll();
        $a->setId_activ(3001);
        $a->setId_tipo_activ(112021);
        $a->setDl_org('dlb');
        $a->setNom_activ('ca n');
        $a->setId_ubi(1);
        $a->setLugar_esp('Montaña');
        $a->setF_ini(new DateTimeLocal('2027-07-01'));
        $a->setF_fin(new DateTimeLocal('2027-07-20'));
        $a->setH_ini(TimeLocal::fromString('20:30:00'));
        $a->setPrecio(300.0);
        $a->setStatus(2);
        $a->setTarifa(5);
        $a->setId_repeticion(11);
        $a->setPublicado(true);
        $a->setPlazas(25);
        $a->setIdiomaVo('ca_ES');
        return $a;
    }

    public function test_actividad_existente_manda_sobre_la_entrada(): void
    {
        $this->actividades->method('findById')->with(3001)->willReturn($this->actividad());

        $out = $this->uc()->ejecutar(['id_activ' => 3001, 'isfsv' => 2, 'dl_org' => 'dlz', 'tarifa' => '9', 'id_ubi' => 99]);

        $this->assertSame('ca n', $out['entidad']['nom_activ']);
        $this->assertSame('20:30', $out['entidad']['h_ini']);
        $this->assertSame('', $out['entidad']['h_fin']);
        $this->assertSame((new DateTimeLocal('2027-07-01'))->getFromLocal(), $out['entidad']['f_ini']);
        $this->assertSame([25, true, 'ca_ES'], [$out['entidad']['plazas'], $out['entidad']['publicado'], $out['entidad']['idioma']]);
        $this->assertSame(1, $out['isfsv'], 'la sección sale del tipo');
        $this->assertSame('dlb', $out['select_dl_org']['selected']);
        $this->assertSame([4 => 'B', 5 => 'C'], $out['select_tarifa']['opciones']);
        $this->assertSame(['5', false], [$out['select_tarifa']['selected'], $out['select_tarifa']['blanco']]);
        // Hallazgo: para 112021 (ca de cuadrienio) la ficha propone N, mientras que
        // NivelStgrId::generarNivelStgr() —el que usa el alta con el nivel vacío— da 2.
        $this->assertSame((string) NivelStgrId::N, $out['select_nivel_stgr']['selected']);
        $this->assertSame(2, NivelStgrId::generarNivelStgr(112021));
        $this->assertSame(['ca_ES', '11'], [$out['select_idioma']['selected'], $out['select_repeticion']['selected']]);
        $this->assertSame('Montaña', $out['nombre_ubi']);
        $this->assertSame(['sv', 'n', 'ca'], [$out['ssfsv'], $out['sasistentes'], $out['sactividad']]);
        $this->assertSame(['id_activ' => 3001], HashB::open($out['ctx_editar'], 'actividad_editar'));
        $this->assertSame(['id_activ' => 3001], HashB::open($out['ctx_cambiar_tipo'], 'actividad_cambiar_tipo'));
        $this->assertArrayNotHasKey('ctx_nuevo', $out);
    }

    public function test_actividad_con_nivel_guardado_lo_conserva(): void
    {
        $a = $this->actividad();
        $a->setNivel_stgr(NivelStgrId::R);
        $this->actividades->method('findById')->willReturn($a);
        $this->assertSame((string) NivelStgrId::R, $this->uc()->ejecutar(['id_activ' => 3001])['select_nivel_stgr']['selected']);
    }

    public function test_alta_usa_la_entrada_y_no_se_repite_por_defecto(): void
    {
        $this->actividades->expects($this->never())->method('findById');

        $out = $this->uc()->ejecutar(['isfsv' => 2, 'dl_org' => 'dlpf', 'Bdl' => 'f', 'tarifa' => '', 'id_tipo_activ' => '212021', 'nivel_stgr' => '4']);

        $this->assertNull($out['entidad']);
        $this->assertSame(['dlpf' => 'Pamplona (H-dlpf)'], $out['select_dl_org']['opciones'], 'sin la propia dl y con la f de sf');
        $this->assertSame([9 => 'sf A'], $out['select_tarifa']['opciones']);
        $this->assertTrue($out['select_tarifa']['blanco']);
        $this->assertSame('10', $out['select_repeticion']['selected']);
        $this->assertSame('4', $out['select_nivel_stgr']['selected']);
        $this->assertSame('sin determinar', $out['nombre_ubi']);
        $this->assertArrayNotHasKey('ssfsv', $out);
        $this->assertSame([], HashB::open($out['ctx_nuevo'], 'actividad_nuevo'));
    }

    public function test_actividad_inexistente_se_trata_como_entrada_suelta(): void
    {
        $this->actividades->method('findById')->willReturn(null);
        $out = $this->uc()->ejecutar(['id_activ' => 3001, 'id_ubi' => 999999991]);
        $this->assertNull($out['entidad']);
        $this->assertSame('ya no existe: cambiarlo', $out['nombre_ubi']);
        $this->assertSame('0', $out['select_repeticion']['selected'], 'con id_activ no se fuerza «no se repite»');
        $this->assertArrayHasKey('ctx_editar', $out);
    }

    public function test_tarifa_inicial_de_la_primera_serie_del_tipo(): void
    {
        $r = new RelacionTarifaTipoActividad();
        $r->setIdTarifaVo(7);
        $this->relaciones->method('matching')->willReturnCallback(function ($c) use ($r): array {
            $this->assertSame(['id_tipo_activ eq 112021'], DescribeCriteria::filtros($c));
            $this->assertSame(['id_serie asc'], DescribeCriteria::orden($c));
            return [$r];
        });
        $this->assertSame(7, $this->uc()->ejecutar(['id_tipo_activ' => '112021', 'calc_tarifa_inicial' => true])['tarifa_inicial']);
    }

    public function test_tarifa_inicial_sin_relacion_es_nula_y_sin_pedirla_no_sale(): void
    {
        $this->relaciones->method('matching')->willReturn([]);
        $out = $this->uc()->ejecutar(['id_tipo_activ' => '112021', 'calc_tarifa_inicial' => true]);
        $this->assertArrayHasKey('tarifa_inicial', $out);
        $this->assertNull($out['tarifa_inicial']);
        $this->assertArrayNotHasKey('tarifa_inicial', $this->uc()->ejecutar(['id_tipo_activ' => '112021']));
    }

    public function test_lugar_sin_determinar_con_lugar_especial_vacio(): void
    {
        $this->assertSame('', $this->uc()->ejecutar(['id_ubi' => 1])['nombre_ubi']);
        $this->assertSame('Playa', $this->uc()->ejecutar(['id_ubi' => 1, 'lugar_esp' => 'Playa'])['nombre_ubi']);
    }
}
