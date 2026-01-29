<?php

namespace Tests\unit\ubis\domain\entity;

use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\entity\Direccion;
use src\ubis\domain\value_objects\APText;
use src\ubis\domain\value_objects\DireccionId;
use src\ubis\domain\value_objects\DireccionText;
use src\ubis\domain\value_objects\LatitudDecimal;
use src\ubis\domain\value_objects\LongitudDecimal;
use src\ubis\domain\value_objects\ObservDireccionText;
use src\ubis\domain\value_objects\PaisName;
use src\ubis\domain\value_objects\PlanoDocText;
use src\ubis\domain\value_objects\PlanoExtensionText;
use src\ubis\domain\value_objects\PlanoNameText;
use src\ubis\domain\value_objects\PoblacionText;
use src\ubis\domain\value_objects\ProvinciaText;
use src\ubis\domain\value_objects\SedeNameText;
use Tests\myTest;

class DireccionTest extends myTest
{
    private Direccion $Direccion;

    public function setUp(): void
    {
        parent::setUp();
        $this->Direccion = new Direccion();
        $this->Direccion->setId_direccion(1);
        $this->Direccion->setPoblacionVo( PoblacionText::fromNullableString('Test'));
    }

    public function test_get_id_direccion()
    {
        $this->assertEquals(1, $this->Direccion->getId_direccion());
    }

    public function test_set_and_get_direccion()
    {
        $direccionVo = new DireccionText('Test');
        $this->Direccion->setDireccionVo($direccionVo);
        $this->assertInstanceOf(DireccionText::class, $this->Direccion->getDireccionVo());
        $this->assertEquals('Test', $this->Direccion->getDireccionVo()->value());
    }

    public function test_set_and_get_c_p()
    {
        $this->Direccion->setC_p('test');
        $this->assertEquals('test', $this->Direccion->getC_p());
    }

    public function test_set_and_get_poblacion()
    {
        $poblacionVo = PoblacionText::fromNullableString('Test');
        $this->Direccion->setPoblacionVo($poblacionVo);
        $this->assertInstanceOf(PoblacionText::class, $this->Direccion->getPoblacionVo());
        $this->assertEquals('Test', $this->Direccion->getPoblacionVo()->value());
    }

    public function test_set_and_get_provincia()
    {
        $provinciaVo = new ProvinciaText('Test');
        $this->Direccion->setProvinciaVo($provinciaVo);
        $this->assertInstanceOf(ProvinciaText::class, $this->Direccion->getProvinciaVo());
        $this->assertEquals('Test', $this->Direccion->getProvinciaVo()->value());
    }

    public function test_set_and_get_a_p()
    {
        $a_pVo = new APText('Test');
        $this->Direccion->setAPVo($a_pVo);
        $this->assertInstanceOf(APText::class, $this->Direccion->getAPVo());
        $this->assertEquals('Test', $this->Direccion->getAPVo()->value());
    }

    public function test_set_and_get_pais()
    {
        $paisVo = new PaisName('Spain');
        $this->Direccion->setPaisVo($paisVo);
        $this->assertInstanceOf(PaisName::class, $this->Direccion->getPaisVo());
        $this->assertEquals('Spain', $this->Direccion->getPaisVo()->value());
    }

