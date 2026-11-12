<?php

declare(strict_types=1);

namespace Tests\unit\actividades\domain\entity;

use PHPUnit\Framework\TestCase;
use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\value_objects\ActividadDescText;
use src\actividades\domain\value_objects\ActividadNomText;
use src\actividades\domain\value_objects\ActividadObserv;
use src\actividades\domain\value_objects\ActividadObservMaterial;
use src\actividades\domain\value_objects\ActividadTipoId;
use src\actividades\domain\value_objects\IdTablaCode;
use src\actividades\domain\value_objects\NivelStgrId;
use src\actividades\domain\value_objects\RepeticionId;
use src\actividades\domain\value_objects\StatusId;
use src\actividadtarifas\domain\value_objects\TarifaId;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\TimeLocal;
use src\ubis\domain\value_objects\DelegacionCode;
use src\ubis\domain\value_objects\Plazas;

/** Duraciones de una actividad (reales, ajustadas, en un periodo) y setters que aceptan VO, entero o texto. */
final class ActividadAllDuracionesTest extends TestCase
{
    private function actividad(string $ini, string $fin, ?string $hIni = null, ?string $hFin = null): ActividadAll
    {
        $a = new ActividadAll();
        $a->setF_ini(new DateTimeLocal($ini));
        $a->setF_fin(new DateTimeLocal($fin));
        $a->setH_ini($hIni === null ? null : TimeLocal::fromString($hIni));
        $a->setH_fin($hFin === null ? null : TimeLocal::fromString($hFin));
        return $a;
    }

    public function test_duracion_real_con_las_horas_por_defecto(): void
    {
        // Sin horas: entra a las 21:00 y sale a las 10:00 → 2 días y 13 h.
        $a = $this->actividad('2027-03-01', '2027-03-04');
        $this->assertSame(2.54, $a->getDuracionReal());
        $this->assertSame(3.0, $a->getDuracion());
        $this->assertSame(3.0, $a->getDuracionAumentada());
    }

    public function test_duracion_con_horas_propias(): void
    {
        $a = $this->actividad('2027-03-01', '2027-03-04', '09:00:00', '18:00:00');
        $this->assertSame(3.38, $a->getDuracionReal());
        $this->assertSame(3.0, $a->getDuracion());
        $this->assertSame(3.5, $a->getDuracionAumentada());
    }

    public function test_hora_vacia_cuenta_como_la_hora_por_defecto(): void
    {
        $a = $this->actividad('2027-03-01', '2027-03-04', '21:00:00', '10:00:00');
        $this->assertSame($this->actividad('2027-03-01', '2027-03-04')->getDuracionReal(), $a->getDuracionReal());
    }

    public function test_duracion_en_periodo_dentro_del_periodo_es_la_real(): void
    {
        $a = $this->actividad('2027-03-05', '2027-03-08');
        $this->assertSame($a->getDuracionReal(), $a->getDuracionEnPeriodo(new DateTimeLocal('2027-03-01'), new DateTimeLocal('2027-03-31')));
    }

    public function test_duracion_en_periodo_recorta_el_inicio(): void
    {
        // Del 1/3 00:00 al 4/3 10:00.
        $a = $this->actividad('2027-02-26', '2027-03-04');
        $this->assertSame(3.42, $a->getDuracionEnPeriodo(new DateTimeLocal('2027-03-01'), new DateTimeLocal('2027-03-31')));
    }

    public function test_duracion_en_periodo_recorta_el_final(): void
    {
        // Del 29/3 21:00 al 31/3 23:59:59.
        $a = $this->actividad('2027-03-29', '2027-04-03');
        $this->assertSame(2.12, $a->getDuracionEnPeriodo(new DateTimeLocal('2027-03-01'), new DateTimeLocal('2027-03-31')));
    }

    public function test_duracion_en_periodo_recorta_los_dos_extremos(): void
    {
        // Actividad del 1 al 10 y periodo del 3 al 5: del 3/3 00:00 al 5/3 23:59:59.
        $a = $this->actividad('2027-03-01', '2027-03-10');
        $this->assertSame(3.0, $a->getDuracionEnPeriodo(new DateTimeLocal('2027-03-03'), new DateTimeLocal('2027-03-05')));
    }

