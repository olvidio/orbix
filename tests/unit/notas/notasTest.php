<?php

namespace Tests\unit\notas;

use core\ConfigGlobal;
use Exception;
use notas\model\EditarPersonaNota;
use notas\model\entity\GestorPersonaNotaDB;
use notas\model\entity\GestorPersonaNotaDlDB;
use notas\model\entity\GestorPersonaNotaOtraRegionStgrDB;
use notas\model\entity\PersonaNotaDB;
use notas\model\entity\PersonaNotaDlDB;
use notas\model\entity\PersonaNotaOtraRegionStgrDB;
use RuntimeException;
use Tests\myTest;
use ubis\model\entity\GestorDelegacion;

class notasTest extends myTest
{
    private string $session_org;

    /**
     * Sets up the test suite prior to every test.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->session_org = $_SESSION['session_auth']['esquema'];
    }


    /////////// Región o dl mal definida (no pertenece al stgr) ///////////

    /**
     * Persona de paso. Nota en r/dl sin pertenecer a una region del stgr
     * debe dar error
     * @return void
     */
    public function test_guardar_nota_sin_region_p_de_paso(): void
    {
        $this->markTestSkipped('Skipped test');
        $this->expectException(RuntimeException::class);

        // dlB desde la que se ejecuta la operación de guardar nota.
        $esquema =  'Pla-crPlav';
        $_SESSION['session_auth']['esquema'] = $esquema;

        // persona de paso: id_nom negativo; esquema = -1001;
        $id_nom = -1001123;
        $id_schema_persona = '-1001'; // restov
        $NotasFactory = new NotasFactory();
        $NotasFactory->setCount(1);
        $dl =  GestorDelegacion::getDlFromSchema($esquema);
        $cPersonaNotas = $NotasFactory->create($id_nom,$dl);
        $personaNota = $cPersonaNotas[0];

        $oEditarPersonaNota = new EditarPersonaNota($personaNota);
        $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr();

        $a_ObjetosPersonaNota = $oEditarPersonaNota->getObjetosPersonaNota($datosRegionStgr, $id_schema_persona);

        $oEditarPersonaNota->crear_nueva_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota);
    }

    ///////////////  Una region ella misma region del stgr ////////////
    //// La única diferencia con la dl del stgr es que la tabla e_notas_otra_region
    /// está en el propio esquema, no existe una instancia superior.
    /**
     * Guardar nota de una persona de paso desde una region del stgr (crB).
     *      nota en la tabla crB.e_notas_otras_region_stgr
     * @return void
     * @throws Exception
     */
    public function test_guardar_nota_region_p_de_paso_desde_crB(): void
    {
        //$this->markTestSkipped('Skipped test');

        // dlB desde la que se ejecuta la operación de guardar nota.
        $esquema = 'GalBel-crGalBelv';
        $_SESSION['session_auth']['esquema'] = $esquema;

        // persona de paso: id_nom negativo; esquema = -1001;
        $id_nom = -1001124;
        $id_schema_persona = '-1001'; // restov
        $NotasFactory = new NotasFactory();
        $NotasFactory->setCount(1);
        $dl =  GestorDelegacion::getDlFromSchema($esquema);
        $cPersonaNotas = $NotasFactory->create($id_nom,$dl);
        $personaNota = $cPersonaNotas[0];

        $oEditarPersonaNota = new EditarPersonaNota($personaNota);
        $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr();

        $a_ObjetosPersonaNota = $oEditarPersonaNota->getObjetosPersonaNota($datosRegionStgr, $id_schema_persona);

        $rta = $oEditarPersonaNota->crear_nueva_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota);
        $oPersonaNota = $rta['nota'];
        $oPersonaNota->DBCarregar(); // Importante: El PDO al hacer execute cambia los integer a string. Con esto vuelven al tipo original.
        $oPersonaNota->getPrimary_key(); // para que tenga el mismo valor que la otra

        $gesDelegacion = new GestorDelegacion();
        $a_mi_region_stgr = $gesDelegacion->mi_region_stgr();
        $esquema_region_stgr = $a_mi_region_stgr['esquema_region_stgr'];

        $gesPersonaNota = new GestorPersonaNotaOtraRegionStgrDB($esquema_region_stgr);
        $cPersonaNota = $gesPersonaNota->getPersonaNotas(['id_nom' => $id_nom, 'id_asignatura' => $personaNota->getIdAsignatura()]);
        $oPersonaNota2 = $cPersonaNota[0];
        $oPersonaNota2->DBCarregar();

        $this->assertEquals($oPersonaNota, $oPersonaNota2);
        $oPersonaNota2->DBEliminar();

        // nota certificado (No debe existir)
        $oPersonaNotaCertificadoDB = $rta['certificado'] ?? '';
        $this->assertEquals('', $oPersonaNotaCertificadoDB);

    }

    ///////////////  Desde una dl/r que pertenece a una region stgr ////////////
    ///
    /**
     * Guardar nota de una persona de paso desde la dlB (que organiza la actividad)
     *      nota en la tabla dlB.e_notas_otras_region_stgr
     * @return void
     * @throws Exception
     */
    public function test_guardar_nota_p_de_paso_desde_dl_stgr(): void
    {
        // dlB desde la que se ejecuta la operación de guardar nota.
        $esquema = 'H-dlbv';
        $_SESSION['session_auth']['esquema'] = $esquema;

        // persona de paso: id_nom negativo; esquema = -1001;
        $id_nom = -1001125;
        $id_schema_persona = '-1001'; // restov
        $NotasFactory = new NotasFactory();
        $NotasFactory->setCount(1);
        $dl =  GestorDelegacion::getDlFromSchema($esquema);
        $cPersonaNotas = $NotasFactory->create($id_nom,$dl);
        $personaNota = $cPersonaNotas[0];

        $oEditarPersonaNota = new EditarPersonaNota($personaNota);
        $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr();

        $a_ObjetosPersonaNota = $oEditarPersonaNota->getObjetosPersonaNota($datosRegionStgr, $id_schema_persona);

        $rta = $oEditarPersonaNota->crear_nueva_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota);
        $oPersonaNota = $rta['nota'];
        $oPersonaNota->DBCarregar(); // Importante: El PDO al hacer execute cambia los integer a string. Con esto vuelven al tipo original.
        $oPersonaNota->getPrimary_key(); // para que tenga el mismo valor que la otra

        $gesDelegacion = new GestorDelegacion();
        $a_mi_region_stgr = $gesDelegacion->mi_region_stgr();
        $esquema_region_stgr = $a_mi_region_stgr['esquema_region_stgr'];

        $gesPersonaNota = new GestorPersonaNotaOtraRegionStgrDB($esquema_region_stgr);
        $cPersonaNota = $gesPersonaNota->getPersonaNotas(['id_nom' => $id_nom, 'id_asignatura' => $personaNota->getIdAsignatura()]);
        $oPersonaNota2 = $cPersonaNota[0];
        $oPersonaNota2->DBCarregar();

        $this->assertEquals($oPersonaNota, $oPersonaNota2);
        $oPersonaNota2->DBEliminar();

        // nota certificado (No debe existir)
        $oPersonaNotaCertificadoDB = $rta['certificado'] ?? '';
        $this->assertEquals('', $oPersonaNotaCertificadoDB);

    }

    /**
     * Guardar nota de una persona dlA (presente en Aquinate) desde la dlB (que organiza la actividad)
     * que pertenecen a distintas regiones del stgr
     *      nota en la tabla dlB.e_notas_otras_region_stgr
     *      nota "falta certificado" en dlA.e_notas_dl.
     * @return void
     * @throws Exception
     */
    public function test_guardar_nota_dl_persona_otra_region_desde_dl_stgr(): void
    {
        // dlB desde la que se ejecuta la operación de guardar nota.
        $esquema = 'H-dlbv';
        $_SESSION['session_auth']['esquema'] = $esquema;
        $_SESSION['session_auth']['mi_id_schema'] = 1001;

        // persona de la dlA
        $id_nom = 1029156;
        $id_schema_persona = 1029; // GalBelv
        $NotasFactory = new NotasFactory();
        $NotasFactory->setCount(1);
        $dl =  GestorDelegacion::getDlFromSchema($esquema);
        $cPersonaNotas = $NotasFactory->create($id_nom,$dl);
        $personaNota = $cPersonaNotas[0];

        $oEditarPersonaNota = new EditarPersonaNota($personaNota);
        $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr();

        $a_ObjetosPersonaNota = $oEditarPersonaNota->getObjetosPersonaNota($datosRegionStgr, $id_schema_persona);

        $rta = $oEditarPersonaNota->crear_nueva_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota);
        $oPersonaNota = $rta['nota'];
        $oPersonaNota->DBCarregar(); // Importante: El PDO al hacer execute cambia los integer a string. Con esto vuelven al tipo original.
        $oPersonaNota->getPrimary_key(); // para que tenga el mismo valor que la otra

        $gesDelegacion = new GestorDelegacion();
        $a_mi_region_stgr = $gesDelegacion->mi_region_stgr();
        $esquema_region_stgr = $a_mi_region_stgr['esquema_region_stgr'];

        // Estoy en H-dlbv. La nota debe estar en GalBel-crGalBelv.
        // por tanto miro en la tabla padre y compruebo que el esquema es el que toca.
        $gesPersonaNota = new GestorPersonaNotaOtraRegionStgrDB($esquema_region_stgr);
        $cPersonaNota = $gesPersonaNota->getPersonaNotas(['id_nom' => $id_nom, 'id_asignatura' => $personaNota->getIdAsignatura()]);
        if (!empty($cPersonaNota)) {
            $oPersonaNota2 = $cPersonaNota[0];
            if (!is_null($oPersonaNota2)) {
                $oPersonaNota2->DBCarregar();
            }
        } else {
            $oPersonaNota2 = null;
        }
        $this->assertEquals($oPersonaNota, $oPersonaNota2);
        $oPersonaNota2->DBEliminar();

        // guardar certificado
        $oPersonaNotaCertificadoDB = $rta['certificado'] ?? '';
        $this->assertNotEquals('', $oPersonaNotaCertificadoDB);

        $oPersonaNotaCertificadoDB->DBCarregar(); // Importante: El PDO al hacer execute cambia los integer a string. Con esto vuelven al tipo original.
        $oPersonaNotaCertificadoDB->getPrimary_key(); // para que tenga el mismo valor que la otra

        // Estoy en H-dlbv. La nota debe estar en GalBel-crGalBelv.
        // por tanto miro en la tabla padre y compruebo que el esquema es el que toca.
        $gesPersonaNotDB = new GestorPersonaNotaDB();
        $cPersonaNotaDB = $gesPersonaNotDB->getPersonaNotas(['id_nom' => $id_nom, 'id_asignatura' => $personaNota->getIdAsignatura()]);
        $oPersonaNotaDB = $cPersonaNotaDB[0];
        $oPersonaNotaDB->DBCarregar();

        // Son dos clases distintas, no se pueden comparar. Miramos las propiedades
        $this->assertNotEquals($oPersonaNotaCertificadoDB, $oPersonaNotaDB);
        $this->assertEquals($oPersonaNotaCertificadoDB->getId_nom(), $oPersonaNotaDB->getId_nom());
        $this->assertEquals($oPersonaNotaCertificadoDB->getId_nivel(), $oPersonaNotaDB->getId_nivel());
        $this->assertEquals($oPersonaNotaCertificadoDB->getId_asignatura(), $oPersonaNotaDB->getId_asignatura());
        $this->assertEquals($oPersonaNotaCertificadoDB->getNota_num(), $oPersonaNotaDB->getNota_num());
        $this->assertEquals($id_schema_persona, $oPersonaNotaDB->getId_schema());

        $oPersonaNotaDB->DBEliminar();
    }
    /**
     * Modificar nota de una persona dlA (presente en Aquinate) desde la dlB (que organiza la actividad)
     * que pertenecen a distintas regiones del stgr
     *      nota en la tabla dlB.e_notas_otras_region_stgr
     *      nota "falta certificado" en dlA.e_notas_dl.
     * @return void
     * @throws Exception
     */
    public function test_modificar_nota_dl_persona_otra_region_desde_dl_stgr(): void
    {
        // dlB desde la que se ejecuta la operación de guardar nota.
        $esquema =  'H-dlbv';
        $_SESSION['session_auth']['esquema'] = $esquema;
        $_SESSION['session_auth']['mi_id_schema'] = 1001;

        // persona de la dlA
        $id_nom = 1029156;
        $id_schema_persona = 1029; // GalBelv
        $NotasFactory = new NotasFactory();
        $NotasFactory->setCount(1);
        $dl =  GestorDelegacion::getDlFromSchema($esquema);
        $cPersonaNotas = $NotasFactory->create($id_nom,$dl);
        $personaNota = $cPersonaNotas[0];

        $oEditarPersonaNota = new EditarPersonaNota($personaNota);
        $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr();

        $a_ObjetosPersonaNota = $oEditarPersonaNota->getObjetosPersonaNota($datosRegionStgr, $id_schema_persona);

        $rta = $oEditarPersonaNota->crear_nueva_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota);
        // No lo compruebo porque ya está el test de guardar.
        // Modifico la Nota:
        $nota_anterior = $personaNota->getNotaNum();
        $personaNota->setNotaNum($nota_anterior-0.5);
        $id_asignatura_real = $personaNota->getIdAsignatura();
        $rta = $oEditarPersonaNota->editar_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota,$id_asignatura_real);


        $oPersonaNota = $rta['nota'];
        $oPersonaNota->DBCarregar(); // Importante: El PDO al hacer execute cambia los integer a string. Con esto vuelven al tipo original.
        $oPersonaNota->getPrimary_key(); // para que tenga el mismo valor que la otra

        $gesDelegacion = new GestorDelegacion();
        $a_mi_region_stgr = $gesDelegacion->mi_region_stgr();
        $esquema_region_stgr = $a_mi_region_stgr['esquema_region_stgr'];

        // Estoy en H-dlbv. La nota debe estar en GalBel-crGalBelv.
        // por tanto miro en la tabla padre y compruebo que el esquema es el que toca.
        $gesPersonaNota = new GestorPersonaNotaOtraRegionStgrDB($esquema_region_stgr);
        $cPersonaNota = $gesPersonaNota->getPersonaNotas(['id_nom' => $id_nom, 'id_asignatura' => $personaNota->getIdAsignatura()]);
        if (!empty($cPersonaNota)) {
            $oPersonaNota2 = $cPersonaNota[0];
            if (!is_null($oPersonaNota2)) {
                $oPersonaNota2->DBCarregar();
            }
        } else {
            $oPersonaNota2 = null;
        }
        $this->assertEquals($oPersonaNota, $oPersonaNota2);
        $oPersonaNota2->DBEliminar();

        // guardar certificado
        $oPersonaNotaCertificadoDB = $rta['certificado'] ?? '';
        $this->assertNotEquals('', $oPersonaNotaCertificadoDB);

        $oPersonaNotaCertificadoDB->DBCarregar(); // Importante: El PDO al hacer execute cambia los integer a string. Con esto vuelven al tipo original.
        $oPersonaNotaCertificadoDB->getPrimary_key(); // para que tenga el mismo valor que la otra

        // Estoy en H-dlbv. La nota debe estar en GalBel-crGalBelv.
        // por tanto miro en la tabla padre y compruebo que el esquema es el que toca.
        $gesPersonaNotDB = new GestorPersonaNotaDB();
        $cPersonaNotaDB = $gesPersonaNotDB->getPersonaNotas(['id_nom' => $id_nom, 'id_asignatura' => $personaNota->getIdAsignatura()]);
        $oPersonaNotaDB = $cPersonaNotaDB[0];
        $oPersonaNotaDB->DBCarregar();

        // Son dos clases distintas, no se pueden comparar. Miramos las propiedades
        $this->assertNotEquals($oPersonaNotaCertificadoDB, $oPersonaNotaDB);
        $this->assertEquals($oPersonaNotaCertificadoDB->getId_nom(), $oPersonaNotaDB->getId_nom());
        $this->assertEquals($oPersonaNotaCertificadoDB->getId_nivel(), $oPersonaNotaDB->getId_nivel());
        $this->assertEquals($oPersonaNotaCertificadoDB->getId_asignatura(), $oPersonaNotaDB->getId_asignatura());
        $this->assertEquals($oPersonaNotaCertificadoDB->getNota_num(), $oPersonaNotaDB->getNota_num());
        $this->assertEquals($id_schema_persona, $oPersonaNotaDB->getId_schema());

        $oPersonaNotaDB->DBEliminar();
    }
    /**
     * Guardar nota de una persona dlA desde la dlB (que organiza la actividad)
     * Si las dos pertenecen a la misma región del stgr
     *      nota en dlA.e_notas_dl.
     * @return void
     * @throws Exception
     */
    public function test_guardar_nota_dl_persona_desde_dl_stgr(): void
    {
        // dlB desde la que se ejecuta la operación de guardar nota.
        $esquema = 'H-dlbv';
        $_SESSION['session_auth']['esquema'] = $esquema;
        $_SESSION['session_auth']['mi_id_schema'] = 1001;

        // persona de la dlA
        $id_nom = 10061256;
        $id_schema_persona = 1006;
        $NotasFactory = new NotasFactory();
        $NotasFactory->setCount(1);
        $dl =  GestorDelegacion::getDlFromSchema($esquema);
        $cPersonaNotas = $NotasFactory->create($id_nom,$dl);
        $personaNota = $cPersonaNotas[0];

        $oEditarPersonaNota = new EditarPersonaNota($personaNota);
        $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr();

        $a_ObjetosPersonaNota = $oEditarPersonaNota->getObjetosPersonaNota($datosRegionStgr, $id_schema_persona);

        $rta = $oEditarPersonaNota->crear_nueva_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota);
        $oPersonaNota = $rta['nota'];
        $oPersonaNota->DBCarregar(); // Importante: El PDO al hacer execute cambia los integer a string. Con esto vuelven al tipo original.
        $oPersonaNota->getPrimary_key(); // para que tenga el mismo valor que la otra

        // Estoy en H-dlbv. La nota debe estar en H-dlsv.
        // por tanto miro en la tabla padre y compruebo que el esquema es el que toca.
        $gesPersonaNota = new GestorPersonaNotaDB();
        $cPersonaNota = $gesPersonaNota->getPersonaNotas(['id_nom' => $id_nom, 'id_asignatura' => $personaNota->getIdAsignatura()]);
        if (!empty($cPersonaNota)) {
            $oPersonaNota2 = $cPersonaNota[0];
            if (!is_null($oPersonaNota2)) {
                $oPersonaNota2->DBCarregar();
            }
        } else {
            $oPersonaNota2 = null;
        }

        // Son dos clases distintas, no se pueden comparar. Miramos las propiedades
        $this->assertNotEquals($oPersonaNota, $oPersonaNota2);
        $this->assertEquals($oPersonaNota->getId_nom(), $oPersonaNota2->getId_nom());
        $this->assertEquals($oPersonaNota->getId_nivel(), $oPersonaNota2->getId_nivel());
        $this->assertEquals($oPersonaNota->getId_asignatura(), $oPersonaNota2->getId_asignatura());
        $this->assertEquals($oPersonaNota->getNota_num(), $oPersonaNota2->getNota_num());

        $this->assertEquals($id_schema_persona, $oPersonaNota2->getId_schema());
        $oPersonaNota2->DBEliminar();

        // guardar certificado
        $oPersonaNotaCertificadoDB = $rta['certificado'] ?? '';
        $this->assertEquals('', $oPersonaNotaCertificadoDB);
    }

    /**
     * Guardar nota de una persona de la dl en su propia dl.
     *      tabla "H-dlbv".e_notas_dl.
     * @return void
     * @throws Exception
     */
    public function test_guardar_nota_dl_persona_dl_desde_dl_stgr(): void
    {
        $esquema = 'H-dlbv';
        $_SESSION['session_auth']['esquema'] = $esquema;
        $_SESSION['session_auth']['mi_id_schema'] = 1001;

        // persona de la dl
        $id_nom = 10011255;
        $id_schema_persona = ConfigGlobal::mi_id_schema(); // El mismo que el de la _SESSION
        $NotasFactory = new NotasFactory();
        $NotasFactory->setCount(1);
        $dl =  GestorDelegacion::getDlFromSchema($esquema);
        $cPersonaNotas = $NotasFactory->create($id_nom,$dl);
        $personaNota = $cPersonaNotas[0];

        $oEditarPersonaNota = new EditarPersonaNota($personaNota);
        $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr();

        $a_ObjetosPersonaNota = $oEditarPersonaNota->getObjetosPersonaNota($datosRegionStgr, $id_schema_persona);

        $rta = $oEditarPersonaNota->crear_nueva_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota);
        $oPersonaNota = $rta['nota'];
        $oPersonaNota->DBCarregar(); // Importante: El PDO al hacer execute cambia los integer a string. Con esto vuelven al tipo original.
        $oPersonaNota->getPrimary_key(); // para que tenga el mismo valor que la otra

        $gesPersonaNota = new GestorPersonaNotaDlDB();
        $cPersonaNota = $gesPersonaNota->getPersonaNotas(['id_nom' => $id_nom, 'id_asignatura' => $personaNota->getIdAsignatura()]);
        $oPersonaNota2 = $cPersonaNota[0];
        $oPersonaNota2->DBCarregar();

        $this->assertEquals($oPersonaNota, $oPersonaNota2);
        $oPersonaNota2->DBEliminar();

        // nota certificado (No debe existir)
        $oPersonaNotaCertificado = $rta['certificado'] ?? '';
        $this->assertEquals('', $oPersonaNotaCertificado);

    }


    /**
     * Comprobar que se puede guardar una nota en la base de datos:
     *  tabla "H-Hv".e_notas_otra_region_stgr
     *
     * @return void
     */
    public function test_save_PersonaNotaOtraRegionStgr(): void
    {
        $esquema_region_stgr = "H-Hv";
        $oPersonaNotaI = new PersonaNotaOtraRegionStgrDB($esquema_region_stgr);
        $oPersonaNota = $this->crear_PersonaNota($oPersonaNotaI);
        $oPersonaNota->DBGuardar();
        $oPersonaNota->DBCarregar();
        $oPersonaNota->getPrimary_key();

        $id_nom = $oPersonaNota->getId_nom();
        $id_asignatura = $oPersonaNota->getId_asignatura();

        $gesPersonaNota = new GestorPersonaNotaOtraRegionStgrDB($esquema_region_stgr);
        $cPersonaNota = $gesPersonaNota->getPersonaNotas(['id_nom' => $id_nom, 'id_asignatura' => $id_asignatura]);
        $oPersonaNota2 = $cPersonaNota[0];
        $oPersonaNota2->DBCarregar();

        $this->assertEquals($oPersonaNota, $oPersonaNota2);
        $oPersonaNota2->DBEliminar();
    }

    /**
     * Comprobar que se puede guardar una nota en la base de datos:
     *  tabla "H-dlbv".e_notas_dl (la dl que está ejecutando el test)
     *
     * @return void
     */
    public function test_save_PersonaNotaDl(): void
    {
        $oPersonaNotaDl = new PersonaNotaDlDB();
        $oPersonaNota = $this->crear_PersonaNota($oPersonaNotaDl);
        $oPersonaNota->DBGuardar();
        $oPersonaNota->DBCarregar();
        $oPersonaNota->getPrimary_key();

        $id_nom = $oPersonaNota->getId_nom();
        $id_asignatura = $oPersonaNota->getId_asignatura();

        $gesPersonaNota = new GestorPersonaNotaDlDB();
        $cPersonaNota = $gesPersonaNota->getPersonaNotas(['id_nom' => $id_nom, 'id_asignatura' => $id_asignatura]);
        $oPersonaNota2 = $cPersonaNota[0];
        $oPersonaNota2->DBCarregar();

        $this->assertEquals($oPersonaNota, $oPersonaNota2);
        $oPersonaNota2->DBEliminar();
    }

    public function crear_PersonaNota($oPersonaNotaDB): PersonaNotaDB
    {
        $esquema = 'H-dlbv';
        $id_nom = 100112345;
        $NotasFactory = new NotasFactory();
        $NotasFactory->setCount(1);
        $dl =  GestorDelegacion::getDlFromSchema($esquema);
        $cPersonaNotas = $NotasFactory->create($id_nom,$dl);
        $personaNota = $cPersonaNotas[0];

        $oPersonaNotaDB->setId_nivel($personaNota->getIdNivel());
        $oPersonaNotaDB->setId_asignatura($personaNota->getIdAsignatura());
        $oPersonaNotaDB->setId_nom($personaNota->getIdNom());
        $oPersonaNotaDB->setId_situacion($personaNota->getIdSituacion());
        $oPersonaNotaDB->setActa($personaNota->getActa());
        $oPersonaNotaDB->setF_acta($personaNota->getFActa());
        $oPersonaNotaDB->setTipo_acta($personaNota->getTipoActa());
        $oPersonaNotaDB->setPreceptor($personaNota->isPreceptor());
        $oPersonaNotaDB->setId_preceptor($personaNota->getIdPreceptor());
        $oPersonaNotaDB->setDetalle($personaNota->getDetalle());
        $oPersonaNotaDB->setEpoca($personaNota->getEpoca());
        $oPersonaNotaDB->setId_activ($personaNota->getIdActiv());
        $oPersonaNotaDB->setNota_num($personaNota->getNotaNum());
        $oPersonaNotaDB->setNota_max($personaNota->getNotaMax());

        return $oPersonaNotaDB;

    }


    /**
     * Runs at the end of every test.
     */
    protected function tearDown(): void
    {
        $_SESSION['session_auth']['esquema'] = $this->session_org;
        parent::tearDown();
    }

}