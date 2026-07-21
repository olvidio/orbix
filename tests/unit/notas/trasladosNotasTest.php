<?php

namespace Tests\unit\notas;

use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\DBConnection;
use src\shared\infrastructure\persistence\postgresql\DBPropiedades;
use src\notas\application\EditarPersonaNota;
use src\notas\domain\contracts\PersonaNotaDlRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaOtraRegionStgrRepositoryInterface;
use src\personas\domain\Trasladar;
use src\ubis\application\services\DelegacionUtils;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use Tests\factories\notas\NotasFactory;
use Tests\myTest;

/**
 * Traslados de notas con el contrato modelo B (notas ancladas al acta).
 *
 * El traslado administrativo de la persona no mueve filas de notas; el expediente
 * se lee vía publicv.e_notas. Tests de intención detallados en trasladosNotasModeloActaTest.
 *
 * @see docs/dev/notas_modelo_acta.md
 * @see trasladosNotasModeloActaTest
 */
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

    private function nuevoEditarPersonaNota(\src\notas\domain\entity\PersonaNota $oPersonaNota): EditarPersonaNota
    {
        $container = $GLOBALS['container'];
        return new EditarPersonaNota(
            $oPersonaNota,
            $container->get(\src\notas\domain\contracts\PersonaNotaRepositoryInterface::class),
            $container->get(\src\ubis\domain\contracts\DelegacionRepositoryInterface::class),
            $container->get(\src\utils_database\domain\contracts\DbSchemaRepositoryInterface::class),
            $container->get(\src\dossiers\domain\contracts\DossierRepositoryInterface::class),
            $container->get(\src\notas\domain\contracts\PersonaNotaDlRepositoryInterface::class),
        );
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
        $esquemaB = 'Galbel-crGalbel';
        $dlA = 'dlb';
        $dlB = 'crGalbel';

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
            $id_asignatura = $oPersonaNotaA->getIdAsignaturaVo()->value();
            $cPersonaNotasB = $PersonaNotaOtraRegionStgrRepository->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
            $oPersonaNotaB = $cPersonaNotasB[0] ?? '';

            $this->assertEquals('', $oPersonaNotaB);
        }

        // 3.2.- No existen en e_notas_dl de crB
        $oDBdst = $this->setConexion($esquemaB . 'v');
        $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaDlRepositoryInterface::class);
        $PersonaNotaDBRepository->setoDbl($oDBdst);
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignaturaVo()->value();
            $cPersonaNotasB = $PersonaNotaDBRepository->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
            $oPersonaNotaB = $cPersonaNotasB[0] ?? '';

            $this->assertEquals('', $oPersonaNotaB);
        }

        // 3.3.- No existen en e_notas_otra_region de la region de dlA
        $oDBdst = $this->setConexion($esquema_region_stgrA);
        $PersonaNotaOtraRegionStgrRepository = $GLOBALS['container']->make(PersonaNotaOtraRegionStgrRepositoryInterface::class, ['esquema_region_stgr' => $esquema_region_stgrA]);
        $PersonaNotaOtraRegionStgrRepository->setoDbl($oDBdst);
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignaturaVo()->value();
            $cPersonaNotasB = $PersonaNotaOtraRegionStgrRepository->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
            $oPersonaNotaB = $cPersonaNotasB[0] ?? '';

            $this->assertEquals('', $oPersonaNotaB);
        }

        // 3.4.- Existen en e_notas_dl. de dlA
        $oDBdst = $this->setConexion($esquemaA . 'v');
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignaturaVo()->value();
            $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaDlRepositoryInterface::class);
            $PersonaNotaDBRepository->setoDbl($oDBdst);
            $cPersonaNotasB = $PersonaNotaDBRepository->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
            $oPersonaNotaB = $cPersonaNotasB[0];

            // Son dos clases distintas, no se pueden comparar. Miramos las propiedades
            //$this->assertEquals($oPersonaNotaA, $oPersonaNotaB);
            $this->assertEquals($oPersonaNotaA->getId_nom(), $oPersonaNotaB->getId_nom());
            $this->assertEquals($oPersonaNotaA->getId_nivel(), $oPersonaNotaB->getId_nivel());
            $this->assertEquals($oPersonaNotaA->getId_asignatura(), $oPersonaNotaB->getId_asignatura());
            $this->assertEquals($oPersonaNotaA->getNota_Num(), $oPersonaNotaB->getNota_num());
            $this->assertEquals($oPersonaNotaA->getId_situacion(), $oPersonaNotaB->getId_situacion());

            // 4.- borrar las pruebas
            $PersonaNotaDBRepository->Eliminar($oPersonaNotaB);
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
        $esquemaA = 'Galbel-crGalbel';
        $esquemaB = 'H-dlb';
        $dlA = 'crGalbel';
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
            $id_asignatura = $oPersonaNotaA->getIdAsignaturaVo()->value();
            $cPersonaNotasB = $PersonaNotaOtraRegionStgrRepository->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
            $oPersonaNotaB = $cPersonaNotasB[0] ?? '';

            $this->assertEquals('', $oPersonaNotaB);
        }

        // 3.2.- No existen en e_notas_dl de dlB
        $oDBdst = $this->setConexion($esquemaB . 'v');
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignaturaVo()->value();
            $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaDlRepositoryInterface::class);
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
            $id_asignatura = $oPersonaNotaA->getIdAsignaturaVo()->value();
            $cPersonaNotasB = $PersonaNotaOtraRegionStgrRepository->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
            $oPersonaNotaB = $cPersonaNotasB[0] ?? '';

            $this->assertEquals('', $oPersonaNotaB);
        }

        // 3.4.- Existen en e_notas_dl. de crA
        $oDBdst = $this->setConexion($esquemaA . 'v');
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignaturaVo()->value() ;
            $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaDlRepositoryInterface::class);
            $PersonaNotaDBRepository->setoDbl($oDBdst);
            $cPersonaNotasB = $PersonaNotaDBRepository->getPersonaNotas(['id_nom' => $this->id_nom, 'id_asignatura' => $id_asignatura]);
            $oPersonaNotaB = $cPersonaNotasB[0];

            // Son dos clases distintas, no se pueden comparar. Miramos las propiedades
            //$this->assertEquals($oPersonaNotaA, $oPersonaNotaB);
            $this->assertEquals($oPersonaNotaA->getId_nom(), $oPersonaNotaB->getId_nom());
            $this->assertEquals($oPersonaNotaA->getId_nivel(), $oPersonaNotaB->getId_nivel());
            $this->assertEquals($oPersonaNotaA->getId_asignatura(), $oPersonaNotaB->getId_asignatura());
            $this->assertEquals($oPersonaNotaA->getNota_num(), $oPersonaNotaB->getNota_num());
            $this->assertEquals($oPersonaNotaA->getId_situacion(), $oPersonaNotaB->getId_situacion());

            // 4.- borrar las pruebas
            $PersonaNotaDBRepository->Eliminar($oPersonaNotaB);
        }
    }



    /////////// Traslado de notas de una región a una dl de H. ///////////
    ///
    /**
     * Traslado crA→dlB (otra región STGR): las notas permanecen en e_notas_dl del acta.
     *
     * @return void
     */
    public function test_traslado_de_crA_a_dlB(): void
    {
        $esquemaA = 'Galbel-crGalbel';
        $esquemaB = 'H-dlb';
        $dlA = 'crGalbel';

        $this->generarNotas($esquemaA);
        $this->guardar_notas($esquemaA);
        $this->trasladar_notas($esquemaA, $esquemaB);

        $DelegacionRepository = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        $esquema_region_stgr = $DelegacionRepository->mi_region_stgr($dlA)['esquema_region_stgr'];

        // Sin filas en e_notas_otra_region_stgr.
        $PersonaNotaOtraRegionStgrRepository = $GLOBALS['container']->make(
            PersonaNotaOtraRegionStgrRepositoryInterface::class,
            ['esquema_region_stgr' => $esquema_region_stgr]
        );
        $PersonaNotaOtraRegionStgrRepository->setoDbl($this->setConexion($esquema_region_stgr));
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignaturaVo()->value();
            $cPersonaNotasOtraRegion = $PersonaNotaOtraRegionStgrRepository->getPersonaNotas([
                'id_nom' => $this->id_nom,
                'id_asignatura' => $id_asignatura,
            ]);
            $this->assertEmpty($cPersonaNotasOtraRegion);
        }

        // Sin placeholders ni notas de acta nuevas en destino.
        $oDBdst = $this->setConexion($esquemaB . 'v');
        $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaDlRepositoryInterface::class);
        $PersonaNotaDBRepository->setoDbl($oDBdst);
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignaturaVo()->value();
            $cPersonaNotasB = $PersonaNotaDBRepository->getPersonaNotas([
                'id_nom' => $this->id_nom,
                'id_asignatura' => $id_asignatura,
            ]);
            $this->assertEmpty($cPersonaNotasB);
        }

        // Las notas siguen en e_notas_dl del esquema del acta (origen).
        $oDBActa = $this->setConexion($esquemaA . 'v');
        $PersonaNotaDBRepository->setoDbl($oDBActa);
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignaturaVo()->value();
            $cPersonaNotasActa = $PersonaNotaDBRepository->getPersonaNotas([
                'id_nom' => $this->id_nom,
                'id_asignatura' => $id_asignatura,
            ]);
            $this->assertNotEmpty($cPersonaNotasActa);
            $oPersonaNotaB = $cPersonaNotasActa[0];
            $this->assertEquals($oPersonaNotaA->getId_nom(), $oPersonaNotaB->getId_nom());
            $this->assertEquals($oPersonaNotaA->getId_nivel(), $oPersonaNotaB->getId_nivel());
            $this->assertEquals($oPersonaNotaA->getId_asignatura(), $oPersonaNotaB->getId_asignatura());
            $this->assertEquals($oPersonaNotaA->getNota_num(), $oPersonaNotaB->getNota_num());
            $this->assertEquals($oPersonaNotaA->getId_situacion(), $oPersonaNotaB->getId_situacion());
            $PersonaNotaDBRepository->Eliminar($oPersonaNotaB);
        }
    }
    /////////// Traslado de notas de una región a otra región del stgr. ///////////

    /**
     * Traslado crA→crB (regiones STGR distintas): notas intactas en el acta origen.
     *
     * @return void
     */
    public function test_traslado_de_crA_a_crB(): void
    {
        $this->generarNotas('Nig-crNig');
        $_SESSION['session_auth']['esquema'] = 'Nig-crNigv';
        $_SESSION['session_auth']['mi_id_schema'] = 1024;
        $this->id_schema_persona = 1024;

        $dlA = 'crNig';
        $dlB = 'crGalbel';

        $sfsv_txt = (ConfigGlobal::mi_sfsv() === 1) ? 'v' : 'f';

        $reg_dl_org = 'Nig-' . $dlA . $sfsv_txt;
        $Qnew_dl = 'Galbel-' . $dlB . $sfsv_txt;

        $this->sreg_dl_org = $reg_dl_org;
        $this->sreg_dl_dst = $Qnew_dl;

        foreach ($this->cPersonaNotas as $oPersonaNota) {
            $oEditarPersonaNota = $this->nuevoEditarPersonaNota($oPersonaNota);
            $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr($dlA);
            $esquema_region_stgr = $datosRegionStgr['esquema_region_stgr'];
            $a_ObjetosPersonaNota = $oEditarPersonaNota->getReposPersonaNota($datosRegionStgr, $this->id_schema_persona);
            $oEditarPersonaNota->crear_nueva_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota);
        }

        $oTrasladoDl = $GLOBALS['container']->get(\src\personas\domain\Trasladar::class);
        $oTrasladoDl->setId_nom($this->id_nom);
        $oTrasladoDl->setDl_persona($dlA);
        $oTrasladoDl->setReg_dl_org($reg_dl_org);
        $oTrasladoDl->setReg_dl_dst($Qnew_dl);
        $oTrasladoDl->copiarNotas();

        $PersonaNotaOtraRegionStgrRepository = $GLOBALS['container']->make(
            PersonaNotaOtraRegionStgrRepositoryInterface::class,
            ['esquema_region_stgr' => $esquema_region_stgr]
        );
        $PersonaNotaOtraRegionStgrRepository->setoDbl($this->setConexion($esquema_region_stgr));
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignaturaVo()->value();
            $cPersonaNotasOtraRegion = $PersonaNotaOtraRegionStgrRepository->getPersonaNotas([
                'id_nom' => $this->id_nom,
                'id_asignatura' => $id_asignatura,
            ]);
            $this->assertEmpty($cPersonaNotasOtraRegion);
        }

        $oDBdst = $this->conexionDst();
        $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaDlRepositoryInterface::class);
        $PersonaNotaDBRepository->setoDbl($oDBdst);
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignaturaVo()->value();
            $cPersonaNotasB = $PersonaNotaDBRepository->getPersonaNotas([
                'id_nom' => $this->id_nom,
                'id_asignatura' => $id_asignatura,
            ]);
            $this->assertEmpty($cPersonaNotasB);
        }

        $oDBActa = $this->setConexion('Nig-crNigv');
        $PersonaNotaDBRepository->setoDbl($oDBActa);
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignaturaVo()->value();
            $cPersonaNotasActa = $PersonaNotaDBRepository->getPersonaNotas([
                'id_nom' => $this->id_nom,
                'id_asignatura' => $id_asignatura,
            ]);
            $this->assertNotEmpty($cPersonaNotasActa);
            $PersonaNotaDBRepository->Eliminar($cPersonaNotasActa[0]);
        }
    }

    /////////// Traslado de notas de dl a otra región del stgr. ///////////

    /**
     * Traslado dlA→crB: notas permanecen en e_notas_dl del acta origen.
     *
     * @return void
     */
    public function test_traslado_de_dlA_a_crB(): void
    {

        $_SESSION['session_auth']['esquema'] = 'H-dlbv';
        $_SESSION['session_auth']['mi_id_schema'] = 1001;

        $dlA = 'dlb';
        $dlB = 'crGalbel';

        $sfsv_txt = (ConfigGlobal::mi_sfsv() === 1) ? 'v' : 'f';

        $reg_dl_org = 'H-' . $dlA . $sfsv_txt;
        $Qnew_dl = 'Galbel-' . $dlB . $sfsv_txt;

        $this->sreg_dl_org = $reg_dl_org;
        $this->sreg_dl_dst = $Qnew_dl;

        foreach ($this->cPersonaNotas as $oPersonaNota) {
            $oEditarPersonaNota = $this->nuevoEditarPersonaNota($oPersonaNota);
            $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr($dlA);
            $esquema_region_stgr = $datosRegionStgr['esquema_region_stgr'];
            $a_ObjetosPersonaNota = $oEditarPersonaNota->getReposPersonaNota($datosRegionStgr, $this->id_schema_persona);
            $oEditarPersonaNota->crear_nueva_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota);
        }

        $oTrasladoDl = $GLOBALS['container']->get(\src\personas\domain\Trasladar::class);
        $oTrasladoDl->setId_nom($this->id_nom);
        $oTrasladoDl->setDl_persona($dlA);
        $oTrasladoDl->setReg_dl_org($reg_dl_org);
        $oTrasladoDl->setReg_dl_dst($Qnew_dl);
        $oTrasladoDl->copiarNotas();

        $PersonaNotaOtraRegionStgrRepository = $GLOBALS['container']->make(
            PersonaNotaOtraRegionStgrRepositoryInterface::class,
            ['esquema_region_stgr' => $esquema_region_stgr]
        );
        $PersonaNotaOtraRegionStgrRepository->setoDbl($this->setConexion($esquema_region_stgr));
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignaturaVo()->value();
            $cPersonaNotasOtraRegion = $PersonaNotaOtraRegionStgrRepository->getPersonaNotas([
                'id_nom' => $this->id_nom,
                'id_asignatura' => $id_asignatura,
            ]);
            $this->assertEmpty($cPersonaNotasOtraRegion);
        }

        $oDBdst = $this->conexionDst();
        $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaDlRepositoryInterface::class);
        $PersonaNotaDBRepository->setoDbl($oDBdst);
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignaturaVo()->value();
            $cPersonaNotasB = $PersonaNotaDBRepository->getPersonaNotas([
                'id_nom' => $this->id_nom,
                'id_asignatura' => $id_asignatura,
            ]);
            $this->assertEmpty($cPersonaNotasB);
        }

        $oDBActa = $this->setConexion('H-dlbv');
        $PersonaNotaDBRepository->setoDbl($oDBActa);
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignaturaVo()->value();
            $cPersonaNotasActa = $PersonaNotaDBRepository->getPersonaNotas([
                'id_nom' => $this->id_nom,
                'id_asignatura' => $id_asignatura,
            ]);
            $this->assertNotEmpty($cPersonaNotasActa);
            $PersonaNotaDBRepository->Eliminar($cPersonaNotasActa[0]);
        }
    }
    ///

    /////////// Traslado de notas de dl a dl de una misma región del stgr. ///////////
    /**
     * Traslado dlA→dlB misma región: las notas permanecen en e_notas_dl del acta origen.
     *
     * @return void
     */
    public function test_traslado_de_dlA_a_dlB(): void
    {
        $_SESSION['session_auth']['esquema'] = 'H-dlbv';
        $_SESSION['session_auth']['mi_id_schema'] = 1001;

        $dlA = 'dlb';
        $dlB = 'dlp';

        $sfsv_txt = (ConfigGlobal::mi_sfsv() === 1) ? 'v' : 'f';

        $reg_dl_org = 'H-' . $dlA . $sfsv_txt;
        $Qnew_dl = 'H-' . $dlB . $sfsv_txt;

        $this->sreg_dl_org = $reg_dl_org;
        $this->sreg_dl_dst = $Qnew_dl;

        $this->borrar_antes_de_crear_notas($this->id_nom, 'H-' . $dlB);

        foreach ($this->cPersonaNotas as $oPersonaNota) {
            $oEditarPersonaNota = $this->nuevoEditarPersonaNota($oPersonaNota);
            $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr($dlA);
            $a_ObjetosPersonaNota = $oEditarPersonaNota->getReposPersonaNota($datosRegionStgr, $this->id_schema_persona);
            $oEditarPersonaNota->crear_nueva_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota);
        }

        $oTrasladoDl = $GLOBALS['container']->get(\src\personas\domain\Trasladar::class);
        $oTrasladoDl->setId_nom($this->id_nom);
        $oTrasladoDl->setDl_persona($dlA);
        $oTrasladoDl->setReg_dl_org($reg_dl_org);
        $oTrasladoDl->setReg_dl_dst($Qnew_dl);

        $this->assertTrue($oTrasladoDl->copiarNotas());

        $oDBdst = $this->conexionDst();
        $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaDlRepositoryInterface::class);
        $PersonaNotaDBRepository->setoDbl($oDBdst);
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignaturaVo()->value();
            $cPersonaNotasB = $PersonaNotaDBRepository->getPersonaNotas([
                'id_nom' => $this->id_nom,
                'id_asignatura' => $id_asignatura,
            ]);
            $this->assertEmpty($cPersonaNotasB, 'El traslado no debe crear notas en el esquema destino');
        }

        $oDBorg = $this->setConexion($reg_dl_org);
        $PersonaNotaDBRepository->setoDbl($oDBorg);
        foreach ($this->cPersonaNotas as $oPersonaNotaA) {
            $id_asignatura = $oPersonaNotaA->getIdAsignaturaVo()->value();
            $cPersonaNotasActa = $PersonaNotaDBRepository->getPersonaNotas([
                'id_nom' => $this->id_nom,
                'id_asignatura' => $id_asignatura,
            ]);
            $this->assertNotEmpty($cPersonaNotasActa, 'La nota debe permanecer en e_notas_dl del acta origen');
            $PersonaNotaDBRepository->Eliminar($cPersonaNotasActa[0]);
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
            $oEditarPersonaNota = $this->nuevoEditarPersonaNota($oPersonaNota);
            $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr();
            $a_ObjetosPersonaNota = $oEditarPersonaNota->getReposPersonaNota($datosRegionStgr, $this->id_schema_persona);
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
        $oTrasladoDl = $GLOBALS['container']->get(\src\personas\domain\Trasladar::class);
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
            $oConfigDB = new ConfigDB($database);
            if (!$oConfigDB->tieneEsquema($esquema)) {
                $oDBPropiedades = new DBPropiedades();
                $aEsquemas = $oDBPropiedades->array_posibles_esquemas();
                if (is_array($aEsquemas)) {
                    $aEsquemas['H-Hv'] = 'H-Hv';
                    if (!in_array($esquema, $aEsquemas, true)) {
                        $esquema = 'restov';
                    }
                } else {
                    $esquema = 'restov';
                }
            }
        }

        if (!isset($oConfigDB)) {
            $oConfigDB = new ConfigDB($database);
        }
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
            case 'P-crP':
                $this->id_nom = 102612;
                $this->id_schema_persona = 1026;
                break;
            case 'Galbel-crGalbel':
                $this->id_nom = 103612;
                $this->id_schema_persona = 1036;
                break;
            case 'Pla-crPla':
                $this->id_nom = 103212;
                $this->id_schema_persona = 1032;
                break;
            case 'Nig-crNig':
                $this->id_nom = 102412;
                $this->id_schema_persona = 1024;
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

        $PersonaNotaDBRepository = $GLOBALS['container']->get(PersonaNotaDlRepositoryInterface::class);
        $PersonaNotaDBRepository->setoDbl($oDBdst);
        $aWhere = ['id_nom' => $id_nom];
        $cPersonaNotas = $PersonaNotaDBRepository->getPersonaNotas($aWhere);
        foreach ($cPersonaNotas as $oPersonaNota) {
            $PersonaNotaDBRepository->Eliminar($oPersonaNota);
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