<?php

namespace src\personas\domain;

use PDO;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\DBConnection;
use src\shared\infrastructure\persistence\postgresql\DBPropiedades;
use src\notas\application\EditarPersonaNota;
use src\notas\domain\contracts\PersonaNotaRepositoryInterface;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteOutRepositoryInterface;
use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\certificados\domain\contracts\CertificadoRecibidoRepositoryInterface;
use src\certificados\domain\entity\CertificadoEmitido;
use src\certificados\domain\entity\CertificadoRecibido;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\domain\entity\PersonaAgd;
use src\personas\domain\entity\PersonaN;
use src\personas\domain\entity\PersonaNax;
use src\personas\domain\entity\PersonaS;
use src\personas\domain\entity\PersonaSSSC;
use src\personas\domain\contracts\TelecoPersonaDlRepositoryInterface;
use src\personas\domain\contracts\TrasladoRepositoryInterface;
use src\personas\domain\entity\Traslado;
use src\personas\domain\value_objects\SituacionCode;
use src\profesores\domain\contracts\ProfesorAmpliacionRepositoryInterface;
use src\profesores\domain\contracts\ProfesorCongresoRepositoryInterface;
use src\profesores\domain\contracts\ProfesorDirectorRepositoryInterface;
use src\profesores\domain\contracts\ProfesorDocenciaStgrRepositoryInterface;
use src\profesores\domain\contracts\ProfesorJuramentoRepositoryInterface;
use src\profesores\domain\contracts\ProfesorLatinRepositoryInterface;
use src\profesores\domain\contracts\ProfesorPublicacionRepositoryInterface;
use src\profesores\domain\contracts\ProfesorStgrRepositoryInterface;
use src\profesores\domain\contracts\ProfesorTituloEstRepositoryInterface;
use src\shared\domain\contracts\ConnectionRepositoryFactoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\application\services\DelegacionUtils;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\shared\infrastructure\GlobalPdo;


class Trasladar
{
    private string $serror = '';

    private int $iid_nom;
    private string $sdl_persona;
    private string $sdl_org;
    private string $sreg_dl_org;
    private string $sdl_dst;
    private string $sreg_dl_dst;
    private SituacionCode $ssituacion;
    private ?DateTimeLocal $df_traslado = null;

    /* para guardar el search path de la conexión a la base de datos */
    private string $snew_esquema;
    private string $sresolved_esquema = '';
    private string $sdatabase = '';
    private string $sconfig_user = '';
    private PDO $oDBR;

    public function __construct(
        private DelegacionRepositoryInterface $delegacionRepository,
        private AsignaturaRepositoryInterface $asignaturaRepository,
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private TipoDossierRepositoryInterface $tipoDossierRepository,
        private CertificadoEmitidoRepositoryInterface $certificadoEmitidoRepository,
        private ConnectionRepositoryFactoryInterface $connectionRepositoryFactory,
    ) {
        $this->oDBR = GlobalPdo::get('oDBR');
    }

    public function getError(): string
    {
        return $this->serror;
    }

    public function getId_nom(): int
    {
        return $this->iid_nom;
    }

    public function setId_nom(int $iid_nom): void
    {
        $this->iid_nom = $iid_nom;
    }

    public function getDl_persona(): string
    {
        return $this->sdl_persona;
    }

    public function setDl_persona(string $sdl_persona): void
    {
        $this->sdl_persona = $sdl_persona;
    }

    public function getDl_org(): string
    {
        return $this->sdl_org;
    }

    public function setDl_org(string $sdl_org): void
    {
        $this->sdl_org = $sdl_org;
    }

    public function getReg_dl_org(): string
    {
        return $this->sreg_dl_org;
    }

    public function setReg_dl_org(string $sreg_dl_org): void
    {
        $this->sreg_dl_org = $sreg_dl_org;

        $a_reg = explode('-', $sreg_dl_org);
        //$this->sdl_org = $a_reg[1];
        $this->sdl_org = substr($a_reg[1], 0, -1); // quito la v o la f.
    }

    public function getReg_dl_dst(): string
    {
        return $this->sreg_dl_dst;
    }

    public function setReg_dl_dst(string $sreg_dl_dst): void
    {
        $this->sreg_dl_dst = $sreg_dl_dst;

        $a_reg = explode('-', $sreg_dl_dst);
        //$this->sdl_dst = $a_reg[1];
        $this->sdl_dst = substr($a_reg[1], 0, -1); // quito la v o la f.
    }

    public function getSituacionVo(): string
    {
        return $this->ssituacion->value();
    }

    public function setSituacionVo(SituacionCode $ssituacion): void
    {
        $this->ssituacion = $ssituacion;
    }

    public function getF_traslado(): ?DateTimeLocal
    {
        return $this->df_traslado;
    }

    public function setF_traslado(?DateTimeLocal $df_traslado): void
    {
        $this->df_traslado = $df_traslado;
    }

