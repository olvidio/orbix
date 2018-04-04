<?php
namespace personas\model;

use actividades\model\ActividadAll;
use asignaturas\model\Asignatura;
use asistentes\model\AsistenteDl;
use asistentes\model\AsistenteOut;
use actividadestudios\model\gestorMatriculaDl;
use core;
use dossiers;
use personas;

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
 * Classe que implementa l'entitat d_traslados
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 12/05/2014
 */
class TrasladoDl {

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
	/**
	 * Recupera l'atribut iid_nom de Traslado
	 *
	 * @return integer iid_nom
	 */
	function getId_nom() {
		return $this->iid_nom;
	}
	/**
	 * estableix el valor de l'atribut iid_nom de Traslado
	 *
	 * @param integer iid_nom
	 */
	function setId_nom($iid_nom) {
		$this->iid_nom = $iid_nom;
	}
	/**
	 * Recupera l'atribut sdl_persona de Traslado
	 *
	 * @return string sdl_persona
	 */
	function getDl_persona() {
		return $this->sdl_persona;
	}
	/**
	 * estableix el valor de l'atribut sdl_persona de Traslado
	 *
	 * @param string sdl_persona
	 */
	function setDl_persona($sdl_persona) {
		$this->sdl_persona = $sdl_persona;
	}
	/**
	 * Recupera l'atribut sdl_org de Traslado
	 *
	 * @return string sdl_org
	 */
	function getDl_org() {
		return $this->sdl_org;
	}
	/**
	 * estableix el valor de l'atribut sdl_org de Traslado
	 *
	 * @param string sdl_org
	 */
	function setDl_org($sdl_org) {
		$this->sdl_org = $sdl_org;
	}
	/**
	 * Recupera l'atribut sreg_dl_org de Traslado
	 *
	 * @return string sreg_dl_org
	 */
	function getReg_dl_org() {
		return $this->sreg_dl_org;
	}
	/**
	 * estableix el valor de l'atribut sreg_dl_org de Traslado
	 *
	 * @param string sreg_dl_org
	 */
	function setReg_dl_org($sreg_dl_org) {
		$this->sreg_dl_org = $sreg_dl_org;

		$a_reg = explode('-',$sreg_dl_org);
		$this->sdl_org = substr($a_reg[1],0,-1); // quito la v o la f.
	}
	/**
	 * Recupera l'atribut sreg_dl_dst de Traslado
	 *
	 * @return string sreg_dl_dst
	 */
	function getReg_dl_dst() {
		return $this->sreg_dl_dst;
	}
	/**
	 * estableix el valor de l'atribut sreg_dl_dst de Traslado
	 *
	 * @param string sreg_dl_dst
	 */
	function setReg_dl_dst($sreg_dl_dst) {
		$this->sreg_dl_dst = $sreg_dl_dst;

		$a_reg = explode('-',$sreg_dl_dst);
		$this->sdl_dst = substr($a_reg[1],0,-1); // quito la v o la f.
	}
	/**
	 * Recupera l'atribut ssituacion de Traslado
	 *
	 * @return string ssituacion
	 */
	function getSituacion() {
		return $this->ssituacion;
	}
	/**
	 * estableix el valor de l'atribut ssituacion de Traslado
	 *
	 * @param string ssituacion
	 */
	function setSituacion($ssituacion) {
		$this->ssituacion = $ssituacion;
	}
	/**
	 * Recupera l'atribut df_dl de Traslado
	 *
	 * @return date df_dl
	 */
	function getF_dl() {
		return $this->df_dl;
	}
	/**
	 * estableix el valor de l'atribut df_dl de Traslado
	 *
	 * @param date df_dl
	 */
	function setF_dl($df_dl) {
		$this->df_dl = $df_dl;
	}
	
