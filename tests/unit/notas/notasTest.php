<?php

namespace Tests\unit\notas;

use core\ConfigGlobal;
use notas\model\EditarPersonaNota;
use notas\model\entity\GestorPersonaNotaDl;
use notas\model\entity\GestorPersonaNotaOtraRegionStgr;
use notas\model\entity\PersonaNotaDl;
use notas\model\entity\PersonaNotaOtraRegionStgr;
use Tests\myTest;
use ubis\model\entity\GestorDelegacion;
use web\DateTimeLocal;

class notasTest extends myTest
{
    /**
     * Sets up the test suite prior to every test.
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_adding()
    {
        $this->assertEquals(3, 1 + 2);
    }

     /////////// Región o dl mal definida (no pertenece al stgr) ///////////
    /**
     * Persona de paso. Nota en r/dl sin pertenecer a una region del stgr
     * debe dar error
     * @return void
     */
    public function test_guardar_nota_sin_region_p_de_paso()
    {
        $this->expectException(\RuntimeException::class);

        $id_nom = -1001123;
        $id_asignatura = 234;
        $id_nivel = 234;

        $session_org = $_SESSION['session_auth']['esquema'];
        $_SESSION['session_auth']['esquema'] = 'Pla-crPlav';

        $oEditarPersonaNota = new EditarPersonaNota($id_nom, $id_asignatura, $id_nivel);
        $camposExtra = $this->crear_camposExtra();
        $oEditarPersonaNota->nuevo( $camposExtra);

        $_SESSION['session_auth']['esquema'] = $session_org;
    }

    ///////////////  Una region ella misma region del stgr ////////////

    /**
     * Persona de paso. Nota en region que pertenecer a una region del stgr
     * debe guardar la nota en la tabla del r-stgr
     * @return void
     * @throws \Exception
     */
    public function test_guardar_nota_region_p_de_paso()
    {
        // persona de paso: id_nom negativo; esquema = -1001;
        $id_nom = -1001123;
        $id_schema_persona = '-1001'; // restov
        $id_asignatura = 234;
        $id_nivel = 234;

        $session_org = $_SESSION['session_auth']['esquema'];
        $_SESSION['session_auth']['esquema'] = 'GalBel-crGalBelv';

        $oEditarPersonaNota = new EditarPersonaNota($id_nom, $id_asignatura, $id_nivel);
        $oEditarPersonaNota->setMock(TRUE);
        $camposExtra = $this->crear_camposExtra();
        $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr();

        $a_ObjetosPersonaNota = $oEditarPersonaNota->getObjetosPersonaNota($datosRegionStgr, $id_schema_persona);

        $rta = $oEditarPersonaNota->nuevo2($a_ObjetosPersonaNota, $camposExtra);
        $oPersonaNota = $rta['nota'];
        $oPersonaNota->DBCarregar(); // Importante: El PDO al hacer execute cambia los integer a string. Con esto vuelven al tipo original.
        $oPersonaNota->getPrimary_key(); // para que tenga el mismo valor que la otra
        $oPersonaNotaCertificado = $rta['certificado']?? '';

        $gesDelegacion = new GestorDelegacion();
        $a_mi_region_stgr = $gesDelegacion->mi_region_stgr();
        $esquema_region_stgr = $a_mi_region_stgr['esquema_region_stgr'];

        $gesPersonaNota = new GestorPersonaNotaOtraRegionStgr($esquema_region_stgr, TRUE);
        $cPersonaNota = $gesPersonaNota->getPersonaNotas(['id_nom' => $id_nom, 'id_asignatura' => $id_asignatura]);
        $oPersonaNota2 = $cPersonaNota[0];
        $oPersonaNota2->DBCarregar();

        $this->assertEquals($oPersonaNota, $oPersonaNota2);

        $_SESSION['session_auth']['esquema'] = $session_org;
    }