    public function test_duracion_en_periodo_no_modifica_las_fechas(): void
    {
        $a = $this->actividad('2027-02-26', '2027-03-04');
        $ini = new DateTimeLocal('2027-03-01 12:00:00');
        $a->getDuracionEnPeriodo($ini, new DateTimeLocal('2027-03-31'));
        $this->assertSame('2027-03-01 12:00:00', $ini->format('Y-m-d H:i:s'));
        $this->assertSame('2027-02-26 00:00:00', $a->getF_ini()->format('Y-m-d H:i:s'));
    }

    public function test_setters_aceptan_vo_entero_texto_y_nulo(): void
    {
        $a = new ActividadAll();
        $a->setIdTipoActivVo(112021);
        $this->assertSame(112021, $a->getId_tipo_activ());
        $a->setIdTipoActivVo(new ActividadTipoId(212021));
        $this->assertSame(212021, $a->getId_tipo_activ());

        $a->setStatusVo(3);
        $this->assertSame(3, $a->getStatus());
        $a->setStatusVo(new StatusId(StatusId::ACTUAL));
        $this->assertSame(StatusId::ACTUAL, $a->getStatus());

        $a->setNomActivVo('cv');
        $this->assertSame('cv', $a->getNom_activ());
        $a->setNomActivVo(new ActividadNomText('ca'));
        $this->assertSame('ca', $a->getNomActivVo()->value());

        $a->setDlOrgVo('dlb');
        $this->assertSame('dlb', $a->getDl_org());
        $a->setDlOrgVo(new DelegacionCode('dlp'));
        $this->assertSame('dlp', $a->getDl_org());
        $a->setDlOrgVo(null);
        $this->assertNull($a->getDl_org());

        $a->setDescActivVo('desc');
        $this->assertSame('desc', $a->getDesc_activ());
        $a->setDescActivVo(new ActividadDescText('otra'));
        $this->assertSame('otra', $a->getDesc_activ());
        $a->setDescActivVo(null);
        $this->assertNull($a->getDesc_activ());

        $a->setObservVo('obs');
        $this->assertSame('obs', $a->getObserv());
        $a->setObservVo(new ActividadObserv('o2'));
        $this->assertSame('o2', $a->getObserv());

        $a->setObserv_materialVo('mat');
        $this->assertSame('mat', $a->getObserv_material());
        $a->setObserv_materialVo(new ActividadObservMaterial('m2'));
        $this->assertSame('m2', $a->getObserv_material());
        $a->setObserv_materialVo(null);
        $this->assertNull($a->getObserv_material());

        $a->setNivelStgrVo(2);
        $this->assertSame(2, $a->getNivel_stgr());
        $a->setNivelStgrVo(new NivelStgrId(NivelStgrId::B));
        $this->assertSame(NivelStgrId::B, $a->getNivel_stgr());
        $a->setNivelStgrVo(null);
        $this->assertNull($a->getNivel_stgr());

        $a->setTarifaVo(4);
        $this->assertSame(4, $a->getTarifa());
        $a->setTarifaVo(new TarifaId(5));
        $this->assertSame(5, $a->getTarifa());
        $a->setTarifaVo(null);
        $this->assertNull($a->getTarifa());

        $a->setIdRepeticionVo(11);
        $this->assertSame(11, $a->getId_repeticion());
        $a->setIdRepeticionVo(new RepeticionId(12));
        $this->assertSame(12, $a->getId_repeticion());
        $a->setIdRepeticionVo(null);
        $this->assertNull($a->getId_repeticion());

        $a->setIdTablaVo('ex');
        $this->assertSame('ex', $a->getId_tabla());
        $a->setIdTablaVo(new IdTablaCode('dl'));
        $this->assertSame('dl', $a->getId_tabla());
        $a->setIdTablaVo(null);
        $this->assertNull($a->getId_tabla());

        $a->setPlazasVo(20);
        $this->assertSame(20, $a->getPlazas());
        $a->setPlazasVo(new Plazas(30));
        $this->assertSame(30, $a->getPlazas());
        $a->setPlazasVo(null);
        $this->assertNull($a->getPlazas());

        $a->setPrecioVo(null);
        $this->assertNull($a->getPrecio());
    }

    public function test_id_auto_y_nombre_de_clase(): void
    {
        $a = new ActividadAll();
        $a->setId_auto(9);
        $this->assertSame(9, $a->getId_auto());
        $this->assertSame('ActividadAll', $a->getClassName());
    }
}
