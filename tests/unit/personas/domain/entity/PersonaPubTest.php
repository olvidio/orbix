<?php

namespace Tests\unit\personas\domain\entity;

use src\actividades\domain\value_objects\NivelStgrId;
use src\personas\domain\entity\PersonaPub;
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

class PersonaPubTest extends myTest
{
    private PersonaPub $PersonaPub;

    public function setUp(): void
    {
        parent::setUp();
        $this->PersonaPub = new PersonaPub();
        $this->PersonaPub->setId_schema(1);
        $this->PersonaPub->setId_nom(1);
    }

    public function test_set_and_get_id_schema()
    {
        $this->PersonaPub->setId_schema(1);
        $this->assertEquals(1, $this->PersonaPub->getId_schema());
    }

    public function test_set_and_get_id_nom()
    {
        $this->PersonaPub->setId_nom(1);
        $this->assertEquals(1, $this->PersonaPub->getId_nom());
    }

    public function test_set_and_get_id_tabla()
    {
        $id_tablaVo = new PersonaTablaCode('dl');
        $this->PersonaPub->setIdTablaVo($id_tablaVo);
        $this->assertInstanceOf(PersonaTablaCode::class, $this->PersonaPub->getIdTablaVo());
        $this->assertEquals('dl', $this->PersonaPub->getIdTablaVo()->value());
    }

    public function test_set_and_get_dl()
    {
        $dlVo = new DelegacionCode('dlb');
        $this->PersonaPub->setDlVo($dlVo);
        $this->assertInstanceOf(DelegacionCode::class, $this->PersonaPub->getDlVo());
        $this->assertEquals('dlb', $this->PersonaPub->getDlVo()->value());
    }

    public function test_set_and_get_sacd()
    {
        $this->PersonaPub->setSacd(true);
        $this->assertTrue($this->PersonaPub->isSacd());
    }

    public function test_set_and_get_trato()
    {
        $tratoVo = new PersonaTratoCode('Dr.');
        $this->PersonaPub->setTratoVo($tratoVo);
        $this->assertInstanceOf(PersonaTratoCode::class, $this->PersonaPub->getTratoVo());
        $this->assertEquals('Dr.', $this->PersonaPub->getTratoVo()->value());
    }

    public function test_set_and_get_nom()
    {
        $nomVo = new PersonaNombreText('Test value');
        $this->PersonaPub->setNomVo($nomVo);
        $this->assertInstanceOf(PersonaNombreText::class, $this->PersonaPub->getNomVo());
        $this->assertEquals('Test value', $this->PersonaPub->getNomVo()->value());
    }

    public function test_set_and_get_nx1()
    {
        $nx1Vo = new PersonaNx1Text('nx1');
        $this->PersonaPub->setNx1Vo($nx1Vo);
        $this->assertInstanceOf(PersonaNx1Text::class, $this->PersonaPub->getNx1Vo());
        $this->assertEquals('nx1', $this->PersonaPub->getNx1Vo()->value());
    }

    public function test_set_and_get_apellido1()
    {
        $apellido1Vo = new PersonaApellido1Text('Test value');
        $this->PersonaPub->setApellido1Vo($apellido1Vo);
        $this->assertInstanceOf(PersonaApellido1Text::class, $this->PersonaPub->getApellido1Vo());
        $this->assertEquals('Test value', $this->PersonaPub->getApellido1Vo()->value());
    }

    public function test_set_and_get_nx2()
    {
        $nx2Vo = new PersonaNx2Text('nx2');
        $this->PersonaPub->setNx2Vo($nx2Vo);
        $this->assertInstanceOf(PersonaNx2Text::class, $this->PersonaPub->getNx2Vo());
        $this->assertEquals('nx2', $this->PersonaPub->getNx2Vo()->value());
    }

    public function test_set_and_get_apellido2()
    {
        $apellido2Vo = new PersonaApellido2Text('Test value');
        $this->PersonaPub->setApellido2Vo($apellido2Vo);
        $this->assertInstanceOf(PersonaApellido2Text::class, $this->PersonaPub->getApellido2Vo());
        $this->assertEquals('Test value', $this->PersonaPub->getApellido2Vo()->value());
    }

