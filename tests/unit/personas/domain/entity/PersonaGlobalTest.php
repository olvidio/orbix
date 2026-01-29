<?php

namespace Tests\unit\personas\domain\entity;

use src\actividades\domain\value_objects\NivelStgrId;
use src\personas\domain\entity\PersonaGlobal;
use src\personas\domain\value_objects\ApelFamText;
use src\personas\domain\value_objects\EapText;
use src\personas\domain\value_objects\IncCode;
use src\personas\domain\value_objects\LugarNacimientoText;
use src\personas\domain\value_objects\ObservText;
use src\personas\domain\value_objects\PersonaApellido1Text;
use src\personas\domain\value_objects\PersonaApellido2Text;
use src\personas\domain\value_objects\PersonaNombreText;
use src\personas\domain\value_objects\PersonaNx1Text;
use src\personas\domain\value_objects\PersonaNx2Text;
use src\personas\domain\value_objects\PersonaTablaCode;
use src\personas\domain\value_objects\PersonaTratoCode;
use src\personas\domain\value_objects\ProfesionText;
use src\personas\domain\value_objects\SituacionCode;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\LocaleCode;
use src\ubis\domain\value_objects\DelegacionCode;
use Tests\myTest;

class PersonaGlobalTest extends myTest
{
    private PersonaGlobal $PersonaGlobal;

    public function setUp(): void
    {
        parent::setUp();
        $this->PersonaGlobal = new PersonaGlobal();
        $this->PersonaGlobal->setId_schema(1);
        $this->PersonaGlobal->setId_nom(1);
    }

    public function test_set_and_get_id_schema()
    {
        $this->PersonaGlobal->setId_schema(1);
        $this->assertEquals(1, $this->PersonaGlobal->getId_schema());
    }

    public function test_set_and_get_id_nom()
    {
        $this->PersonaGlobal->setId_nom(1);
        $this->assertEquals(1, $this->PersonaGlobal->getId_nom());
    }

    public function test_set_and_get_id_tabla()
    {
        $id_tablaVo = new PersonaTablaCode('dl');
        $this->PersonaGlobal->setIdTablaVo($id_tablaVo);
        $this->assertInstanceOf(PersonaTablaCode::class, $this->PersonaGlobal->getIdTablaVo());
        $this->assertEquals('dl', $this->PersonaGlobal->getIdTablaVo()->value());
    }

    public function test_set_and_get_dl()
    {
        $dlVo = new DelegacionCode('dlb');
        $this->PersonaGlobal->setDlVo($dlVo);
        $this->assertInstanceOf(DelegacionCode::class, $this->PersonaGlobal->getDlVo());
        $this->assertEquals('dlb', $this->PersonaGlobal->getDlVo()->value());
    }

    public function test_set_and_get_sacd()
    {
        $this->PersonaGlobal->setSacd(true);
        $this->assertTrue($this->PersonaGlobal->isSacd());
    }

    public function test_set_and_get_trato()
    {
        $tratoVo = new PersonaTratoCode('Dr.');
        $this->PersonaGlobal->setTratoVo($tratoVo);
        $this->assertInstanceOf(PersonaTratoCode::class, $this->PersonaGlobal->getTratoVo());
        $this->assertEquals('Dr.', $this->PersonaGlobal->getTratoVo()->value());
    }

    public function test_set_and_get_nom()
    {
        $nomVo = new PersonaNombreText('Test value');
        $this->PersonaGlobal->setNomVo($nomVo);
        $this->assertInstanceOf(PersonaNombreText::class, $this->PersonaGlobal->getNomVo());
        $this->assertEquals('Test value', $this->PersonaGlobal->getNomVo()->value());
    }

    public function test_set_and_get_nx1()
    {
        $nx1Vo = new PersonaNx1Text('nx1');
        $this->PersonaGlobal->setNx1Vo($nx1Vo);
        $this->assertInstanceOf(PersonaNx1Text::class, $this->PersonaGlobal->getNx1Vo());
        $this->assertEquals('nx1', $this->PersonaGlobal->getNx1Vo()->value());
    }

    public function test_set_and_get_apellido1()
    {
        $apellido1Vo = new PersonaApellido1Text('Test value');
        $this->PersonaGlobal->setApellido1Vo($apellido1Vo);
        $this->assertInstanceOf(PersonaApellido1Text::class, $this->PersonaGlobal->getApellido1Vo());
        $this->assertEquals('Test value', $this->PersonaGlobal->getApellido1Vo()->value());
    }