    ///////////////  Una dl/r que pertenece a una region stgr ////////////
    ///
    /**
     * Persona de paso. Nota en dl que pertenece a una region del stgr
     * debe guardar la nota en la tabla del r-stgr
     * @return void
     * @throws \Exception
     */
    public function test_guardar_nota_delegacion_p_de_paso()
    {
        // persona de paso: id_nom negativo; esquema = -1001;
        $id_nom = -1001123;
        $id_schema_persona = '-1001'; // restov
        $id_asignatura = 234;
        $id_nivel = 234;

        $session_org = $_SESSION['session_auth']['esquema'];
        $_SESSION['session_auth']['esquema'] = 'H-dlbv';

        $oEditarPersonaNota = new EditarPersonaNota($id_nom, $id_asignatura, $id_nivel);
        $oEditarPersonaNota->setMock(TRUE);
        $camposExtra = $this->crear_camposExtra();
        $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr();

        $a_ObjetosPersonaNota = $oEditarPersonaNota->getObjetosPersonaNota($datosRegionStgr, $id_schema_persona);

        $rta = $oEditarPersonaNota->nuevo2($a_ObjetosPersonaNota, $camposExtra);
        $oPersonaNota = $rta['nota'];
        $oPersonaNota->DBCarregar(); // Importante: El PDO al hacer execute cambia los integer a string. Con esto vuelven al tipo original.
        $oPersonaNota->getPrimary_key(); // para que tenga el mismo valor que la otra
        $oPersonaNotaCertificado = $rta['certificado']?? '';

        $gesDelegacion = new GestorDelegacion();
        $a_mi_region_stgr = $gesDelegacion->mi_region_stgr();
        $esquema_region_stgr = $a_mi_region_stgr['esquema_region_stgr'];

        $gesPersonaNota = new GestorPersonaNotaOtraRegionStgr($esquema_region_stgr, TRUE);
        $cPersonaNota = $gesPersonaNota->getPersonaNotas(['id_nom' => $id_nom, 'id_asignatura' => $id_asignatura]);
        $oPersonaNota2 = $cPersonaNota[0];
        $oPersonaNota2->DBCarregar();

        $this->assertEquals($oPersonaNota, $oPersonaNota2);

        $_SESSION['session_auth']['esquema'] = $session_org;
    }

    /**
     * Persona de otra dl de Aquinate. Nota en dl que pertenece a una region del stgr
     * debe guardar la nota en la tabla r-stgr + certificado en dl alumno.
     * @return void
     * @throws \Exception
     */
    public function test_guardar_nota_dl_persona_otra_region()
    {
        $session_org = $_SESSION['session_auth']['esquema'];
        $_SESSION['session_auth']['esquema'] = 'H-dlbv';
        $_SESSION['session_auth']['mi_id_schema'] = 1001;

        // persona de otra dl
        $id_nom = 10011255;
        $id_schema_persona = ConfigGlobal::mi_id_schema(); // El mismo que el de la _SESSION
        $id_asignatura = 23456;
        $id_nivel = 23456;

        $oEditarPersonaNota = new EditarPersonaNota($id_nom, $id_asignatura, $id_nivel);
        $oEditarPersonaNota->setMock(TRUE);
        $camposExtra = $this->crear_camposExtra();
        $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr();

        $a_ObjetosPersonaNota = $oEditarPersonaNota->getObjetosPersonaNota($datosRegionStgr, $id_schema_persona);

        $rta = $oEditarPersonaNota->nuevo2($a_ObjetosPersonaNota, $camposExtra);
        $oPersonaNota = $rta['nota'];
        $oPersonaNota->DBCarregar(); // Importante: El PDO al hacer execute cambia los integer a string. Con esto vuelven al tipo original.
        $oPersonaNota->getPrimary_key(); // para que tenga el mismo valor que la otra

        $gesDelegacion = new GestorDelegacion();
        $a_mi_region_stgr = $gesDelegacion->mi_region_stgr();
        $esquema_region_stgr = $a_mi_region_stgr['esquema_region_stgr'];

        $gesPersonaNota = new GestorPersonaNotaOtraRegionStgr($esquema_region_stgr, TRUE);
        $cPersonaNota = $gesPersonaNota->getPersonaNotas(['id_nom' => $id_nom, 'id_asignatura' => $id_asignatura]);
        $oPersonaNota2 = $cPersonaNota[0];
        $oPersonaNota2->DBCarregar();

        $this->assertEquals($oPersonaNota, $oPersonaNota2);

        // guardar certificado
        $oPersonaNotaCertificado = $rta['certificado']?? '';
        $this->assertNotEquals('', $oPersonaNotaCertificado);

        $oPersonaNotaCertificado->DBCarregar(); // Importante: El PDO al hacer execute cambia los integer a string. Con esto vuelven al tipo original.
        $oPersonaNotaCertificado->getPrimary_key(); // para que tenga el mismo valor que la otra

        $gesDelegacion = new GestorDelegacion();
        $a_mi_region_stgr = $gesDelegacion->mi_region_stgr();
        $esquema_region_stgr = $a_mi_region_stgr['esquema_region_stgr'];

        $gesPersonaNotaCertficado = new GestorPersonaNotaOtraRegionStgr($esquema_region_stgr, TRUE);
        $cPersonaNotaCertificado = $gesPersonaNotaCertficado->getPersonaNotas(['id_nom' => $id_nom, 'id_asignatura' => $id_asignatura]);
        $oPersonaNotaCertificado2 = $cPersonaNotaCertificado[0];
        $oPersonaNotaCertificado2->DBCarregar();

        $this->assertEquals($oPersonaNotaCertificado, $oPersonaNotaCertificado2);

        $_SESSION['session_auth']['esquema'] = $session_org;
    }