	private function conexionOrg() {
		$this->snew_esquema = $this->sreg_dl_org;
		if (core\ConfigGlobal::mi_region_dl() == $this->snew_esquema) {
			//Utilizo la conexión oDB para cambiar momentáneamente el search_path.
			$oDB = $GLOBALS['oDB'];
		} else {
			// Sólo funciona con la conexión oDBR porque el usuario es orbixv que 
			// tiene permiso de lectura para todos los esquemas
			$oDB = $GLOBALS['oDBR'];
		}
		$qRs = $oDB->query('SHOW search_path');
		$aPath = $qRs->fetch(\PDO::FETCH_ASSOC);
		$this->path_ini_org = $aPath['search_path'];
		$oDB->exec('SET search_path TO public,"'.$this->snew_esquema.'"');
		return $oDB;
	}
	private function restaurarConexionOrg($oDB) {
		// Volver oDB a su estado original:
		$oDB->exec("SET search_path TO $this->path_ini_org");
		//$GLOBALS['oDBR'] = $oDBR;
	}
	private function conexionDst() {
		$this->snew_esquema = $this->sreg_dl_dst;
		//Utilizo la conexión oDBR para cambiar momentáneamente el search_path.
		if (core\ConfigGlobal::mi_region_dl() == $this->snew_esquema) {
			//Utilizo la conexión oDB para cambiar momentáneamente el search_path.
			$oDB = $GLOBALS['oDB'];
		} else {
			// Sólo funciona con la conexión oDBR porque el usuario es orbixv que 
			// tiene permiso de lectura para todos los esquemas
			$oDB = $GLOBALS['oDBR'];
		}
		$qRs = $oDB->query('SHOW search_path');
		$aPath = $qRs->fetch(\PDO::FETCH_ASSOC);
		//$this->path_ini_dst = addslashes($aPath['search_path']);
		$this->path_ini_dst = $aPath['search_path'];
		$oDB->exec('SET search_path TO public,"'.$this->snew_esquema.'"');
		return $oDB;
	}
	private function restaurarConexionDst($oDB) {
		// Volver oDBR a su estado original:
		$oDB->exec("SET search_path TO $this->path_ini_dst");
	}
	
/* -----------------------------------------------------------------------*/
	public function trasladar() {
		$msg = '';
		if ($this->comprobar() === false) {
			return true;
			return $this->serror;
		}
		// Aviso si le faltan notas
		if ($this->comprobarNotas() === false) {
			$msg = $this->serror;
		}

		// Cambio la situación de la persona. Debo hacerlo lo primero, pues no puedo
		// tener la misma persona en dos dl en la misma situación
		if ($this->cambiarFichaPersona() === false) {
			$msg = $this->serror;
			return _("OJO: Debería cambiar el campo situación. No se ha hecho ningún cambio.").$msg;
		}

		// Trasladar persona
		if ($this->copiarPersona() === false) {
			return $this->serror;
		}

		if ($this->copiarNotas() === false) {
			return $this->serror;
		}
		
		// apunto el traslado. Lo pongo antes para que se copie trasladar dossiers.
		if ($this->apuntar() === false) {
			return $this->serror;
		}
		
		if ($this->trasladarDossiers() === false) {
			return $this->serror;
		}
		return true;
	}

	public function comprobar() {
		$error = '';
		if (!empty($this->sdl_dst) AND $this->sdl_dst == $this->sdl_persona) {
			$error = _("Ya esta trasladado. No se ha hecho ningún cambio.");
		}
		if (empty($error)) {
			return true;
		} else {
			$this->serror = $error;
			return false;
		}
	}

	public function comprobarNotas() {
		// Aviso si le faltan notas
		$error = '';
		$oDBorg = $this->conexionOrg();
		$qRs = $oDBorg->query('SHOW search_path');
		$aPath = $qRs->fetch(\PDO::FETCH_ASSOC);
		
		$gesMatriculas = new gestorMatriculaDl();
		$gesMatriculas->setoDbl($oDBorg);
		$cMatriculasPendientes = $gesMatriculas->getMatriculasPendientes($this->iid_nom);
		$msg = '';
		foreach ($cMatriculasPendientes as $oMatricula) {
			$id_activ = $oMatricula->getId_activ();
			$id_asignatura = $oMatricula->getId_asignatura();
			$oActividad = new ActividadAll($id_activ);
			$nom_activ = $oActividad->getNom_activ();
			$oAsignatura = new Asignatura($id_asignatura);
			$nombre_corto=$oAsignatura->getNombre_corto();
			$msg .= empty($msg)? '' : '<br>';
			$msg .= sprintf(_("ca: %s, asignatura: %s"),$nom_activ,$nombre_corto);
		}
		$this->restaurarConexionOrg($oDBorg);
		if (!empty($msg)) {
			$error = _("Tiene pendiente de poner las notas de:") .'<br>'.$msg;
		}
		if (empty($error)) {
			return true;
		} else {
			$this->serror = $error;
			return false;
		}
	}