    private function getConexionEsquema(string $esquema, bool $exterior = false): PDO
    {
        $this->sresolved_esquema = $esquema;

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

                if (!is_array($aEsquemas) || !in_array($esquema, $aEsquemas, true)) {
                    $esquema = 'restov';
                }
            }
        }

        $this->sdatabase = $database;
        $this->sresolved_esquema = $esquema;

        if (!isset($oConfigDB)) {
            $oConfigDB = new ConfigDB($database);
        }
        $config = $oConfigDB->getEsquema($esquema);
        $configUser = $config['user'] ?? '';
        $this->sconfig_user = is_string($configUser) ? $configUser : '';
        // Fuerzo el schema efectivo para evitar sobrescrituras accidentales desde ficheros .inc.
        $config['schema'] = $esquema;
        $oConexion = new DBConnection($config);

        return $oConexion->getPDO();
    }

    private function getConexionOrg(bool $exterior = false): PDO
    {
        $this->snew_esquema = $this->sreg_dl_org;
        return $this->getConexionEsquema($this->snew_esquema, $exterior);
    }

    private function getConexionDst(bool $exterior = false): PDO
    {
        $this->snew_esquema = $this->sreg_dl_dst;
        return $this->getConexionEsquema($this->snew_esquema, $exterior);
    }

    /* -----------------------------------------------------------------------*/
    /**
     * @return array{success: bool, mensaje?: string}
     */
    public function trasladar(): array
    {
        if (($rta = $this->comprobar()) > 0) {
            $msg = $this->serror;
            if ($rta === 3) {
                if ($this->cambiarFichaPersona() === false) {
                    $msg .= "\n";
                    $msg .= _("OJO: Debería cambiar el campo situación. No se ha hecho ningún cambio.");
                }

            }
            $error_txt = _("comprobar:") . " " . $msg;

            $jsondata['success'] = FALSE;
            $jsondata['mensaje'] = $error_txt;
            return $jsondata;
        }

        // Aviso si le faltan notas
        if ($this->comprobarNotas() === false) {
            $msg = $this->serror;
        }

        // Cambio la situación de la persona. Debo hacerlo lo primero, pues no puedo
        // tener la misma persona en dos dl en la misma situación
        if ($this->cambiarFichaPersona() === false) {
            $msg = $this->serror;
            $error_txt = _("OJO: Debería cambiar el campo situación. No se ha hecho ningún cambio.") . $msg;
            $jsondata['success'] = FALSE;
            $jsondata['mensaje'] = $error_txt;
            return $jsondata;
        }

        // Trasladar persona
        if ($this->copiarPersona() === false) {
            $msg = $this->serror;
            $error_txt = _("copiar persona.") . $msg;
            $jsondata['success'] = FALSE;
            $jsondata['mensaje'] = $error_txt;
            return $jsondata;
        }

        if ($this->copiarNotas() === false) {
            $msg = $this->serror;
            $error_txt = _("copiar notas") . $msg;
            $jsondata['success'] = FALSE;
            $jsondata['mensaje'] = $error_txt;
            return $jsondata;
        }
        // apunto el traslado. Lo pongo antes para que se copie trasladar dossiers.
        if ($this->apuntar() === false) {
            $msg = $this->serror;
            $error_txt = _("apuntar traslado") . $msg;
            $jsondata['success'] = FALSE;
            $jsondata['mensaje'] = $error_txt;
            return $jsondata;
        }
        if ($this->trasladarDossiers() === false) {
            $msg = $this->serror;
            $error_txt = _("copiar dossiers") . $msg;
            $jsondata['success'] = FALSE;
            $jsondata['mensaje'] = $error_txt;
            return $jsondata;
        }

        if ($this->trasladarDossierCertificados() === false) {
            $msg = $this->serror;
            $error_txt = _("trasladar certificados") . $msg;
            $jsondata['success'] = FALSE;
            $jsondata['mensaje'] = $error_txt;
            return $jsondata;
        }

        if ($this->copiarAsistencias() === false) {
            $msg = $this->serror;
            $error_txt = _("copiar asistencias") . $msg;
            $jsondata['success'] = FALSE;
            $jsondata['mensaje'] = $error_txt;
            return $jsondata;
        }
        $jsondata['success'] = TRUE;
        return $jsondata;
    }

    private function comprobar(): int
    {
        $error = '';
        $rta = 0;
        if (!empty($this->sdl_dst) && ($this->sdl_dst === $this->sdl_persona)) {
            $error = _("ya está trasladado. No se ha hecho ningún cambio.");
            $rta = 1;
        }
        // Que la dl destino exista:
        $repoDelegacion = $this->delegacionRepository;
        $cDelegAll = $repoDelegacion->getDelegaciones(['active' => true]);
        $a_dl = [];
        foreach ($cDelegAll as $oDl) {
            $a_dl[] = $oDl->getDlVo()->value();
        }
        if (!empty($this->sdl_org) && !in_array($this->sdl_org, $a_dl, true)) {
            $error = _("No existe la dl origen. Ponerla bien en la ficha de la persona.");
            $rta = 2;
        }
        if (!empty($this->sdl_dst) && !in_array($this->sdl_dst, $a_dl, true)) {
            $error = _("No existe la dl destino.");
            $error .= "\n";
            $error .= _("Solamente se anotará el traslado. No se mueven los datos.");
            $rta = 3;
        }

        $this->serror = $error;
        return $rta;
    }

    public function comprobarNotas(): bool
    {
        // Aviso si le faltan notas
        $error = '';
        $oDBorg = $this->getConexionOrg();

        $MatriculaDlRepository = $this->repositoryWithConnection(MatriculaDlRepositoryInterface::class, $oDBorg);
        $cMatriculasPendientes = $MatriculaDlRepository->getMatriculasPendientes($this->iid_nom);
        $msg = '';
        $AsignaturaRepository = $this->asignaturaRepository;
        $ActividadAllRepository = $this->actividadAllRepository;
        foreach ($cMatriculasPendientes as $oMatricula) {
            $id_activ = $oMatricula->getId_activ();
            $id_asignatura = $oMatricula->getIdAsignaturaVo()->value();
            $oActividad = $ActividadAllRepository->findById($id_activ);
            if ($oActividad === null) {
                continue;
            }
            $nom_activ = $oActividad->getNom_activ();
            $oAsignatura = $AsignaturaRepository->findById($id_asignatura);
            if ($oAsignatura === null) {
                throw new \Exception(sprintf(_("No se ha encontrado la asignatura con id: %s"), $id_asignatura));
            }
            $nombre_corto = $oAsignatura->getNombre_corto();
            $msg .= empty($msg) ? '' : "\n";
            $msg .= sprintf(_("ca: %s, asignatura: %s"), $nom_activ, $nombre_corto);
        }
        //$this->restaurarConexionOrg($oDBorg);
        if (!empty($msg)) {
            $error = _("tiene pendiente de poner las notas de:") . "\n" . $msg;
        }
        if (!empty($error)) {
            $this->serror = $error;
            return false;
        }
        return true;
    }

    public function cambiarFichaPersona(): bool
    {
        // Cambio la situación de la persona. Debo hacerlo lo primero, pues no puedo tener la misma persona en dos dl en la misma situación
        if ($this->ssituacion->value() === 'A') exit (_("OJO: Debería cambiar el campo situación. No se ha hecho ningún cambio."));

        $error = '';
        $oDBorg = $this->getConexionOrg();
        $PersonaDlRepository = $this->repositoryWithConnection(PersonaDlRepositoryInterface::class, $oDBorg);
        $oPersonaDl = $PersonaDlRepository->findById($this->iid_nom);
        if ($oPersonaDl === null) {
            $error = $this->appendDebugContext(
                sprintf(_("No se ha encontrado la persona con id: %s"), $this->iid_nom),
                $oDBorg,
                null,
                $PersonaDlRepository
            );
        } else {
            $idTabla = $oPersonaDl->getIdTablaVo()->value();
            $repositoryId = $this->getPersonaRepositoryByTable($idTabla);
            if ($repositoryId === null) {
                $error = sprintf(_("No se reconoce el tipo de persona: %s"), $idTabla);
            } else {
                $personaOrgRepository = $this->createPersonaTypeRepository($repositoryId, $oDBorg);
                if ($personaOrgRepository === null) {
                    $error = sprintf(_("No se reconoce el tipo de persona: %s"), $idTabla);
                } else {
                    $oPersona = $personaOrgRepository->findById($this->iid_nom);
                    if ($oPersona === null) {
                        $error = $this->appendDebugContext(
                            sprintf(_("No se ha encontrado la persona origen con id: %s"), $this->iid_nom),
                            $oDBorg,
                            $idTabla,
                            $personaOrgRepository
                        );
                    } else {
                        $oPersona->setSituacionVo($this->ssituacion);
                        $oPersona->setF_situacion($this->df_traslado);
                        $oPersona->setDlVo($this->sdl_dst);
                        if ($this->guardarPersonaTraslado($personaOrgRepository, $oPersona) === false) {
                            $error .= '<br>' . _("hay un error, no se ha guardado");
                        }
                    }
                }
            }
        }
        //$this->restaurarConexionOrg($oDBorg);
        if (!empty($error)) {
            $this->serror = $error;
            return false;
        }
        return true;
    }

    /**
     * dado un id_nom, lo busca en todos los esquemas y si lo encuentra
     * devuelve un array con la informacion del esquema
     *
     * @return list<array<string, mixed>>
     */
    public function getEsquemas(int $id_orbix, string $tipo_persona): array
    {
        // posibles esquemas
        /*
         * @todo: filtrar por regiones?
         */
        //Utilizo la conexión oDBR para cambiar momentáneamente el search_path.
        $oDBR = $this->oDBR;
        $qRs = $oDBR->query("SELECT DISTINCT schemaname FROM pg_stat_user_tables");
        if ($qRs === false) {
            return [];
        }
        $aResultSql = $qRs->fetchAll(PDO::FETCH_ASSOC);
        $aEsquemas = $aResultSql;
        $qRs = $oDBR->query('SHOW search_path');
        if ($qRs === false) {
            return [];
        }
        $aPath = $qRs->fetch(PDO::FETCH_ASSOC);
        $path_org = addslashes(is_array($aPath) && isset($aPath['search_path']) && is_string($aPath['search_path']) ? $aPath['search_path'] : '');
        $aResult = [];
        $tabla_personas = match ($tipo_persona) {
            'n' => 'p_numerarios',
            'a' => 'p_agregados',
            'nax' => 'p_nax',
            's' => 'p_supernumerarios',
            default => 'p_numerarios',
        };
        foreach ($aEsquemas as $esquemaName) {
            if (!is_array($esquemaName) || !is_string($esquemaName['schemaname'] ?? null)) {
                continue;
            }
            $esquema = $esquemaName['schemaname'];
            //elimino el de H-H
            if (strpos($esquema, '-')) {
                $a_reg = explode('-', $esquema);
                $reg = $a_reg[0];
                $dl = substr($a_reg[1], 0, -1); // quito la v o la f.
                if ($reg === $dl) {
                    continue;
                }
            }
            //elimino public, publicv, global
            if ($esquema === 'global') {
                continue;
            }
            if ($esquema === 'public') {
                continue;
            }
            if ($esquema === 'publicv') {
                continue;
            }
            if ($esquema === 'restov') {
                $tabla_personas = 'p_de_paso_ex';
            }
            $esquema_slash = '"' . $esquema . '"';
            $oDBR->exec("SET search_path TO public,$esquema_slash");
            $qRs = $oDBR->query("SELECT '$esquema' as schemaName,id_schema,situacion,f_situacion FROM $tabla_personas WHERE id_nom=$id_orbix");
            if ($qRs === false) {
                continue;
            }
            $Result = $qRs->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($Result) && is_array($Result[0])) {
                if (count($Result) === 1) {
                    $aResult[] = $Result[0];
                } else {
                    exit(_("no puede existir una persona con el mismo id!!"));
                }
            }
        }
        //restaurarConexion($oDBR);
        $oDBR->exec("SET search_path TO $path_org");

        return $aResult;
    }

    public function copiarPersona(): bool
    {
        $error = '';
        $oDBorg = $this->getOrgConnectionForCopyPersona();
        $PersonaDlRepository = $this->repositoryWithConnection(PersonaDlRepositoryInterface::class, $oDBorg);
        $oPersonaDl = $PersonaDlRepository->findById($this->iid_nom);
        if ($oPersonaDl === null) {
            $error = $this->appendDebugContext(
                sprintf(_("No se ha encontrado la persona con id: %s"), $this->iid_nom),
                $oDBorg,
                null,
                $PersonaDlRepository
            );
            $this->serror = $error;
            return false;
        }
        $idTabla = $oPersonaDl->getIdTablaVo()->value();
        $repositoryId = $this->getPersonaRepositoryByTable($idTabla);
        if ($repositoryId === null) {
            $this->serror = sprintf(_("No se reconoce el tipo de persona: %s"), $idTabla);
            return false;
        }
        $personaOrgRepository = $this->createPersonaTypeRepository($repositoryId, $oDBorg);
        if ($personaOrgRepository === null) {
            $this->serror = sprintf(_("No se reconoce el tipo de persona: %s"), $idTabla);
            return false;
        }
        $oPersona = $personaOrgRepository->findById($this->iid_nom);
        if ($oPersona === null) {
            $this->serror = $this->appendDebugContext(
                sprintf(_("No se ha encontrado la persona en origen con id: %s"), $this->iid_nom),
                $oDBorg,
                $idTabla,
                $personaOrgRepository
            );
            return false;
        }

        // Trasladar persona
        $this->snew_esquema = $this->sreg_dl_dst;
        $oDBdst = $this->getDstConnectionForCopyPersona();

        // Copiar los datos a la dl destino si existe en orbix.
        $schemaExists = $this->schemaExistsInDatabase($oDBorg, $this->snew_esquema);
        if ($schemaExists === null) {
            return false;
        }
        // si existe el esquema (dl)
        if ($schemaExists === false) {
            $error = sprintf(_("no existe el esquema destino %s en la base de datos"), $this->snew_esquema);
        }
        if ($schemaExists === true) {
            $personaDstRepository = $this->createPersonaTypeRepository($repositoryId, $oDBdst);
            if ($personaDstRepository === null) {
                $this->serror = sprintf(_("No se reconoce el tipo de persona: %s"), $idTabla);
                return false;
            }
            $oPersonaNew = clone $oPersona;
            $oPersonaNew->setDlVo($this->sdl_dst);
            $oPersonaNew->setSituacionVo(SituacionCode::fromNullableString('A'));

            $fSituacion = $this->getF_traslado();
            $oPersonaNew->setF_situacion($fSituacion);

            if ($this->guardarPersonaTraslado($personaDstRepository, $oPersonaNew) === false) {
                $error .= '<br>' . _("hay un error, no se ha guardado");
            }
        }
        //$this->restaurarConexionOrg($oDBorg);
        //$this->restaurarConexionDst($oDBdst);
        if (!empty($error)) {
            $this->serror = $error;
            return false;
        }
        return true;
    }

    protected function getOrgConnectionForCopyPersona(): PDO
    {
        return $this->getConexionOrg();
    }

    protected function getDstConnectionForCopyPersona(): PDO
    {
        return $this->getConexionDst();
    }

    protected function schemaExistsInDatabase(PDO $oDBorg, string $schema): ?bool
    {
        $stmt = $oDBorg->prepare("SELECT EXISTS(SELECT 1 FROM pg_namespace WHERE nspname = :schema) AS existe");
        if ($stmt === false || $stmt->execute(['schema' => $schema]) === false) {
            if (isset($_SESSION['oGestorErrores']) && is_object($_SESSION['oGestorErrores']) && method_exists($_SESSION['oGestorErrores'], 'addErrorAppLastError')) {
                $sClauError = 'Controller.Traslados';
                $_SESSION['oGestorErrores']->addErrorAppLastError($stmt, $sClauError, __LINE__, __FILE__);
            }
            return null;
        }
        $aDades = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDades)) {
            return null;
        }
        $rawExists = $aDades['existe'] ?? false;

        return in_array($rawExists, [true, 1, '1', 't', 'true'], true);
    }

    /**
     * @return PersonaNRepositoryInterface|PersonaAgdRepositoryInterface|PersonaNaxRepositoryInterface|PersonaSRepositoryInterface|PersonaSSSCRepositoryInterface|null
     */
    private function createPersonaTypeRepository(string $repositoryId, PDO $oDbl): PersonaNRepositoryInterface|PersonaAgdRepositoryInterface|PersonaNaxRepositoryInterface|PersonaSRepositoryInterface|PersonaSSSCRepositoryInterface|null
    {
        return match ($repositoryId) {
            PersonaNRepositoryInterface::class => $this->repositoryWithConnection(PersonaNRepositoryInterface::class, $oDbl),
            PersonaAgdRepositoryInterface::class => $this->repositoryWithConnection(PersonaAgdRepositoryInterface::class, $oDbl),
            PersonaNaxRepositoryInterface::class => $this->repositoryWithConnection(PersonaNaxRepositoryInterface::class, $oDbl),
            PersonaSRepositoryInterface::class => $this->repositoryWithConnection(PersonaSRepositoryInterface::class, $oDbl),
            PersonaSSSCRepositoryInterface::class => $this->repositoryWithConnection(PersonaSSSCRepositoryInterface::class, $oDbl),
            default => null,
        };
    }

    /**
     * @param PersonaNRepositoryInterface|PersonaAgdRepositoryInterface|PersonaNaxRepositoryInterface|PersonaSRepositoryInterface|PersonaSSSCRepositoryInterface $repository
     */
    private function guardarPersonaTraslado(
        PersonaNRepositoryInterface|PersonaAgdRepositoryInterface|PersonaNaxRepositoryInterface|PersonaSRepositoryInterface|PersonaSSSCRepositoryInterface $repository,
        PersonaN|PersonaAgd|PersonaNax|PersonaS|PersonaSSSC $persona,
    ): bool {
        if ($repository instanceof PersonaNRepositoryInterface && $persona instanceof PersonaN) {
            return $repository->Guardar($persona);
        }
        if ($repository instanceof PersonaAgdRepositoryInterface && $persona instanceof PersonaAgd) {
            return $repository->Guardar($persona);
        }
        if ($repository instanceof PersonaNaxRepositoryInterface && $persona instanceof PersonaNax) {
            return $repository->Guardar($persona);
        }
        if ($repository instanceof PersonaSRepositoryInterface && $persona instanceof PersonaS) {
            return $repository->Guardar($persona);
        }
        if ($repository instanceof PersonaSSSCRepositoryInterface && $persona instanceof PersonaSSSC) {
            return $repository->Guardar($persona);
        }

        return false;
    }

    /**
     * @return class-string|null
     */
    private function getPersonaRepositoryByTable(string $idTabla): ?string
    {
        return match ($idTabla) {
            'n' => PersonaNRepositoryInterface::class,
            'a' => PersonaAgdRepositoryInterface::class,
            's' => PersonaSRepositoryInterface::class,
            'sssc' => PersonaSSSCRepositoryInterface::class,
            'x', 'nax' => PersonaNaxRepositoryInterface::class,
            default => null,
        };
    }

    private function getPersonaTableByType(string $idTabla): ?string
    {
        return match ($idTabla) {
            'n' => 'p_numerarios',
            'a' => 'p_agregados',
            's' => 'p_supernumerarios',
            'sssc' => 'p_sssc',
            'x', 'nax' => 'p_nax',
            default => null,
        };
    }

    private function appendDebugContext(string $error, PDO $oDB, ?string $idTabla = null, ?object $repository = null): string
    {
        if (!$this->isTrasladoDebugEnabled()) {
            return $error;
        }

        $debug = $this->buildDebugContext($oDB, $idTabla, $repository);
        if (empty($debug)) {
            return $error;
        }

        return $error . "\n" . $debug;
    }

    private function isTrasladoDebugEnabled(): bool
    {
        return ConfigGlobal::is_debug_mode() || getenv('ORBIX_DEBUG_TRASLADO') === '1';
    }

    private function buildDebugContext(PDO $oDB, ?string $idTabla = null, ?object $repository = null): string
    {
        $parts = [];
        $parts[] = sprintf('[debug traslado] schema_solicitado=%s', $this->snew_esquema ?? '');
        if (!empty($this->sresolved_esquema) || !empty($this->sdatabase)) {
            $parts[] = sprintf('database=%s schema_resuelto=%s', $this->sdatabase, $this->sresolved_esquema);
        }
        if (!empty($this->sconfig_user)) {
            $parts[] = sprintf('config_user=%s', $this->sconfig_user);
        }
        if ($repository !== null) {
            $parts[] = sprintf('repo=%s', get_class($repository));
        }

        try {
            $qRs = $oDB->query("SELECT current_database() AS db, current_user AS usr, current_schema() AS sch, current_setting('search_path') AS sp");
            if ($qRs !== false) {
                $row = $qRs->fetch(PDO::FETCH_ASSOC);
                if (is_array($row)) {
                    $parts[] = sprintf(
                        'db=%s user=%s current_schema=%s search_path=%s',
                        $this->mixedToString($row['db'] ?? ''),
                        $this->mixedToString($row['usr'] ?? ''),
                        $this->mixedToString($row['sch'] ?? ''),
                        $this->mixedToString($row['sp'] ?? '')
                    );
                }
            }
        } catch (\Throwable) {
            // noop: seguimos con el resto del contexto
        }

        $repoDb = null;
        if ($repository !== null && method_exists($repository, 'getoDbl')) {
            try {
                /** @var PDO $repoDb */
                $repoDb = $repository->getoDbl();
                $sameConnection = spl_object_id($repoDb) === spl_object_id($oDB) ? 'SI' : 'NO';
                $parts[] = sprintf('repo_misma_conexion=%s', $sameConnection);

                $repoRow = null;
                $repoQuery = $repoDb->query("SELECT current_database() AS db, current_user AS usr, current_schema() AS sch, current_setting('search_path') AS sp");
                if ($repoQuery !== false) {
                    $repoRow = $repoQuery->fetch(PDO::FETCH_ASSOC);
                }
                if (is_array($repoRow)) {
                    $parts[] = sprintf(
                        'repo_db=%s repo_user=%s repo_current_schema=%s repo_search_path=%s',
                        $this->mixedToString($repoRow['db'] ?? ''),
                        $this->mixedToString($repoRow['usr'] ?? ''),
                        $this->mixedToString($repoRow['sch'] ?? ''),
                        $this->mixedToString($repoRow['sp'] ?? '')
                    );
                }
            } catch (\Throwable) {
                // noop
            }
        }

        try {
            $stmt = $oDB->prepare('SELECT id_tabla, dl, situacion FROM personas_dl WHERE id_nom = :id_nom');
            if ($stmt !== false) {
                $stmt->execute(['id_nom' => $this->iid_nom]);
                $personaDl = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($personaDl === false) {
                    $parts[] = 'personas_dl=NO';
                } elseif (is_array($personaDl)) {
                    $parts[] = sprintf(
                        'personas_dl=SI(id_tabla=%s,dl=%s,situacion=%s)',
                        $this->mixedToString($personaDl['id_tabla'] ?? ''),
                        $this->mixedToString($personaDl['dl'] ?? ''),
                        $this->mixedToString($personaDl['situacion'] ?? '')
                    );
                }
            }
        } catch (\Throwable) {
            // noop
        }

        if (!empty($idTabla)) {
            $tabla = $this->getPersonaTableByType($idTabla);
            if (!empty($tabla)) {
                try {
                    $stmt = $oDB->prepare("SELECT 1 FROM $tabla WHERE id_nom = :id_nom");
                    if ($stmt !== false) {
                        $stmt->execute(['id_nom' => $this->iid_nom]);
                        $exists = $stmt->fetchColumn() !== false ? 'SI' : 'NO';
                        $parts[] = sprintf('%s=%s', $tabla, $exists);
                    }
                } catch (\Throwable) {
                    // noop
                }

                if ($repoDb instanceof PDO) {
                    try {
                        $stmtRepo = $repoDb->prepare("SELECT 1 FROM $tabla WHERE id_nom = :id_nom");
                        if ($stmtRepo !== false) {
                            $stmtRepo->execute(['id_nom' => $this->iid_nom]);
                            $existsRepo = $stmtRepo->fetchColumn() !== false ? 'SI' : 'NO';
                            $parts[] = sprintf('repo_%s=%s', $tabla, $existsRepo);
                        }
                    } catch (\Throwable) {
                        // noop
                    }
                }
            } else {
                $parts[] = sprintf('tabla_por_tipo=NO_MAPEADA(%s)', $idTabla);
            }
        }

        return implode(' | ', $parts);
    }

    public function copiarNotas(): bool
    {
        // Las Notas si o si (Aunque no se tenga el dossier abierto)
        // No cal fer res. Les notes són visibles per tothom.
        // -->CAMBIADO: Las notas pertenecen a la dl destino, si se
        // borraran de la tabla porque no existe la persona, también
        // se perderían para todos...
        $error = '';
        $oDBorg = $this->getConexionOrg();
        $oDBdst = $this->getConexionDst();

        $PersonaNotaDBRepository = $this->repositoryWithConnection(PersonaNotaDlRepositoryInterface::class, $oDBorg);
        $collection = $PersonaNotaDBRepository->getPersonaNotas(array('id_nom' => $this->iid_nom));
        if (!empty($collection)) {
            $new_dl = DelegacionUtils::getDlFromSchema($this->snew_esquema);
            // Obtener datos de región STGR de la nueva dl mediante el repositorio
            $gesDelegacion = $this->delegacionRepository;
            $a_mi_region_stgr = $gesDelegacion->mi_region_stgr($new_dl);
            $esquema_region_stgr = $a_mi_region_stgr['esquema_region_stgr'] ?? '';
            $esquemaRegionStgrStr = $this->mixedToString($esquema_region_stgr);
            if (($qRs = $oDBdst->query("SELECT id FROM public.db_idschema WHERE schema = " . $oDBdst->quote($this->snew_esquema))) === false) {
                $sClauError = 'Controller.Traslados';
                if (isset($_SESSION['oGestorErrores']) && is_object($_SESSION['oGestorErrores']) && method_exists($_SESSION['oGestorErrores'], 'addErrorAppLastError')) {
                    $_SESSION['oGestorErrores']->addErrorAppLastError($qRs, $sClauError, __LINE__, __FILE__);
                }
                return false;
            }
            $aSchema = $qRs->fetch(PDO::FETCH_ASSOC);
            if (!is_array($aSchema) || !isset($aSchema['id']) || !is_numeric($aSchema['id'])) {
                return false;
            }
            $id_schema_persona = (int)$aSchema['id'];
            $PersonaNotaDstRepository = $this->repositoryWithConnection(PersonaNotaDlRepositoryInterface::class, $oDBdst);
            $a_region_stgr_org = $gesDelegacion->mi_region_stgr($this->sdl_persona);
            $mismaRegionStgr = ($a_region_stgr_org['esquema_region_stgr'] ?? '') === ($a_mi_region_stgr['esquema_region_stgr'] ?? '');
            // Para saber el nuevo id_schema de la dl destino:
            foreach ($collection as $oPersonaNotaDB) {
                /*
                $oPersonaNota = new PersonaNota();
                $oPersonaNota->setId_nom($oPersonaNotaDB->getId_nom());
                $oPersonaNota->setId_nivel($oPersonaNotaDB->getIdNivelVo()->value());
                $oPersonaNota->setIdAsignaturaVo($oPersonaNotaDB->getIdAsignaturaVo()->value());
                $oPersonaNota->setIdSituacionVo($oPersonaNotaDB->getIdSituacionVo()->value());
                $oPersonaNota->setActaVo($oPersonaNotaDB->getActaVo()->value());
                $oPersonaNota->setF_acta($oPersonaNotaDB->getF_acta());
                $oPersonaNota->setTipoActaVo($oPersonaNotaDB->getTipoActaVo()->value());
                $oPersonaNota->setPreceptor($oPersonaNotaDB->isPreceptor());
                $oPersonaNota->setId_preceptor($oPersonaNotaDB->getId_preceptor());
                $oPersonaNota->setDetalleVo($oPersonaNotaDB->getDetalleVo()->value());
                $oPersonaNota->setEpocaVo($oPersonaNotaDB->getEpocaVo()->value());
                $oPersonaNota->setIdActivVo($oPersonaNotaDB->getIdActivVo()->value());
                $oPersonaNota->setNotaNumVo($oPersonaNotaDB->getNotaNumVo()->value());
                $oPersonaNota->setNotaMaxVo($oPersonaNotaDB->getNotaMaxVo()->value());
                */

                if ($mismaRegionStgr) {
                    $oEditarPersonaNota = new EditarPersonaNota(
                        $oPersonaNotaDB,
                        $this->repositoryWithConnection(PersonaNotaRepositoryInterface::class, $oDBdst),
                        $this->delegacionRepository,
                        $this->repositoryWithConnection(DbSchemaRepositoryInterface::class, $oDBdst),
                        $this->repositoryWithConnection(DossierRepositoryInterface::class, $oDBdst),
                        $PersonaNotaDstRepository,
                    );
                    $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr($new_dl);
                } else {
                    $oEditarPersonaNota = new EditarPersonaNota(
                        $oPersonaNotaDB,
                        $this->repositoryWithConnection(PersonaNotaRepositoryInterface::class, $oDBorg),
                        $this->delegacionRepository,
                        $this->repositoryWithConnection(DbSchemaRepositoryInterface::class, $oDBorg),
                        $this->repositoryWithConnection(DossierRepositoryInterface::class, $oDBorg),
                        $PersonaNotaDBRepository,
                    );
                    $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr($this->sdl_persona);
                }
                $a_ObjetosPersonaNota = $oEditarPersonaNota->getReposPersonaNota($datosRegionStgr, $id_schema_persona);
                $aNotasCreadas = $oEditarPersonaNota->crear_nueva_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota, $esquemaRegionStgrStr);

                //borrar la origen solo si se copió correctamente:
                if (!empty($aNotasCreadas['nota_real']) || !empty($aNotasCreadas['nota_certificado'])) {
                    $PersonaNotaDBRepository->Eliminar($oPersonaNotaDB);
                }
            }

        }
        return true;
    }

    public function copiarAsistencias(): bool
    {
        $error = '';
        // Está en la DB externa.
        $oDBorgE = $this->getConexionOrg(TRUE);
        $oDBdstE = $this->getConexionDst(TRUE);

        // Los Out pasan a Dl si la dl destino es la que organiza.
        $AsistenteOutOrgRepository = $this->repositoryWithConnection(AsistenteOutRepositoryInterface::class, $oDBorgE);
        $AsistenteDlDstRepository = $this->repositoryWithConnection(AsistenteDlRepositoryInterface::class, $oDBdstE);
        $collection = $AsistenteOutOrgRepository->getAsistentes(array('id_nom' => $this->iid_nom));
        $ActividadAllRepository = $this->actividadAllRepository;
        foreach ($collection as $oAsistenteOut) {
            $err = 0;
            $id_activ = $oAsistenteOut->getId_activ();
            $oActividad = $ActividadAllRepository->findById($id_activ);
            if ($oActividad === null) {
                continue; // no existe la actividad, no se puede copiar. Se pierde la asistencia, total no se sabe donde
            }
            // si es de la sf quito la 'f'
            $dl_org = preg_replace('/f$/', '', $oActividad->getDl_org() ?? '');
            if ($dl_org === $this->sdl_dst) {
                if ($AsistenteDlDstRepository->Guardar($oAsistenteOut, false) === false) {
                    $error .= '<br>' . sprintf(_("No se ha guardado la asistencia(dl) a id_activ: %s"), $id_activ);
                    $err = 1;
                }
            } else {
                if ($AsistenteDlDstRepository->Guardar($oAsistenteOut, false) === false) {
                    $error .= '<br>' . sprintf(_("No se ha guardado la asistencia(out) a id_activ: %s"), $id_activ);
                    $err = 1;
                }
            }
            // borrar el original
            if ($err === 0) {
                $AsistenteOutOrgRepository->Eliminar($oAsistenteOut, false);
            }
        }
        // Los Dl pasan a Out
        $AsistenteDlOrgRepository = $this->repositoryWithConnection(AsistenteDlRepositoryInterface::class, $oDBorgE);
        $collection = $AsistenteDlOrgRepository->getAsistentes(['id_nom' => $this->iid_nom]);
        $AsistenteOutDstRepository = $this->repositoryWithConnection(AsistenteOutRepositoryInterface::class, $oDBdstE);
        foreach ($collection as $oAsistenteDl) {
            $err = 0;
            // cambio para que la dl responsable sea la actual:
            $oAsistenteDl->setDlResponsableVo(ConfigGlobal::mi_delef());
            $id_activ = $oAsistenteDl->getId_activ();
            if ($this->testActividad($id_activ) && $AsistenteOutDstRepository->Guardar($oAsistenteDl, false) === false) {
                $error .= '<br>' . sprintf(_("No se ha guardado la asistencia(out) a id_activ: %s"), $id_activ);
                $err = 1;
            }
            // borrar el origen
            if ($err === 0) {
                $AsistenteDlOrgRepository->Eliminar($oAsistenteDl, false);
            }
        }
        // Los Ex no deberían existir, son gente de otras dl, no afecta al traslado

        //$this->restaurarConexionOrg($oDBorgE);
        //$this->restaurarConexionDst($oDBdstE);
        if (!empty($error)) {
            $this->serror = $error;
            return false;
        }
        return true;
    }

    public function trasladarDossiers(): bool
    {
        $error = '';
        $oDBorg = $this->getConexionOrg();
        $oDBdst = $this->getConexionDst();
        $DossierRepository = $this->repositoryWithConnection(DossierRepositoryInterface::class, $oDBorg);
        $DossierRepositoryDst = $this->repositoryWithConnection(DossierRepositoryInterface::class, $oDBdst);
        // Comprobar que están apuntados.
        $cDossiers = $DossierRepository->getDossieres(['tabla' => 'p', 'id_pau' => $this->iid_nom]);

        $TipoDossierRepository = $this->tipoDossierRepository;
        foreach ($cDossiers as $oDossier) {
            $id_tipo_dossier = $oDossier->getId_tipo_dossier();
            $oTipoDossier = $TipoDossierRepository->findById($id_tipo_dossier);
            if ($oTipoDossier === null) {
                continue;
            }
            $classVo = $oTipoDossier->getClassVo();
            if ($classVo === null) {
                continue;
            }
            $class = $classVo->value();
            if (empty($class)) {
                continue;
            }
            $collection = [];
            $repo = null;
            $repo_dst = null;
            switch ($class) {
                case 'TelecoPersonaDl':
                    $repo = $this->repositoryWithConnection(TelecoPersonaDlRepositoryInterface::class, $oDBorg);
                    $repo_dst = $this->repositoryWithConnection(TelecoPersonaDlRepositoryInterface::class, $oDBdst);
                    $collection = $repo->getTelecosPersona(['id_nom' => $this->iid_nom]);
                    break;
                case 'Profesor':
                    $repo = $this->repositoryWithConnection(ProfesorStgrRepositoryInterface::class, $oDBorg);
                    $repo_dst = $this->repositoryWithConnection(ProfesorStgrRepositoryInterface::class, $oDBdst);
                    $collection = $repo->getProfesoresStgr(['id_nom' => $this->iid_nom]);
                    break;
                case 'ProfesorAmpliacion':
                    $repo = $this->repositoryWithConnection(ProfesorAmpliacionRepositoryInterface::class, $oDBorg);
                    $repo_dst = $this->repositoryWithConnection(ProfesorAmpliacionRepositoryInterface::class, $oDBdst);
                    $collection = $repo->getProfesorAmpliaciones(array('id_nom' => $this->iid_nom));
                    break;
                case 'ProfesorCongreso':
                    $repo = $this->repositoryWithConnection(ProfesorCongresoRepositoryInterface::class, $oDBorg);
                    $repo_dst = $this->repositoryWithConnection(ProfesorCongresoRepositoryInterface::class, $oDBdst);
                    $collection = $repo->getProfesorCongresos(array('id_nom' => $this->iid_nom));
                    break;
                case 'ProfesorDirector':
                    $repo = $this->repositoryWithConnection(ProfesorDirectorRepositoryInterface::class, $oDBorg);
                    $repo_dst = $this->repositoryWithConnection(ProfesorDirectorRepositoryInterface::class, $oDBdst);
                    $collection = $repo->getProfesoresDirectores(array('id_nom' => $this->iid_nom));
                    break;
                case 'ProfesorDocenciaStgr':
                    $repo = $this->repositoryWithConnection(ProfesorDocenciaStgrRepositoryInterface::class, $oDBorg);
                    $repo_dst = $this->repositoryWithConnection(ProfesorDocenciaStgrRepositoryInterface::class, $oDBdst);
                    $collection = $repo->getProfesorDocenciasStgr(array('id_nom' => $this->iid_nom));
                    break;
                case 'ProfesorJuramento':
                    $repo = $this->repositoryWithConnection(ProfesorJuramentoRepositoryInterface::class, $oDBorg);
                    $repo_dst = $this->repositoryWithConnection(ProfesorJuramentoRepositoryInterface::class, $oDBdst);
                    $collection = $repo->getProfesorJuramentos(array('id_nom' => $this->iid_nom));
                    break;
                case 'ProfesorLatin':
                    $repo = $this->repositoryWithConnection(ProfesorLatinRepositoryInterface::class, $oDBorg);
                    $repo_dst = $this->repositoryWithConnection(ProfesorLatinRepositoryInterface::class, $oDBdst);
                    $collection = $repo->getProfesoresLatin(array('id_nom' => $this->iid_nom));
                    break;
                case 'ProfesorPublicacion':
                    $repo = $this->repositoryWithConnection(ProfesorPublicacionRepositoryInterface::class, $oDBorg);
                    $repo_dst = $this->repositoryWithConnection(ProfesorPublicacionRepositoryInterface::class, $oDBdst);
                    $collection = $repo->getProfesorPublicaciones(array('id_nom' => $this->iid_nom));
                    break;
                case 'ProfesorTituloEst':
                    $repo = $this->repositoryWithConnection(ProfesorTituloEstRepositoryInterface::class, $oDBorg);
                    $repo_dst = $this->repositoryWithConnection(ProfesorTituloEstRepositoryInterface::class, $oDBdst);
                    $collection = $repo->getProfesorTitulosEst(array('id_nom' => $this->iid_nom));
                    break;
                case 'PersonaNotaDl':
                    // Lo hago a parte.
                    break;
                case 'MatriculaDl':
                    $repo = $this->repositoryWithConnection(MatriculaDlRepositoryInterface::class, $oDBorg);
                    $repo_dst = $this->repositoryWithConnection(MatriculaDlRepositoryInterface::class, $oDBdst);
                    $collection = $repo->getMatriculas(array('id_nom' => $this->iid_nom));
                    break;
                case 'Traslado':
                    $repo = $this->repositoryWithConnection(TrasladoRepositoryInterface::class, $oDBorg);
                    $repo_dst = $this->repositoryWithConnection(TrasladoRepositoryInterface::class, $oDBdst);
                    $collection = $repo->getTraslados(array('id_nom' => $this->iid_nom));
                    break;
                case 'AsistenteDl':
                    // Lo hago a parte porque está en la base de datos exterior.
                    break;
                case 'AsistenteCargo':
                    // No hace falta, porque se guardan en la dl que organiza la actividad.
                    // Está en la base de datos exterior.
                    break;
            }
            if ($repo !== null && $repo_dst !== null && !empty($collection)) {
                $error = $this->copiarColeccionDossier($collection, $repo, $repo_dst, $class, $error);
            }
            // también copia el estado del dossier
            $DossierRepositoryDst->Guardar($oDossier);
        }
        // Volver oDBdst a su estado original:
        //$this->restaurarConexionDst($oDBdst);
        //$this->restaurarConexionOrg($oDBorg);
        if (!empty($error)) {
            $this->serror = $error;
            return false;
        }
        return true;
    }

    public function trasladarDossierCertificados(): bool
    {
        $error = '';
        $oDBorg = $this->getConexionOrg();
        // si es una dl, hay que buscarlos en la region del stgr

        $certificadoRecibidoRepository = $this->repositoryWithConnection(CertificadoRecibidoRepositoryInterface::class, $oDBorg);
        $cCertificados = $certificadoRecibidoRepository->getCertificados(['id_nom' => $this->iid_nom]);
        foreach ($cCertificados as $Certificado) {
            if (!$this->trasladar_certificados($Certificado)) {
                $error .= '<br>' . $this->serror = $error;
            }
        }

        if (!empty($error)) {
            $this->serror = $error;
            return false;
        }
        return true;
    }

    public function trasladar_certificados(CertificadoRecibido $CertificadoRecibido): bool
    {
        $error = '';
        $oDBorg = $this->getConexionOrg();
        $oDBdst = $this->getConexionDst();

        $id_item = $CertificadoRecibido->getId_item();
        // para que ponga el suyo según la DB

        $certificadoRecibidoRepository = $this->repositoryWithConnection(CertificadoRecibidoRepositoryInterface::class, $oDBdst);
        $newId_item = $certificadoRecibidoRepository->getNewId_item();
        $CertificadoRecibido->setId_item(is_numeric($newId_item) ? (int) $newId_item : 0);
        if ($certificadoRecibidoRepository->Guardar($CertificadoRecibido) === FALSE) {
            $error .= $certificadoRecibidoRepository->getErrorTxt();
        }

        // eliminar el original
        $certificadoRecibidoRepository2 = $this->repositoryWithConnection(CertificadoRecibidoRepositoryInterface::class, $oDBorg);
        $oCertificadoRecibido = $certificadoRecibidoRepository2->findById($id_item);
        if (!empty($oCertificadoRecibido)) {
            $certificado = $oCertificadoRecibido->getCertificado();
            if ($certificadoRecibidoRepository2->Eliminar($oCertificadoRecibido) === FALSE) {
                $error .= _("Algo falló");
            }
        }

        if (!empty($error)) {
            $this->serror = $error;
            return false;
        }

        return true;
    }

    public function copiar_certificados_a_dl(CertificadoEmitido $Certificado): bool
    {
        $error = '';
        $oDBdst = $this->getConexionDst();

        $id_item = $Certificado->getId_item();
        // para que ponga el suyo según la DB
        $CertificadoRecibido = $this->copyCertificado2Dl($Certificado);

        $certificadoRecibidoRepository = $this->repositoryWithConnection(CertificadoRecibidoRepositoryInterface::class, $oDBdst);
        $newId_item = $certificadoRecibidoRepository->getNewId_item();
        $CertificadoRecibido->setId_item(is_numeric($newId_item) ? (int) $newId_item : 0);
        if ($certificadoRecibidoRepository->Guardar($CertificadoRecibido) === FALSE) {
            $error .= $certificadoRecibidoRepository->getErrorTxt();
        }
        // pongo fecha enviado
        $certificadoEmitidoRepository = $this->certificadoEmitidoRepository;
        $Certificado->setF_enviado(new DateTimeLocal());
        if ($certificadoEmitidoRepository->Guardar($Certificado) === FALSE) {
            $error .= $certificadoEmitidoRepository->getErrorTxt();
        }

        if (!empty($error)) {
            $this->serror = $error;
            return false;
        }

        return true;
    }

    public function apuntar(): bool
    {
        $error = '';
        // apunto el traslado.
        $oDBorg = $this->getConexionOrg();
        $TrasladoRepository = $this->repositoryWithConnection(TrasladoRepositoryInterface::class, $oDBorg);
        $newId_item = $TrasladoRepository->getNewId();
        $oTraslado = new Traslado();
        $oTraslado->setId_item($newId_item);
        $oTraslado->setId_nom($this->iid_nom);
        $oTraslado->setF_traslado($this->df_traslado);
        $oTraslado->setTipoCmbVo('dl');
        $oTraslado->setId_ctr_origen(null);
        $oTraslado->setCtrOrigenVo($this->sdl_org);
        $oTraslado->setId_ctr_destino(null);
        $oTraslado->setCtrDestinoVo($this->sdl_dst);
        if ($TrasladoRepository->Guardar($oTraslado) === false) {
            $error .= '<br>' . _("hay un error, no se ha guardado");
        }
        //$this->restaurarConexionOrg($oDBorg);
        if (!empty($error)) {
            $this->serror = $error;
            return false;
        }

        return true;
    }

    /**
     * @template T of object
     * @param class-string<T> $repositoryId
     * @return T
     */
    /**
     * @template T of object
     * @param class-string<T> $repositoryId
     * @return T
     */
    protected function repositoryWithConnection(string $repositoryId, PDO $oDbl, ?PDO $oDblSelect = null): object
    {
        $factory = $this->connectionRepositoryFactory;

        /** @var T $repo */
        $repo = $factory->createWithConnection($repositoryId, $oDbl, $oDblSelect);

        return $repo;
    }

    /**
     * @param iterable<mixed> $collection
     */
    private function copiarColeccionDossier(iterable $collection, object $repo, object $repo_dst, string $class, string $error): string
    {
        foreach ($collection as $Objeto) {
            if (!is_object($Objeto)) {
                continue;
            }
            if (method_exists($Objeto, 'setId_item') && method_exists($repo_dst, 'getNewId')) {
                $newIdMethod = new \ReflectionMethod($repo_dst, 'getNewId');
                $newId = $newIdMethod->invoke($repo_dst);
                if (is_int($newId)) {
                    $Objeto->setId_item($newId);
                }
            }
            if (method_exists($repo_dst, 'Guardar')) {
                $guardarMethod = new \ReflectionMethod($repo_dst, 'Guardar');
                $saved = $guardarMethod->invoke($repo_dst, $Objeto);
                if ($saved === false) {
                    $error .= '<br>' . sprintf(_("No se ha guardado el dossier: %s"), $class);
                    continue;
                }
            }
            if ($class !== 'Traslado' && method_exists($repo, 'Eliminar')) {
                $eliminarMethod = new \ReflectionMethod($repo, 'Eliminar');
                $eliminarMethod->invoke($repo, $Objeto);
            }
        }

        return $error;
    }

    private function mixedToString(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }
        if (is_int($value) || is_float($value) || is_bool($value)) {
            return (string)$value;
        }

        return '';
    }

    private function testActividad(int $id_activ): bool
    {
        $ActividadAllRepository = $this->actividadAllRepository;
        $cActividades = $ActividadAllRepository->getActividades(['id_activ' => $id_activ]);

        return (!empty($cActividades) && count($cActividades) === 1);
    }

    private function copyCertificado2Dl(CertificadoEmitido $Certificado): CertificadoRecibido
    {
        $oCertificadoRecibido = new CertificadoRecibido();
        $oCertificadoRecibido->setId_nom($Certificado->getId_nom());
        $oCertificadoRecibido->setNom($Certificado->getNom());
        $oCertificadoRecibido->setIdiomaVo($Certificado->getIdiomaVo());
        $oCertificadoRecibido->setDestino($Certificado->getDestino());
        $oCertificadoRecibido->setCertificado($Certificado->getCertificado());
        $oCertificadoRecibido->setF_certificado($Certificado->getF_certificado());
        $oCertificadoRecibido->setEsquema_emisor($Certificado->getEsquema_emisor());
        $oCertificadoRecibido->setFirmado($Certificado->isFirmado());
        $oCertificadoRecibido->setDocumento($Certificado->getDocumento());
        $oCertificadoRecibido->setF_recibido(new DateTimeLocal());

        return $oCertificadoRecibido;
    }

}

