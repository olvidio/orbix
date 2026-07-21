<?php

namespace Tests\unit\notas;

use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\DBConnection;
use src\shared\infrastructure\persistence\postgresql\DBPropiedades;
use src\notas\application\EditarPersonaNota;
use src\notas\domain\contracts\PersonaNotaDlRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaOtraRegionStgrRepositoryInterface;
use src\notas\domain\value_objects\NotaSituacion;
use src\notas\domain\value_objects\TipoActa;
use src\personas\domain\Trasladar;
use src\ubis\application\services\DelegacionUtils;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use Tests\factories\notas\NotasFactory;
use Tests\myTest;

/**
 * Tests de intención del contrato «notas ancladas al acta» (modelo B).
 *
 * @see docs/dev/notas_modelo_acta.md
 */
class trasladosNotasModeloActaTest extends myTest
{
    private string $session_org;

    private string $snew_esquema;
    private string $sreg_dl_org;
    private string $sreg_dl_dst;

    private int $id_nom;
    private array $cPersonaNotas;
    private int $id_schema_persona;

    public function setUp(): void
    {
        parent::setUp();
        putenv('UBICACION=sv');
        $this->session_org = $_SESSION['session_auth']['esquema'];

        $this->generarNotas('H-dlb');
    }

    /**
     * Traslado DL→DL misma región STGR: las notas permanecen en e_notas_dl del esquema del acta.
     *
     * Contrato: no se borran del origen ni aparecen como notas de acta nuevas en destino
     * solo por el traslado administrativo de la persona.
     */
    public function test_traslado_orbix_misma_region_no_mueve_notas_del_acta(): void
    {
        $dlA = 'dlb';
        $dlB = 'dlp';
        $esquemaActa = 'H-dlb';

        $this->guardar_notas($esquemaActa);
        $this->trasladar_notas($esquemaActa, 'H-dlp');

        $oDBActa = $this->setConexion($esquemaActa . 'v');
        $PersonaNotaDlRepository = $GLOBALS['container']->get(PersonaNotaDlRepositoryInterface::class);
        $PersonaNotaDlRepository->setoDbl($oDBActa);

        foreach ($this->cPersonaNotas as $oPersonaNotaEsperada) {
            $id_asignatura = $oPersonaNotaEsperada->getIdAsignaturaVo()->value();
            $cNotasActa = $PersonaNotaDlRepository->getPersonaNotas([
                'id_nom' => $this->id_nom,
                'id_asignatura' => $id_asignatura,
            ]);
            $this->assertNotEmpty($cNotasActa, 'La nota debe seguir en e_notas_dl del esquema del acta');
        }

        $oDBDestino = $this->conexionDst();
        $PersonaNotaDlRepository->setoDbl($oDBDestino);
        foreach ($this->cPersonaNotas as $oPersonaNotaEsperada) {
            $id_asignatura = $oPersonaNotaEsperada->getIdAsignaturaVo()->value();
            $cNotasDestino = $PersonaNotaDlRepository->getPersonaNotas([
                'id_nom' => $this->id_nom,
                'id_asignatura' => $id_asignatura,
            ]);
            $this->assertEmpty($cNotasDestino, 'El traslado no debe crear notas de acta en el esquema destino');
        }
    }

