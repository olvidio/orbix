<?php

namespace Tests\unit\notas;

use core\ConfigDB;
use core\ConfigGlobal;
use core\DBConnection;
use core\DBPropiedades;
use notas\model\EditarPersonaNota;
use notas\model\entity\GestorPersonaNotaDlDB;
use notas\model\entity\GestorPersonaNotaOtraRegionStgrDB;
use notas\model\entity\Nota;
use personas\model\entity\TrasladoDl;
use Tests\myTest;

class trasladosNotasTest extends myTest
{
    private string $session_org;
    private int $id_schema_rog;

    private string $snew_esquema;
    private string $sreg_dl_org;
    private string $sreg_dl_dst;

    private int $id_nom;
    private array $cPersonaNotas;
    private int $id_schema_persona;

    public function __construct(string $name)
    {
        //  Generar notas para id_nom (debe existir, para que no dé error)!!!
        $this->id_nom = 100111832;
        $this->id_schema_persona = 1001;
        $NotasFactory = new NotasFactory();
        $NotasFactory->setCount(10);
        $this->cPersonaNotas = $NotasFactory->create($this->id_nom);

        parent::__construct($name);
    }

    /**
     * Sets up the test suite prior to every test.
     */
    public function setUp(): void
    {
        parent::setUp();
        // Lo usa el setConnection
        putenv("UBICACION=sv");
        $this->session_org = $_SESSION['session_auth']['esquema'];
        $this->id_schema_rog = $_SESSION['session_auth']['mi_id_schema'] = 1027;
    }

    /////////// Traslado de notas de una región a una dl de H. ///////////
    ///
    /**
     * 1.- crear notas y guardar en crA
     * 2.- trasladar:
     *      - se pasan las notas de la tabla crA.e_notas_dl a la tabla crA.e_notas_otra_region_stgr.
     *      - se crean las notas 'falta certificado' en dlB.e_notas_dl
     * 3.- comprobar
     *
     * @return void
     */
    public function test_traslado_de_crA_a_dlB(): void
    {
        $_SESSION['session_auth']['esquema'] = 'M-crMv';
        $_SESSION['session_auth']['mi_id_schema'] = 1027;
        $this->id_schema_persona = 1027;

        $dlA = 'crM'; // Doy por supuesto que estoy conectado como dlb.
        $dlB = 'dlb';

        $sfsv_txt = (ConfigGlobal::mi_sfsv() === 1) ? 'v' : 'f';

        $reg_dl_org = 'M-' . $dlA . $sfsv_txt;
        $Qnew_dl = 'H-' . $dlB . $sfsv_txt;

        $this->sreg_dl_org = $reg_dl_org;
        $this->sreg_dl_dst = $Qnew_dl;

        // 1.- guardar notas del dlA
        foreach ($this->cPersonaNotas as $oPersonaNota) {
            $oEditarPersonaNota = new EditarPersonaNota($oPersonaNota);
            $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr();
            $esquema_region_stgr = $datosRegionStgr['esquema_region_stgr'];
            $a_ObjetosPersonaNota = $oEditarPersonaNota->getObjetosPersonaNota($datosRegionStgr, $this->id_schema_persona);
            $oEditarPersonaNota->crear_nueva_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota);
        }

        // 2.- trasladar
        $oTrasladoDl = new TrasladoDl();
        $oTrasladoDl->setId_nom($this->id_nom);
        $oTrasladoDl->setDl_persona($dlA);
        $oTrasladoDl->setReg_dl_org($reg_dl_org);
        $oTrasladoDl->setReg_dl_dst($Qnew_dl);

        $oTrasladoDl->copiarNotas();

        // 3.- Comprobar:

        // 3.1.-Existen en e_notas_otra_region.
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignatura();
            $gesPersonaNota = new GestorPersonaNotaOtraRegionStgrDB($esquema_region_stgr);
            $cPersonaNotasB = $gesPersonaNota->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
            $oPersonaNotaB = $cPersonaNotasB[0];