    /**
     * Persona de la dl. Nota en dl que pertenece a una region del stgr
     * debe guardar la nota en la tabla dl.
     * @return void
     * @throws \Exception
     */
    public function test_guardar_nota_dl_persona_dl()
    {
        $session_org = $_SESSION['session_auth']['esquema'];
        $_SESSION['session_auth']['esquema'] = 'H-dlbv';
        $_SESSION['session_auth']['mi_id_schema'] = 1001;

        // persona de la dl
        $id_nom = 10011255;
        $id_schema_persona = ConfigGlobal::mi_id_schema(); // El mismo que el de la _SESSION
        $id_asignatura = 23456;
        $id_nivel = 23456;

        $oEditarPersonaNota = new EditarPersonaNota($id_nom, $id_asignatura, $id_nivel);
        $oEditarPersonaNota->setMock(TRUE);
        $camposExtra = $this->crear_camposExtra();
        $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr();

        $a_ObjetosPersonaNota = $oEditarPersonaNota->getObjetosPersonaNota($datosRegionStgr, $id_schema_persona);

        $rta = $oEditarPersonaNota->nuevo2($a_ObjetosPersonaNota, $camposExtra);
        $oPersonaNota = $rta['nota'];
        $oPersonaNota->DBCarregar(); // Importante: El PDO al hacer execute cambia los integer a string. Con esto vuelven al tipo original.
        $oPersonaNota->getPrimary_key(); // para que tenga el mismo valor que la otra

        $gesPersonaNota = new GestorPersonaNotaDl(TRUE);
        $cPersonaNota = $gesPersonaNota->getPersonaNotas(['id_nom' => $id_nom, 'id_asignatura' => $id_asignatura]);
        $oPersonaNota2 = $cPersonaNota[0];
        $oPersonaNota2->DBCarregar();

        $this->assertEquals($oPersonaNota, $oPersonaNota2);

        // nota certificado (No debe existir)
        $oPersonaNotaCertificado = $rta['certificado']?? '';
        $this->assertEquals('', $oPersonaNotaCertificado);

        $_SESSION['session_auth']['esquema'] = $session_org;
    }



    public function test_save_PersonaNotaOtraRegionStgr()
    {
        $esquema_region_stgr = "H-Hv";
        $oPersonaNotaI = new PersonaNotaOtraRegionStgr($esquema_region_stgr, TRUE);
        $oPersonaNota = $this->crear_PersonaNota($oPersonaNotaI);
        $oPersonaNota->DBGuardar();
        $oPersonaNota->DBCarregar();
        $oPersonaNota->getPrimary_key();

        $id_nom = $oPersonaNota->getId_nom();
        $id_asignatura = $oPersonaNota->getId_asignatura();

        $gesPersonaNota = new GestorPersonaNotaOtraRegionStgr($esquema_region_stgr, TRUE);
        $cPersonaNota = $gesPersonaNota->getPersonaNotas(['id_nom' => $id_nom, 'id_asignatura' => $id_asignatura]);
        $oPersonaNota2 = $cPersonaNota[0];
        $oPersonaNota2->DBCarregar();

        $result = $this->assertEquals($oPersonaNota, $oPersonaNota2);

        //$oPersonaNota2->DBEliminar();

        return $result;

    }

