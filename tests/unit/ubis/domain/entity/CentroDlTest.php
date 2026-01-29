<?php

namespace Tests\unit\ubis\domain\entity;

use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\entity\CentroDl;
use src\ubis\domain\value_objects\CentroId;
use src\ubis\domain\value_objects\DelegacionCode;
use src\ubis\domain\value_objects\NBuzon;
use src\ubis\domain\value_objects\NumCartas;
use src\ubis\domain\value_objects\NumHabitIndiv;
use src\ubis\domain\value_objects\NumPi;
use src\ubis\domain\value_objects\ObservCentroText;
use src\ubis\domain\value_objects\PaisName;
use src\ubis\domain\value_objects\Plazas;
use src\ubis\domain\value_objects\RegionNameText;
use src\ubis\domain\value_objects\TipoCentroCode;
use src\ubis\domain\value_objects\TipoLaborId;
use src\ubis\domain\value_objects\UbiNombreText;
use src\ubis\domain\value_objects\ZonaId;
use Tests\myTest;

class CentroDlTest extends myTest
{
    private CentroDl $CentroDl;

    public function setUp(): void
    {
        parent::setUp();
        $this->CentroDl = new CentroDl();
        $this->CentroDl->setIdUbiVo(new CentroId(1));
        $this->CentroDl->setNombreUbiVo(new UbiNombreText('Test'));
    }

    public function test_set_and_get_tipo_ubi()
    {
        $this->CentroDl->setTipo_ubi('test');
        $this->assertEquals('test', $this->CentroDl->getTipo_ubi());
    }

    public function test_set_and_get_id_ubi()
    {
        $id_ubiVo = new CentroId(1);
        $this->CentroDl->setIdUbiVo($id_ubiVo);
        $this->assertInstanceOf(CentroId::class, $this->CentroDl->getIdUbiVo());
        $this->assertEquals(1, $this->CentroDl->getIdUbiVo()->value());
    }

    public function test_set_and_get_nombre_ubi()
    {
        $nombre_ubiVo = new UbiNombreText('Test');
        $this->CentroDl->setNombreUbiVo($nombre_ubiVo);
        $this->assertInstanceOf(UbiNombreText::class, $this->CentroDl->getNombreUbiVo());
        $this->assertEquals('Test', $this->CentroDl->getNombreUbiVo()->value());
    }

    public function test_set_and_get_dl()
    {
        $dlVo = new DelegacionCode('TST');
        $this->CentroDl->setDlVo($dlVo);
        $this->assertInstanceOf(DelegacionCode::class, $this->CentroDl->getDlVo());
        $this->assertEquals('TST', $this->CentroDl->getDlVo()->value());
    }

    public function test_set_and_get_pais()
    {
        $paisVo = new PaisName('Spain');
        $this->CentroDl->setPaisVo($paisVo);
        $this->assertInstanceOf(PaisName::class, $this->CentroDl->getPaisVo());
        $this->assertEquals('Spain', $this->CentroDl->getPaisVo()->value());
    }

    public function test_set_and_get_region()
    {
        $regionVo = new RegionNameText('Test');
        $this->CentroDl->setRegionVo($regionVo);
        $this->assertInstanceOf(RegionNameText::class, $this->CentroDl->getRegionVo());
        $this->assertEquals('Test', $this->CentroDl->getRegionVo()->value());
    }

    public function test_set_and_get_active()
    {
        $this->CentroDl->setActive(true);
        $this->assertTrue($this->CentroDl->isActive());
    }