	public function cambiarFichaPersona() {
		// Cambio la situación de la persona. Debo hacerlo lo primero, pues no puedo tener la misma persona en dos dl en la misma situación
//		if ($this->ssituacion == 'A') exit (_("OJO: Debería cambiar el campo situación. No se ha hecho ningún cambio."));
		
		$error = '';
		$oDBorg = $this->conexionOrg();
		// dar permisos al usuario orbixv para acceder a personas_dl (?) o buscar tipo de perona
		$oPersonaDl = new personas\model\PersonaDl();
		$oPersonaDl->setoDbl($oDBorg);
		$oPersonaDl->setId_nom($this->iid_nom);
		$oPersonaDl->DBCarregar();
		$oPersonaDl->setSituacion($this->ssituacion);
		$oPersonaDl->setF_situacion($this->df_dl);
		$oPersonaDl->setDl($this->sdl_dst);
		if ($oPersonaDl->DBGuardar() === false) {
			$error .= '<br>'._('Hay un error, no se ha guardado');
			$this->restaurarConexionOrg($oDBorg);
			return false;
		}
		$this->restaurarConexionOrg($oDBorg);
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
	public function getEsquemas($id_orbix,$tipo_persona) {
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
			//elimino public, publicv, global
			if ($esquema == 'global') { continue; }
			if ($esquema == 'public') { continue; }
			if ($esquema == 'publicv') { continue; }
			if ($esquema == 'restov') { $tabla_personas = 'p_de_paso_ex'; }
			$esquema_slash = '"'.$esquema.'"';
			$oDBR->exec("SET search_path TO public,$esquema_slash");
			$qRs = $oDBR->query("SELECT '$esquema' as schemaName,id_schema,situacion,f_situacion FROM $tabla_personas WHERE id_nom=$id_orbix");
			$Result = $qRs->fetchAll(\PDO::FETCH_ASSOC);
			if (!empty($Result)) {
				if (count($Result) == 1) {
					$aResult[] = $Result[0];
				} else {
					exit(_("No puede existir una persona con el mismo id!!"));
				}
			}
		}
		//restaurarConexion($oDBR);
		$oDBR->exec("SET search_path TO $path_org");
		
		return $aResult;
	}
	
