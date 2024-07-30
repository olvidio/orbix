<?php

namespace Tests\unit\notas;

use notas\model\entity\GestorPersonaNotaDl;
use notas\model\entity\PersonaNotaDl;
use Tests\myTest;

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

    public function crear_PersonaNota()
    {
        $oPersonaNota = new PersonaNotaDl();

        $camposExtra = [
            'id_nom' => 1001123,
            'id_asignatura' => 234,
            'id_nivel' => 234,
            'id_situacion' => 1,
            'acta' => 'dlb 23/24',
            'f_acta' => '2024-11-07',
            'tipo_acta' => 1,
            'preceptor' => false,
            'id_preceptor' => 0,
            'detalle' => 'algo',
            'epoca' => 2,
            'id_activ' => 30012345,
            'nota_num' => 9,
            'nota_max' => 10,
        ];

        $oPersonaNota->setId_nivel($camposExtra['id_nivel']);
        $oPersonaNota->setId_asignatura($camposExtra['id_asignatura']);
        $oPersonaNota->setId_nom($camposExtra['id_nom']);
        $oPersonaNota->setId_situacion($camposExtra['id_situacion']);
        $oPersonaNota->setActa($camposExtra['acta']);
        $oPersonaNota->setF_acta($camposExtra['f_acta'], false);
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

    public function test_save_PersonaNota()
    {
        $oPersonaNota = $this->crear_PersonaNota();
        $oPersonaNota->DBGuardar();
        $oPersonaNota->DBCarregar();
        $oPersonaNota->getPrimary_key();

        $id_nom = $oPersonaNota->getId_nom();
        $id_asignatura = $oPersonaNota->getId_asignatura();

        $gesPersonaNota = new GestorPersonaNotaDl();
        $cPersonaNota = $gesPersonaNota->getPersonaNotas(['id_nom' => $id_nom, 'id_asignatura' => $id_asignatura]);
        $oPersonaNota2 = $cPersonaNota[0];
        $oPersonaNota2->DBCarregar();

        $result = $this->assertEquals($oPersonaNota, $oPersonaNota2);

        $oPersonaNota2->DBEliminar();

        return $result;

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