    /**
     * Traslado a otra región STGR Orbix: sin placeholders FALTA_CERTIFICADO ni vaciado del acta.
     */
    public function test_traslado_orbix_otra_region_stgr_no_crea_placeholder_certificado(): void
    {
        $esquemaActa = 'Galbel-crGalbel';
        $esquemaDestino = 'H-dlb';

        $this->generarNotas($esquemaActa);
        $this->guardar_notas($esquemaActa);
        $this->trasladar_notas($esquemaActa, $esquemaDestino);

        $DelegacionRepository = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        $esquema_region_stgr = $DelegacionRepository->mi_region_stgr('crGalbel')['esquema_region_stgr'];

        // Sin filas placeholder tipo_acta=2 / FALTA_CERTIFICADO en destino.
        $oDBDestino = $this->conexionDst();
        $PersonaNotaDlRepository = $GLOBALS['container']->get(PersonaNotaDlRepositoryInterface::class);
        $PersonaNotaDlRepository->setoDbl($oDBDestino);
        foreach ($this->cPersonaNotas as $oPersonaNotaEsperada) {
            $id_asignatura = $oPersonaNotaEsperada->getIdAsignaturaVo()->value();
            $cNotasDestino = $PersonaNotaDlRepository->getPersonaNotas([
                'id_nom' => $this->id_nom,
                'id_asignatura' => $id_asignatura,
            ]);
            foreach ($cNotasDestino as $oNotaDestino) {
                $this->assertNotEquals(
                    TipoActa::FORMATO_CERTIFICADO,
                    $oNotaDestino->getTipo_acta(),
                    'No debe crearse placeholder certificado (tipo_acta=2) por traslado interno'
                );
                $this->assertNotEquals(
                    NotaSituacion::FALTA_CERTIFICADO,
                    $oNotaDestino->getId_situacion(),
                    'No debe crearse fila FALTA_CERTIFICADO por traslado interno'
                );
            }
        }

        // La nota real sigue en e_notas_dl del acta; no se desplaza a e_notas_otra_region_stgr.
        $oDBActa = $this->setConexion($esquemaActa . 'v');
        $PersonaNotaDlRepository->setoDbl($oDBActa);
        foreach ($this->cPersonaNotas as $oPersonaNotaEsperada) {
            $id_asignatura = $oPersonaNotaEsperada->getIdAsignaturaVo()->value();
            $cNotasActa = $PersonaNotaDlRepository->getPersonaNotas([
                'id_nom' => $this->id_nom,
                'id_asignatura' => $id_asignatura,
            ]);
            $this->assertNotEmpty($cNotasActa, 'La nota real debe permanecer en e_notas_dl del acta');
        }

        $PersonaNotaOtraRegionRepository = $GLOBALS['container']->make(
            PersonaNotaOtraRegionStgrRepositoryInterface::class,
            ['esquema_region_stgr' => $esquema_region_stgr]
        );
        $PersonaNotaOtraRegionRepository->setoDbl($this->setConexion($esquema_region_stgr));
        foreach ($this->cPersonaNotas as $oPersonaNotaEsperada) {
            $id_asignatura = $oPersonaNotaEsperada->getIdAsignaturaVo()->value();
            $cNotasOtraRegion = $PersonaNotaOtraRegionRepository->getPersonaNotas([
                'id_nom' => $this->id_nom,
                'id_asignatura' => $id_asignatura,
            ]);
            $this->assertEmpty($cNotasOtraRegion, 'La nota no debe moverse a e_notas_otra_region_stgr');
        }
    }

    /**
     * Traslado interno Orbix: e_notas_otra_region_stgr no actúa como almacén de viaje de la nota.
     */
    public function test_traslado_interno_no_usa_e_notas_otra_region_como_almacen_de_viaje(): void
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

        $oTrasladoDl = $GLOBALS['container']->get(Trasladar::class);
        $oTrasladoDl->setId_nom($this->id_nom);
        $oTrasladoDl->setDl_persona($dlA);
        $oTrasladoDl->setReg_dl_org($reg_dl_org);
        $oTrasladoDl->setReg_dl_dst($Qnew_dl);
        $oTrasladoDl->copiarNotas();