    public function test_set_and_get_f_active()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->CentroDl->setF_active($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->CentroDl->getF_active());
        $this->assertEquals('2024-01-15 10:30:00', $this->CentroDl->getF_active()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_tipo_ctr()
    {
        $tipo_ctrVo = new TipoCentroCode('TST');
        $this->CentroDl->setTipoCtrVo($tipo_ctrVo);
        $this->assertInstanceOf(TipoCentroCode::class, $this->CentroDl->getTipoCtrVo());
        $this->assertEquals('TST', $this->CentroDl->getTipoCtrVo()->value());
    }

    public function test_set_and_get_tipo_labor()
    {
        $tipo_laborVo = new TipoLaborId(1);
        $this->CentroDl->setTipoLaborVo($tipo_laborVo);
        $this->assertInstanceOf(TipoLaborId::class, $this->CentroDl->getTipoLaborVo());
        $this->assertEquals(1, $this->CentroDl->getTipoLaborVo()->value());
    }

    public function test_set_and_get_id_ctr_padre()
    {
        $id_ctr_padreVo = new CentroId(1);
        $this->CentroDl->setIdCtrPadreVo($id_ctr_padreVo);
        $this->assertInstanceOf(CentroId::class, $this->CentroDl->getIdCtrPadreVo());
        $this->assertEquals(1, $this->CentroDl->getIdCtrPadreVo()->value());
    }

    public function test_set_and_get_n_buzon()
    {
        $n_buzonVo = new NBuzon(1);
        $this->CentroDl->setNBuzonVo($n_buzonVo);
        $this->assertInstanceOf(NBuzon::class, $this->CentroDl->getNBuzonVo());
        $this->assertEquals(1, $this->CentroDl->getNBuzonVo()->value());
    }

    public function test_set_and_get_num_pi()
    {
        $num_piVo = new NumPi(1);
        $this->CentroDl->setNumPiVo($num_piVo);
        $this->assertInstanceOf(NumPi::class, $this->CentroDl->getNumPiVo());
        $this->assertEquals(1, $this->CentroDl->getNumPiVo()->value());
    }

    public function test_set_and_get_num_cartas()
    {
        $num_cartasVo = new NumCartas(1);
        $this->CentroDl->setNumCartasVo($num_cartasVo);
        $this->assertInstanceOf(NumCartas::class, $this->CentroDl->getNumCartasVo());
        $this->assertEquals(1, $this->CentroDl->getNumCartasVo()->value());
    }

    public function test_set_and_get_observ()
    {
        $observVo = new ObservCentroText('Test');
        $this->CentroDl->setObservVo($observVo);
        $this->assertInstanceOf(ObservCentroText::class, $this->CentroDl->getObservVo());
        $this->assertEquals('Test', $this->CentroDl->getObservVo()->value());
    }

    public function test_set_and_get_num_habit_indiv()
    {
        $num_habit_indivVo = new NumHabitIndiv(1);
        $this->CentroDl->setNumHabitIndivVo($num_habit_indivVo);
        $this->assertInstanceOf(NumHabitIndiv::class, $this->CentroDl->getNumHabitIndivVo());
        $this->assertEquals(1, $this->CentroDl->getNumHabitIndivVo()->value());
    }

    public function test_set_and_get_plazas()
    {
        $plazasVo = new Plazas(1);
        $this->CentroDl->setPlazasVo($plazasVo);
        $this->assertInstanceOf(Plazas::class, $this->CentroDl->getPlazasVo());
        $this->assertEquals(1, $this->CentroDl->getPlazasVo()->value());
    }

    public function test_set_and_get_id_zona()
    {
        $id_zonaVo = new ZonaId(1);
        $this->CentroDl->setIdZonaVo($id_zonaVo);
        $this->assertInstanceOf(ZonaId::class, $this->CentroDl->getIdZonaVo());
        $this->assertEquals(1, $this->CentroDl->getIdZonaVo()->value());
    }

    public function test_set_and_get_num_cartas_mensuales()
    {
        $num_cartas_mensualesVo = new NumCartas(1);
        $this->CentroDl->setNumCartasMensualesVo($num_cartas_mensualesVo);
        $this->assertInstanceOf(NumCartas::class, $this->CentroDl->getNumCartasMensualesVo());
        $this->assertEquals(1, $this->CentroDl->getNumCartasMensualesVo()->value());
    }

    public function test_set_all_attributes()
    {
        $centroDl = new CentroDl();
        $attributes = [
            'tipo_ubi' => 'test',
            'id_ubi' => new CentroId(1),
            'nombre_ubi' => new UbiNombreText('Test'),
            'dl' => new DelegacionCode('TST'),
            'pais' => new PaisName('Spain'),
            'region' => new RegionNameText('Test'),
            'active' => true,
            'f_active' => new DateTimeLocal('2024-01-15 10:30:00'),
            'tipo_ctr' => new TipoCentroCode('TST'),
            'tipo_labor' => new TipoLaborId(1),
            'id_ctr_padre' => new CentroId(1),
            'n_buzon' => new NBuzon(1),
            'num_pi' => new NumPi(1),
            'num_cartas' => new NumCartas(1),
            'observ' => new ObservCentroText('Test'),
            'num_habit_indiv' => new NumHabitIndiv(1),
            'plazas' => new Plazas(1),
            'id_zona' => new ZonaId(1),
            'num_cartas_mensuales' => new NumCartas(1),
        ];
        $centroDl->setAllAttributes($attributes);

        $this->assertEquals('test', $centroDl->getTipo_ubi());
        $this->assertEquals(1, $centroDl->getIdUbiVo()->value());
        $this->assertEquals('Test', $centroDl->getNombreUbiVo()->value());
        $this->assertEquals('TST', $centroDl->getDlVo()->value());
        $this->assertEquals('Spain', $centroDl->getPaisVo()->value());
        $this->assertEquals('Test', $centroDl->getRegionVo()->value());
        $this->assertTrue($centroDl->isActive());
        $this->assertEquals('2024-01-15 10:30:00', $centroDl->getF_active()->format('Y-m-d H:i:s'));
        $this->assertEquals('TST', $centroDl->getTipoCtrVo()->value());
        $this->assertEquals(1, $centroDl->getTipoLaborVo()->value());
        $this->assertEquals(1, $centroDl->getIdCtrPadreVo()->value());
        $this->assertEquals(1, $centroDl->getNBuzonVo()->value());
        $this->assertEquals(1, $centroDl->getNumPiVo()->value());
        $this->assertEquals(1, $centroDl->getNumCartasVo()->value());
        $this->assertEquals('Test', $centroDl->getObservVo()->value());
        $this->assertEquals(1, $centroDl->getNumHabitIndivVo()->value());
        $this->assertEquals(1, $centroDl->getPlazasVo()->value());
        $this->assertEquals(1, $centroDl->getIdZonaVo()->value());
        $this->assertEquals(1, $centroDl->getNumCartasMensualesVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $centroDl = new CentroDl();
        $attributes = [
            'tipo_ubi' => 'test',
            'id_ubi' => 1,
            'nombre_ubi' => 'Test',
            'dl' => 'TST',
            'pais' => 'Spain',
            'region' => 'Test',
            'active' => true,
            'f_active' => new DateTimeLocal('2024-01-15 10:30:00'),
            'tipo_ctr' => 'TST',
            'tipo_labor' => 1,
            'id_ctr_padre' => 1,
            'n_buzon' => 1,
            'num_pi' => 1,
            'num_cartas' => 1,
            'observ' => 'Test',
            'num_habit_indiv' => 1,
            'plazas' => 1,
            'id_zona' => 1,
            'num_cartas_mensuales' => 1,
        ];
        $centroDl->setAllAttributes($attributes);

        $this->assertEquals('test', $centroDl->getTipo_ubi());
        $this->assertEquals(1, $centroDl->getIdUbiVo()->value());
        $this->assertEquals('Test', $centroDl->getNombreUbiVo()->value());
        $this->assertEquals('TST', $centroDl->getDlVo()->value());
        $this->assertEquals('Spain', $centroDl->getPaisVo()->value());
        $this->assertEquals('Test', $centroDl->getRegionVo()->value());
        $this->assertTrue($centroDl->isActive());
        $this->assertEquals('2024-01-15 10:30:00', $centroDl->getF_active()->format('Y-m-d H:i:s'));
        $this->assertEquals('TST', $centroDl->getTipoCtrVo()->value());
        $this->assertEquals(1, $centroDl->getTipoLaborVo()->value());
        $this->assertEquals(1, $centroDl->getIdCtrPadreVo()->value());
        $this->assertEquals(1, $centroDl->getNBuzonVo()->value());
        $this->assertEquals(1, $centroDl->getNumPiVo()->value());
        $this->assertEquals(1, $centroDl->getNumCartasVo()->value());
        $this->assertEquals('Test', $centroDl->getObservVo()->value());
        $this->assertEquals(1, $centroDl->getNumHabitIndivVo()->value());
        $this->assertEquals(1, $centroDl->getPlazasVo()->value());
        $this->assertEquals(1, $centroDl->getIdZonaVo()->value());
        $this->assertEquals(1, $centroDl->getNumCartasMensualesVo()->value());
    }
}