    public function test_set_and_get_nx2()
    {
        $nx2Vo = new PersonaNx2Text('nx2');
        $this->PersonaGlobal->setNx2Vo($nx2Vo);
        $this->assertInstanceOf(PersonaNx2Text::class, $this->PersonaGlobal->getNx2Vo());
        $this->assertEquals('nx2', $this->PersonaGlobal->getNx2Vo()->value());
    }

    public function test_set_and_get_apellido2()
    {
        $apellido2Vo = new PersonaApellido2Text('Test value');
        $this->PersonaGlobal->setApellido2Vo($apellido2Vo);
        $this->assertInstanceOf(PersonaApellido2Text::class, $this->PersonaGlobal->getApellido2Vo());
        $this->assertEquals('Test value', $this->PersonaGlobal->getApellido2Vo()->value());
    }

    public function test_set_and_get_f_nacimiento()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->PersonaGlobal->setF_nacimiento($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->PersonaGlobal->getF_nacimiento());
        $this->assertEquals('2024-01-15 10:30:00', $this->PersonaGlobal->getF_nacimiento()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_idioma_preferido()
    {
        $idioma_preferidoVo = new LocaleCode('es_ES.UTF-8');
        $this->PersonaGlobal->setIdiomaPreferidoVo($idioma_preferidoVo);
        $this->assertInstanceOf(LocaleCode::class, $this->PersonaGlobal->getIdiomaPreferidoVo());
        $this->assertEquals('es_ES.UTF-8', $this->PersonaGlobal->getIdiomaPreferidoVo()->value());
    }

    public function test_set_and_get_situacion()
    {
        $situacionVo = new SituacionCode('A');
        $this->PersonaGlobal->setSituacionVo($situacionVo);
        $this->assertInstanceOf(SituacionCode::class, $this->PersonaGlobal->getSituacionVo());
        $this->assertEquals('A', $this->PersonaGlobal->getSituacionVo()->value());
    }

    public function test_set_and_get_f_situacion()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->PersonaGlobal->setF_situacion($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->PersonaGlobal->getF_situacion());
        $this->assertEquals('2024-01-15 10:30:00', $this->PersonaGlobal->getF_situacion()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_apel_fam()
    {
        $apel_famVo = new ApelFamText('Test value');
        $this->PersonaGlobal->setApelFamVo($apel_famVo);
        $this->assertInstanceOf(ApelFamText::class, $this->PersonaGlobal->getApelFamVo());
        $this->assertEquals('Test value', $this->PersonaGlobal->getApelFamVo()->value());
    }

    public function test_set_and_get_inc()
    {
        $incVo = new IncCode('ad');
        $this->PersonaGlobal->setIncVo($incVo);
        $this->assertInstanceOf(IncCode::class, $this->PersonaGlobal->getIncVo());
        $this->assertEquals('ad', $this->PersonaGlobal->getIncVo()->value());
    }

    public function test_set_and_get_f_inc()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->PersonaGlobal->setF_inc($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->PersonaGlobal->getF_inc());
        $this->assertEquals('2024-01-15 10:30:00', $this->PersonaGlobal->getF_inc()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_nivel_stgr()
    {
        $nivel_stgrVo = new NivelStgrId(1);
        $this->PersonaGlobal->setNivelStgrVo($nivel_stgrVo);
        $this->assertInstanceOf(NivelStgrId::class, $this->PersonaGlobal->getNivelStgrVo());
        $this->assertEquals(1, $this->PersonaGlobal->getNivelStgrVo()->value());
    }

    public function test_set_and_get_profesion()
    {
        $profesionVo = new ProfesionText('Test');
        $this->PersonaGlobal->setProfesionVo($profesionVo);
        $this->assertInstanceOf(ProfesionText::class, $this->PersonaGlobal->getProfesionVo());
        $this->assertEquals('Test', $this->PersonaGlobal->getProfesionVo()->value());
    }

    public function test_set_and_get_eap()
    {
        $eapVo = new EapText('Test');
        $this->PersonaGlobal->setEapVo($eapVo);
        $this->assertInstanceOf(EapText::class, $this->PersonaGlobal->getEapVo());
        $this->assertEquals('Test', $this->PersonaGlobal->getEapVo()->value());
    }

    public function test_set_and_get_observ()
    {
        $observVo = new ObservText('Test');
        $this->PersonaGlobal->setObservVo($observVo);
        $this->assertInstanceOf(ObservText::class, $this->PersonaGlobal->getObservVo());
        $this->assertEquals('Test', $this->PersonaGlobal->getObservVo()->value());
    }

    public function test_set_and_get_id_ctr()
    {
        $this->PersonaGlobal->setId_ctr(1);
        $this->assertEquals(1, $this->PersonaGlobal->getId_ctr());
    }

    public function test_set_and_get_lugar_nacimiento()
    {
        $lugar_nacimientoVo = new LugarNacimientoText('Test');
        $this->PersonaGlobal->setLugarNacimientoVo($lugar_nacimientoVo);
        $this->assertInstanceOf(LugarNacimientoText::class, $this->PersonaGlobal->getLugarNacimientoVo());
        $this->assertEquals('Test', $this->PersonaGlobal->getLugarNacimientoVo()->value());
    }

    public function test_set_and_get_es_publico()
    {
        $this->PersonaGlobal->setEs_publico(true);
        $this->assertTrue($this->PersonaGlobal->isEs_publico());
    }


    public function test_set_all_attributes()
    {
        $personaGlobal = new PersonaGlobal();
        $attributes = [
            'id_schema' => 1,
            'id_nom' => 1,
            'id_tabla' => new PersonaTablaCode('dl'),
            'dl' => new DelegacionCode('dlb'),
            'sacd' => true,
            'trato' => new PersonaTratoCode('Dr.'),
            'nom' => new PersonaNombreText('Test value'),
            'nx1' => new PersonaNx1Text('nx1'),
            'apellido1' => new PersonaApellido1Text('Test value'),
            'nx2' => new PersonaNx2Text('nx2'),
            'apellido2' => new PersonaApellido2Text('Test value'),
            'f_nacimiento' => new DateTimeLocal('2024-01-15 10:30:00'),
            'idioma_preferido' => new LocaleCode('es_ES.UTF-8'),
            'situacion' => new SituacionCode('A'),
            'f_situacion' => new DateTimeLocal('2024-01-15 10:30:00'),
            'apel_fam' => new ApelFamText('Test value'),
            'inc' => new IncCode('ad'),
            'f_inc' => new DateTimeLocal('2024-01-15 10:30:00'),
            'nivel_stgr' => new NivelStgrId(1),
            'profesion' => new ProfesionText('Test'),
            'eap' => new EapText('Test'),
            'observ' => new ObservText('Test'),
            'id_ctr' => 1,
            'lugar_nacimiento' => new LugarNacimientoText('Test'),
            'es_publico' => true,
            'Apellidos' => 'test',
            'ApellidosNombre' => 'test',
            'ApellidosNombreCr1_05' => 'test',
            'NombreApellidos' => 'test',
            'NombreApellidosCrSin' => 'test',
            'TituloNombre' => 'test',
            'Centro_o_dl' => 'test',
        ];
        $personaGlobal->setAllAttributes($attributes);

        $this->assertEquals(1, $personaGlobal->getId_schema());
        $this->assertEquals(1, $personaGlobal->getId_nom());
        $this->assertEquals('dl', $personaGlobal->getIdTablaVo()->value());
        $this->assertEquals('dlb', $personaGlobal->getDlVo()->value());
        $this->assertTrue($personaGlobal->isSacd());
        $this->assertEquals('Dr.', $personaGlobal->getTratoVo()->value());
        $this->assertEquals('Test value', $personaGlobal->getNomVo()->value());
        $this->assertEquals('nx1', $personaGlobal->getNx1Vo()->value());
        $this->assertEquals('Test value', $personaGlobal->getApellido1Vo()->value());
        $this->assertEquals('nx2', $personaGlobal->getNx2Vo()->value());
        $this->assertEquals('Test value', $personaGlobal->getApellido2Vo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $personaGlobal->getF_nacimiento()->format('Y-m-d H:i:s'));
        $this->assertEquals('es_ES.UTF-8', $personaGlobal->getIdiomaPreferidoVo()->value());
        $this->assertEquals('A', $personaGlobal->getSituacionVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $personaGlobal->getF_situacion()->format('Y-m-d H:i:s'));
        $this->assertEquals('Test value', $personaGlobal->getApelFamVo()->value());
        $this->assertEquals('ad', $personaGlobal->getIncVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $personaGlobal->getF_inc()->format('Y-m-d H:i:s'));
        $this->assertEquals(1, $personaGlobal->getNivelStgrVo()->value());
        $this->assertEquals('Test', $personaGlobal->getProfesionVo()->value());
        $this->assertEquals('Test', $personaGlobal->getEapVo()->value());
        $this->assertEquals('Test', $personaGlobal->getObservVo()->value());
        $this->assertEquals(1, $personaGlobal->getId_ctr());
        $this->assertEquals('Test', $personaGlobal->getLugarNacimientoVo()->value());
        $this->assertTrue($personaGlobal->isEs_publico());
        $this->assertEquals('nx1 Test value nx2 Test value', $personaGlobal->getApellidos());
        $this->assertEquals('test', $personaGlobal->getApellidosNombre());
        $this->assertEquals('test', $personaGlobal->getApellidosNombreCr1_05());
        $this->assertEquals('Dr. Test value nx1 Test value nx2 Test value', $personaGlobal->getNombreApellidos());
        $this->assertEquals('Test value nx1 Test value nx2 Test value', $personaGlobal->getNombreApellidosCrSin());
        $this->assertEquals('Dnus. Dr. Test value nx1 Test value nx2 Test value', $personaGlobal->getTituloNombre());
        $this->assertEquals('?', $personaGlobal->getCentro_o_dl());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $personaGlobal = new PersonaGlobal();
        $attributes = [
            'id_schema' => 1,
            'id_nom' => 1,
            'id_tabla' => 'dl',
            'dl' => 'dlb',
            'sacd' => true,
            'trato' => 'Dr.',
            'nom' => 'Test value',
            'nx1' => 'nx1',
            'apellido1' => 'Test value',
            'nx2' => 'nx2',
            'apellido2' => 'Test value',
            'f_nacimiento' => new DateTimeLocal('2024-01-15 10:30:00'),
            'idioma_preferido' => 'es_ES.UTF-8',
            'situacion' => 'A',
            'f_situacion' => new DateTimeLocal('2024-01-15 10:30:00'),
            'apel_fam' => 'Test value',
            'inc' => 'ad',
            'f_inc' => new DateTimeLocal('2024-01-15 10:30:00'),
            'nivel_stgr' => 1,
            'profesion' => 'Test',
            'eap' => 'Test',
            'observ' => 'Test',
            'id_ctr' => 1,
            'lugar_nacimiento' => 'Test',
            'es_publico' => true,
            'Apellidos' => 'test',
            'ApellidosNombre' => 'test',
            'ApellidosNombreCr1_05' => 'test',
            'NombreApellidos' => 'test',
            'NombreApellidosCrSin' => 'test',
            'TituloNombre' => 'test',
            'Centro_o_dl' => 'test',
        ];
        $personaGlobal->setAllAttributes($attributes);

        $this->assertEquals(1, $personaGlobal->getId_schema());
        $this->assertEquals(1, $personaGlobal->getId_nom());
        $this->assertEquals('dl', $personaGlobal->getIdTablaVo()->value());
        $this->assertEquals('dlb', $personaGlobal->getDlVo()->value());
        $this->assertTrue($personaGlobal->isSacd());
        $this->assertEquals('Dr.', $personaGlobal->getTratoVo()->value());
        $this->assertEquals('Test value', $personaGlobal->getNomVo()->value());
        $this->assertEquals('nx1', $personaGlobal->getNx1Vo()->value());
        $this->assertEquals('Test value', $personaGlobal->getApellido1Vo()->value());
        $this->assertEquals('nx2', $personaGlobal->getNx2Vo()->value());
        $this->assertEquals('Test value', $personaGlobal->getApellido2Vo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $personaGlobal->getF_nacimiento()->format('Y-m-d H:i:s'));
        $this->assertEquals('es_ES.UTF-8', $personaGlobal->getIdiomaPreferidoVo()->value());
        $this->assertEquals('A', $personaGlobal->getSituacionVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $personaGlobal->getF_situacion()->format('Y-m-d H:i:s'));
        $this->assertEquals('Test value', $personaGlobal->getApelFamVo()->value());
        $this->assertEquals('ad', $personaGlobal->getIncVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $personaGlobal->getF_inc()->format('Y-m-d H:i:s'));
        $this->assertEquals(1, $personaGlobal->getNivelStgrVo()->value());
        $this->assertEquals('Test', $personaGlobal->getProfesionVo()->value());
        $this->assertEquals('Test', $personaGlobal->getEapVo()->value());
        $this->assertEquals('Test', $personaGlobal->getObservVo()->value());
        $this->assertEquals(1, $personaGlobal->getId_ctr());
        $this->assertEquals('Test', $personaGlobal->getLugarNacimientoVo()->value());
        $this->assertTrue($personaGlobal->isEs_publico());
        $this->assertEquals('nx1 Test value nx2 Test value', $personaGlobal->getApellidos());
        $this->assertEquals('test', $personaGlobal->getApellidosNombre());
        $this->assertEquals('test', $personaGlobal->getApellidosNombreCr1_05());
        $this->assertEquals('Dr. Test value nx1 Test value nx2 Test value', $personaGlobal->getNombreApellidos());
        $this->assertEquals('Test value nx1 Test value nx2 Test value', $personaGlobal->getNombreApellidosCrSin());
        $this->assertEquals('Dnus. Dr. Test value nx1 Test value nx2 Test value', $personaGlobal->getTituloNombre());
        $this->assertEquals('?', $personaGlobal->getCentro_o_dl());
    }
}
