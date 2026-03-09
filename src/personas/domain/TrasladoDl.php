<?php

namespace src\personas\domain;

use core\ConfigDB;
use core\ConfigGlobal;
use core\ConverterDate;
use core\DBConnection;
use core\DBPropiedades;
use notas\model\EditarPersonaNota;
use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\actividadestudios\domain\contracts\MatriculaDlRepositoryInterface;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteOutRepositoryInterface;
use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;
use src\certificados\domain\contracts\CertificadoRecibidoRepositoryInterface;
use src\certificados\domain\entity\CertificadoRecibido;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use src\notas\domain\contracts\PersonaNotaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaAgdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryFactoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\contracts\PersonaNaxRepositoryInterface;
use src\personas\domain\contracts\PersonaNRepositoryInterface;
use src\personas\domain\contracts\PersonaSRepositoryInterface;
use src\personas\domain\contracts\PersonaSSSCRepositoryInterface;
use src\personas\domain\contracts\TelecoPersonaDlRepositoryInterface;
use src\personas\domain\contracts\TrasladoRepositoryInterface;
use src\personas\domain\entity\Traslado;
use src\profesores\domain\contracts\ProfesorAmpliacionRepositoryInterface;
use src\profesores\domain\contracts\ProfesorCongresoRepositoryInterface;
use src\profesores\domain\contracts\ProfesorDirectorRepositoryInterface;
use src\profesores\domain\contracts\ProfesorDocenciaStgrRepositoryInterface;
use src\profesores\domain\contracts\ProfesorJuramentoRepositoryInterface;
use src\profesores\domain\contracts\ProfesorLatinRepositoryInterface;
use src\profesores\domain\contracts\ProfesorPublicacionRepositoryInterface;
use src\profesores\domain\contracts\ProfesorStgrRepositoryInterface;
use src\profesores\domain\contracts\ProfesorTituloEstRepositoryInterface;
use src\shared\domain\contracts\ConnectionObjectBinderInterface;
use src\shared\domain\contracts\ConnectionRepositoryFactoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\domain\value_objects\NullDateTimeLocal;
use src\ubis\application\services\DelegacionUtils;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;


/**
 * Fitxer amb la Classe que accedeix a la taula d_traslados
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 12/05/2014
 */

/**
 * Clase que implementa la entidad d_traslados
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 12/05/2014
 */
class TrasladoDl
{

    private $serror;

    private $iid_nom;
    private $sdl_persona;
    private $sdl_org;
    private $sreg_dl_org;
    private $sdl_dst;
    private $sreg_dl_dst;
    private $ssituacion;
    private $df_dl;

    /* para guardar el search path de la conexión a la base de datos */
    private $path_ini_org;
    private $path_ini_dst;
    private $snew_esquema;
    private bool $bLoaded = FALSE;

    public function getError()
    {
        return $this->serror;
    }


    /**
     * Recupera el atributo iid_nom de Traslado
     *
     * @return integer iid_nom
     */
    function getId_nom()
    {
        return $this->iid_nom;
    }

    /**
     * Establece el valor del atributo iid_nom de Traslado
     *
     * @param integer iid_nom
     */
    function setId_nom($iid_nom)
    {
        $this->iid_nom = $iid_nom;
    }

    /**
     * Recupera el atributo sdl_persona de Traslado
     *
     * @return string sdl_persona
     */
    function getDl_persona()
    {
        return $this->sdl_persona;
    }

    /**
     * Establece el valor del atributo sdl_persona de Traslado
     *
     * @param string sdl_persona
     */
    function setDl_persona($sdl_persona)
    {
        $this->sdl_persona = $sdl_persona;
    }

    /**
     * Recupera el atributo sdl_org de Traslado
     *
     * @return string sdl_org
     */
    function getDl_org()
    {
        return $this->sdl_org;
    }

    /**
     * Establece el valor del atributo sdl_org de Traslado
     *
     * @param string sdl_org
     */
    function setDl_org($sdl_org)
    {
        $this->sdl_org = $sdl_org;
    }

