<?php

namespace Tests\unit\notas;

use core\ConfigGlobal;
use Exception;
use notas\model\EditarPersonaNota;
use RuntimeException;
use src\notas\domain\contracts\PersonaNotaDlRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaOtraRegionStgrRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\notas\domain\entity\PersonaNota;
use src\notas\domain\entity\PersonaNotaOtraRegionStgr;
use src\ubis\application\services\DelegacionUtils;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use Tests\factories\notas\NotasFactory;
use Tests\myTest;

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
        $esquema = 'Pla-crPlav';
        $_SESSION['session_auth']['esquema'] = $esquema;

        // persona de paso: id_nom negativo; esquema = -1001;
        $id_nom = -1001123;
        $id_schema_persona = '-1001'; // restov
        $NotasFactory = new NotasFactory();
        $NotasFactory->setCount(1);
        $dl = DelegacionUtils::getDlFromSchema($esquema);
        $cPersonaNotas = $NotasFactory->create($id_nom, $dl);
        $personaNota = $cPersonaNotas[0];

        $oEditarPersonaNota = new EditarPersonaNota($personaNota);
        $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr();

        $a_ObjetosPersonaNota = $oEditarPersonaNota->getReposPersonaNota($datosRegionStgr, $id_schema_persona);

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
        $dl = DelegacionUtils::getDlFromSchema($esquema);
        $cPersonaNotas = $NotasFactory->create($id_nom, $dl);
        $personaNota = $cPersonaNotas[0];

        $oEditarPersonaNota = new EditarPersonaNota($personaNota);
        $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr();

        $a_ObjetosPersonaNota = $oEditarPersonaNota->getReposPersonaNota($datosRegionStgr, $id_schema_persona);

        $rta = $oEditarPersonaNota->crear_nueva_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota);
        $oPersonaNota = $rta['nota_real'];

        $DelegacionRepository = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        $a_mi_region_stgr = $DelegacionRepository->mi_region_stgr();
        $esquema_region_stgr = $a_mi_region_stgr['esquema_region_stgr'];

        $PersonaNotaOtraRegionStgrRepository = $GLOBALS['container']->make(PersonaNotaOtraRegionStgrRepositoryInterface::class, ['esquema_region_stgr' => $esquema_region_stgr]);
        $cPersonaNota = $PersonaNotaOtraRegionStgrRepository->getPersonaNotas(['id_nom' => $id_nom, 'id_asignatura' => $personaNota->getId_asignatura()]);
        $oPersonaNota2 = $cPersonaNota[0];

        $this->assertEquals($oPersonaNota, $oPersonaNota2);
        $PersonaNotaOtraRegionStgrRepository->Eliminar($oPersonaNota2);

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
        $dl = DelegacionUtils::getDlFromSchema($esquema);
        $cPersonaNotas = $NotasFactory->create($id_nom, $dl);
        $personaNota = $cPersonaNotas[0];

        $oEditarPersonaNota = new EditarPersonaNota($personaNota);
        $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr();

        $a_ObjetosPersonaNota = $oEditarPersonaNota->getReposPersonaNota($datosRegionStgr, $id_schema_persona);

        $rta = $oEditarPersonaNota->crear_nueva_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota);
        $oPersonaNota = $rta['nota_real'];

        $DelegacionRepository = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        $a_mi_region_stgr = $DelegacionRepository->mi_region_stgr();
        $esquema_region_stgr = $a_mi_region_stgr['esquema_region_stgr'];

        $PersonaNotaOtraRegionStgrRepository = $GLOBALS['container']->make(PersonaNotaOtraRegionStgrRepositoryInterface::class, ['esquema_region_stgr' => $esquema_region_stgr]);
        $cPersonaNota = $PersonaNotaOtraRegionStgrRepository->getPersonaNotas(['id_nom' => $id_nom, 'id_asignatura' => $personaNota->getId_asignatura()]);
        $oPersonaNota2 = $cPersonaNota[0];

        $this->assertEquals($oPersonaNota, $oPersonaNota2);
        $PersonaNotaOtraRegionStgrRepository->Eliminar($oPersonaNota2);

        // nota certificado (No debe existir)
        $oPersonaNotaCertificadoDB = $rta['nota_certificado'] ?? '';
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
        $dl = DelegacionUtils::getDlFromSchema($esquema);
        $cPersonaNotas = $NotasFactory->create($id_nom, $dl);
        $personaNota = $cPersonaNotas[0];

        $oEditarPersonaNota = new EditarPersonaNota($personaNota);
        $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr();

        $a_ObjetosPersonaNota = $oEditarPersonaNota->getReposPersonaNota($datosRegionStgr, $id_schema_persona);

        $rta = $oEditarPersonaNota->crear_nueva_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota);
        $oPersonaNota = $rta['nota_real'];

        $DelegacionRepository = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        $a_mi_region_stgr = $DelegacionRepository->mi_region_stgr();
        $esquema_region_stgr = $a_mi_region_stgr['esquema_region_stgr'];

        // Estoy en H-dlbv. La nota debe estar en GalBel-crGalBelv.
        // por tanto miro en la tabla padre y compruebo que el esquema es el que toca.
        $PersonaNotaOtraRegionStgrRepository = $GLOBALS['container']->make(PersonaNotaOtraRegionStgrRepositoryInterface::class, ['esquema_region_stgr' => $esquema_region_stgr]);
        $cPersonaNota = $PersonaNotaOtraRegionStgrRepository->getPersonaNotas(['id_nom' => $id_nom, 'id_asignatura' => $personaNota->getId_asignatura()]);
        if ($cPersonaNota !== null) {
            $oPersonaNota2 = $cPersonaNota[0];
        } else {
            $oPersonaNota2 = null;
        }
        $this->assertEquals($oPersonaNota, $oPersonaNota2);
        $PersonaNotaOtraRegionStgrRepository->Eliminar($oPersonaNota2);

        // guardar certificado
        $oPersonaNotaCertificadoDB = $rta['nota_certificado'] ?? '';
        $this->assertNotEquals('', $oPersonaNotaCertificadoDB);

        // Estoy en H-dlbv. La nota debe estar en GalBel-crGalBelv.
        // por tanto miro en la tabla padre y compruebo que el esquema es el que toca.
        $PersonaNotaRepository = $GLOBALS['container']->get(PersonaNotaRepositoryInterface::class);
        $cPersonaNotaDB = $PersonaNotaRepository->getPersonaNotas(['id_nom' => $id_nom, 'id_asignatura' => $personaNota->getId_asignatura()]);
        $oPersonaNotaDB = $cPersonaNotaDB[0];

        $this->assertEquals($oPersonaNotaCertificadoDB, $oPersonaNotaDB);

        $PersonaNotaRepository->Eliminar($oPersonaNotaDB);
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
        $esquema = 'H-dlbv';
        $_SESSION['session_auth']['esquema'] = $esquema;
        $_SESSION['session_auth']['mi_id_schema'] = 1001;

        // persona de la dlA
        $id_nom = 1029156;
        $id_schema_persona = 1029; // GalBelv
        $NotasFactory = new NotasFactory();
        $NotasFactory->setCount(1);
        $dl = DelegacionUtils::getDlFromSchema($esquema);
        $cPersonaNotas = $NotasFactory->create($id_nom, $dl);
        $personaNota = $cPersonaNotas[0];

        $oEditarPersonaNota = new EditarPersonaNota($personaNota);
        $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr();

        $a_ObjetosPersonaNota = $oEditarPersonaNota->getReposPersonaNota($datosRegionStgr, $id_schema_persona);

        $oEditarPersonaNota->crear_nueva_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota);
        // No lo compruebo porque ya está el test de guardar.
        // Modifico la Nota:
        $nota_anterior = $personaNota->getNotaNumVo()->value();
        $personaNota->setNotaNumVo($nota_anterior - 0.5);
        $id_asignatura_real = $personaNota->getId_asignatura();
        $rta = $oEditarPersonaNota->editar_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota, $id_asignatura_real);

        $oPersonaNota = $rta['nota_real'];

        $DelegacionRepository = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        $a_mi_region_stgr = $DelegacionRepository->mi_region_stgr();
        $esquema_region_stgr = $a_mi_region_stgr['esquema_region_stgr'];

        // Estoy en H-dlbv. La nota debe estar en GalBel-crGalBelv.
        // por tanto miro en la tabla padre y compruebo que el esquema es el que toca.
        $PersonaNotaOtraRegionStgrRepository = $GLOBALS['container']->make(PersonaNotaOtraRegionStgrRepositoryInterface::class, ['esquema_region_stgr' => $esquema_region_stgr]);
        $cPersonaNota = $PersonaNotaOtraRegionStgrRepository->getPersonaNotas(['id_nom' => $id_nom, 'id_asignatura' => $personaNota->getId_asignatura()]);
        if ($cPersonaNota !== null) {
            $oPersonaNota2 = $cPersonaNota[0];
        } else {
            $oPersonaNota2 = null;
        }
        $this->assertEquals($oPersonaNota, $oPersonaNota2);
        $PersonaNotaOtraRegionStgrRepository->Eliminar($oPersonaNota2);

        // guardar certificado
        $oPersonaNotaCertificadoDB = $rta['nota_certificado'] ?? '';
        $this->assertNotEquals('', $oPersonaNotaCertificadoDB);

        // Estoy en H-dlbv. La nota debe estar en GalBel-crGalBelv.
        // por tanto miro en la tabla padre y compruebo que el esquema es el que toca.
        $PersonaNotaRepository = $GLOBALS['container']->get(PersonaNotaRepositoryInterface::class);
        $cPersonaNotaDB = $PersonaNotaRepository->getPersonaNotas(['id_nom' => $id_nom, 'id_asignatura' => $personaNota->getId_asignatura()]);
        if ($cPersonaNotaDB !== null) {
            $oPersonaNotaDB = $cPersonaNotaDB[0];
        } else {
            $oPersonaNotaDB = null;
        }

        // Son dos clases distintas, no se pueden comparar. Miramos las propiedades
        $this->assertEquals($oPersonaNotaCertificadoDB, $oPersonaNotaDB);

        $PersonaNotaRepository->Eliminar($oPersonaNotaDB);
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
        $dl = DelegacionUtils::getDlFromSchema($esquema);
        $cPersonaNotas = $NotasFactory->create($id_nom, $dl);
        $personaNota = $cPersonaNotas[0];

        $oEditarPersonaNota = new EditarPersonaNota($personaNota);
        $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr();

        $a_ObjetosPersonaNota = $oEditarPersonaNota->getReposPersonaNota($datosRegionStgr, $id_schema_persona);

        $rta = $oEditarPersonaNota->crear_nueva_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota);
        $oPersonaNota = $rta['nota_real'];

        // Estoy en H-dlbv. La nota debe estar en H-dlsv.
        // por tanto miro en la tabla padre y compruebo que el esquema es el que toca.
        $PersonaNotaDlRepository = $GLOBALS['container']->get(PersonaNotaDlRepositoryInterface::class);
        $cPersonaNota = $PersonaNotaDlRepository->getPersonaNotas(['id_nom' => $id_nom, 'id_asignatura' => $personaNota->getId_asignatura()]);
        if ($cPersonaNota !== null) {
            $oPersonaNota2 = $cPersonaNota[0];
        } else {
            $oPersonaNota2 = null;
        }

        $this->assertEquals($oPersonaNota, $oPersonaNota2);

        $PersonaNotaDlRepository->Eliminar($oPersonaNota2);

        // guardar certificado
        $oPersonaNotaCertificadoDB = $rta['nota_certificado'] ?? '';
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
        $dl = DelegacionUtils::getDlFromSchema($esquema);
        $cPersonaNotas = $NotasFactory->create($id_nom, $dl);
        $oPersonaNota = $cPersonaNotas[0];
        $oPersonaNota->setId_schema($id_schema_persona);

        $oEditarPersonaNota = new EditarPersonaNota($oPersonaNota);
        $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr();

        $a_ObjetosPersonaNota = $oEditarPersonaNota->getReposPersonaNota($datosRegionStgr, $id_schema_persona);

        $rta = $oEditarPersonaNota->crear_nueva_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota);
        $oPersonaNota1 = $rta['nota_real'];

        $PersonaNotaDlRepository =$GLOBALS['container']->get(PersonaNotaDlRepositoryInterface::class);
        $cPersonaNota = $PersonaNotaDlRepository->getPersonaNotas(['id_nom' => $id_nom, 'id_asignatura' => $oPersonaNota->getId_asignatura()]);
        $oPersonaNota2 = $cPersonaNota[0];

        $this->assertEquals($oPersonaNota1, $oPersonaNota2);
        $PersonaNotaDlRepository->Eliminar($oPersonaNota2);

        // nota certificado (No debe existir)
        $oPersonaNotaCertificado = $rta['nota_certificado'] ?? '';
        $this->assertEquals('', $oPersonaNotaCertificado);
    }


    /**
     * Comprobar que se puede guardar una nota en la base de datos:
     *  tabla "H-Hv".e_notas_otra_region_stgr
     * Se diferencia de PersonaNota en la propiedad de json_certificados
     *
     * @return void
     */
    public function test_save_PersonaNotaOtraRegionStgr(): void
    {
        $esquema_region_stgr = "H-Hv";
        $PersonaNotaOtraRegionStgrRepository = $GLOBALS['container']->make(PersonaNotaOtraRegionStgrRepositoryInterface::class, ['esquema_region_stgr' => $esquema_region_stgr]);
        $oPersonaNota = $this->crear_PersonaNotaOtraRegion();
        $PersonaNotaOtraRegionStgrRepository->Guardar($oPersonaNota);

        $id_nom = $oPersonaNota->getId_nom();
        $id_asignatura = $oPersonaNota->getId_asignatura();

        $cPersonaNota = $PersonaNotaOtraRegionStgrRepository->getPersonaNotas(['id_nom' => $id_nom, 'id_asignatura' => $id_asignatura]);
        $oPersonaNota2 = $cPersonaNota[0];

        // Falla al comprara el json, porque no está formateado exactamente igual.
        // Comparamos las propiedades de las clases.
        $this->assertEquals($oPersonaNota->getId_schema(), $oPersonaNota2->getId_schema());
        $this->assertEquals($oPersonaNota->getId_nom(), $oPersonaNota2->getId_nom());
        $this->assertEquals($oPersonaNota->getId_nivel(), $oPersonaNota2->getId_nivel());
        $this->assertEquals($oPersonaNota->getId_asignatura(), $oPersonaNota2->getId_asignatura());
        $this->assertEquals($oPersonaNota->getId_situacion(), $oPersonaNota2->getId_situacion());
        $this->assertEquals($oPersonaNota->getActa(), $oPersonaNota2->getActa());
        $this->assertEquals($oPersonaNota->getF_acta(), $oPersonaNota2->getF_acta());
        $this->assertEquals($oPersonaNota->getTipo_acta(), $oPersonaNota2->getTipo_acta());
        $this->assertEquals($oPersonaNota->isPreceptor(), $oPersonaNota2->isPreceptor());
        $this->assertEquals($oPersonaNota->getId_preceptor(), $oPersonaNota2->getId_preceptor());
        $this->assertEquals($oPersonaNota->getDetalle(), $oPersonaNota2->getDetalle());
        $this->assertEquals($oPersonaNota->getEpoca(), $oPersonaNota2->getEpoca());
        $this->assertEquals($oPersonaNota->getId_activ(), $oPersonaNota2->getId_activ());
        $this->assertEquals($oPersonaNota->getNota_num(), $oPersonaNota2->getNota_num());
        $this->assertEquals($oPersonaNota->getNota_max(), $oPersonaNota2->getNota_max());
        $this->assertEquals($oPersonaNota->getTipo_acta(), $oPersonaNota2->getTipo_acta());

        // Para el JSON, lo decodificamos para comparar la estructura de datos real, no el string
        $json1 = $oPersonaNota->getJson_certificados();
        $json2 = $oPersonaNota2->getJson_certificados();
        $this->assertEquals($json1, $json2);
        $PersonaNotaOtraRegionStgrRepository->Eliminar($oPersonaNota2);
    }

    /**
     * Comprobar que se puede guardar una nota en la base de datos:
     *  tabla "H-dlbv".e_notas_dl (la dl que está ejecutando el test)
     *
     * @return void
     */
    public function test_save_PersonaNotaDl(): void
    {
        $PersonaNotaDlRepository = $GLOBALS['container']->get(PersonaNotaDlRepositoryInterface::class);
        $oPersonaNota = $this->crear_PersonaNota();
        $PersonaNotaDlRepository->Guardar($oPersonaNota);

        $id_nom = $oPersonaNota->getId_nom();
        $id_asignatura = $oPersonaNota->getIdAsignaturaVo()->value();

        $cPersonaNota = $PersonaNotaDlRepository->getPersonaNotas(['id_nom' => $id_nom, 'id_asignatura' => $id_asignatura]);
        $oPersonaNota2 = $cPersonaNota[0];

        $this->assertEquals($oPersonaNota, $oPersonaNota2);
        $PersonaNotaDlRepository->Eliminar($oPersonaNota2);
    }

    public function crear_PersonaNotaOtraRegion(): PersonaNotaOtraRegionStgr
    {
        $esquema = 'H-dlbv';
        $id_schema = 1012; // para dlb, el esquema de la region es H: 1012
        $id_nom = 100112345;
        $NotasFactory = new NotasFactory();
        $NotasFactory->setCount(1);
        $dl = DelegacionUtils::getDlFromSchema($esquema);
        $cPersonaNotas = $NotasFactory->create($id_nom, $dl);
        $personaNota = $cPersonaNotas[0];

        $oPersonaNota = new PersonaNotaOtraRegionStgr();
        $oPersonaNota->setId_schema($id_schema);
        $oPersonaNota->setId_nivel($personaNota->getId_nivel());
        $oPersonaNota->setId_asignatura($personaNota->getIdAsignaturaVo()->value());
        $oPersonaNota->setId_nom($personaNota->getId_nom());
        $oPersonaNota->setId_situacion($personaNota->getIdSituacionVo()->value());
        $oPersonaNota->setActa($personaNota->getActaVo()->value());
        $oPersonaNota->setF_acta($personaNota->getF_acta());
        $oPersonaNota->setTipo_acta($personaNota->getTipoActaVo()->value());
        $oPersonaNota->setPreceptor($personaNota->isPreceptor());
        $oPersonaNota->setId_preceptor($personaNota->getId_preceptor());
        $oPersonaNota->setDetalle($personaNota->getDetalleVo()?->value());
        $oPersonaNota->setEpoca($personaNota->getEpocaVo()?->value());
        $oPersonaNota->setId_activ($personaNota->getIdActivVo()?->value());
        $oPersonaNota->setNota_num($personaNota->getNotaNumVo()?->value());
        $oPersonaNota->setNota_max($personaNota->getNotaMaxVo()?->value());
        // Añado el json_certificados
        $json_certificados = $NotasFactory->getJson_certificados();
        $oPersonaNota->setJson_certificados($json_certificados);

        return $oPersonaNota;
    }

    public function crear_PersonaNota(): PersonaNota
    {
        $esquema = 'H-dlbv';
        $id_schema = 1001;
        $id_nom = 100112345;
        $NotasFactory = new NotasFactory();
        $NotasFactory->setCount(1);
        $dl = DelegacionUtils::getDlFromSchema($esquema);
        $cPersonaNotas = $NotasFactory->create($id_nom, $dl);
        $personaNota = $cPersonaNotas[0];

        $oPersonaNotaNew = new PersonaNota();
        $oPersonaNotaNew->setId_schema($id_schema);
        $oPersonaNotaNew->setId_nivel($personaNota->getId_nivel());
        $oPersonaNotaNew->setId_asignatura($personaNota->getIdAsignaturaVo()->value());
        $oPersonaNotaNew->setId_nom($personaNota->getId_nom());
        $oPersonaNotaNew->setId_situacion($personaNota->getIdSituacionVo()->value());
        $oPersonaNotaNew->setActa($personaNota->getActaVo()->value());
        $oPersonaNotaNew->setF_acta($personaNota->getF_acta());
        $oPersonaNotaNew->setTipo_acta($personaNota->getTipoActaVo()->value());
        $oPersonaNotaNew->setPreceptor($personaNota->isPreceptor());
        $oPersonaNotaNew->setId_preceptor($personaNota->getId_preceptor());
        $oPersonaNotaNew->setDetalle($personaNota->getDetalleVo()?->value());
        $oPersonaNotaNew->setEpoca($personaNota->getEpocaVo()?->value());
        $oPersonaNotaNew->setId_activ($personaNota->getIdActivVo()?->value());
        $oPersonaNotaNew->setNota_num($personaNota->getNotaNumVo()?->value());
        $oPersonaNotaNew->setNota_max($personaNota->getNotaMaxVo()?->value());

        return $oPersonaNotaNew;

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