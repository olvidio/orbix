<?php
namespace personas\model;

use actividades\model\ActividadAll;
use asignaturas\model\Asignatura;
use asistentes\model\AsistenteDl;
use asistentes\model\AsistenteOut;
use actividadestudios\model\gestorMatriculaDl;
use core;
use dossiers;
use PDO;
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
	private $sreg_dl_dst;
	private $sdl_dst;
	private $ssituacion;
	private $df_dl;
	
	/* para guardar el search path de la conexión a la base de datos */
	private $path_org;
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
		$this->sdl_dst = $a_reg[1];
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

	public function comprobar() {
		if (!empty($this->sdl_dst) AND $this->sdl_dst == $this->sdl_persona) {
			//"Ya esta trasladado. No se ha hecho ningún cambio."
			return false;
		}
		return true;
	}

	public function comprobarNotas() {
		// Aviso si le faltan notas
		$gesMatriculas = new gestorMatriculaDl();
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
		if (!empty($msg)) {
			$error .= _("Tiene pendiente de poner las notas de:") .'<br>'.$msg;
			return $error;
		}
		return true;
	}

	public function cambiarFichaPersona() {
		// Cambio la situación de la persona. Debo hacerlo lo primero, pues no puedo tener la misma persona en dos dl en la misma situación
		if ($this->ssituacion == 'A') exit (_("OJO: Debería cambiar el campo situación. No se ha hecho ningún cambio."));
		
		$oPersonaDl = new personas\model\PersonaDl($this->iid_nom);
		$oPersonaDl->DBCarregar();
		$oPersonaDl->setSituacion($this->ssituacion);
		$oPersonaDl->setF_situacion($this->df_dl);
		$oPersonaDl->setDl($this->sdl_dst);
		if ($oPersonaDl->DBGuardar() === false) {
			$error .= '<br>'._('Hay un error, no se ha guardado');
			return false;
		}
		return true;
	}
	
	private function nuevaConexion() {
		$sfsv_txt = (core\configGlobal::mi_sfsv() == 1)? 'v' :'f';
		$this->snew_esquema = $this->sreg_dl_dst.$sfsv_txt;
		//Utilizo la conexión oDBR para cambiar momentáneamente el search_path.
		$oDBR = $GLOBALS['oDBR'];
		$qRs = $oDBR->query('SHOW search_path');
		$aPath = $qRs->fetch(PDO::FETCH_ASSOC);
		$this->path_org = addslashes($aPath['search_path']);
		$oDBR->exec("SET search_path TO public,\"$this->snew_esquema\"");
		//$oDBR->exec("SET DATESTYLE TO '".ConfigGlexecobal::$datestyle."'");
		return $oDBR;
	}
	private function restaurarConexion($oDBR) {
		// Volver oDBR a su estado original:
		$oDBR->exec("SET search_path TO $this->path_org");
	}
	
	public function copiarPersona() {
		$oPersonaDl = new personas\model\PersonaDl($this->iid_nom);
		$oPersonaDl->DBCarregar();
		// Trasladar persona
		$oDbl = $GLOBALS['oDB'];
		$oDBR = $this->nuevaConexion();

		// Copiar los datos a la dl destino si existe en orbix.
		if (($qRs = $oDbl->query("SELECT EXISTS(SELECT 1 FROM pg_namespace WHERE nspname = '$this->snew_esquema') AS existe")) === false) {
				$sClauError = 'Controller.Traslados';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
		}
		$aDades = $qRs->fetch(PDO::FETCH_ASSOC);
		// si existe el esquema (dl)
		if (!empty($aDades['existe'])) {
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
			$oPersona = new $obj($this->iid_nom);
			$oPersona->DBCarregar();
			$oPersonaNew = clone $oPersona;
			$oPersonaNew->setoDbl($oDBR);
			$oPersonaNew->setDl($this->sdl_dst);
			$oPersonaNew->setSituacion('A');
			$oPersonaNew->setF_situacion($this->df_dl);
			$oPersonaNew->setId_ctr('');
			if ($oPersonaNew->DBGuardar() === false) {
				$error .= '<br>'._('Hay un error, no se ha guardado');
			}
		}
		$this->restaurarConexion($oDBR);
	}
		
	public function copiarNotas() {
		// Las Notas si o si (Aunque no se tenga el dossier abierto)
		// No cal fer res. Les notes són visibles per tothom.
		// -->CAMBIADO: Las notas pertenecen a la dl destino, si se 
		// borraran de la tabla porque no existe la persona, también
		// se perderían para todos...
		$oDbl = $GLOBALS['oDB'];
		$oDBR = $this->nuevaConexion();

		$gestor = "notas\model\GestorPersonaNotaDl";
		$ges = new $gestor();
		$colection = $ges->getPersonaNotas(array('id_nom'=>$this->iid_nom));
		if (!empty($colection)) {
			// Para saber el nuevo id_schema de la dl destino:
			if (($qRs = $oDbl->query("SELECT id FROM public.db_idschema WHERE schema = '$this->snew_esquema'")) === false) {
					$sClauError = 'Controller.Traslados';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
					return false;
				}
			$aSchema = $qRs->fetch(PDO::FETCH_ASSOC);
			$id_schema = $aSchema['id'];
			foreach ($colection as $Objeto) {
				$Objeto->DBCarregar();
				//print_r($Objeto);
				$NuevoObj = clone $Objeto;
				if (method_exists($NuevoObj,'getId_item') === true) $NuevoObj->setId_item('');
				$NuevoObj->setoDbl($oDBR);
				$NuevoObj->setId_schema($id_schema);
				if ($NuevoObj->DBGuardar() === false) {
					$error .= '<br>'._('No se ha guardado la nota');
				} else {
					//borrar la origen:
					$Objeto->DBEliminar();
				}
			}
		}
		$this->restaurarConexion($oDBR);
	}

	public function trasladarDossiers () {
		$oDBR = $this->nuevaConexion();
		$GesDossiers = new dossiers\model\GestorDossier();
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
					$colection = $ges->getTelecos(array('id_nom'=>$this->iid_nom));
					break;
				case 'Profesor':
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$colection = $ges->getProfesores(array('id_nom'=>$this->iid_nom));
					break;
				case 'ProfesorAmpliacion':
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$colection = $ges->getProfesorAmpliaciones(array('id_nom'=>$this->iid_nom));
					break;
				case 'ProfesorCongreso':
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$colection = $ges->getProfesorCongresos(array('id_nom'=>$this->iid_nom));
					break;
				case 'ProfesorDirector':
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$colection = $ges->getProfesoresDirectores(array('id_nom'=>$this->iid_nom));
					break;
				case 'ProfesorDocenciaStgr':
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$colection = $ges->getProfesorDocenciasStgr(array('id_nom'=>$this->iid_nom));
					break;
				case 'ProfesorJuramento':
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$colection = $ges->getProfesorJuramentos(array('id_nom'=>$this->iid_nom));
					break;
				case 'ProfesorLatin':
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$colection = $ges->getProfesoresLatin(array('id_nom'=>$this->iid_nom));
					break;
				case 'ProfesorPublicacion':
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$colection = $ges->getProfesorPublicaciones(array('id_nom'=>$this->iid_nom));
					break;
				case 'ProfesorTituloEst':
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$colection = $ges->getTitulosEst(array('id_nom'=>$this->iid_nom));
					break;
				case 'PersonaNotaDl':
					// Lo hago a parte.
					break;
				case 'MatriculaDl':
					/*
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$colection = $ges->getMatriculas(array('id_nom'=>$this->iid_nom));
					*/
					break;
				case 'Traslado':
					$gestor = "$app\model\Gestor$class";
					$ges = new $gestor();
					$colection = $ges->getTraslados(array('id_nom'=>$this->iid_nom));
					break;
				case 'AsistenteDl':
					// Los Out pasan a Dl si la dl destino es la que organiza.
					$gestor = "$app\model\GestorAsistenteOut";
					$ges = new $gestor();
					$colection = $ges->getAsistentesOut(array('id_nom'=>$this->iid_nom));
					foreach ($colection as $oAsistenteOut) {
						$oAsistenteOut->DBCarregar();
						$id_activ = $oAsistenteOut->getId_activ();
						$oActividad = new ActividadAll($id_activ);
						// si es de la sf quito la 'f'
						$dl_org = preg_replace('/f$/', '', $oActividad->getDl_org());
						if ($dl_org == $dl) {
							$oAsistenteDl = new AsistenteDl();
							$oAsistenteDl = $this->copiar($oAsistenteOut,$oAsistenteDl); 
							$oAsistenteDl->setoDbl($oDBR);
							$oAsistenteDl->DBGuardar();
						} else{
							$NuevoObj = clone $oAsistenteOut;
							if (method_exists($NuevoObj,'getId_item') === true) $NuevoObj->setId_item('');
							$NuevoObj->setoDbl($oDBR);
							$NuevoObj->setTraslado('t');
							$NuevoObj->DBGuardar();
						}
					}
					// Los Dl pasan a Out
					$gestor = "$app\model\GestorAsistenteDl";
					$ges = new $gestor();
					$colection = $ges->getAsistentesDl(array('id_nom'=>$this->iid_nom));
					foreach ($colection as $oAsistenteDl) {
						$oAsistenteDl->DBCarregar();
						$oAsistenteOut = new AsistenteOut();
						$oAsistenteOut = $this->copiar($oAsistenteDl,$oAsistenteOut); 
						$oAsistenteOut->setoDbl($oDBR);
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
					$colection = $ges->getActividadCargos(array('id_nom'=>$this->iid_nom));
					*/
					break;
			}
			if (!empty($colection)) {
				foreach ($colection as $Objeto) {
					$Objeto->DBCarregar();
					//print_r($Objeto);
					$NuevoObj = clone $Objeto;
					if (method_exists($NuevoObj,'getId_item') === true) $NuevoObj->setId_item('');
					$NuevoObj->setoDbl($oDBR);
					$NuevoObj->DBGuardar();
				}
			}
			// también copia el estado del dossier
			$NuevoObj = clone $oDossier;
			$NuevoObj->setoDbl($oDBR);
			$NuevoObj->DBGuardar();
		}
		// Volver oDBR a su estado original:
		$this->restaurarConexion($oDBR);
	}
		
	public function apuntar() {	
		// apunto el traslado.
		$oTraslado = new personas\model\Traslado();
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