    /**
     * Recupera el atributo sreg_dl_org de Traslado
     *
     * @return string sreg_dl_org
     */
    function getReg_dl_org()
    {
        return $this->sreg_dl_org;
    }

    /**
     * Establece el valor del atributo sreg_dl_org de Traslado
     *
     * @param string sreg_dl_org
     */
    function setReg_dl_org($sreg_dl_org)
    {
        $this->sreg_dl_org = $sreg_dl_org;

        $a_reg = explode('-', $sreg_dl_org);
        //$this->sdl_org = $a_reg[1];
        $this->sdl_org = substr($a_reg[1], 0, -1); // quito la v o la f.
    }

    /**
     * Recupera el atributo sreg_dl_dst de Traslado
     *
     * @return string sreg_dl_dst
     */
    function getReg_dl_dst()
    {
        return $this->sreg_dl_dst;
    }

    /**
     * Establece el valor del atributo sreg_dl_dst de Traslado
     *
     * @param string sreg_dl_dst
     */
    function setReg_dl_dst($sreg_dl_dst)
    {
        $this->sreg_dl_dst = $sreg_dl_dst;

        $a_reg = explode('-', $sreg_dl_dst);
        //$this->sdl_dst = $a_reg[1];
        $this->sdl_dst = substr($a_reg[1], 0, -1); // quito la v o la f.
    }

    /**
     * Recupera el atributo ssituacion de Traslado
     *
     * @return string ssituacion
     */
    function getSituacion()
    {
        return $this->ssituacion;
    }

    /**
     * Establece el valor del atributo ssituacion de Traslado
     *
     * @param string ssituacion
     */
    function setSituacion($ssituacion)
    {
        $this->ssituacion = $ssituacion;
    }

    /**
     * Recupera el atributo df_traslado de TrasladoDl
     *
     * @returnDateTimeLocal df_dl
     */
    function getF_dl()
    {
        if (empty($this->df_dl)) {
            return new NullDateTimeLocal();
        }
        $oConverter = new ConverterDate('date', $this->df_dl);
        return $oConverter->fromPg();
    }

    /**
     * Establece el valor del atributo df_dl de TrasladoDl
     * Si df_dl es string, y convert=true se convierte usando el formato webDateTimeLocal->getFormat().
     * Si convert es false, df_dl debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
     *
     * @param DateTimeLocal|string df_dl='' optional.
     * @param boolean convert=true optional. Si es false, df_dl debe ser un string en formato ISO (Y-m-d).
     */
    function setF_dl($df_dl = '', $convert = true)
    {
        if ($convert === true && !empty($df_dl)) {
            $oConverter = new ConverterDate('date', $df_dl);
            $this->df_dl = $oConverter->toPg();
        } else {
            $this->df_dl = $df_dl;
        }
    }