            // Son dos clases distintas, no se pueden comparar. Miramos las propiedades
            //$this->assertEquals($oPersonaNotaA, $oPersonaNotaB);
            $this->assertEquals($oPersonaNotaA->getIdNom(), $oPersonaNotaB->getId_nom());
            $this->assertEquals($oPersonaNotaA->getIdNivel(), $oPersonaNotaB->getId_nivel());
            $this->assertEquals($oPersonaNotaA->getIdAsignatura(), $oPersonaNotaB->getId_asignatura());
            $this->assertEquals($oPersonaNotaA->getNotaNum(), $oPersonaNotaB->getNota_num());
            $this->assertEquals($oPersonaNotaA->getIdSituacion(), $oPersonaNotaB->getId_situacion());

            // 4.- borrar las pruebas
            $oPersonaNotaB->DBEliminar();
        }

        // 3.2.- Existen en e_notas_dl region destino con 'falta certificado'
        $oDBdst = $this->conexionDst();
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignatura();
            $gesPersonaNota = new GestorPersonaNotaDlDB();
            $gesPersonaNota->setoDbl($oDBdst);
            $cPersonaNotasB = $gesPersonaNota->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
            $oPersonaNotaB = $cPersonaNotasB[0];

            // Son dos clases distintas, no se pueden comparar. Miramos las propiedades
            //$this->assertEquals($oPersonaNotaA, $oPersonaNotaB);
            $this->assertEquals($oPersonaNotaA->getIdNom(), $oPersonaNotaB->getId_nom());
            $this->assertEquals($oPersonaNotaA->getIdNivel(), $oPersonaNotaB->getId_nivel());
            $this->assertEquals($oPersonaNotaA->getIdAsignatura(), $oPersonaNotaB->getId_asignatura());
            $this->assertEquals($oPersonaNotaA->getNotaNum(), $oPersonaNotaB->getNota_num());
            $this->assertEquals(Nota::FALTA_CERTIFICADO, $oPersonaNotaB->getId_situacion());

            // 4.- borrar las pruebas
            $oPersonaNotaB->DBEliminar();
        }

        // 3.3.- No existen en origen crA.e_notas_dl
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignatura();
            $gesPersonaNota = new GestorPersonaNotaDlDB();
            $cPersonaNotasB = $gesPersonaNota->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
            $oPersonaNotaB = $cPersonaNotasB[0] ?? '';

            $this->assertEquals('', $oPersonaNotaB);
        }

    }
    /////////// Traslado de notas de una región a otra región del stgr. ///////////
    /**
     * 1.- crear notas y guardar en crA
     * 2.- trasladar:
     *      - se pasan las notas de la tabla crA.e_notas_dl a la tabla crA.e_notas_otra_region_stgr.
     *      - se crean las notas 'falta certificado' en crB.e_notas_dl
     * 3.- comprobar
     *
     * @return void
     */
    public function test_traslado_de_crA_a_crB(): void
    {
        $_SESSION['session_auth']['esquema'] = 'M-crMv';
        $_SESSION['session_auth']['mi_id_schema'] = 1027;
        $this->id_schema_persona = 1027;

        $dlA = 'crM'; // Doy por supuesto que estoy conectado como dlb.
        $dlB = 'crGalBel';

        $sfsv_txt = (ConfigGlobal::mi_sfsv() === 1) ? 'v' : 'f';

        $reg_dl_org = 'M-' . $dlA . $sfsv_txt;
        $Qnew_dl = 'GalBel-' . $dlB . $sfsv_txt;

        $this->sreg_dl_org = $reg_dl_org;
        $this->sreg_dl_dst = $Qnew_dl;

        // 1.- guardar notas del dlA
        foreach ($this->cPersonaNotas as $oPersonaNota) {
            $oEditarPersonaNota = new EditarPersonaNota($oPersonaNota);
            $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr();
            $esquema_region_stgr = $datosRegionStgr['esquema_region_stgr'];
            $a_ObjetosPersonaNota = $oEditarPersonaNota->getObjetosPersonaNota($datosRegionStgr, $this->id_schema_persona);
            $oEditarPersonaNota->crear_nueva_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota);
        }

        // 2.- trasladar
        $oTrasladoDl = new TrasladoDl();
        $oTrasladoDl->setId_nom($this->id_nom);
        $oTrasladoDl->setDl_persona($dlA);
        $oTrasladoDl->setReg_dl_org($reg_dl_org);
        $oTrasladoDl->setReg_dl_dst($Qnew_dl);

        $oTrasladoDl->copiarNotas();

        // 3.- Comprobar:

        // 3.1.-Existen en e_notas_otra_region.
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignatura();
            $gesPersonaNota = new GestorPersonaNotaOtraRegionStgrDB($esquema_region_stgr);
            $cPersonaNotasB = $gesPersonaNota->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
            $oPersonaNotaB = $cPersonaNotasB[0];

            // Son dos clases distintas, no se pueden comparar. Miramos las propiedades
            //$this->assertEquals($oPersonaNotaA, $oPersonaNotaB);
            $this->assertEquals($oPersonaNotaA->getIdNom(), $oPersonaNotaB->getId_nom());
            $this->assertEquals($oPersonaNotaA->getIdNivel(), $oPersonaNotaB->getId_nivel());
            $this->assertEquals($oPersonaNotaA->getIdAsignatura(), $oPersonaNotaB->getId_asignatura());
            $this->assertEquals($oPersonaNotaA->getNotaNum(), $oPersonaNotaB->getNota_num());
            $this->assertEquals($oPersonaNotaA->getIdSituacion(), $oPersonaNotaB->getId_situacion());

            // 4.- borrar las pruebas
            $oPersonaNotaB->DBEliminar();
        }

        // 3.2.- Existen en e_notas_dl region destino con 'falta certificado'
        $oDBdst = $this->conexionDst();
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignatura();
            $gesPersonaNota = new GestorPersonaNotaDlDB();
            $gesPersonaNota->setoDbl($oDBdst);
            $cPersonaNotasB = $gesPersonaNota->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
            $oPersonaNotaB = $cPersonaNotasB[0];

            // Son dos clases distintas, no se pueden comparar. Miramos las propiedades
            //$this->assertEquals($oPersonaNotaA, $oPersonaNotaB);
            $this->assertEquals($oPersonaNotaA->getIdNom(), $oPersonaNotaB->getId_nom());
            $this->assertEquals($oPersonaNotaA->getIdNivel(), $oPersonaNotaB->getId_nivel());
            $this->assertEquals($oPersonaNotaA->getIdAsignatura(), $oPersonaNotaB->getId_asignatura());
            $this->assertEquals($oPersonaNotaA->getNotaNum(), $oPersonaNotaB->getNota_num());
            $this->assertEquals(Nota::FALTA_CERTIFICADO, $oPersonaNotaB->getId_situacion());

            // 4.- borrar las pruebas
            $oPersonaNotaB->DBEliminar();
        }

        // 3.3.- No existen en origen crA.e_notas_dl
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignatura();
            $gesPersonaNota = new GestorPersonaNotaDlDB();
            $cPersonaNotasB = $gesPersonaNota->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
            $oPersonaNotaB = $cPersonaNotasB[0] ?? '';

            $this->assertEquals('', $oPersonaNotaB);
        }


    }

    /////////// Traslado de notas de dl a otra región del stgr. ///////////
    /**
     * 1.- guardar notas en dlA
     * 2.- trasladar
     * 3.- comprobar que:
     *      - se han creado las notas en e_notas_otra_region_stgr (region de dlA)
     *      - se han creado notas 'falta certificado' en crB
     *      - se han borrado de dlA
     *
     * @return void
     */
    public function test_traslado_de_dlA_a_crB(): void
    {

        $_SESSION['session_auth']['esquema'] = 'H-dlbv';
        $_SESSION['session_auth']['mi_id_schema'] = 1001;

        $dlA = 'dlb'; // Doy por supuesto que estoy conectado como dlb.
        $dlB = 'crGalBel';

        $sfsv_txt = (ConfigGlobal::mi_sfsv() === 1) ? 'v' : 'f';

        $reg_dl_org = 'H-' . $dlA . $sfsv_txt;
        $Qnew_dl = 'GalBel-' . $dlB . $sfsv_txt;

        $this->sreg_dl_org = $reg_dl_org;
        $this->sreg_dl_dst = $Qnew_dl;

        // 1.- guardar notas del dlA
        foreach ($this->cPersonaNotas as $oPersonaNota) {
            $oEditarPersonaNota = new EditarPersonaNota($oPersonaNota);
            $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr();
            $esquema_region_stgr = $datosRegionStgr['esquema_region_stgr']; // para usar la información más abajo
            $a_ObjetosPersonaNota = $oEditarPersonaNota->getObjetosPersonaNota($datosRegionStgr, $this->id_schema_persona);
            $oEditarPersonaNota->crear_nueva_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota);
        }

        // 2.- trasladar
        $oTrasladoDl = new TrasladoDl();
        $oTrasladoDl->setId_nom($this->id_nom);
        $oTrasladoDl->setDl_persona($dlA);
        $oTrasladoDl->setReg_dl_org($reg_dl_org);
        $oTrasladoDl->setReg_dl_dst($Qnew_dl);

        $oTrasladoDl->copiarNotas();

        // 3.- Comprobar:
        $oDBdst = $this->conexionDst();

        // 3.1.-Existen en e_notas_otra_region.
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignatura();
            $gesPersonaNota = new GestorPersonaNotaOtraRegionStgrDB($esquema_region_stgr);
            $cPersonaNotasB = $gesPersonaNota->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
            $oPersonaNotaB = $cPersonaNotasB[0];

            // Son dos clases distintas, no se pueden comparar. Miramos las propiedades
            //$this->assertEquals($oPersonaNotaA, $oPersonaNotaB);
            $this->assertEquals($oPersonaNotaA->getIdNom(), $oPersonaNotaB->getId_nom());
            $this->assertEquals($oPersonaNotaA->getIdNivel(), $oPersonaNotaB->getId_nivel());
            $this->assertEquals($oPersonaNotaA->getIdAsignatura(), $oPersonaNotaB->getId_asignatura());
            $this->assertEquals($oPersonaNotaA->getNotaNum(), $oPersonaNotaB->getNota_num());
            $this->assertEquals($oPersonaNotaA->getIdSituacion(), $oPersonaNotaB->getId_situacion());

            // 4.- borrar las pruebas
            $oPersonaNotaB->DBEliminar();
        }

        // 3.2.- Existen en e_notas_dl region destino con 'falta certificado'
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignatura();
            $gesPersonaNota = new GestorPersonaNotaDlDB();
            $gesPersonaNota->setoDbl($oDBdst);
            $cPersonaNotasB = $gesPersonaNota->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
            $oPersonaNotaB = $cPersonaNotasB[0];

            // Son dos clases distintas, no se pueden comparar. Miramos las propiedades
            //$this->assertEquals($oPersonaNotaA, $oPersonaNotaB);
            $this->assertEquals($oPersonaNotaA->getIdNom(), $oPersonaNotaB->getId_nom());
            $this->assertEquals($oPersonaNotaA->getIdNivel(), $oPersonaNotaB->getId_nivel());
            $this->assertEquals($oPersonaNotaA->getIdAsignatura(), $oPersonaNotaB->getId_asignatura());
            $this->assertEquals($oPersonaNotaA->getNotaNum(), $oPersonaNotaB->getNota_num());
            $this->assertEquals(Nota::FALTA_CERTIFICADO, $oPersonaNotaB->getId_situacion());

            // 4.- borrar las pruebas
            $oPersonaNotaB->DBEliminar();
        }
        // 3.3.- No existen en origen
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignatura();
            $gesPersonaNota = new GestorPersonaNotaDlDB();
            //$gesPersonaNota->setoDbl($oDBorg);
            $cPersonaNotasB = $gesPersonaNota->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
            $oPersonaNotaB = $cPersonaNotasB[0] ?? '';

            $this->assertEquals('', $oPersonaNotaB);
        }
    }
    ///

    /////////// Traslado de notas de dl a dl de una misma región del stgr. ///////////
    /**
     * 1.- crear notas y guardar en dlA
     * 2.- trasladar (pasan de la tabla dlA.e_notas_dl a la tabla dlB.e_notas_dl)
     * 3.- comprobar notas en dlB y que no existen en dlA
     *
     * @return void
     */
    public function test_traslado_de_dlA_a_dlB(): void
    {
        $_SESSION['session_auth']['esquema'] = 'H-dlbv';
        $_SESSION['session_auth']['mi_id_schema'] = 1001;

        $dlA = 'dlb'; // Doy por supuesto que estoy conectado como dlb.
        $dlB = 'dls';

        $sfsv_txt = (ConfigGlobal::mi_sfsv() === 1) ? 'v' : 'f';

        $reg_dl_org = 'H-' . $dlA . $sfsv_txt;
        $Qnew_dl = 'H-' . $dlB . $sfsv_txt;

        $this->sreg_dl_org = $reg_dl_org;
        $this->sreg_dl_dst = $Qnew_dl;

        // 1.- guardar notas del dlA
        foreach ($this->cPersonaNotas as $oPersonaNota) {
            $oEditarPersonaNota = new EditarPersonaNota($oPersonaNota);
            $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr();
            $a_ObjetosPersonaNota = $oEditarPersonaNota->getObjetosPersonaNota($datosRegionStgr, $this->id_schema_persona);
            $oEditarPersonaNota->crear_nueva_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota);
        }

        // 2.- trasladar
        $oTrasladoDl = new TrasladoDl();
        $oTrasladoDl->setId_nom($this->id_nom);
        $oTrasladoDl->setDl_persona($dlA);
        $oTrasladoDl->setReg_dl_org($reg_dl_org);
        $oTrasladoDl->setReg_dl_dst($Qnew_dl);

        $oTrasladoDl->copiarNotas();

        // 3.- Comprobar:
        // 3.1.-Existen en destino, y no existen en origen
        $oDBdst = $this->conexionDst();

        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignatura();
            $gesPersonaNota = new GestorPersonaNotaDlDB();
            $gesPersonaNota->setoDbl($oDBdst);
            $cPersonaNotasB = $gesPersonaNota->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
            $oPersonaNotaB = $cPersonaNotasB[0];


            // Son dos clases distintas, no se pueden comparar. Miramos las propiedades
            //$this->assertEquals($oPersonaNotaA, $oPersonaNotaB);
            $this->assertEquals($oPersonaNotaA->getIdNom(), $oPersonaNotaB->getId_nom());
            $this->assertEquals($oPersonaNotaA->getIdNivel(), $oPersonaNotaB->getId_nivel());
            $this->assertEquals($oPersonaNotaA->getIdAsignatura(), $oPersonaNotaB->getId_asignatura());
            $this->assertEquals($oPersonaNotaA->getNotaNum(), $oPersonaNotaB->getNota_num());
            $this->assertEquals($oPersonaNotaA->getIdSituacion(), $oPersonaNotaB->getId_situacion());

            // 4.- borrar las pruebas
            $oPersonaNotaB->DBEliminar();
        }
        // 3.2.- No existen en origen
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignatura();
            $gesPersonaNota = new GestorPersonaNotaDlDB();
            //$gesPersonaNota->setoDbl($oDBorg);
            $cPersonaNotasB = $gesPersonaNota->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
            $oPersonaNotaB = $cPersonaNotasB[0] ?? '';

            $this->assertEquals('', $oPersonaNotaB);
        }
    }

    ///////////// Conexiones DB. Copiado del TrasladoDl ////////////////////////
    private function setConexion($esquema, $exterior = FALSE): \PDO
    {

        if (ConfigGlobal::mi_sfsv() === 2) {
            $database = 'sf';
            if ($exterior) {
                $database = 'sf-e';
            }
            if (ConfigGlobal::mi_region_dl() !== $esquema) {
                $esquema = 'restof';
            }
        } else {
            $database = 'sv';
            if ($exterior) {
                $database = 'sv-e';
            }
            // dlp?
            $oDBPropiedades = new DBPropiedades();
            $aEsquemas = $oDBPropiedades->array_posibles_esquemas();

            if (!in_array($esquema, $aEsquemas, true)) {
                $esquema = 'restov';
            }
        }

        $oConfigDB = new ConfigDB($database);
        $config = $oConfigDB->getEsquema($esquema);
        $oConexion = new DBConnection($config);

        $oDB = $oConexion->getPDO();
        //$this->verConexion($oDB);
        return $oDB;
    }

    private function conexionOrg($exterior = FALSE): \PDO
    {

        $this->snew_esquema = $this->sreg_dl_org;
        return $this->setConexion($this->snew_esquema, $exterior);
    }

    private function conexionDst($exterior = FALSE): \PDO
    {
        $this->snew_esquema = $this->sreg_dl_dst;
        return $this->setConexion($this->snew_esquema, $exterior);
    }

    /**
     * Runs at the end of every test.
     */
    protected function tearDown(): void
    {
        $_SESSION['session_auth']['esquema'] = $this->session_org;
        $_SESSION['session_auth']['mi_id_schema'] = $this->id_schema_rog;
        parent::tearDown();
    }
}