    public function test_save_PersonaNotaDl()
    {
        $oPersonaNotaDl = new PersonaNotaDl();
        $oPersonaNota = $this->crear_PersonaNota($oPersonaNotaDl);
        $oPersonaNota->DBGuardar();
        $oPersonaNota->DBCarregar();
        $oPersonaNota->getPrimary_key();

        $id_nom = $oPersonaNota->getId_nom();
        $id_asignatura = $oPersonaNota->getId_asignatura();

        $gesPersonaNota = new GestorPersonaNotaDl();
        $cPersonaNota = $gesPersonaNota->getPersonaNotas(['id_nom' => $id_nom, 'id_asignatura' => $id_asignatura]);
        $oPersonaNota2 = $cPersonaNota[0];
        $oPersonaNota2->DBCarregar();

        $this->assertEquals($oPersonaNota, $oPersonaNota2);

        $oPersonaNota2->DBEliminar();
    }

    public function crear_camposExtra()
    {
        $oFActa = new DateTimeLocal('2024-11-07');
        $camposExtra = [
            'id_nom' => 1001123,
            'id_asignatura' => 234,
            'id_nivel' => 234,
            'id_situacion' => 1,
            'acta' => 'dlb 23/24',
            'f_acta' => $oFActa,
            'tipo_acta' => 1,
            'preceptor' => false,
            'id_preceptor' => 0,
            'detalle' => 'algo',
            'epoca' => 2,
            'id_activ' => 30012345,
            'nota_num' => 9,
            'nota_max' => 10,
        ];
        return $camposExtra;
    }

    public function crear_PersonaNota($oPersonaNota)
    {

        $camposExtra = $this->crear_camposExtra();

        $oPersonaNota->setId_nivel($camposExtra['id_nivel']);
        $oPersonaNota->setId_asignatura($camposExtra['id_asignatura']);
        $oPersonaNota->setId_nom($camposExtra['id_nom']);
        $oPersonaNota->setId_situacion($camposExtra['id_situacion']);
        $oPersonaNota->setActa($camposExtra['acta']);
        $oPersonaNota->setF_acta($camposExtra['f_acta']);
        $oPersonaNota->setTipo_acta($camposExtra['tipo_acta']);
        $oPersonaNota->setPreceptor($camposExtra['preceptor']);
        $oPersonaNota->setId_preceptor($camposExtra['id_preceptor']);
        $oPersonaNota->setDetalle($camposExtra['detalle']);
        $oPersonaNota->setEpoca($camposExtra['epoca']);
        $oPersonaNota->setId_activ($camposExtra['id_activ']);
        $oPersonaNota->setNota_num($camposExtra['nota_num']);
        $oPersonaNota->setNota_max($camposExtra['nota_max']);

        return $oPersonaNota;

    }


    /**
     * Runs at the end of every test.
     */
    public function tearDown(): void
    {

        //$oPersonaNota2->DBEliminar();

        parent::tearDown();
    }
    /*
    public function getDataSet()
    {

        return $this->createFlatXmlDataSet('./tests/guestbook_fixture.xml');
    }
    */

    /*
    public function testRowCount()
    {

        $this->assertSame(2, $this->getConnection()->getRowCount('guestbook'), 'Pre-Condition');
    }
    */

    /*
    public function testAddGuest()
    {

        // get the class to be tested, providing the PDObject as database connection
        $guestbook = new Guestbook($this->pdo);

        // insert a new guest
        $guestbook->addGuest('Daniel', 'St Kilda, Scotland', '4545');

        // get the resulting table from our database
        $queryTable = $this->getConnection()->createQueryTable(
            'guestbook', 'SELECT id, name, address, phone FROM guestbook'
        );

        // get the table we would expect after inserting a new guest
        $expectedTable = $this->createFlatXmlDataSet('./tests/guestbook_expected.xml')->getTable('guestbook');

        // ...and compare both tables ...it passes!
        $this->assertTablesEqual($expectedTable, $queryTable, "New User Added");
    }
    */

    /*
    public function testFailingAddGuest()
    {

        // get the class to be tested, providing the PDObject as database connection
        $guestbook = new Guestbook($this->pdo);

        // insert a new guest, but omit the value for 'phone' to let the test fail
        $guestbook->addGuest('Daniel', 'St Kilda, Scotland', '');

        // get the resulting table from our database, changed by the Guestbook Class
        $queryTable = $this->getConnection()->createQueryTable(
            'guestbook', 'SELECT id, name, address, phone FROM guestbook'
        );

        // get the *expected* table from a flat XML dataset
        $expectedTable = $this->createFlatXmlDataSet('./tests/guestbook_expected.xml')->getTable('guestbook');

        // ...and compare both tables which will fail
        $this->assertTablesEqual($expectedTable, $queryTable, 'Failure On Purpose');
    }
    */
}