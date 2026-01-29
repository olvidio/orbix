<?php

namespace Tests\unit\actividades\domain\entity;

use src\actividades\domain\entity\ActividadAll;
use src\actividades\domain\value_objects\ActividadDescText;
use src\actividades\domain\value_objects\ActividadNomText;
use src\actividades\domain\value_objects\ActividadObserv;
use src\actividades\domain\value_objects\IdTablaCode;
use src\actividades\domain\value_objects\NivelStgrId;
use src\actividades\domain\value_objects\RepeticionId;
use src\actividades\domain\value_objects\StatusId;
use src\actividadtarifas\domain\value_objects\TarifaId;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\Dinero;
use src\shared\domain\value_objects\TimeLocal;
use src\ubis\domain\value_objects\DelegacionCode;
use src\ubis\domain\value_objects\Plazas;
use src\usuarios\domain\value_objects\IdLocale;
use Tests\myTest;

class ActividadAllTest extends myTest
{
    private ActividadAll $ActividadAll;

    public function setUp(): void
    {
        parent::setUp();
        $this->ActividadAll = new ActividadAll();
        $this->ActividadAll->setId_activ(1);
        $this->ActividadAll->setId_tipo_activ(123456);
    }

    public function test_set_and_get_id_activ()
    {
        $this->ActividadAll->setId_activ(1);
        $this->assertEquals(1, $this->ActividadAll->getId_activ());
    }

    public function test_set_and_get_id_tipo_activ()
    {
        $this->ActividadAll->setId_tipo_activ(123456);
        $this->assertEquals(123456, $this->ActividadAll->getId_tipo_activ());
    }

    public function test_set_and_get_dl_org()
    {
        $dl_orgVo = new DelegacionCode('Test');
        $this->ActividadAll->setDlOrgVo($dl_orgVo);
        $this->assertInstanceOf(DelegacionCode::class, $this->ActividadAll->getDlOrgVo());
        $this->assertEquals('Test', $this->ActividadAll->getDlOrgVo()->value());
    }

    public function test_set_and_get_nom_activ()
    {
        $nom_activVo = new ActividadNomText(1);
        $this->ActividadAll->setNomActivVo($nom_activVo);
        $this->assertInstanceOf(ActividadNomText::class, $this->ActividadAll->getNomActivVo());
        $this->assertEquals(1, $this->ActividadAll->getNomActivVo()->value());
    }

    public function test_set_and_get_id_ubi()
    {
        $this->ActividadAll->setId_ubi(1);
        $this->assertEquals(1, $this->ActividadAll->getId_ubi());
    }

    public function test_set_and_get_desc_activ()
    {
        $desc_activVo = new ActividadDescText(1);
        $this->ActividadAll->setDescActivVo($desc_activVo);
        $this->assertInstanceOf(ActividadDescText::class, $this->ActividadAll->getDescActivVo());
        $this->assertEquals(1, $this->ActividadAll->getDescActivVo()->value());
    }

