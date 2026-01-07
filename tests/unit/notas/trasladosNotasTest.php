<?php

namespace Tests\unit\notas;

use core\ConfigDB;
use core\ConfigGlobal;
use core\DBConnection;
use core\DBPropiedades;
use notas\model\EditarPersonaNota;
use src\notas\domain\contracts\PersonaNotaDBRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaOtraRegionStgrRepositoryInterface;
use src\notas\domain\entity\Nota;
use src\notas\domain\value_objects\NotaSituacion;
use src\personas\domain\TrasladoDl;
use src\ubis\application\services\DelegacionUtils;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use Tests\factories\notas\NotasFactory;
use Tests\myTest;

class trasladosNotasTest extends myTest
{
    private string $session_org;

    private string $snew_esquema;
    private string $sreg_dl_org;
    private string $sreg_dl_dst;

    private int $id_nom;
    private array $cPersonaNotas;
    private int $id_schema_persona;

    public function __construct(string $name)
    {

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

        $this->generarNotas('H-dlb');
    }

    /////////// Traslado de vuelta a notas de una región a una dl de H. ///////////
    ///
    /**
     * Al revés que el siguiente
     *
     * 1.- traslado de dlA a crB, sin borrar
     * 2.- traslado de crB a dlA
     * 3.- comprobar:
     *
     * @return void
     */
    public function test_traslado_de_vuelta_de_dlA_a_crB(): void
    {
        $esquemaA = 'H-dlb';
        $esquemaB = 'GalBel-crGalBel';
        $dlA = 'dlb';
        $dlB = 'crGalBel';

        // preparara entorno con traslado de dlB a crA
        $this->generarNotas($esquemaA);
        $this->guardar_notas($esquemaA);
        $this->trasladar_notas($esquemaA, $esquemaB);

        // trasladar del crA a dlB
        $this->trasladar_notas($esquemaB, $esquemaA);

        $DelegacionRepository = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        $a_mi_region_stgr = $DelegacionRepository->mi_region_stgr($dlA);
        $esquema_region_stgrA = $a_mi_region_stgr['esquema_region_stgr'];
        $a_mi_region_stgr = $DelegacionRepository->mi_region_stgr($dlB);
        $esquema_region_stgrB = $a_mi_region_stgr['esquema_region_stgr'];

        // 3.- Comprobar:

        // 3.1.- No existen en e_notas_otra_region. de crB
        // 3.2.- No existen en e_notas_dl de crB
        // 3.3.- No existen en e_notas_otra_region de la region de dlA
        // 3.4.- Existen en e_notas_dl. de dlA

        // 3.1.- No existen en e_notas_otra_region. de cr B
        $oDBdst = $this->setConexion($esquemaB . 'v');
        $PersonaNotaOtraRegionStgrRepository = $GLOBALS['container']->make(PersonaNotaOtraRegionStgrRepositoryInterface::class, ['esquema_region_stgr' => $esquema_region_stgrB]);
        $PersonaNotaOtraRegionStgrRepository->setoDbl($oDBdst);
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignatura();
            $cPersonaNotasB = $PersonaNotaOtraRegionStgrRepository->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
            $oPersonaNotaB = $cPersonaNotasB[0] ?? '';

            $this->assertEquals('', $oPersonaNotaB);
        }