    private function setConexion($esquema, $exterior = FALSE)
    {

        if (ConfigGlobal::mi_sfsv() == 2) {
            $database = 'sf';
            if ($exterior) {
                $database = 'sf-e';
            }
            if (ConfigGlobal::mi_region_dl() != $esquema) {
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

            if (!in_array($esquema, $aEsquemas)) {
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

    private function conexionOrg($exterior = FALSE)
    {

        $this->snew_esquema = $this->sreg_dl_org;

        return $this->setConexion($this->snew_esquema, $exterior);

        /*
        $this->snew_esquema = $this->sreg_dl_org;
        if (ConfigGlobal::mi_region_dl() == $this->snew_esquema) {
            //Utilizo la conexión oDB para cambiar momentáneamente el search_path.
            if ($exterior) {
                $oDB = $GLOBALS['oDBE'];
            } else {
                $oDB = $GLOBALS['oDB'];
            }
        } else {
            // Sólo funciona con la conexión oDBR porque el usuario es orbixv que
            // tiene permiso de lectura para todos los esquemas
            if ($exterior) {
                $oDB = $GLOBALS['oDBER'];
            } else {
                $oDB = $GLOBALS['oDBR'];
            }
        }
        $qRs = $oDB->query('SHOW search_path');
        $aPath = $qRs->fetch(\PDO::FETCH_ASSOC);
        $this->path_ini_org = $aPath['search_path'];
        $oDB->exec('SET search_path TO public,"'.$this->snew_esquema.'"');
        */
    }

    private function restaurarConexionOrg($oDB)
    {
        // Volver oDB a su estado original:
        $oDB->exec("SET search_path TO $this->path_ini_org");
        //$GLOBALS['oDBR'] = $oDBR;
    }

    private function conexionDst($exterior = FALSE)
    {
        $this->snew_esquema = $this->sreg_dl_dst;
        return $this->setConexion($this->snew_esquema, $exterior);
        /*
        //Utilizo la conexión oDBR para cambiar momentáneamente el search_path.
        if (ConfigGlobal::mi_region_dl() == $this->snew_esquema) {
            //Utilizo la conexión oDB para cambiar momentáneamente el search_path.
            if ($exterior) {
                $oDB = $GLOBALS['oDBE'];
            } else {
                $oDB = $GLOBALS['oDB'];
            }
        } else {
            // Sólo funciona con la conexión oDBR porque el usuario es orbixv que
            // tiene permiso de lectura para todos los esquemas
            if ($exterior) {
                $oDB = $GLOBALS['oDBER'];
            } else {
                $oDB = $GLOBALS['oDBR'];
            }
        }
        $qRs = $oDB->query('SHOW search_path');
        $aPath = $qRs->fetch(\PDO::FETCH_ASSOC);
        //$this->path_ini_dst = addslashes($aPath['search_path']);
        $this->path_ini_dst = $aPath['search_path'];
        $oDB->exec('SET search_path TO public,"'.$this->snew_esquema.'"');
        return $oDB;
        */
    }

    private function restaurarConexionDst($oDB)
    {
        // Volver oDBR a su estado original:
        $oDB->exec("SET search_path TO $this->path_ini_dst");
    }

    /* -----------------------------------------------------------------------*/
    public function trasladar()
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

    private function comprobar()
    {
        $error = '';
        $rta = 0;
        if (!empty($this->sdl_dst) && ($this->sdl_dst === $this->sdl_persona)) {
            $error = _("ya está trasladado. No se ha hecho ningún cambio.");
            $rta = 1;
        }
        // Que la dl destino exista:
        $repoDelegacion = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        $cDelegAll = $repoDelegacion->getDelegaciones(['active' => true]);
        $a_dl = [];
        if (is_array($cDelegAll)) {
            foreach ($cDelegAll as $oDl) {
                $a_dl[] = $oDl->getDlVo()->value();
            }
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

    public function comprobarNotas()
    {
        // Aviso si le faltan notas
        $error = '';
        $oDBorg = $this->conexionOrg();

        $MatriculaDlRepository = $this->repositoryWithConnection(MatriculaDlRepositoryInterface::class, $oDBorg);
        $cMatriculasPendientes = $MatriculaDlRepository->getMatriculasPendientes($this->iid_nom);
        $msg = '';
        $AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
        $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        foreach ($cMatriculasPendientes as $oMatricula) {
            $id_activ = $oMatricula->getId_activ();
            $id_asignatura = $oMatricula->getIdAsignaturaVo()->value();
            $oActividad = $ActividadAllRepository->findById($id_activ);
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

    public function cambiarFichaPersona()
    {
        // Cambio la situación de la persona. Debo hacerlo lo primero, pues no puedo tener la misma persona en dos dl en la misma situación
//		if ($this->ssituacion == 'A') exit (_("OJO: Debería cambiar el campo situación. No se ha hecho ningún cambio."));

        $error = '';
        $oDBorg = $this->conexionOrg();
        // dar permisos al usuario orbixv para acceder a personas_dl (?) o buscar tipo de persona
        $PersonaDlRepositoryFactory = $GLOBALS['container']->get(PersonaDlRepositoryFactoryInterface::class);
        $PersonaDlRepossitory = $PersonaDlRepositoryFactory->createWithConnection($oDBorg);
        $oPersonaDl = $PersonaDlRepossitory->findById($this->iid_nom);
        $oPersonaDl->setSituacionVo($this->ssituacion);
        $oPersonaDl->setF_situacion($this->df_dl, FALSE);
        $oPersonaDl->setDlVo($this->sdl_dst);
        if ($PersonaDlRepossitory->Guardar($oPersonaDl) === false) {
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
     * dado un id_nom, lo busca en todos los esquemas y si lo encuentra
     * devuelve un array con la informacion del esquema
     *
     * @param integer id_mnom
     * @return array(schemaName, id_schema, situacion, f_situacion)
     */
    public function getEsquemas($id_orbix, $tipo_persona)
    {
        // posibles esquemas
        /*
         * @todo: filtrar por regiones?
         */
        $oDBR = $GLOBALS['oDBR'];
        $qRs = $oDBR->query("SELECT DISTINCT schemaname FROM pg_stat_user_tables");
        $aResultSql = $qRs->fetchAll(\PDO::FETCH_ASSOC);
        $aEsquemas = $aResultSql;
        //Utilizo la conexión oDBR para cambiar momentáneamente el search_path.
        $oDBR = $GLOBALS['oDBR'];
        $qRs = $oDBR->query('SHOW search_path');
        $aPath = $qRs->fetch(\PDO::FETCH_ASSOC);
        $path_org = addslashes($aPath['search_path']);
        $aResult = [];
        foreach ($aEsquemas as $esquemaName) {
            $esquema = $esquemaName['schemaname'];
            switch ($tipo_persona) {
                case 'n':
                    $tabla_personas = 'p_numerarios';
                    break;
                case 'a':
                    $tabla_personas = 'p_agregados';
                    break;
                case 'nax':
                    $tabla_personas = 'p_nax';
                    break;
                case 's':
                    $tabla_personas = 'p_supernumerarios';
                    break;
            }
            //elimino el de H-H
            if (strpos($esquema, '-')) {
                $a_reg = explode('-', $esquema);
                $reg = $a_reg[0];
                $dl = substr($a_reg[1], 0, -1); // quito la v o la f.
                if ($reg == $dl) {
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
            $Result = $qRs->fetchAll(\PDO::FETCH_ASSOC);
            if (!empty($Result)) {
                if (count($Result) == 1) {
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

    public function copiarPersona()
    {
        $error = '';
        $oDBorg = $this->getOrgConnectionForCopyPersona();
        $PersonaDlRepository = $this->repositoryWithConnection(PersonaDlRepositoryInterface::class, $oDBorg);
        $oPersonaDl = $PersonaDlRepository->findById($this->iid_nom);
        if ($oPersonaDl === null) {
            $error = sprintf(_("No se ha encontrado la persona con id: %s"), $this->iid_nom);
            $this->serror = $error;
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
            $idTabla = $oPersonaDl->getIdTablaVo()->value();
            $repositoryId = $this->getPersonaRepositoryByTable($idTabla);
            if ($repositoryId === null) {
                $error = sprintf(_("No se reconoce el tipo de persona: %s"), $idTabla);
            } else {
                $personaOrgRepository = $this->repositoryWithConnection($repositoryId, $oDBorg);
                $personaDstRepository = $this->repositoryWithConnection($repositoryId, $oDBdst);

                $oPersona = $personaOrgRepository->findById($this->iid_nom);
                if ($oPersona === null) {
                    $error = sprintf(_("No se ha encontrado la persona origen con id: %s"), $this->iid_nom);
                } else {
                    $oPersonaNew = clone $oPersona;
                    $oPersonaNew->setDlVo($this->sdl_dst);
                    $oPersonaNew->setSituacionVo('A');

                    $fSituacion = $this->getF_dl();
                    if ($fSituacion instanceof NullDateTimeLocal) {
                        $fSituacion = null;
                    }
                    $oPersonaNew->setF_situacion($fSituacion);

                    if ($personaDstRepository->Guardar($oPersonaNew) === false) {
                        $error .= '<br>' . _("hay un error, no se ha guardado");
                    }
                }
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

    protected function getOrgConnectionForCopyPersona(): \PDO
    {
        return $this->conexionOrg();
    }

    protected function getDstConnectionForCopyPersona(): \PDO
    {
        return $this->conexionDst();
    }

    protected function schemaExistsInDatabase(\PDO $oDBorg, string $schema): ?bool
    {
        $stmt = $oDBorg->prepare("SELECT EXISTS(SELECT 1 FROM pg_namespace WHERE nspname = :schema) AS existe");
        if ($stmt === false || $stmt->execute(['schema' => $schema]) === false) {
            if (isset($_SESSION['oGestorErrores'])) {
                $sClauError = 'Controller.Traslados';
                $_SESSION['oGestorErrores']->addErrorAppLastError($stmt, $sClauError, __LINE__, __FILE__);
            }
            return null;
        }
        $aDades = $stmt->fetch(\PDO::FETCH_ASSOC);
        $rawExists = $aDades['existe'] ?? false;

        return in_array($rawExists, [true, 1, '1', 't', 'true'], true);
    }

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

    public function copiarNotas()
    {
        // Las Notas si o si (Aunque no se tenga el dossier abierto)
        // No cal fer res. Les notes són visibles per tothom.
        // -->CAMBIADO: Las notas pertenecen a la dl destino, si se
        // borraran de la tabla porque no existe la persona, también
        // se perderían para todos...
        $error = '';
        $oDBorg = $this->conexionOrg();
        $oDBdst = $this->conexionDst();

        $PersonaNotaDBRepository = $this->repositoryWithConnection(PersonaNotaDlRepositoryInterface::class, $oDBorg);
        $collection = $PersonaNotaDBRepository->getPersonaNotas(array('id_nom' => $this->iid_nom));
        if (!empty($collection)) {
            $new_dl = DelegacionUtils::getDlFromSchema($this->snew_esquema);
            // Obtener datos de región STGR de la nueva dl mediante el repositorio
            $gesDelegacion = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
            $a_mi_region_stgr = $gesDelegacion->mi_region_stgr($new_dl);
            $esquema_region_stgr = $a_mi_region_stgr['esquema_region_stgr'];
            // Para saber el nuevo id_schema de la dl destino:
            if (($qRs = $oDBorg->query("SELECT id FROM public.db_idschema WHERE schema = '$this->snew_esquema'")) === false) {
                $sClauError = 'Controller.Traslados';
                $_SESSION['oGestorErrores']->addErrorAppLastError($qRs, $sClauError, __LINE__, __FILE__);
                return false;
            }
            $aSchema = $qRs->fetch(\PDO::FETCH_ASSOC);
            $id_schema_persona = $aSchema['id'];
            foreach ($collection as $oPersonaNotaDB) {
                /*
                $oPersonaNota = new PersonaNota();
                $oPersonaNota->setId_nom($oPersonaNotaDB->getId_nom());
                $oPersonaNota->setId_nivel($oPersonaNotaDB->getId_nivel());
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

                $oEditarPersonaNota = new EditarPersonaNota($oPersonaNotaDB);
                $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr();
                $a_ObjetosPersonaNota = $oEditarPersonaNota->getReposPersonaNota($datosRegionStgr, $id_schema_persona);
                $oEditarPersonaNota->crear_nueva_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota, $esquema_region_stgr);

                //borrar la origen:
                $PersonaNotaDBRepository->Eliminar($oPersonaNotaDB);
            }

        }
        if (!empty($error)) {
            $this->serror = $error;
            return false;
        }
        return true;
    }

    public function copiarAsistencias()
    {
        $error = '';
        // Está en la DB externa.
        $oDBorgE = $this->conexionOrg(TRUE);
        $oDBdstE = $this->conexionDst(TRUE);
        // Los Out pasan a Dl si la dl destino es la que organiza.
        $AsistenteOutOrgRepository = $this->repositoryWithConnection(AsistenteOutRepositoryInterface::class, $oDBorgE);
        $AsistenteDlDstRepository = $this->repositoryWithConnection(AsistenteDlRepositoryInterface::class, $oDBdstE);
        $collection = $AsistenteOutOrgRepository->getAsistentesOut(array('id_nom' => $this->iid_nom));
        $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        foreach ($collection as $oAsistenteOut) {
            $err = 0;
            $id_activ = $oAsistenteOut->getId_activ();
            $oActividad = $ActividadAllRepository->findById($id_activ);
            // si es de la sf quito la 'f'
            $dl_org = preg_replace('/f$/', '', $oActividad->getDl_org());
            if ($dl_org === $this->sdl_dst) {
                if ($AsistenteDlDstRepository->Guardar($oAsistenteOut) === false) { // param quiet=1 para que no anote cambios
                    $error .= '<br>' . sprintf(_("No se ha guardado la asistencia(dl) a id_activ: %s"), $id_activ);
                    $err = 1;
                }
            } else {
                $NuevoObj = clone $oAsistenteOut;
                $NuevoObj = $this->objectWithConnection($NuevoObj, $oDBdstE);
                if (method_exists($NuevoObj, 'setId_item') === true) $NuevoObj->setId_item(null);
                $NuevoObj->setTraslado('t');
                if ($NuevoObj->DBGuardar(1) === false) { // param quiet=1 para que no anote cambios
                    $error .= '<br>' . sprintf(_("No se ha guardado la asistencia(out) a id_activ: %s"), $id_activ);
                    $err = 1;
                }
            }
            // borrar el original
            if ($err === 0) {
                $oAsistenteOut->DBEliminar();
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
            if ($this->testActividad($id_activ) && $AsistenteOutDstRepository->DBGuardar($oAsistenteDl) === false) {
                $error .= '<br>' . sprintf(_("No se ha guardado la asistencia(out) a id_activ: %s"), $id_activ);
                $err = 1;
            }
            // borrar el origen
            if ($err === 0) {
                $AsistenteDlOrgRepository->Eliminar($oAsistenteDl);
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

    public function trasladarDossiers()
    {
        $error = '';
        $oDBorg = $this->conexionOrg();
        $oDBdst = $this->conexionDst();
        $DossierRepository = $this->repositoryWithConnection(DossierRepositoryInterface::class, $oDBorg);
        // Comprobar que están apuntados.
        $cDossiers = $DossierRepository->DossiersNotEmpty('p', $this->iid_nom);

        $TipoDossierRepository = $GLOBALS['container']->get(TipoDossierRepositoryInterface::class);
        foreach ($cDossiers as $oDossier) {
            $id_tipo_dossier = $oDossier->getId_tipo_dossier();
            $oTipoDossier = $TipoDossierRepository->findById($id_tipo_dossier);
            $app = $oTipoDossier->getAppVo()->value();
            $class = $oTipoDossier->getClassVo()->value();
            if (empty($class)) {
                continue;
            }
            $collection = [];
            switch ($class) {
                case 'TelecoPersonaDl':
                    $repo = $this->repositoryWithConnection(TelecoPersonaDlRepositoryInterface::class, $oDBorg);
                    $collection = $repo->getTelecosPersona(['id_nom' => $this->iid_nom]);
                    break;
                case 'Profesor':
                    $repo = $this->repositoryWithConnection(ProfesorStgrRepositoryInterface::class, $oDBorg);
                    $collection = $repo->getProfesoresStgr(['id_nom' => $this->iid_nom]);
                    break;
                case 'ProfesorAmpliacion':
                    $repo = $this->repositoryWithConnection(ProfesorAmpliacionRepositoryInterface::class, $oDBorg);
                    $collection = $repo->getProfesorAmpliaciones(array('id_nom' => $this->iid_nom));
                    break;
                case 'ProfesorCongreso':
                    $repo = $this->repositoryWithConnection(ProfesorCongresoRepositoryInterface::class, $oDBorg);
                    $collection = $repo->getProfesorCongresos(array('id_nom' => $this->iid_nom));
                    break;
                case 'ProfesorDirector':
                    $repo = $this->repositoryWithConnection(ProfesorDirectorRepositoryInterface::class, $oDBorg);
                    $collection = $repo->getProfesoresDirectores(array('id_nom' => $this->iid_nom));
                    break;
                case 'ProfesorDocenciaStgr':
                    $repo = $this->repositoryWithConnection(ProfesorDocenciaStgrRepositoryInterface::class, $oDBorg);
                    $collection = $repo->getProfesorDocenciasStgr(array('id_nom' => $this->iid_nom));
                    break;
                case 'ProfesorJuramento':
                    $repo = $this->repositoryWithConnection(ProfesorJuramentoRepositoryInterface::class, $oDBorg);
                    $collection = $repo->getProfesorJuramentos(array('id_nom' => $this->iid_nom));
                    break;
                case 'ProfesorLatin':
                    $repo = $this->repositoryWithConnection(ProfesorLatinRepositoryInterface::class, $oDBorg);
                    $collection = $repo->getProfesoresLatin(array('id_nom' => $this->iid_nom));
                    break;
                case 'ProfesorPublicacion':
                    $repo = $this->repositoryWithConnection(ProfesorPublicacionRepositoryInterface::class, $oDBorg);
                    $collection = $repo->getProfesorPublicaciones(array('id_nom' => $this->iid_nom));
                    break;
                case 'ProfesorTituloEst':
                    $repo = $this->repositoryWithConnection(ProfesorTituloEstRepositoryInterface::class, $oDBorg);
                    $collection = $repo->getProfesorTitulosEst(array('id_nom' => $this->iid_nom));
                    break;
                case 'PersonaNotaDl':
                    // Lo hago a parte.
                    break;
                case 'MatriculaDl':
                    $repo = $this->repositoryWithConnection(MatriculaDlRepositoryInterface::class, $oDBorg);
                    $collection = $repo->getMatriculas(array('id_nom' => $this->iid_nom));
                    break;
                case 'Traslado':
                    $repo = $this->repositoryWithConnection(TrasladoRepositoryInterface::class, $oDBorg);
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
            if (!empty($collection)) {
                foreach ($collection as $Objeto) {
                    $Objeto = $this->objectWithConnection($Objeto, $oDBorg);
                    $Objeto->DBCarregar();
                    $NuevoObj = clone $Objeto;
                    $NuevoObj = $this->objectWithConnection($NuevoObj, $oDBdst);
                    if (method_exists($NuevoObj, 'setId_item') === true) {
                        $NuevoObj->setId_item(null);
                    }
                    if ($NuevoObj->DBGuardar() === false) {
                        $error .= '<br>' . sprintf(_("No se ha guardado el dossier: %s"), $class);
                    } else { // Borrar excepto traslado
                        if ($class !== 'Traslado') {
                            $Objeto->DBEliminar();
                        }
                    }
                }
            }
            // también copia el estado del dossier
            $NuevoObj = clone $oDossier;
            $NuevoObj = $this->objectWithConnection($NuevoObj, $oDBdst);
            $NuevoObj->DBGuardar();
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

    public function trasladarDossierCertificados()
    {
        $error = '';
        $oDBorg = $this->conexionOrg();
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

    public function trasladar_certificados($CertificadoRecibido)
    {
        $error = '';
        $oDBorg = $this->conexionOrg();
        $oDBdst = $this->conexionDst();

        $id_item = $CertificadoRecibido->getId_item();
        // para que ponga el suyo según la DB

        $certificadoRecibidoRepository = $this->repositoryWithConnection(CertificadoRecibidoRepositoryInterface::class, $oDBdst);
        $newId_item = $certificadoRecibidoRepository->getNewId_item();
        $CertificadoRecibido->setId_item($newId_item);
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

    public function copiar_certificados_a_dl($Certificado)
    {
        $error = '';
        $oDBdst = $this->conexionDst();

        $id_item = $Certificado->getId_item();
        // para que ponga el suyo según la DB
        $CertificadoRecibido = $this->copyCertificado2Dl($Certificado);

        $certificadoRecibidoRepository = $this->repositoryWithConnection(CertificadoRecibidoRepositoryInterface::class, $oDBdst);
        $newId_item = $certificadoRecibidoRepository->getNewId_item();
        $CertificadoRecibido->setId_item($newId_item);
        if ($certificadoRecibidoRepository->Guardar($CertificadoRecibido) === FALSE) {
            $error .= $certificadoRecibidoRepository->getErrorTxt();
        }
        // pongo fecha enviado
        $certificadoEmitidoRepository = $GLOBALS['container']->get(CertificadoEmitidoRepositoryInterface::class);
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

    public function apuntar()
    {
        $error = '';
        // apunto el traslado.
        $oDBorg = $this->conexionOrg();
        $TrasladoRepository = $this->repositoryWithConnection(TrasladoRepositoryInterface::class, $oDBorg);
        $newId_item = $TrasladoRepository->getNewId_item();
        $oTraslado = new Traslado();
        $oTraslado->setId_item($newId_item);
        $oTraslado->setId_nom($this->iid_nom);
        $oTraslado->setF_traslado($this->df_dl, FALSE);
        $oTraslado->setTipoCmbVo('dl');
        $oTraslado->setId_ctr_origen('');
        $oTraslado->setCtrOrigenVo($this->sdl_org);
        $oTraslado->setId_ctr_destino('');
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

    protected function repositoryWithConnection(string $repositoryId, \PDO $oDbl, ?\PDO $oDblSelect = null): object
    {
        $factory = $GLOBALS['container']->get(ConnectionRepositoryFactoryInterface::class);

        return $factory->createWithConnection($repositoryId, $oDbl, $oDblSelect);
    }

    protected function objectWithConnection(object $object, \PDO $oDbl): object
    {
        $binder = $GLOBALS['container']->get(ConnectionObjectBinderInterface::class);

        return $binder->bindConnection($object, $oDbl);
    }

    private function copiarAsistencia($oOrigen, $oDestino)
    {
        // Hay que comprobar que la actividad existe,
        // TODO: y que esta accesible. Sino, ver si hay que importarla.
        if ($this->testActividad($oOrigen->getIdActividadVo()->value())) {
            $oDestino->setIdActividadVo($oOrigen->getIdActividadVo()->value());
            $oDestino->setId_nom($oOrigen->getId_nom());
            $oDestino->setPropio($oOrigen->isPropio());
            $oDestino->setEst_ok($oOrigen->isEst_ok());
            $oDestino->setCfi($oOrigen->isCfi());
            $oDestino->setCfi_con($oOrigen->getCfi_con());
            $oDestino->setFalta($oOrigen->isFalta());
            $oDestino->setEncargoVo($oOrigen->getEncargoVo()->value());
            // cambio para que la dl responsable sea la actual:
            $oDestino->setDlResponsableVo(ConfigGlobal::mi_delef());
            $oDestino->setObservVo($oOrigen->getObservVo()->value());
            return $oDestino;
        }
        return null;
    }

    private function testActividad($id_activ)
    {
        $ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        $cActividades = $ActividadAllRepository->getActividades(['id_activ' => $id_activ]);

        return (!empty($cActividades) && count($cActividades) === 1);
    }

    /**
     * para poder ver donde esta conectado al hacer pruebas.
     *
     * @param \PDO $conn
     */
    private function verConexion(\PDO $conn)
    {
        $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $attributes = array(
            "ERRMODE", "CASE", "CLIENT_VERSION", "CONNECTION_STATUS",
            "ORACLE_NULLS", "SERVER_INFO", "SERVER_VERSION",
        );

        $attr = '';
        foreach ($attributes as $val) {
            echo "PDO::ATTR_$val: ";
            try {
                $attr .= $conn->getAttribute(constant("PDO::ATTR_$val")) . "\n";
            } catch (\PDOException $e) {
                echo $e->getMessage() . "\n";
            }
        }
        echo $attr;
    }

    private function copyCertificado2Dl($Certificado)
    {
        $oCertificadoRecibido = new CertificadoRecibido();
        $oCertificadoRecibido->setId_nom($Certificado->getId_nom());
        $oCertificadoRecibido->setNom($Certificado->getNom());
        $oCertificadoRecibido->setIdioma($Certificado->getIdioma());
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