    public function test_set_and_get_f_ini()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->ActividadAll->setF_ini($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->ActividadAll->getF_ini());
        $this->assertEquals('2024-01-15 10:30:00', $this->ActividadAll->getF_ini()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_h_ini()
    {
        $this->ActividadAll->setH_ini(TimeLocal::fromString('10:35'));
        $this->assertEquals('10:35', $this->ActividadAll->getH_ini()->format('H:i'));
    }

    public function test_set_and_get_f_fin()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->ActividadAll->setF_fin($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->ActividadAll->getF_fin());
        $this->assertEquals('2024-01-15 10:30:00', $this->ActividadAll->getF_fin()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_h_fin()
    {
        $this->ActividadAll->setH_fin(TimeLocal::fromString('14:15'));
        $this->assertEquals('14:15', $this->ActividadAll->getH_fin()->format('H:i'));
    }

    public function test_set_and_get_tipo_horario()
    {
        $this->ActividadAll->setTipo_horario(1);
        $this->assertEquals(1, $this->ActividadAll->getTipo_horario());
    }

    public function test_set_and_get_precio()
    {
        $precioVo = new Dinero(325.80);
        $this->ActividadAll->setPrecioVo($precioVo);
        $this->assertInstanceOf(Dinero::class, $this->ActividadAll->getPrecioVo());
        $this->assertEquals(325.80, $this->ActividadAll->getPrecioVo()->asFloat());
    }

    public function test_set_and_get_num_asistentes()
    {
        $this->ActividadAll->setNum_asistentes(1);
        $this->assertEquals(1, $this->ActividadAll->getNum_asistentes());
    }

    public function test_set_and_get_status()
    {
        $statusVo = new StatusId(1);
        $this->ActividadAll->setStatusVo($statusVo);
        $this->assertInstanceOf(StatusId::class, $this->ActividadAll->getStatusVo());
        $this->assertEquals(1, $this->ActividadAll->getStatusVo()->value());
    }

    public function test_set_and_get_observ()
    {
        $observVo = new ActividadObserv(1);
        $this->ActividadAll->setObservVo($observVo);
        $this->assertInstanceOf(ActividadObserv::class, $this->ActividadAll->getObservVo());
        $this->assertEquals(1, $this->ActividadAll->getObservVo()->value());
    }

    public function test_set_and_get_nivel_stgr()
    {
        $nivel_stgrVo = new NivelStgrId(1);
        $this->ActividadAll->setNivelStgrVo($nivel_stgrVo);
        $this->assertInstanceOf(NivelStgrId::class, $this->ActividadAll->getNivelStgrVo());
        $this->assertEquals(1, $this->ActividadAll->getNivelStgrVo()->value());
    }

    public function test_set_and_get_observ_material()
    {
        $this->ActividadAll->setObserv_material('test');
        $this->assertEquals('test', $this->ActividadAll->getObserv_material());
    }

    public function test_set_and_get_lugar_esp()
    {
        $this->ActividadAll->setLugar_esp('test');
        $this->assertEquals('test', $this->ActividadAll->getLugar_esp());
    }

    public function test_set_and_get_tarifa()
    {
        $tarifaVo = new TarifaId(1);
        $this->ActividadAll->setTarifaVo($tarifaVo);
        $this->assertInstanceOf(TarifaId::class, $this->ActividadAll->getTarifaVo());
        $this->assertEquals(1, $this->ActividadAll->getTarifaVo()->value());
    }

    public function test_set_and_get_id_repeticion()
    {
        $id_repeticionVo = new RepeticionId(1);
        $this->ActividadAll->setIdRepeticionVo($id_repeticionVo);
        $this->assertInstanceOf(RepeticionId::class, $this->ActividadAll->getIdRepeticionVo());
        $this->assertEquals(1, $this->ActividadAll->getIdRepeticionVo()->value());
    }

    public function test_set_and_get_publicado()
    {
        $this->ActividadAll->setPublicado(true);
        $this->assertTrue($this->ActividadAll->isPublicado());
    }

    public function test_set_and_get_id_tabla()
    {
        $id_tablaVo = new IdTablaCode('dl');
        $this->ActividadAll->setIdTablaVo($id_tablaVo);
        $this->assertInstanceOf(IdTablaCode::class, $this->ActividadAll->getIdTablaVo());
        $this->assertEquals('dl', $this->ActividadAll->getIdTablaVo()->value());
    }

    public function test_set_and_get_plazas()
    {
        $plazasVo = new Plazas(1);
        $this->ActividadAll->setPlazasVo($plazasVo);
        $this->assertInstanceOf(Plazas::class, $this->ActividadAll->getPlazasVo());
        $this->assertEquals(1, $this->ActividadAll->getPlazasVo()->value());
    }

    public function test_set_and_get_idioma()
    {
        $idiomaVo = new IdLocale(1);
        $this->ActividadAll->setIdiomaVo($idiomaVo);
        $this->assertInstanceOf(IdLocale::class, $this->ActividadAll->getIdiomaVo());
        $this->assertEquals(1, $this->ActividadAll->getIdiomaVo()->value());
    }

    public function test_set_all_attributes()
    {
        $actividadAll = new ActividadAll();
        $attributes = [
            'id_activ' => 1,
            'id_tipo_activ' => 123456,
            'dl_org' => new DelegacionCode('Test'),
            'nom_activ' => new ActividadNomText(1),
            'id_ubi' => 1,
            'desc_activ' => new ActividadDescText(1),
            'f_ini' => new DateTimeLocal('2024-01-15 10:30:00'),
            'h_ini' => Timelocal::fromString('10:35'),
            'f_fin' => new DateTimeLocal('2024-01-15 10:30:00'),
            'h_fin' => Timelocal::fromString('14:15'),
            'tipo_horario' => 1,
            'precio' => new Dinero(435.30),
            'num_asistentes' => 1,
            'status' => new StatusId(1),
            'observ' => new ActividadObserv(1),
            'nivel_stgr' => new NivelStgrId(1),
            'observ_material' => 'test',
            'lugar_esp' => 'test',
            'tarifa' => new TarifaId(1),
            'id_repeticion' => new RepeticionId(1),
            'publicado' => true,
            'id_tabla' => new IdTablaCode('dl'),
            'plazas' => new Plazas(1),
            'idioma' => new IdLocale(1),
        ];
        $actividadAll->setAllAttributes($attributes);

        $this->assertEquals(1, $actividadAll->getId_activ());
        $this->assertEquals(123456, $actividadAll->getId_tipo_activ());
        $this->assertEquals('Test', $actividadAll->getDlOrgVo()->value());
        $this->assertEquals(1, $actividadAll->getNomActivVo()->value());
        $this->assertEquals(1, $actividadAll->getId_ubi());
        $this->assertEquals(1, $actividadAll->getDescActivVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $actividadAll->getF_ini()->format('Y-m-d H:i:s'));
        $this->assertEquals('10:35', $actividadAll->getH_ini()->format('H:i'));
        $this->assertEquals('2024-01-15 10:30:00', $actividadAll->getF_fin()->format('Y-m-d H:i:s'));
        $this->assertEquals('14:15', $actividadAll->getH_fin()->format('H:i'));
        $this->assertEquals(1, $actividadAll->getTipo_horario());
        $this->assertEquals(435.30, $actividadAll->getPrecioVo()->asFloat());
        $this->assertEquals(1, $actividadAll->getNum_asistentes());
        $this->assertEquals(1, $actividadAll->getStatusVo()->value());
        $this->assertEquals(1, $actividadAll->getObservVo()->value());
        $this->assertEquals(1, $actividadAll->getNivelStgrVo()->value());
        $this->assertEquals('test', $actividadAll->getObserv_material());
        $this->assertEquals('test', $actividadAll->getLugar_esp());
        $this->assertEquals(1, $actividadAll->getTarifaVo()->value());
        $this->assertEquals(1, $actividadAll->getIdRepeticionVo()->value());
        $this->assertTrue($actividadAll->isPublicado());
        $this->assertEquals('dl', $actividadAll->getIdTablaVo()->value());
        $this->assertEquals(1, $actividadAll->getPlazasVo()->value());
        $this->assertEquals(1, $actividadAll->getIdiomaVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $actividadAll = new ActividadAll();
        $attributes = [
            'id_activ' => 1,
            'id_tipo_activ' => 123456,
            'dl_org' => 'Test',
            'nom_activ' => 1,
            'id_ubi' => 1,
            'desc_activ' => 1,
            'f_ini' => new DateTimeLocal('2024-01-15 10:30:00'),
            'h_ini' => Timelocal::fromString('10:35'),
            'f_fin' => new DateTimeLocal('2024-01-15 10:30:00'),
            'h_fin' => Timelocal::fromString('14:15'),
            'tipo_horario' => 1,
            'precio' => 435.60,
            'num_asistentes' => 1,
            'status' => 1,
            'observ' => 1,
            'nivel_stgr' => 1,
            'observ_material' => 'test',
            'lugar_esp' => 'test',
            'tarifa' => 1,
            'id_repeticion' => 1,
            'publicado' => true,
            'id_tabla' => 'dl',
            'plazas' => 1,
            'idioma' => 1,
        ];
        $actividadAll->setAllAttributes($attributes);

        $this->assertEquals(1, $actividadAll->getId_activ());
        $this->assertEquals(123456, $actividadAll->getId_tipo_activ());
        $this->assertEquals('Test', $actividadAll->getDlOrgVo()->value());
        $this->assertEquals(1, $actividadAll->getNomActivVo()->value());
        $this->assertEquals(1, $actividadAll->getId_ubi());
        $this->assertEquals(1, $actividadAll->getDescActivVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $actividadAll->getF_ini()->format('Y-m-d H:i:s'));
        $this->assertEquals('10:35', $actividadAll->getH_ini()->format('H:i'));
        $this->assertEquals('2024-01-15 10:30:00', $actividadAll->getF_fin()->format('Y-m-d H:i:s'));
        $this->assertEquals('14:15', $actividadAll->getH_fin()->format('H:i'));
        $this->assertEquals(1, $actividadAll->getTipo_horario());
        $this->assertEquals(435.60, $actividadAll->getPrecioVo()->asFloat());
        $this->assertEquals(1, $actividadAll->getNum_asistentes());
        $this->assertEquals(1, $actividadAll->getStatusVo()->value());
        $this->assertEquals(1, $actividadAll->getObservVo()->value());
        $this->assertEquals(1, $actividadAll->getNivelStgrVo()->value());
        $this->assertEquals('test', $actividadAll->getObserv_material());
        $this->assertEquals('test', $actividadAll->getLugar_esp());
        $this->assertEquals(1, $actividadAll->getTarifaVo()->value());
        $this->assertEquals(1, $actividadAll->getIdRepeticionVo()->value());
        $this->assertTrue($actividadAll->isPublicado());
        $this->assertEquals('dl', $actividadAll->getIdTablaVo()->value());
        $this->assertEquals(1, $actividadAll->getPlazasVo()->value());
        $this->assertEquals(1, $actividadAll->getIdiomaVo()->value());
    }
}