        // 3.2.- No existen en e_notas_dl de crB
        $oDBdst = $this->setConexion($esquemaB . 'v');
        $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaDBRepositoryInterface::class);
        $PersonaNotaDBRepository->setoDbl($oDBdst);
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignatura();
            $cPersonaNotasB = $PersonaNotaDBRepository->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
            $oPersonaNotaB = $cPersonaNotasB[0] ?? '';

            $this->assertEquals('', $oPersonaNotaB);
        }

        // 3.3.- No existen en e_notas_otra_region de la region de dlA
        $oDBdst = $this->setConexion($esquema_region_stgrA);
        $PersonaNotaOtraRegionStgrRepository = $GLOBALS['container']->make(PersonaNotaOtraRegionStgrRepositoryInterface::class, ['esquema_region_stgr' => $esquema_region_stgrA]);
        $PersonaNotaOtraRegionStgrRepository->setoDbl($oDBdst);
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignatura();
            $cPersonaNotasB = $PersonaNotaOtraRegionStgrRepository->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
            $oPersonaNotaB = $cPersonaNotasB[0] ?? '';

            $this->assertEquals('', $oPersonaNotaB);
        }

        // 3.4.- Existen en e_notas_dl. de dlA
        $oDBdst = $this->setConexion($esquemaA . 'v');
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignatura();
            $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaDBRepositoryInterface::class);
            $PersonaNotaDBRepository->setoDbl($oDBdst);
            $cPersonaNotasB = $PersonaNotaDBRepository->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
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
    }

    ///

    /**
     * 1.- traslado de dlB a crA, sin borrar
     * 2.- traslado de crA a dlB
     * 3.- comprobar:
     *      - las notas de crB.e_notas_dl que son certificados y están en la tabla crA.e_notas_otra_region_stgr
     *          se ponen en dlA.e_notas_dl y se quitan de crA.e_notas_otra_region_stgr.
     *
     * @return void
     */
    public function test_traslado_de_vuelta_de_crA_a_dlB(): void
    {
        $esquemaA = 'GalBel-crGalBel';
        $esquemaB = 'H-dlb';
        $dlA = 'crGalBel';
        $dlB = 'dlb';

        // preparara entorno con traslado de dlB a crA
        $this->generarNotas($esquemaA);
        $this->guardar_notas($esquemaA);
        $this->trasladar_notas($esquemaA, $esquemaB);

        // trasladar del crA a dlB
        $this->trasladar_notas($esquemaB, $esquemaA);

        $DelegacionRepository = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        $a_mi_region_stgr = $DelegacionRepository->mi_region_stgr($dlA);
        $esquema_region_stgrA = $a_mi_region_stgr['esquema_region_stgr'];
        $a_mi_region_stgr = $DelegacionRepository->mi_region_stgr($dlB);
        $esquema_region_stgrB = $a_mi_region_stgr['esquema_region_stgr'];

        // 3.- Comprobar:

        // 3.1.- No existen en e_notas_otra_region. de cr A
        // 3.2.- No existen en e_notas_dl de dlB
        // 3.3.- No existen en e_notas_otra_region de la region de dlB
        // 3.4.- Existen en e_notas_dl. de crA

        // 3.1.- No existen en e_notas_otra_region. de cr A
        $PersonaNotaOtraRegionStgrRepository = $GLOBALS['container']->make(PersonaNotaOtraRegionStgrRepositoryInterface::class, ['esquema_region_stgr' => $esquema_region_stgrA]);
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignatura();
            $cPersonaNotasB = $PersonaNotaOtraRegionStgrRepository->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
            $oPersonaNotaB = $cPersonaNotasB[0] ?? '';

            $this->assertEquals('', $oPersonaNotaB);
        }

        // 3.2.- No existen en e_notas_dl de dlB
        $oDBdst = $this->setConexion($esquemaB . 'v');
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignatura();
            $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaDBRepositoryInterface::class);
            $PersonaNotaDBRepository->setoDbl($oDBdst);
            $cPersonaNotasB = $PersonaNotaDBRepository->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
            $oPersonaNotaB = $cPersonaNotasB[0] ?? '';

            $this->assertEquals('', $oPersonaNotaB);
        }

        // 3.3.- No existen en e_notas_otra_region de la region de dlB
        $oDBdst = $this->setConexion($esquema_region_stgrB);
        $PersonaNotaOtraRegionStgrRepository = $GLOBALS['container']->make(PersonaNotaOtraRegionStgrRepositoryInterface::class, ['esquema_region_stgr' => $esquema_region_stgrB]);
        $PersonaNotaOtraRegionStgrRepository->setoDbl($oDBdst);
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignatura();
            $cPersonaNotasB = $PersonaNotaOtraRegionStgrRepository->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
            $oPersonaNotaB = $cPersonaNotasB[0] ?? '';

            $this->assertEquals('', $oPersonaNotaB);
        }

        // 3.4.- Existen en e_notas_dl. de crA
        $oDBdst = $this->setConexion($esquemaA . 'v');
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignatura();
            $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaDBRepositoryInterface::class);
            $PersonaNotaDBRepository->setoDbl($oDBdst);
            $cPersonaNotasB = $PersonaNotaDBRepository->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
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
        $esquemaA = 'GalBel-crGalBel';
        $esquemaB = 'H-dlb';
        $dlA = 'crGalBel';
        $dlB = 'dlb';

        // preparara entorno con traslado de dlB a crA
        $this->generarNotas($esquemaA);
        $this->guardar_notas($esquemaA);
        $this->trasladar_notas($esquemaA, $esquemaB);

        $DelegacionRepository = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        $a_mi_region_stgr = $DelegacionRepository->mi_region_stgr($dlA);
        $esquema_region_stgr = $a_mi_region_stgr['esquema_region_stgr'];

        // 3.- Comprobar:

        // 3.1.-Existen en e_notas_otra_region.
        $PersonaNotaOtraRegionStgrRepository = $GLOBALS['container']->make(PersonaNotaOtraRegionStgrRepositoryInterface::class, ['esquema_region_stgr' => $esquema_region_stgr]);
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignatura();
            $cPersonaNotasB = $PersonaNotaOtraRegionStgrRepository->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
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
        $oDBdst = $this->setConexion($esquemaB . 'v');
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignatura();
            $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaDBRepositoryInterface::class);
            $PersonaNotaDBRepository->setoDbl($oDBdst);
            $cPersonaNotasB = $PersonaNotaDBRepository->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
            $oPersonaNotaB = $cPersonaNotasB[0];

            // Son dos clases distintas, no se pueden comparar. Miramos las propiedades
            //$this->assertEquals($oPersonaNotaA, $oPersonaNotaB);
            $this->assertEquals($oPersonaNotaA->getIdNom(), $oPersonaNotaB->getId_nom());
            $this->assertEquals($oPersonaNotaA->getIdNivel(), $oPersonaNotaB->getId_nivel());
            $this->assertEquals($oPersonaNotaA->getIdAsignatura(), $oPersonaNotaB->getId_asignatura());
            $this->assertEquals($oPersonaNotaA->getNotaNum(), $oPersonaNotaB->getNota_num());
            $this->assertEquals(NotaSituacion::FALTA_CERTIFICADO, $oPersonaNotaB->getId_situacion());

            // 4.- borrar las pruebas
            $oPersonaNotaB->DBEliminar();
        }

        // 3.3.- No existen en origen crA.e_notas_dl
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignatura();
            $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaDBRepositoryInterface::class);
            $cPersonaNotasB = $PersonaNotaDBRepository->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
            $oPersonaNotaB = $cPersonaNotasB[0] ?? '';

            $this->assertEquals('', $oPersonaNotaB);
        }

        // 4.- borrar las pruebas
        $oDBdst = $this->setConexion($esquemaA . 'v');
        $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaDBRepositoryInterface::class);
        $PersonaNotaDBRepository->setoDbl($oDBdst);
        $cPersonaNotasC = $PersonaNotaDBRepository->getPersonaNotas(['id_nom' => $this->id_nom]);
        foreach ($cPersonaNotasC as $oPersonaNotaC) {
            $oPersonaNotaC->DBEliminar();
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
        $_SESSION['session_auth']['esquema'] = 'L-crLv';
        $_SESSION['session_auth']['mi_id_schema'] = 1030;
        $this->id_schema_persona = 1030;

        $dlA = 'crL'; // Doy por supuesto que estoy conectado como dlb.
        $dlB = 'crGalBel';

        $sfsv_txt = (ConfigGlobal::mi_sfsv() === 1) ? 'v' : 'f';

        $reg_dl_org = 'L-' . $dlA . $sfsv_txt;
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
        $PersonaNotaOtraRegionStgrRepository = $GLOBALS['container']->make(PersonaNotaOtraRegionStgrRepositoryInterface::class, ['esquema_region_stgr' => $esquema_region_stgr]);
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignatura();
            $cPersonaNotasB = $PersonaNotaOtraRegionStgrRepository->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
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
            $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaDBRepositoryInterface::class);
            $PersonaNotaDBRepository->setoDbl($oDBdst);
            $cPersonaNotasB = $PersonaNotaDBRepository->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
            $oPersonaNotaB = $cPersonaNotasB[0];

            // Son dos clases distintas, no se pueden comparar. Miramos las propiedades
            //$this->assertEquals($oPersonaNotaA, $oPersonaNotaB);
            $this->assertEquals($oPersonaNotaA->getIdNom(), $oPersonaNotaB->getId_nom());
            $this->assertEquals($oPersonaNotaA->getIdNivel(), $oPersonaNotaB->getId_nivel());
            $this->assertEquals($oPersonaNotaA->getIdAsignatura(), $oPersonaNotaB->getId_asignatura());
            $this->assertEquals($oPersonaNotaA->getNotaNum(), $oPersonaNotaB->getNota_num());
            $this->assertEquals(NotaSituacion::FALTA_CERTIFICADO, $oPersonaNotaB->getId_situacion());

            // 4.- borrar las pruebas
            $oPersonaNotaB->DBEliminar();
        }

        // 3.3.- No existen en origen crA.e_notas_dl
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignatura();
            $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaDBRepositoryInterface::class);
            $cPersonaNotasB = $PersonaNotaDBRepository->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
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
        $PersonaNotaOtraRegionStgrRepository = $GLOBALS['container']->make(PersonaNotaOtraRegionStgrRepositoryInterface::class, ['esquema_region_stgr' => $esquema_region_stgr]);
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignatura();
            $cPersonaNotasB = $PersonaNotaOtraRegionStgrRepository->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
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
            $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaDBRepositoryInterface::class);
            $PersonaNotaDBRepository->setoDbl($oDBdst);
            $cPersonaNotasB = $PersonaNotaDBRepository->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
            $oPersonaNotaB = $cPersonaNotasB[0];

            // Son dos clases distintas, no se pueden comparar. Miramos las propiedades
            //$this->assertEquals($oPersonaNotaA, $oPersonaNotaB);
            $this->assertEquals($oPersonaNotaA->getIdNom(), $oPersonaNotaB->getId_nom());
            $this->assertEquals($oPersonaNotaA->getIdNivel(), $oPersonaNotaB->getId_nivel());
            $this->assertEquals($oPersonaNotaA->getIdAsignatura(), $oPersonaNotaB->getId_asignatura());
            $this->assertEquals($oPersonaNotaA->getNotaNum(), $oPersonaNotaB->getNota_num());
            $this->assertEquals(NotaSituacion::FALTA_CERTIFICADO, $oPersonaNotaB->getId_situacion());

            // 4.- borrar las pruebas
            $oPersonaNotaB->DBEliminar();
        }
        // 3.3.- No existen en origen
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignatura();
            $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaDBRepositoryInterface::class);
            $cPersonaNotasB = $PersonaNotaDBRepository->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
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
            $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaDBRepositoryInterface::class);
            $PersonaNotaDBRepository->setoDbl($oDBdst);
            $cPersonaNotasB = $PersonaNotaDBRepository->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
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
            $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaDBRepositoryInterface::class);
            $cPersonaNotasB = $PersonaNotaDBRepository->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
            $oPersonaNotaB = $cPersonaNotasB[0] ?? '';

            $this->assertEquals('', $oPersonaNotaB);
        }
    }

    ///////////////////////////////////////////////////////////////////////
    private function guardar_notas($esquemaA)
    {
        $DelegacionRepository = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        $dlA = DelegacionUtils::getDlFromSchema($esquemaA);
        $a_mi_region_stgr = $DelegacionRepository->mi_region_stgr($dlA);
        $id_esquemaA = $a_mi_region_stgr['mi_id_schema'];

        $sfsv_txt = (ConfigGlobal::mi_sfsv() === 1) ? 'v' : 'f';
        $reg_dl_org = $esquemaA . $sfsv_txt;

        $this->sreg_dl_org = $reg_dl_org;

        $_SESSION['session_auth']['esquema'] = $reg_dl_org;
        $_SESSION['session_auth']['mi_id_schema'] = $id_esquemaA;
        $this->id_schema_persona = $id_esquemaA;

        // 1.- guardar notas del dlA
        foreach ($this->cPersonaNotas as $oPersonaNota) {
            $oEditarPersonaNota = new EditarPersonaNota($oPersonaNota);
            $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr();
            $a_ObjetosPersonaNota = $oEditarPersonaNota->getObjetosPersonaNota($datosRegionStgr, $this->id_schema_persona);
            $oEditarPersonaNota->crear_nueva_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota);
        }
    }

    private function trasladar_notas($esquemaA, $esquemaB)
    {

        $DelegacionRepository = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        $dlA = DelegacionUtils::getDlFromSchema($esquemaA);
        $a_mi_region_stgr = $DelegacionRepository->mi_region_stgr($dlA);
        $id_esquemaA = $a_mi_region_stgr['mi_id_schema'];

        $sfsv_txt = (ConfigGlobal::mi_sfsv() === 1) ? 'v' : 'f';
        $reg_dl_org = $esquemaA . $sfsv_txt;
        $Qnew_dl = $esquemaB . $sfsv_txt;

        // Es necesario para que las funciones que detecten  "mi_schema" lo hagan correctamente
        $_SESSION['session_auth']['esquema'] = $reg_dl_org;
        $_SESSION['session_auth']['mi_id_schema'] = $id_esquemaA;
        $this->id_schema_persona = $id_esquemaA;

        // 2.- trasladar
        $oTrasladoDl = new TrasladoDl();
        $oTrasladoDl->setId_nom($this->id_nom);
        $oTrasladoDl->setDl_persona($dlA);
        $oTrasladoDl->setReg_dl_org($reg_dl_org);
        $oTrasladoDl->setReg_dl_dst($Qnew_dl);

        $oTrasladoDl->copiarNotas();
    }

    ///
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
            // añadir el H-Hv
            $aEsquemas['H-Hv'] = 'H-Hv';

            if (!in_array($esquema, $aEsquemas, true)) {
                $esquema = 'restov';
            }
        }

        $oConfigDB = new ConfigDB($database);
        $config = $oConfigDB->getEsquema($esquema);
        $oConexion = new DBConnection($config);

        return $oConexion->getPDO();
    }

    public function generarNotas(string $esquema): void
    {
        //  Generar notas para id_nom (debe existir, para que no dé error)!!!
        switch ($esquema) {
            case 'H-dlb':
                $this->id_nom = 100111832;
                $this->id_schema_persona = 1001;
                break;
            case 'M-crM':
                $this->id_nom = 10271837;
                $this->id_schema_persona = 1027;
                break;
            case 'L-crL':
                $this->id_nom = 103012;
                $this->id_schema_persona = 1030;
                break;
            case 'GalBel-crGalBel':
                $this->id_nom = 102912;
                $this->id_schema_persona = 1029;
                break;
        }
        $NotasFactory = new NotasFactory();
        $NotasFactory->setCount(10);

        $dl = DelegacionUtils::getDlFromSchema($esquema);

        $this->borrar_antes_de_crear_notas($this->id_nom, $esquema);
        $this->cPersonaNotas = $NotasFactory->create($this->id_nom, $dl);
    }

    private function borrar_antes_de_crear_notas($id_nom, $esquema)
    {
        $oDBdst = $this->setConexion($esquema . 'v');

        $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaDBRepositoryInterface::class);
        $PersonaNotaDBRepository->setoDbl($oDBdst);
        $aWhere = ['id_nom' => $id_nom];
        $cPersonaNotas = $PersonaNotaDBRepository->getPersonaNotas($aWhere);
        foreach ($cPersonaNotas as $oPersonaNota) {
            $oPersonaNota->DBEliminar();
        }
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
        parent::tearDown();
    }
}