    public function test_set_and_get_f_nacimiento()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->PersonaPub->setF_nacimiento($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->PersonaPub->getF_nacimiento());
        $this->assertEquals('2024-01-15 10:30:00', $this->PersonaPub->getF_nacimiento()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_idioma_preferido()
    {
        $idioma_preferidoVo = new LocaleCode('es_ES.UTF-8');
        $this->PersonaPub->setIdiomaPreferidoVo($idioma_preferidoVo);
        $this->assertInstanceOf(LocaleCode::class, $this->PersonaPub->getIdiomaPreferidoVo());
        $this->assertEquals('es_ES.UTF-8', $this->PersonaPub->getIdiomaPreferidoVo()->value());
    }

    public function test_set_and_get_situacion()
    {
        $situacionVo = new SituacionCode('A');
        $this->PersonaPub->setSituacionVo($situacionVo);
        $this->assertInstanceOf(SituacionCode::class, $this->PersonaPub->getSituacionVo());
        $this->assertEquals('A', $this->PersonaPub->getSituacionVo()->value());
    }

    public function test_set_and_get_f_situacion()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->PersonaPub->setF_situacion($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->PersonaPub->getF_situacion());
        $this->assertEquals('2024-01-15 10:30:00', $this->PersonaPub->getF_situacion()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_apel_fam()
    {
        $apel_famVo = new ApelFamText('Test value');
        $this->PersonaPub->setApelFamVo($apel_famVo);
        $this->assertInstanceOf(ApelFamText::class, $this->PersonaPub->getApelFamVo());
        $this->assertEquals('Test value', $this->PersonaPub->getApelFamVo()->value());
    }

    public function test_set_and_get_inc()
    {
        $incVo = new IncCode('ad');
        $this->PersonaPub->setIncVo($incVo);
        $this->assertInstanceOf(IncCode::class, $this->PersonaPub->getIncVo());
        $this->assertEquals('ad', $this->PersonaPub->getIncVo()->value());
    }

    public function test_set_and_get_f_inc()
    {
        $date = new DateTimeLocal('2024-01-15 10:30:00');
        $this->PersonaPub->setF_inc($date);
        $this->assertInstanceOf(DateTimeLocal::class, $this->PersonaPub->getF_inc());
        $this->assertEquals('2024-01-15 10:30:00', $this->PersonaPub->getF_inc()->format('Y-m-d H:i:s'));
    }

    public function test_set_and_get_nivel_stgr()
    {
        $nivel_stgrVo = new NivelStgrId(1);
        $this->PersonaPub->setNivelStgrVo($nivel_stgrVo);
        $this->assertInstanceOf(NivelStgrId::class, $this->PersonaPub->getNivelStgrVo());
        $this->assertEquals(1, $this->PersonaPub->getNivelStgrVo()->value());
    }

    public function test_set_and_get_profesion()
    {
        $profesionVo = new ProfesionText('Test');
        $this->PersonaPub->setProfesionVo($profesionVo);
        $this->assertInstanceOf(ProfesionText::class, $this->PersonaPub->getProfesionVo());
        $this->assertEquals('Test', $this->PersonaPub->getProfesionVo()->value());
    }

    public function test_set_and_get_eap()
    {
        $eapVo = new EapText('Test');
        $this->PersonaPub->setEapVo($eapVo);
        $this->assertInstanceOf(EapText::class, $this->PersonaPub->getEapVo());
        $this->assertEquals('Test', $this->PersonaPub->getEapVo()->value());
    }

    public function test_set_and_get_observ()
    {
        $observVo = new ObservText('Test');
        $this->PersonaPub->setObservVo($observVo);
        $this->assertInstanceOf(ObservText::class, $this->PersonaPub->getObservVo());
        $this->assertEquals('Test', $this->PersonaPub->getObservVo()->value());
    }

    public function test_set_and_get_id_ctr()
    {
        $this->PersonaPub->setId_ctr(1);
        $this->assertEquals(1, $this->PersonaPub->getId_ctr());
    }

    public function test_set_and_get_lugar_nacimiento()
    {
        $lugar_nacimientoVo = new LugarNacimientoText('Test');
        $this->PersonaPub->setLugarNacimientoVo($lugar_nacimientoVo);
        $this->assertInstanceOf(LugarNacimientoText::class, $this->PersonaPub->getLugarNacimientoVo());
        $this->assertEquals('Test', $this->PersonaPub->getLugarNacimientoVo()->value());
    }

    public function test_set_and_get_edad()
    {
        $this->PersonaPub->setEdad(1);
        $this->assertEquals(1, $this->PersonaPub->getEdad());
    }

    public function test_set_and_get_profesor_stgr()
    {
        $this->PersonaPub->setProfesor_stgr(true);
        $this->assertTrue($this->PersonaPub->isProfesor_stgr());
    }


    public function test_set_all_attributes()
    {
        $personaPub = new PersonaPub();
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
            'edad' => 1,
            'profesor_stgr' => true,
            'Apellidos' => 'test',
            'ApellidosNombre' => 'test',
            'ApellidosNombreCr1_05' => 'test',
            'NombreApellidos' => 'test',
            'NombreApellidosCrSin' => 'test',
            'TituloNombre' => 'test',
            'Centro_o_dl' => 'test',
        ];
        $personaPub->setAllAttributes($attributes);

        $this->assertEquals(1, $personaPub->getId_schema());
        $this->assertEquals(1, $personaPub->getId_nom());
        $this->assertEquals('dl', $personaPub->getIdTablaVo()->value());
        $this->assertEquals('dlb', $personaPub->getDlVo()->value());
        $this->assertTrue($personaPub->isSacd());
        $this->assertEquals('Dr.', $personaPub->getTratoVo()->value());
        $this->assertEquals('Test value', $personaPub->getNomVo()->value());
        $this->assertEquals('nx1', $personaPub->getNx1Vo()->value());
        $this->assertEquals('Test value', $personaPub->getApellido1Vo()->value());
        $this->assertEquals('nx2', $personaPub->getNx2Vo()->value());
        $this->assertEquals('Test value', $personaPub->getApellido2Vo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $personaPub->getF_nacimiento()->format('Y-m-d H:i:s'));
        $this->assertEquals('es_ES.UTF-8', $personaPub->getIdiomaPreferidoVo()->value());
        $this->assertEquals('A', $personaPub->getSituacionVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $personaPub->getF_situacion()->format('Y-m-d H:i:s'));
        $this->assertEquals('Test value', $personaPub->getApelFamVo()->value());
        $this->assertEquals('ad', $personaPub->getIncVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $personaPub->getF_inc()->format('Y-m-d H:i:s'));
        $this->assertEquals(1, $personaPub->getNivelStgrVo()->value());
        $this->assertEquals('Test', $personaPub->getProfesionVo()->value());
        $this->assertEquals('Test', $personaPub->getEapVo()->value());
        $this->assertEquals('Test', $personaPub->getObservVo()->value());
        $this->assertEquals(1, $personaPub->getId_ctr());
        $this->assertEquals('Test', $personaPub->getLugarNacimientoVo()->value());
        $this->assertEquals(1, $personaPub->getEdad());
        $this->assertTrue($personaPub->isProfesor_stgr());
        $this->assertEquals('nx1 Test value nx2 Test value', $personaPub->getApellidos());
        $this->assertEquals('test', $personaPub->getApellidosNombre());
        $this->assertEquals('test', $personaPub->getApellidosNombreCr1_05());
        $this->assertEquals('Dr. Test value nx1 Test value nx2 Test value', $personaPub->getNombreApellidos());
        $this->assertEquals('Test value nx1 Test value nx2 Test value', $personaPub->getNombreApellidosCrSin());
        $this->assertEquals('Dnus. Dr. Test value nx1 Test value nx2 Test value', $personaPub->getTituloNombre());
        $this->assertEquals('dlb', $personaPub->getCentro_o_dl());
    }

    public function test_set_all_attributes_with_string_values()
    {
        $personaPub = new PersonaPub();
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
            'edad' => 1,
            'profesor_stgr' => true,
            'Apellidos' => 'test',
            'ApellidosNombre' => 'test',
            'ApellidosNombreCr1_05' => 'test',
            'NombreApellidos' => 'test',
            'NombreApellidosCrSin' => 'test',
            'TituloNombre' => 'test',
            'Centro_o_dl' => 'test',
        ];
        $personaPub->setAllAttributes($attributes);

        $this->assertEquals(1, $personaPub->getId_schema());
        $this->assertEquals(1, $personaPub->getId_nom());
        $this->assertEquals('dl', $personaPub->getIdTablaVo()->value());
        $this->assertEquals('dlb', $personaPub->getDlVo()->value());
        $this->assertTrue($personaPub->isSacd());
        $this->assertEquals('Dr.', $personaPub->getTratoVo()->value());
        $this->assertEquals('Test value', $personaPub->getNomVo()->value());
        $this->assertEquals('nx1', $personaPub->getNx1Vo()->value());
        $this->assertEquals('Test value', $personaPub->getApellido1Vo()->value());
        $this->assertEquals('nx2', $personaPub->getNx2Vo()->value());
        $this->assertEquals('Test value', $personaPub->getApellido2Vo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $personaPub->getF_nacimiento()->format('Y-m-d H:i:s'));
        $this->assertEquals('es_ES.UTF-8', $personaPub->getIdiomaPreferidoVo()->value());
        $this->assertEquals('A', $personaPub->getSituacionVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $personaPub->getF_situacion()->format('Y-m-d H:i:s'));
        $this->assertEquals('Test value', $personaPub->getApelFamVo()->value());
        $this->assertEquals('ad', $personaPub->getIncVo()->value());
        $this->assertEquals('2024-01-15 10:30:00', $personaPub->getF_inc()->format('Y-m-d H:i:s'));
        $this->assertEquals(1, $personaPub->getNivelStgrVo()->value());
        $this->assertEquals('Test', $personaPub->getProfesionVo()->value());
        $this->assertEquals('Test', $personaPub->getEapVo()->value());
        $this->assertEquals('Test', $personaPub->getObservVo()->value());
        $this->assertEquals(1, $personaPub->getId_ctr());
        $this->assertEquals('Test', $personaPub->getLugarNacimientoVo()->value());
        $this->assertEquals(1, $personaPub->getEdad());
        $this->assertTrue($personaPub->isProfesor_stgr());
        $this->assertEquals('nx1 Test value nx2 Test value', $personaPub->getApellidos());
        $this->assertEquals('test', $personaPub->getApellidosNombre());
        $this->assertEquals('test', $personaPub->getApellidosNombreCr1_05());
        $this->assertEquals('Dr. Test value nx1 Test value nx2 Test value', $personaPub->getNombreApellidos());
        $this->assertEquals('Test value nx1 Test value nx2 Test value', $personaPub->getNombreApellidosCrSin());
        $this->assertEquals('Dnus. Dr. Test value nx1 Test value nx2 Test value', $personaPub->getTituloNombre());
        $this->assertEquals('dlb', $personaPub->getCentro_o_dl());
    }
}