        $PersonaNotaOtraRegionRepository = $GLOBALS['container']->make(
            PersonaNotaOtraRegionStgrRepositoryInterface::class,
            ['esquema_region_stgr' => $esquema_region_stgr]
        );
        $PersonaNotaOtraRegionRepository->setoDbl($this->setConexion($esquema_region_stgr));
        foreach ($this->cPersonaNotas as $oPersonaNotaEsperada) {
            $id_asignatura = $oPersonaNotaEsperada->getIdAsignaturaVo()->value();
            $cNotasOtraRegion = $PersonaNotaOtraRegionRepository->getPersonaNotas([
                'id_nom' => $this->id_nom,
                'id_asignatura' => $id_asignatura,
            ]);
            $this->assertEmpty(
                $cNotasOtraRegion,
                'Traslado interno Orbix no debe poblar e_notas_otra_region_stgr'
            );
        }
    }

    /**
     * Expediente agregado: publicv.e_notas (tabla padre de e_notas_dl) expone las notas por id_nom.
     *
     * Slice 3 implementará ExpedienteNotasPersona leyendo esta vista/tabla padre;
     * aquí solo fijamos el contrato de lectura cross-esquema.
     */
    public function test_expediente_legible_via_publicv_e_notas(): void
    {
        $esquemaActa = 'H-dlb';
        $this->guardar_notas($esquemaActa);

        $oDB = $this->setConexion('H-dlbv');
        $sql = 'SELECT COUNT(*) FROM publicv.e_notas WHERE id_nom = :id_nom';
        $stmt = $oDB->prepare($sql);
        $stmt->execute(['id_nom' => $this->id_nom]);
        $count = (int) $stmt->fetchColumn();

        $this->assertGreaterThanOrEqual(
            count($this->cPersonaNotas),
            $count,
            'publicv.e_notas debe agregar las filas de e_notas_dl del acta para id_nom'
        );
    }

    /**
     * Destino entidad externa: el vehículo es certificado documental, no copia de nota como acta.
     *
     * Esqueleto: cuando el destino cumple §2 del ADR (resto, paso, fuera Orbix),
     * no se inventa e_notas_dl en un esquema destino; la comunicación es vía certificado/PDF.
     */
    public function test_destino_externo_certificado_documental_no_copia_nota_como_acta(): void
    {
        $this->markTestIncomplete('Slice 2/5: certificado externo — ver docs/dev/notas_modelo_acta.md §2');

        // Contrato (§2 ADR): destino externo → certificado documental opcional;
        // la nota permanece en e_notas_dl de la DL examinadora.
        // Implementación futura: traslado hacia restov/restof o id_nom negativo;
        // assert: sin filas nuevas en e_notas_dl del destino externo;
        // assert: nota intacta en esquema del acta.
        $this->assertTrue(true);
    }

    // --- Helpers (copiados mínimos de trasladosNotasTest) ---

    private function nuevoEditarPersonaNota(\src\notas\domain\entity\PersonaNota $oPersonaNota): EditarPersonaNota
    {
        $container = $GLOBALS['container'];
        return new EditarPersonaNota(
            $oPersonaNota,
            $container->get(\src\notas\domain\contracts\PersonaNotaRepositoryInterface::class),
            $container->get(DelegacionRepositoryInterface::class),
            $container->get(\src\utils_database\domain\contracts\DbSchemaRepositoryInterface::class),
            $container->get(\src\dossiers\domain\contracts\DossierRepositoryInterface::class),
            $container->get(PersonaNotaDlRepositoryInterface::class),
        );
    }

    private function guardar_notas($esquemaA): void
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

        foreach ($this->cPersonaNotas as $oPersonaNota) {
            $oEditarPersonaNota = $this->nuevoEditarPersonaNota($oPersonaNota);
            $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr();
            $a_ObjetosPersonaNota = $oEditarPersonaNota->getReposPersonaNota($datosRegionStgr, $this->id_schema_persona);
            $oEditarPersonaNota->crear_nueva_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota);
        }
    }

    private function trasladar_notas($esquemaA, $esquemaB): void
    {
        $DelegacionRepository = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        $dlA = DelegacionUtils::getDlFromSchema($esquemaA);
        $a_mi_region_stgr = $DelegacionRepository->mi_region_stgr($dlA);
        $id_esquemaA = $a_mi_region_stgr['mi_id_schema'];

        $sfsv_txt = (ConfigGlobal::mi_sfsv() === 1) ? 'v' : 'f';
        $reg_dl_org = $esquemaA . $sfsv_txt;
        $Qnew_dl = $esquemaB . $sfsv_txt;

        $_SESSION['session_auth']['esquema'] = $reg_dl_org;
        $_SESSION['session_auth']['mi_id_schema'] = $id_esquemaA;
        $this->id_schema_persona = $id_esquemaA;

        $oTrasladoDl = $GLOBALS['container']->get(Trasladar::class);
        $oTrasladoDl->setId_nom($this->id_nom);
        $oTrasladoDl->setDl_persona($dlA);
        $oTrasladoDl->setReg_dl_org($reg_dl_org);
        $oTrasladoDl->setReg_dl_dst($Qnew_dl);

        $oTrasladoDl->copiarNotas();
    }

    private function setConexion($esquema, $exterior = false): \PDO
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
        switch ($esquema) {
            case 'H-dlb':
                $this->id_nom = 100111832;
                $this->id_schema_persona = 1001;
                break;
            case 'Galbel-crGalbel':
                $this->id_nom = 103612;
                $this->id_schema_persona = 1036;
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

    private function borrar_antes_de_crear_notas($id_nom, $esquema): void
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

    private function conexionDst($exterior = false): \PDO
    {
        $this->snew_esquema = $this->sreg_dl_dst;
        return $this->setConexion($this->snew_esquema, $exterior);
    }

    protected function tearDown(): void
    {
        $_SESSION['session_auth']['esquema'] = $this->session_org;
        parent::tearDown();
    }
}
