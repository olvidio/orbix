<?php

namespace personas\model\entity;

use actividades\model\entity\ActividadAll;
use actividades\model\entity\GestorActividadAll;
use actividadestudios\model\entity\GestorMatriculaDl;
use asignaturas\model\entity\Asignatura;
use asistentes\model\entity\AsistenteDl;
use asistentes\model\entity\AsistenteOut;
use asistentes\model\entity\GestorAsistenteDl;
use asistentes\model\entity\GestorAsistenteOut;
use core\ConfigDB;
use core\ConfigGlobal;
use core\ConverterDate;
use core\DBConnection;
use core\DBPropiedades;
use dossiers\model\entity\GestorDossier;
use dossiers\model\entity\TipoDossier;
use notas\model\EditarPersonaNota;
use notas\model\PersonaNota;
use ubis\model\entity\GestorDelegacion;
use web;


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
     * @return web\DateTimeLocal df_dl
     */
    function getF_dl()
    {
        if (!isset($this->df_dl) && !$this->bLoaded) {
            $this->DBCarregar();
        }
        if (empty($this->df_dl)) {
            return new web\NullDateTimeLocal();
        }
        $oConverter = new ConverterDate('date', $this->df_dl);
        return $oConverter->fromPg();
    }

    /**
     * Establece el valor del atributo df_dl de TrasladoDl
     * Si df_dl es string, y convert=true se convierte usando el formato webDateTimeLocal->getFormat().
     * Si convert es false, df_dl debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
     *
     * @param date|string df_dl='' optional.
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
            return _("comprobar:") . " " . $msg;
        }

        // Aviso si le faltan notas
        if ($this->comprobarNotas() === false) {
            $msg = $this->serror;
        }

        // Cambio la situación de la persona. Debo hacerlo lo primero, pues no puedo
        // tener la misma persona en dos dl en la misma situación
        if ($this->cambiarFichaPersona() === false) {
            $msg = $this->serror;
            return _("OJO: Debería cambiar el campo situación. No se ha hecho ningún cambio.") . $msg;
        }

        // Trasladar persona
        if ($this->copiarPersona() === false) {
            $msg = $this->serror;
            return _("copiar persona.") . $msg;
        }

        if ($this->copiarNotas() === false) {
            $msg = $this->serror;
            return _("copiar notas") . $msg;
        }
        // apunto el traslado. Lo pongo antes para que se copie trasladar dossiers.
        if ($this->apuntar() === false) {
            $msg = $this->serror;
            return _("apuntar traslado") . $msg;
        }
        if ($this->trasladarDossiers() === false) {
            $msg = $this->serror;
            return _("copiar dossiers") . $msg;
        }

        if ($this->copiarAsistencias() === false) {
            $msg = $this->serror;
            return _("copiar asistencias") . $msg;
        }
        return true;
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
        $gesDelegacion = new GestorDelegacion();
        $a_dl = $gesDelegacion->getArrayDelegaciones();
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


        $gesMatriculas = new GestorMatriculaDl();
        $gesMatriculas->setoDbl($oDBorg);
        $cMatriculasPendientes = $gesMatriculas->getMatriculasPendientes($this->iid_nom);
        $msg = '';
        foreach ($cMatriculasPendientes as $oMatricula) {
            $id_activ = $oMatricula->getId_activ();
            $id_asignatura = $oMatricula->getId_asignatura();
            $oActividad = new ActividadAll($id_activ);
            $nom_activ = $oActividad->getNom_activ();
            $oAsignatura = new Asignatura($id_asignatura);
            $nombre_corto = $oAsignatura->getNombre_corto();
            $msg .= empty($msg) ? '' : '<br>';
            $msg .= sprintf(_("ca: %s, asignatura: %s"), $nom_activ, $nombre_corto);
        }
        //$this->restaurarConexionOrg($oDBorg);
        if (!empty($msg)) {
            $error = _("tiene pendiente de poner las notas de:") . '<br>' . $msg;
        }
        if (empty($error)) {
            return true;
        } else {
            $this->serror = $error;
            return false;
        }
    }

    public function cambiarFichaPersona()
    {
        // Cambio la situación de la persona. Debo hacerlo lo primero, pues no puedo tener la misma persona en dos dl en la misma situación
//		if ($this->ssituacion == 'A') exit (_("OJO: Debería cambiar el campo situación. No se ha hecho ningún cambio."));

        $error = '';
        $oDBorg = $this->conexionOrg();
        // dar permisos al usuario orbixv para acceder a personas_dl (?) o buscar tipo de perona
        $oPersonaDl = new PersonaDl();
        $oPersonaDl->setoDbl($oDBorg);
        $oPersonaDl->setId_nom($this->iid_nom);
        $oPersonaDl->DBCarregar();
        $oPersonaDl->setSituacion($this->ssituacion);
        $oPersonaDl->setF_situacion($this->df_dl, FALSE);
        $oPersonaDl->setDl($this->sdl_dst);
        if ($oPersonaDl->DBGuardar() === false) {
            $error .= '<br>' . _("hay un error, no se ha guardado");
        }
        //$this->restaurarConexionOrg($oDBorg);
        if (empty($error)) {
            return true;
        } else {
            $this->serror = $error;
            return false;
        }
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
            if ($esquema == 'global') {
                continue;
            }
            if ($esquema == 'public') {
                continue;
            }
            if ($esquema == 'publicv') {
                continue;
            }
            if ($esquema == 'restov') {
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
        $oDBorg = $this->conexionOrg();
        $oPersonaDl = new PersonaDl();
        $oPersonaDl->setoDbl($oDBorg);
        $oPersonaDl->setId_nom($this->iid_nom);
        $oPersonaDl->DBCarregar();
        // Trasladar persona
        $oDBdst = $this->conexionDst();

        // Copiar los datos a la dl destino si existe en orbix.
        if (($qRs = $oDBorg->query("SELECT EXISTS(SELECT 1 FROM pg_namespace WHERE nspname = '$this->snew_esquema') AS existe")) === false) {
            $sClauError = 'Controller.Traslados';
            $_SESSION['oGestorErrores']->addErrorAppLastError($qRs, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aDades = $qRs->fetch(\PDO::FETCH_ASSOC);
        // si existe el esquema (dl)
        if (empty($aDades['existe'])) {
            $error = sprintf(_("no existe el esquema destino %s en la base de datos"), $this->snew_esquema);
        }
        if (!empty($aDades['existe']) && $aDades['existe'] === true) {
            $id_tabla = $oPersonaDl->getId_tabla();
            switch ($id_tabla) {
                case 'n':
                    $obj = 'personas\model\entity\PersonaN';
                    break;
                case 'a':
                    $obj = 'personas\model\entity\PersonaAgd';
                    break;
                case 's':
                    $obj = 'personas\model\entity\PersonaS';
                    break;
                case 'sssc':
                    $obj = 'personas\model\entity\PersonaSSSC';
                    break;
                case 'x':
                    $obj = 'personas\model\entity\PersonaNax';
                    break;
            }
            $oPersona = new $obj();
            $oPersona->setoDbl($oDBorg);
            $oPersona->setId_nom($this->iid_nom);
            $oPersona->DBCarregar();
            $oPersonaNew = clone $oPersona;
            $oPersonaNew->setoDbl($oDBdst);
            $oPersonaNew->setDl($this->sdl_dst);
            $oPersonaNew->setSituacion('A');
            $oPersonaNew->setF_situacion($this->df_dl, FALSE);
            //$oPersonaNew->setId_ctr(''); // Por si también se traslada el ctr (Torreciudad de dlz a dlb)
            if ($oPersonaNew->DBGuardar() === false) {
                $error .= '<br>' . _("hay un error, no se ha guardado");
            }
        }
        //$this->restaurarConexionOrg($oDBorg);
        //$this->restaurarConexionDst($oDBdst);
        if (empty($error)) {
            return true;
        } else {
            $this->serror = $error;
            return false;
        }
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

        $gestor = "notas\\model\\entity\\GestorPersonaNotaDlDB";
        $ges = new $gestor();
        $ges->setoDbl($oDBorg);
        $colection = $ges->getPersonaNotas(array('id_nom' => $this->iid_nom));
        if (!empty($colection)) {
            // Para saber el nuevo id_schema de la dl destino:
            if (($qRs = $oDBorg->query("SELECT id FROM public.db_idschema WHERE schema = '$this->snew_esquema'")) === false) {
                $sClauError = 'Controller.Traslados';
                $_SESSION['oGestorErrores']->addErrorAppLastError($qRs, $sClauError, __LINE__, __FILE__);
                return false;
            }
            $aSchema = $qRs->fetch(\PDO::FETCH_ASSOC);
            $id_schema_persona =  $aSchema['id'];
            foreach ($colection as $oPersonaNotaDB) {
                $oPersonaNota = new PersonaNota();
                $oPersonaNota->setIdNom($oPersonaNotaDB->getId_nom());
                $oPersonaNota->setIdNivel($oPersonaNotaDB->getId_nivel());
                $oPersonaNota->setIdAsignatura($oPersonaNotaDB->getId_asignatura());
                $oPersonaNota->setIdSituacion($oPersonaNotaDB->getId_situacion());
                $oPersonaNota->setActa($oPersonaNotaDB->getActa());
                $oPersonaNota->setFActa($oPersonaNotaDB->getF_acta());
                $oPersonaNota->setTipoActa($oPersonaNotaDB->getTipo_acta());
                $oPersonaNota->setPreceptor($oPersonaNotaDB->getPreceptor());
                $oPersonaNota->setIdPreceptor($oPersonaNotaDB->getId_preceptor());
                $oPersonaNota->setDetalle($oPersonaNotaDB->getDetalle());
                $oPersonaNota->setEpoca($oPersonaNotaDB->getEpoca());
                $oPersonaNota->setIdActiv($oPersonaNotaDB->getId_activ());
                $oPersonaNota->setNotaNum($oPersonaNotaDB->getNota_num());
                $oPersonaNota->setNotaMax($oPersonaNotaDB->getNota_max());

                $oEditarPersonaNota = new EditarPersonaNota($oPersonaNota);
                $datosRegionStgr = $oEditarPersonaNota->getDatosRegionStgr();
                $a_ObjetosPersonaNota = $oEditarPersonaNota->getObjetosPersonaNota($datosRegionStgr, $id_schema_persona);
                $oEditarPersonaNota->crear_nueva_personaNota_para_cada_objeto_del_array($a_ObjetosPersonaNota);

                //borrar la origen:
                $oPersonaNotaDB->DBEliminar();
            }

        }
        if (empty($error)) {
            return true;
        } else {
            $this->serror = $error;
            return false;
        }
    }

    public function copiarAsistencias()
    {
        $error = '';
        // Está en la DB externa.
        $oDBorgE = $this->conexionOrg(TRUE);
        $oDBdstE = $this->conexionDst(TRUE);
        // Los Out pasan a Dl si la dl destino es la que organiza.
        $ges = new GestorAsistenteOut();
        $colection = $ges->getAsistentesOut(array('id_nom' => $this->iid_nom));
        foreach ($colection as $oAsistenteOut) {
            $err = 0;
            $oAsistenteOut->setoDbl($oDBorgE);
            $oAsistenteOut->DBCarregar();
            $id_activ = $oAsistenteOut->getId_activ();
            $oActividad = new ActividadAll($id_activ);
            // si es de la sf quito la 'f'
            $dl_org = preg_replace('/f$/', '', $oActividad->getDl_org());
            if ($dl_org == $this->sdl_dst) {
                $oAsistenteDl = new AsistenteDl();
                $oAsistenteDl->setoDbl($oDBdstE);
                $oAsistenteDl = $this->copiarAsistencia($oAsistenteOut, $oAsistenteDl);
                if ($oAsistenteDl->DBGuardar(1) === false) { // param quiet=1 para que no anote cambios
                    $error .= '<br>' . sprintf(_("No se ha guardado la asistencia(dl) a id_activ: %s"), $id_activ);
                    $err = 1;
                }
            } else {
                $NuevoObj = clone $oAsistenteOut;
                $NuevoObj->setoDbl($oDBdstE);
                if (method_exists($NuevoObj, 'setId_item') === true) $NuevoObj->setId_item(null);
                $NuevoObj->setTraslado('t');
                if ($NuevoObj->DBGuardar(1) === false) { // param quiet=1 para que no anote cambios
                    $error .= '<br>' . sprintf(_("No se ha guardado la asistencia(out) a id_activ: %s"), $id_activ);
                    $err = 1;
                }
            }
            // borrar el original
            if ($err == 0) {
                $oAsistenteOut->DBEliminar();
            }
        }
        // Los Dl pasan a Out
        $ges = new GestorAsistenteDl();
        $colection = $ges->getAsistentesDl(array('id_nom' => $this->iid_nom));
        foreach ($colection as $oAsistenteDl) {
            $err = 0;
            $oAsistenteDl->setoDbl($oDBorgE);
            $oAsistenteDl->DBCarregar();
            $oAsistenteOut1 = new AsistenteOut();
            $oAsistenteOut1->setoDbl($oDBdstE);
            $oAsistenteOut = $this->copiarAsistencia($oAsistenteDl, $oAsistenteOut1);
            if ($oAsistenteOut === NULL) {
                $error .= '<br>' . sprintf(_("No se ha guardado la asistencia(out) a id_activ: %s"), $id_activ);
                $err = 1;
            } else {
                $oAsistenteOut->setTraslado('t');
                if ($oAsistenteOut->DBGuardar() === false) {
                    $error .= '<br>' . sprintf(_("No se ha guardado la asistencia(out) a id_activ: %s"), $id_activ);
                    $err = 1;
                }
            }
            // borrar el origen
            if ($err == 0) {
                $oAsistenteDl->DBEliminar();
            }
        }
        // Los Ex no deberían existir, son gente de otras dl, no afecta al traslado

        //$this->restaurarConexionOrg($oDBorgE);
        //$this->restaurarConexionDst($oDBdstE);
        if (empty($error)) {
            return true;
        } else {
            $this->serror = $error;
            return false;
        }
    }

    public function trasladarDossiers()
    {
        $error = '';
        $oDBorg = $this->conexionOrg();
        $oDBdst = $this->conexionDst();
        $GesDossiers = new GestorDossier();
        $GesDossiers->setoDbl($oDBorg);
        // Comprobar que estan apuntados.
        $cDossiers = $GesDossiers->DossiersNotEmpty('p', $this->iid_nom);

        //$cDossiers = $GesDossiers->getDossiers(array('tabla'=>'p','id_nom'=>$this->iid_nom));
        foreach ($cDossiers as $oDossier) {
            $id_tipo_dossier = $oDossier->getId_tipo_dossier();
            $oTipoDossier = new TipoDossier($id_tipo_dossier);
            $app = $oTipoDossier->getApp();
            $class = $oTipoDossier->getClass();
            if (empty($class)) {
                continue;
            }
            $colection = array();
            switch ($class) {
                case 'TelecoPersonaDl':
                    $gestor = "$app\\model\\entity\\GestorTelecoPersonaDl";
                    $ges = new $gestor();
                    $ges->setoDbl($oDBorg);
                    $colection = $ges->getTelecos(array('id_nom' => $this->iid_nom));
                    break;
                case 'Profesor':
                    $gestor = "$app\\model\\entity\\Gestor$class";
                    $ges = new $gestor();
                    $ges->setoDbl($oDBorg);
                    $colection = $ges->getProfesores(array('id_nom' => $this->iid_nom));
                    break;
                case 'ProfesorAmpliacion':
                    $gestor = "$app\\model\\entity\\Gestor$class";
                    $ges = new $gestor();
                    $ges->setoDbl($oDBorg);
                    $colection = $ges->getProfesorAmpliaciones(array('id_nom' => $this->iid_nom));
                    break;
                case 'ProfesorCongreso':
                    $gestor = "$app\\model\\entity\\Gestor$class";
                    $ges = new $gestor();
                    $ges->setoDbl($oDBorg);
                    $colection = $ges->getProfesorCongresos(array('id_nom' => $this->iid_nom));
                    break;
                case 'ProfesorDirector':
                    $gestor = "$app\\model\\entity\\Gestor$class";
                    $ges = new $gestor();
                    $ges->setoDbl($oDBorg);
                    $colection = $ges->getProfesoresDirectores(array('id_nom' => $this->iid_nom));
                    break;
                case 'ProfesorDocenciaStgr':
                    $gestor = "$app\\model\\entity\\Gestor$class";
                    $ges = new $gestor();
                    $ges->setoDbl($oDBorg);
                    $colection = $ges->getProfesorDocenciasStgr(array('id_nom' => $this->iid_nom));
                    break;
                case 'ProfesorJuramento':
                    $gestor = "$app\\model\\entity\\Gestor$class";
                    $ges = new $gestor();
                    $ges->setoDbl($oDBorg);
                    $colection = $ges->getProfesorJuramentos(array('id_nom' => $this->iid_nom));
                    break;
                case 'ProfesorLatin':
                    $gestor = "$app\\model\\entity\\Gestor$class";
                    $ges = new $gestor();
                    $ges->setoDbl($oDBorg);
                    $colection = $ges->getProfesoresLatin(array('id_nom' => $this->iid_nom));
                    break;
                case 'ProfesorPublicacion':
                    $gestor = "$app\\model\\entity\\Gestor$class";
                    $ges = new $gestor();
                    $ges->setoDbl($oDBorg);
                    $colection = $ges->getProfesorPublicaciones(array('id_nom' => $this->iid_nom));
                    break;
                case 'ProfesorTituloEst':
                    $gestor = "$app\\model\\entity\\Gestor$class";
                    $ges = new $gestor();
                    $ges->setoDbl($oDBorg);
                    $colection = $ges->getTitulosEst(array('id_nom' => $this->iid_nom));
                    break;
                case 'PersonaNotaDl':
                    // Lo hago a parte.
                    break;
                case 'MatriculaDl':
                    $gestor = "$app\\model\\entity\\Gestor$class";
                    $ges = new $gestor();
                    $ges->setoDbl($oDBorg);
                    $colection = $ges->getMatriculas(array('id_nom' => $this->iid_nom));
                    break;
                case 'Traslado':
                    $gestor = "$app\\model\\entity\\Gestor$class";
                    $ges = new $gestor();
                    $ges->setoDbl($oDBorg);
                    $colection = $ges->getTraslados(array('id_nom' => $this->iid_nom));
                    break;
                case 'AsistenteDl':
                    // Lo hago a parte porque está en la base de datos exterior.
                    break;
                case 'AsistenteCargo':
                    // No hace falta, porque se guardan en la dl que organiza la actividad.
                    // Está en la base de datos exterior.
                    break;
            }
            if (!empty($colection)) {
                foreach ($colection as $Objeto) {
                    $Objeto->setoDbl($oDBorg);
                    $Objeto->DBCarregar();
                    $NuevoObj = clone $Objeto;
                    $NuevoObj->setoDbl($oDBdst);
                    if (method_exists($NuevoObj, 'setId_item') === true) {
                        $NuevoObj->setId_item(null);
                    }
                    if ($NuevoObj->DBGuardar() === false) {
                        $error .= '<br>' . sprintf(_("No se ha guardado el dossier: %s"), $class);
                    } else { // Borrar excepto traslado
                        if ($class != 'Traslado') {
                            $Objeto->DBEliminar();
                        }
                    }
                }
            }
            // también copia el estado del dossier
            $NuevoObj = clone $oDossier;
            $NuevoObj->setoDbl($oDBdst);
            $NuevoObj->DBGuardar();
        }
        // Volver oDBdst a su estado original:
        //$this->restaurarConexionDst($oDBdst);
        //$this->restaurarConexionOrg($oDBorg);
        if (empty($error)) {
            return true;
        } else {
            $this->serror = $error;
            return false;
        }
    }

    public function apuntar()
    {
        $error = '';
        // apunto el traslado.
        $oDBorg = $this->conexionOrg();
        $oTraslado = new Traslado();
        $oTraslado->setoDbl($oDBorg);
        $oTraslado->setId_nom($this->iid_nom);
        $oTraslado->setF_traslado($this->df_dl, FALSE);
        $oTraslado->setTipo_cmb('dl');
        $oTraslado->setId_ctr_origen('');
        $oTraslado->setCtr_origen($this->sdl_org);
        $oTraslado->setId_ctr_destino('');
        $oTraslado->setCtr_destino($this->sdl_dst);
        if ($oTraslado->DBGuardar() === false) {
            $error .= '<br>' . _("hay un error, no se ha guardado");
        }
        //$this->restaurarConexionOrg($oDBorg);
        if (empty($error)) {
            return true;
        } else {
            $this->serror = $error;
            return false;
        }
    }

    private function copiarAsistencia($oOrigen, $oDestino)
    {
        // Hay que comprobar que la actividad existe,
        // TODO: y que esta accesible. Sino, ver si hay que importarla.
        if ($this->testActividad($oOrigen->getId_activ())) {
            $oDestino->setId_activ($oOrigen->getId_activ());
            $oDestino->setId_nom($oOrigen->getId_nom());
            $oDestino->setPropio($oOrigen->getPropio());
            $oDestino->setEst_ok($oOrigen->getEst_ok());
            $oDestino->setCfi($oOrigen->getCfi());
            $oDestino->setCfi_con($oOrigen->getCfi_con());
            $oDestino->setFalta($oOrigen->getFalta());
            $oDestino->setEncargo($oOrigen->getEncargo());
            // cambio para que la dl responsable sea la actual:
            $oDestino->setDl_responsable(ConfigGlobal::mi_delef());
            $oDestino->setObserv($oOrigen->getObserv());
            return $oDestino;
        }
        return null;
    }

    private function testActividad($id_activ)
    {
        $gesActividades = new GestorActividadAll();
        $cActividades = $gesActividades->getActividades(['id_activ' => $id_activ]);
        if (!empty($cActividades) && count($cActividades) == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * para poder ver donde esta conectado al hacer pruebas.
     *
     * @param \PDO $conn
     */
    private function verConexion($conn)
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

}