	public function copiarPersona() {
		$error = '';
		$oDBorg = $this->conexionOrg();
		$oPersonaDl = new personas\model\PersonaDl();
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
		$aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
		// si existe el esquema (dl)
		if (empty($aDades['existe'])) {
			$error = sprintf(_("No existe el esquema destino %s en la base de datos"),  $this->snew_esquema);
		}
		if (!empty($aDades['existe']) && $aDades['existe'] === true) {
			$id_tabla = $oPersonaDl->getId_tabla();
			switch ($id_tabla) {
				case 'n':
					$obj = 'personas\model\PersonaN';
					break;
				case 'a':
					$obj = 'personas\model\PersonaAgd';
					break;
				case 's':
					$obj = 'personas\model\PersonaS';
					break;
				case 'sssc':
					$obj = 'personas\model\PersonaSSSC';
					break;
				case 'x':
					$obj = 'personas\model\PersonaNax';
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
			$oPersonaNew->setF_situacion($this->df_dl);
			$oPersonaNew->setId_ctr('');
			if ($oPersonaNew->DBGuardar() === false) {
				$error .= '<br>'._('Hay un error, no se ha guardado');
			}
		}
		$this->restaurarConexionOrg($oDBorg);
		$this->restaurarConexionDst($oDBdst);
		if (empty($error)) {
			return true;
		} else {
			$this->serror = $error;
			return false;
		}
	}
		
	public function copiarNotas() {
		// Las Notas si o si (Aunque no se tenga el dossier abierto)
		// No cal fer res. Les notes són visibles per tothom.
		// -->CAMBIADO: Las notas pertenecen a la dl destino, si se 
		// borraran de la tabla porque no existe la persona, también
		// se perderían para todos...
		$error = '';
		$oDBorg = $this->conexionOrg();
		$oDBdst = $this->conexionDst();

		$gestor = "notas\model\GestorPersonaNotaDl";
		$ges = new $gestor();
		$ges->setoDbl($oDBorg);
		$colection = $ges->getPersonaNotas(array('id_nom'=>$this->iid_nom));
		if (!empty($colection)) {
			// Para saber el nuevo id_schema de la dl destino:
			if (($qRs = $oDBorg->query("SELECT id FROM public.db_idschema WHERE schema = '$this->snew_esquema'")) === false) {
					$sClauError = 'Controller.Traslados';
					$_SESSION['oGestorErrores']->addErrorAppLastError($qRs, $sClauError, __LINE__, __FILE__);
					return false;
				}
			$aSchema = $qRs->fetch(\PDO::FETCH_ASSOC);
			$id_schema = $aSchema['id'];
			foreach ($colection as $Objeto) {
				$Objeto->DBCarregar();
				//print_r($Objeto);
				$NuevoObj = clone $Objeto;
				if (method_exists($NuevoObj,'getId_item') === true) $NuevoObj->setId_item('');
				$NuevoObj->setoDbl($oDBdst);
				$NuevoObj->setId_schema($id_schema);
				if ($NuevoObj->DBGuardar() === false) {
					$error .= '<br>'._('No se ha guardado la nota');
				} else {
					//borrar la origen:
					$Objeto->DBEliminar();
				}
			}
		}
		$this->restaurarConexionOrg($oDBorg);
		$this->restaurarConexionDst($oDBdst);
		if (empty($error)) {
			return true;
		} else {
			$this->serror = $error;
			return false;
		}
	}

	public function trasladarDossiers () {
		$error = '';
		$oDBorg = $this->conexionOrg();
		$oDBdst = $this->conexionDst();
		$GesDossiers = new dossiers\model\GestorDossier();
		$GesDossiers->setoDbl($oDBorg);
		// Comprobar que estan apuntados.
		$cDossiers = $GesDossiers->DossiersNotEmpty('p',$this->iid_nom);

		//$cDossiers = $GesDossiers->getDossiers(array('tabla'=>'p','id_nom'=>$this->iid_nom));
		foreach ($cDossiers as $oDossier) {
			$id_tipo_dossier = $oDossier->getId_tipo_dossier();
			$oTipoDossier = new dossiers\model\TipoDossier($id_tipo_dossier);
			$app = $oTipoDossier->getApp();
			$class = $oTipoDossier->getClass();
			if (empty($class)) continue;
			$colection = array();
			switch ($class) {
				case 'TelecoPersonaDl':
					$gestor = "$app\model\GestorTelecoPersonaDl";
					$ges = new $gestor();
					$ges->setoDbl($oDBorg);
					$colection = $ges->getTelecos(array('id_nom'=>$this->iid_nom));
					break;
				case 'Profesor':
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$ges->setoDbl($oDBorg);
					$colection = $ges->getProfesores(array('id_nom'=>$this->iid_nom));
					break;
				case 'ProfesorAmpliacion':
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$ges->setoDbl($oDBorg);
					$colection = $ges->getProfesorAmpliaciones(array('id_nom'=>$this->iid_nom));
					break;
				case 'ProfesorCongreso':
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$ges->setoDbl($oDBorg);
					$colection = $ges->getProfesorCongresos(array('id_nom'=>$this->iid_nom));
					break;
				case 'ProfesorDirector':
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$ges->setoDbl($oDBorg);
					$colection = $ges->getProfesoresDirectores(array('id_nom'=>$this->iid_nom));
					break;
				case 'ProfesorDocenciaStgr':
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$ges->setoDbl($oDBorg);
					$colection = $ges->getProfesorDocenciasStgr(array('id_nom'=>$this->iid_nom));
					break;
				case 'ProfesorJuramento':
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$ges->setoDbl($oDBorg);
					$colection = $ges->getProfesorJuramentos(array('id_nom'=>$this->iid_nom));
					break;
				case 'ProfesorLatin':
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$ges->setoDbl($oDBorg);
					$colection = $ges->getProfesoresLatin(array('id_nom'=>$this->iid_nom));
					break;
				case 'ProfesorPublicacion':
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$ges->setoDbl($oDBorg);
					$colection = $ges->getProfesorPublicaciones(array('id_nom'=>$this->iid_nom));
					break;
				case 'ProfesorTituloEst':
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$ges->setoDbl($oDBorg);
					$colection = $ges->getTitulosEst(array('id_nom'=>$this->iid_nom));
					break;
				case 'PersonaNotaDl':
					// Lo hago a parte.
					break;
				case 'MatriculaDl':
					/*
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$ges->setoDbl($oDBorg);
					$colection = $ges->getMatriculas(array('id_nom'=>$this->iid_nom));
					*/
					break;
				case 'Traslado':
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$ges->setoDbl($oDBorg);
					$colection = $ges->getTraslados(array('id_nom'=>$this->iid_nom));
					break;
				case 'AsistenteDl':
					// Los Out pasan a Dl si la dl destino es la que organiza.
					$gestor = "$app\model\GestorAsistenteOut";
					$ges = new $gestor();
					$ges->setoDbl($oDBorg);
					$colection = $ges->getAsistentesOut(array('id_nom'=>$this->iid_nom));
					foreach ($colection as $oAsistenteOut) {
						$oAsistenteOut->DBCarregar();
						$id_activ = $oAsistenteOut->getId_activ();
						$oActividad = new ActividadAll($id_activ);
						// si es de la sf quito la 'f'
						$dl_org = preg_replace('/f$/', '', $oActividad->getDl_org());
						if ($dl_org == $this->sdl_dst) {
							$oAsistenteDl = new AsistenteDl();
							$oAsistenteDl->setoDbl($oDBdst);
							$oAsistenteDl = $this->copiar($oAsistenteOut,$oAsistenteDl); 
							$oAsistenteDl->DBGuardar();
						} else{
							$NuevoObj = clone $oAsistenteOut;
							$NuevoObj->setoDbl($oDBdst);
							if (method_exists($NuevoObj,'getId_item') === true) $NuevoObj->setId_item('');
							$NuevoObj->setTraslado('t');
							$NuevoObj->DBGuardar();
						}
					}
					// Los Dl pasan a Out
					$gestor = "$app\model\GestorAsistenteDl";
					$ges = new $gestor();
					$ges->setoDbl($oDBorg);
					$colection = $ges->getAsistentesDl(array('id_nom'=>$this->iid_nom));
					foreach ($colection as $oAsistenteDl) {
						$oAsistenteDl->DBCarregar();
						$oAsistenteOut = new AsistenteOut();
						$oAsistenteOut->setoDbl($oDBdst);
						$oAsistenteOut = $this->copiar($oAsistenteDl,$oAsistenteOut); 
						$oAsistenteOut->setTraslado('t');
						$oAsistenteOut->DBGuardar();
					}
					// Los Ex no deberían existir, son gente de otras dl, no afecta al traslado
					// reseteo la variable.
					$colection = array();
					break;
				case 'AsistenteCargo':
					// De momento no lo traslado.
					/*
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$ges->setoDbl($oDBorg);
					$colection = $ges->getActividadCargos(array('id_nom'=>$this->iid_nom));
					*/
					break;
			}
			if (!empty($colection)) {
				foreach ($colection as $Objeto) {
					$Objeto->DBCarregar();
					//print_r($Objeto);
					$NuevoObj = clone $Objeto;
					$NuevoObj->setoDbl($oDBdst);
					if (method_exists($NuevoObj,'getId_item') === true) $NuevoObj->setId_item('');
					$NuevoObj->DBGuardar();
				}
			}
			// también copia el estado del dossier
			$NuevoObj = clone $oDossier;
			$NuevoObj->setoDbl($oDBdst);
			$NuevoObj->DBGuardar();
		}
		// Volver oDBdst a su estado original:
		$this->restaurarConexionDst($oDBdst);
		$this->restaurarConexionOrg($oDBorg);
		if (empty($error)) {
			return true;
		} else {
			$this->serror = $error;
			return false;
		}
	}
		
	public function apuntar() {
		$error = '';
		// apunto el traslado.
		$oDBorg = $this->conexionOrg();
		$oTraslado = new personas\model\Traslado();
		$oTraslado->setoDbl($oDBorg);
		$oTraslado->setId_nom($this->iid_nom);
		$oTraslado->setF_traslado($this->df_dl);
		$oTraslado->setTipo_cmb('dl');
		$oTraslado->setId_ctr_origen('');
		$oTraslado->setCtr_origen($this->sdl_org);
		$oTraslado->setId_ctr_destino('');
		$oTraslado->setCtr_destino($this->sdl_dst);
		if ($oTraslado->DBGuardar() === false) {
			$error .= '<br>'._('Hay un error, no se ha guardado');
		}
		$this->restaurarConexionOrg($oDBorg);
		if (empty($error)) {
			return true;
		} else {
			$this->serror = $error;
			return false;
		}
	}

	private function copiar($oOrigen, $oDestino) {
		$oDestino->setId_activ($oOrigen->getId_activ()); 
		$oDestino->setId_nom($oOrigen->getId_nom()); 
		$oDestino->setPropio($oOrigen->getPropio()); 
		$oDestino->setEst_ok($oOrigen->getEst_ok()); 
		$oDestino->setCfi($oOrigen->getCfi());    
		$oDestino->setCfi_con($oOrigen->getCfi_con()); 
		$oDestino->setFalta($oOrigen->getFalta()); 
		$oDestino->setEncargo($oOrigen->getEncargo()); 
		$oDestino->setCama($oOrigen->getCama()); 
		$oDestino->setObserv($oOrigen->getObserv()); 
		return $oDestino;
	}

}