    public function test_set_and_get_f_direccion()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->Direccion->setF_direccion($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->Direccion->getF_direccion());
        $this->assertEquals('2024-01-15 10:30:00', $this->Direccion->getF_direccion()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_observ()
    {
        $observVo = new ObservDireccionText('Test');
        $this->Direccion->setObservVo($observVo);
        $this->assertInstanceOf(ObservDireccionText::class, $this->Direccion->getObservVo());
        $this->assertEquals('Test', $this->Direccion->getObservVo()->value());
    }

    public function test_set_and_get_cp_dcha()
    {
        $this->Direccion->setCp_dcha(true);
        $this->assertTrue($this->Direccion->isCp_dcha());
    }

    public function test_set_and_get_latitud()
    {
        $latitudVo = new LatitudDecimal(1);
        $this->Direccion->setLatitudVo($latitudVo);
        $this->assertInstanceOf(LatitudDecimal::class, $this->Direccion->getLatitudVo());
        $this->assertEquals(1, $this->Direccion->getLatitudVo()->value());
    }

    public function test_set_and_get_longitud()
    {
        $longitudVo = new LongitudDecimal(1);
        $this->Direccion->setLongitudVo($longitudVo);
        $this->assertInstanceOf(LongitudDecimal::class, $this->Direccion->getLongitudVo());
        $this->assertEquals(1, $this->Direccion->getLongitudVo()->value());
    }

    public function test_set_and_get_plano_doc()
    {
        $plano_docVo = new PlanoDocText('Test');
        $this->Direccion->setPlanoDocVo($plano_docVo);
        $this->assertInstanceOf(PlanoDocText::class, $this->Direccion->getPlanoDocVo());
        $this->assertEquals('Test', $this->Direccion->getPlanoDocVo()->value());
    }

    public function test_set_and_get_plano_extension()
    {
        $plano_extensionVo = new PlanoExtensionText('Test');
        $this->Direccion->setPlanoExtensionVo($plano_extensionVo);
        $this->assertInstanceOf(PlanoExtensionText::class, $this->Direccion->getPlanoExtensionVo());
        $this->assertEquals('test', $this->Direccion->getPlanoExtensionVo()->value());
    }

    public function test_set_and_get_plano_nom()
    {
        $plano_nomVo = new PlanoNameText('Test');
        $this->Direccion->setPlanoNomVo($plano_nomVo);
        $this->assertInstanceOf(PlanoNameText::class, $this->Direccion->getPlanoNomVo());
        $this->assertEquals('Test', $this->Direccion->getPlanoNomVo()->value());
    }

    public function test_set_and_get_nom_sede()
    {
        $nom_sedeVo = new SedeNameText('Test');
        $this->Direccion->setNomSedeVo($nom_sedeVo);
        $this->assertInstanceOf(SedeNameText::class, $this->Direccion->getNomSedeVo());
        $this->assertEquals('Test', $this->Direccion->getNomSedeVo()->value());
    }

    public function test_set_all_attributes()
    {
        $direccion = new Direccion();
        $attributes = [
            'id_direccion' => new DireccionId(1),
            'direccion' => new DireccionText('Test'),
            'c_p' => 'test',
            'poblacion' => new PoblacionText('Test'),
            'provincia' => new ProvinciaText('Test'),
            'a_p' => new APText('Test'),
            'pais' => new PaisName('Spain'),
            'f_direccion' => new DateTimeLocal('2024-01-15 10:30:00'),
            'observ' => new ObservDireccionText('Test'),
            'cp_dcha' => true,
            'latitud' => new LatitudDecimal(1),
            'longitud' => new LongitudDecimal(1),
            'plano_doc' => new PlanoDocText('Test'),
            'plano_extension' => new PlanoExtensionText('Test'),
            'plano_nom' => new PlanoNameText('Test'),
            'nom_sede' => new SedeNameText('Test'),
        ];
        $direccion->setAllAttributes($attributes);

        $this->assertEquals(1, $direccion->getIdDireccionVo()->value());
        $this->assertEquals('Test', $direccion->getDireccionVo()->value());
        $this->assertEquals('test', $direccion->getC_p());
        $this->assertEquals('Test', $direccion->getPoblacionVo()->value());
        $this->assertEquals('Test', $direccion->getProvinciaVo()->value());
        $this->assertEquals('Test', $direccion->getAPVo()->value());
        $this->assertEquals('Spain', $direccion->getPaisVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $direccion->getF_direccion()->format('Y-m-d H:i:s'));
        $this->assertEquals('Test', $direccion->getObservVo()->value());
        $this->assertTrue($direccion->isCp_dcha());
        $this->assertEquals(1, $direccion->getLatitudVo()->value());
        $this->assertEquals(1, $direccion->getLongitudVo()->value());
        $this->assertEquals('Test', $direccion->getPlanoDocVo()->value());
        $this->assertEquals('test', $direccion->getPlanoExtensionVo()->value());
        $this->assertEquals('Test', $direccion->getPlanoNomVo()->value());
        $this->assertEquals('Test', $direccion->getNomSedeVo()->value());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $direccion = new Direccion();
        $attributes = [
            'id_direccion' => 1,
            'direccion' => 'Test',
            'c_p' => 'test',
            'poblacion' => 'Test',
            'provincia' => 'Test',
            'a_p' => 'Test',
            'pais' => 'Spain',
            'f_direccion' => new DateTimeLocal('2024-01-15 10:30:00'),
            'observ' => 'Test',
            'cp_dcha' => true,
            'latitud' => 1,
            'longitud' => 1,
            'plano_doc' => 'Test',
            'plano_extension' => 'Test',
            'plano_nom' => 'Test',
            'nom_sede' => 'Test',
        ];
        $direccion->setAllAttributes($attributes);

        $this->assertEquals(1, $direccion->getIdDireccionVo()->value());
        $this->assertEquals('Test', $direccion->getDireccionVo()->value());
        $this->assertEquals('test', $direccion->getC_p());
        $this->assertEquals('Test', $direccion->getPoblacionVo()->value());
        $this->assertEquals('Test', $direccion->getProvinciaVo()->value());
        $this->assertEquals('Test', $direccion->getAPVo()->value());
        $this->assertEquals('Spain', $direccion->getPaisVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $direccion->getF_direccion()->format('Y-m-d H:i:s'));
        $this->assertEquals('Test', $direccion->getObservVo()->value());
        $this->assertTrue($direccion->isCp_dcha());
        $this->assertEquals(1, $direccion->getLatitudVo()->value());
        $this->assertEquals(1, $direccion->getLongitudVo()->value());
        $this->assertEquals('Test', $direccion->getPlanoDocVo()->value());
        $this->assertEquals('test', $direccion->getPlanoExtensionVo()->value());
        $this->assertEquals('Test', $direccion->getPlanoNomVo()->value());
        $this->assertEquals('Test', $direccion->getNomSedeVo()->value());
    }
}
