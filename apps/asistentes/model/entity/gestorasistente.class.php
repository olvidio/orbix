<?php
namespace asistentes\model\entity;
use core;
use web;
use actividades\model\entity as actividades;
use personas\model\entity as personas;
/**
 * GestorAsistente
 *
 * Classe per gestionar la llista d'objectes de la clase Asistente
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */

class GestorAsistente Extends core\ClaseGestor {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */


	/**
	 * Constructor de la classe.
	 *
	 * @return $gestor
	 *
	 */
	function __construct() {
		//$oDbl = $GLOBALS['oDB'];
		//$this->setoDbl($oDbl);
		//$this->setNomTabla('d_asistentes_dl');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	
	
	/**
	 * retorna un objecte del tipus Desplegable
	 * Les posibles asignatures
	 *
	 * @return object del tipus Desplegable
	 */
	function getOpcionesPosiblesPlaza() {
		$aOpciones[Asistente::PLAZA_PEDIDA] = _("pedida");
		$aOpciones[Asistente::PLAZA_EN_ESPERA] = _("en espera");
//		$aOpciones[Asistente::PLAZA_DENEGADA] = _("denegada");
		$aOpciones[Asistente::PLAZA_ASIGNADA] = _("asignada");
		$aOpciones[Asistente::PLAZA_CONFIRMADA] = _("confirmada");
		return $aOpciones;
	}
	
	/**
	 * retorna un objecte del tipus Desplegable
	 * Les posibles asignatures
	 *
	 * @return object del tipus Desplegable
	 */
	function getPosiblesPlaza() {
		$aOpciones =  $this->getOpcionesPosiblesPlaza();
		return new web\Desplegable('',$aOpciones,'',true);
	}
	
	/**
	 * retorna l'array d'objectes de tipus Asistente
	 *
	 * @param integre id_nom. de la persona
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus Asistente
	 */
	function getActividadesDeAsistente($aWhereNom,$aWhere=array(),$aOperators=array(),$reverse=FALSE) {
		// todas las actividades de la persona
		//Importa el orden, se queda con la última.
		$a_Clases[] = array('clase'=>'AsistenteEx','get'=>'getAsistentesEx');
		$a_Clases[] = array('clase'=>'AsistenteOut','get'=>'getAsistentesOut');
		$a_Clases[] = array('clase'=>'AsistenteIn','get'=>'getAsistentesIn');
		$a_Clases[] = array('clase'=>'AsistenteDl','get'=>'getAsistentesDl');

		$namespace = __NAMESPACE__;
		$cAsistencias = $this->getConjunt($a_Clases,$namespace,$aWhereNom, array());
		//$cAsistencias = $this->getConjunt($a_Clases,$namespace,array('id_nom'=>$id_nom), array());
		// seleccionar las actividades segun los criterios de búsqueda.
		$GesActividades = new actividades\GestorActividad();
		$aListaIds = $GesActividades->getArrayIds($aWhere,$aOperators);
		// descarto los que no estan.
		$cActividadesOk = array();
		foreach ($cAsistencias as $oAsistente) {
			$id_activ = $oAsistente->getId_activ();
			if (in_array($id_activ,$aListaIds)) {
				$oActividad = new actividades\Actividad($id_activ);
				$f_ini = $oActividad->getF_ini();
				$oFini= \DateTime::createFromFormat('j/m/Y', $f_ini);
				$f_ini_iso = $oFini->format('Y-m-d'); 
				$oAsistente->DBCarregar();
				$cActividadesOk[$f_ini_iso] = $oAsistente;
			}
		}
		if ($reverse === true) {
			krsort($cActividadesOk);
		} else {
			ksort($cActividadesOk);
		}
		return $cActividadesOk;
	}
	
	/**
	 * retorna numero de places ocupades
	 *
	 * @param integer iid_activ el id de l'activitat.
	 * @param string sdl sigla de la dl
	 * @param string dl_hub sigla de la dl propietaria de las plazas
	 * @return integer
	 */
	function getPlazasOcupadasPorDl($iid_activ,$sdl='',$dl_hub='') {
		$mi_dele = core\ConfigGlobal::mi_dele();
		/* Mirar si la actividad es mia o no */
		$oActividad = new actividades\Actividad($iid_activ);
		$dl_org = $oActividad->getDl_org();
		$id_tabla = $oActividad->getId_tabla();
		$aWhere['id_activ'] = $iid_activ;
		$aOperators = array();
		$namespace = __NAMESPACE__;
		$msg_err = '';
		
		if ($sdl == $mi_dele) {
			if ($dl_org == $sdl) {
				//$gesAsistenteDl = new GestorAsistenteDl();
				//$cAsistentes = $gesAsistenteDl->getAsistentesDl(array('id_activ'=>$iid_activ));
				$a_Clases[] = array('clase'=>'AsistenteDl','get'=>'getAsistentesDl');
				$a_Clases[] = array('clase'=>'AsistenteIn','get'=>'getAsistentesIn');
				$cAsistentes = $this->getConjunt($a_Clases,$namespace,$aWhere,$aOperators);
			} else {
				$a_Clases[] = array('clase'=>'AsistenteOut','get'=>'getAsistentesOut');
				$cAsistentes = $this->getConjunt($a_Clases,$namespace,$aWhere,$aOperators);
			}
		} else {
			// No hace falta saber las plazas ocupadas de otra dl.
			//return -1;
			//o si:
			if ($dl_org == $sdl) {
				$cAsistentes = array();
			} else {
				if ($dl_org == $mi_dele) {
					$a_Clases[] = array('clase'=>'AsistenteEx','get'=>'getAsistentesEx');
					//$a_Clases[] = array('clase'=>'AsistenteIn','get'=>'getAsistentesIn');
					$cAsistentes = $this->getConjunt($a_Clases,$namespace,$aWhere, $aOperators);
					//$gesAsistenteIn = new GestorAsistenteIn();
					//$cAsistentes = $gesAsistenteIn->getAsistentesIn(array('id_activ'=>$iid_activ));
				} else {
					$a_Clases[] = array('clase'=>'AsistenteOut','get'=>'getAsistentesOut');
					$cAsistentes = $this->getConjunt($a_Clases,$namespace,$aWhere, $aOperators);
					//$cAsistentes = array();

				}
			}
		}
		
		$numAsis = 0;
		foreach ($cAsistentes as $oAsistente) {
			$id_nom = $oAsistente->getId_nom();
			$propietario = $oAsistente->getPropietario();
			$padre = strtok($propietario,'>');
			$child = strtok('>');
			//if ($sdl != $mi_dele) {
			if (!empty($dl_hub) && $dl_hub != $padre) continue;
			if ($sdl != $child) continue;
			$oPersona = personas\Persona::NewPersona($id_nom);
			if (!is_object($oPersona)) {
				$msg_err .= "<br>$oPersona con id_nom: $id_nom en  ".__FILE__.": line ". __LINE__;
				$msg_err .= "<br>"._("Borro la asistencia");
				$oAsistente->DBEliminar();
				continue;
			}
			// También hay que contar a los de paso (ocupan plaza)
			/*$dl = $oPersona->getDl();
			if ($sdl != $dl) continue;
			*/
			$plaza= empty($oAsistente->getPlaza())? Asistente::PLAZA_PEDIDA : $oAsistente->getPlaza();
			// sólo cuento las asignadas
			if ($plaza < Asistente::PLAZA_ASIGNADA) continue;
			$numAsis++; 
		}
		if (!empty($msg_err)) { echo $msg_err; }
		return $numAsis;
	}

	/**
	 * retorna l'array d'objectes de tipus ActividadAsistente
	 *   ordenats sOrder. Per defecte: apellido1,apellido1,nom.per apellido1
	 *
	 * @param integer iid_activ el id de l'activitat.
	 * @param string sOrder(null) l'ordre que es vol. Per defecte: apellido1,apellido1,nom.
	 * @return array Una col·lecció d'objectes de tipus ActividadAsistente
	 */
	function getAsistentesDeActividad($iid_activ,$sOrder='') {
		//$oDbl = $this->getoDbl();
		if (empty($sOrder)) $sOrder='apellido1,apellido2,nom';

		/* Mirar si la actividad es mia o no */
		$oActividad = new actividades\Actividad($iid_activ);
		$dl = $oActividad->getDl_org();
		$id_tabla = $oActividad->getId_tabla();
		$aWhere['id_activ'] = $iid_activ;
		$aOperators = array();
		$namespace = __NAMESPACE__;
		$msg_err = '';
		switch($id_tabla) {
			case 'dl': // AsistentesDl + AsistentesIn
				$gesAsistenteDl = new GestorAsistenteDl();
				$cAsistentesDl = $gesAsistenteDl->getAsistentesDl(array('id_activ'=>$iid_activ));
				// todas las actividades de la persona
				$a_Clases[] = array('clase'=>'AsistenteDl','get'=>'getAsistentesDl');
				$a_Clases[] = array('clase'=>'AsistenteIn','get'=>'getAsistentesIn');
				$namespace = __NAMESPACE__;
				$cAsistentes = $this->getConjunt($a_Clases,$namespace,$aWhere,$aOperators);
				break;
			case 'ex': // asistentesEx
				$a_Clases[] = array('clase'=>'AsistenteEx','get'=>'getAsistentesEx');
				$namespace = __NAMESPACE__;
				$cAsistentes = $this->getConjunt($a_Clases,$namespace,$aWhere,$aOperators);
				break;
//			case 'ex': // asistentesOut
//				$a_Clases[] = array('clase'=>'AsistenteOut','get'=>'getAsistentesOut');
//				$namespace = __NAMESPACE__;
//				$cAsistentes = $this->getConjunt($a_Clases,$namespace,$aWhere,$aOperators);
//				break;
		}
		$cAsistentesOk = array();
		foreach ($cAsistentes as $oAsistente) {
			$id_nom = $oAsistente->getId_nom();
			$oPersona = personas\Persona::NewPersona($id_nom);
			if (!is_object($oPersona)) {
				$msg_err .= "<br>$oPersona con id_nom: $id_nom en  ".__FILE__.": line ". __LINE__;
				continue;
			}
			$apellidos = $oPersona->getApellidosNombre();
			$cAsistentesOk[$apellidos] = $oAsistente;
		}
		uksort($cAsistentesOk,"core\strsinacentocmp");
		if (!empty($msg_err)) { echo $msg_err; }
		return $cAsistentesOk;
	}
	/**
	 * retorna l'array de id_nom d'Asistents
	 *   ordenats sOrder. Per defecte: id_nom
	 *
	 * @param integer iid_activ el id de l'activitat.
	 * @param string sOrder(null) l'ordre que es vol. Per defecte: apellido1,apellido1,nom.
	 * @return array llista de id_nom d'Asistents
	 */
	function getListaAsistentesDeActividad($iid_activ,$sOrder='') {
		//$oDbl = $this->getoDbl();
		if (empty($sOrder)) $sOrder='id_nom';
		$a_Lista = array();
		$sQry = "SELECT a.* FROM d_asistentes_activ a JOIN personas p USING (id_nom)
		   		WHERE a.id_activ=$iid_activ
				ORDER BY ".$sOrder;
		if (($oDblSt = $oDbl->query($sQry)) === false) {
			$sClauError = 'GestorActividadAsistente.query_order';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDbl->query($sQry) as $aDades) {
			$a_Lista[] = $aDades['id_nom'];
		}
		return $a_Lista;
	}

	/**
	 * retorna l'array d'objectes de tipus Asistente
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus Asistente
	 */
	function getAsistentes($aWhere=array(),$aOperators=array()) {
		/* Mirar si la actividad es mia o no */
		$iid_activ = $aWhere['id_activ'];
		$oActividad = new actividades\Actividad($iid_activ);
		// si es de la sf quito la 'f'
		$dl = preg_replace('/f$/', '', $oActividad->getDl_org());
		$id_tabla = $oActividad->getId_tabla();
		if ($dl == core\ConfigGlobal::mi_dele()) {
			// Todos los asistentes
			/* Buscar en los tres tipos de asistente: Dl, IN y Out. */
			$a_Clases[] = array('clase'=>'AsistenteDl','get'=>'getAsistentesDl');
			$a_Clases[] = array('clase'=>'AsistenteIn','get'=>'getAsistentesIn');
			$a_Clases[] = array('clase'=>'AsistenteOut','get'=>'getAsistentesOut');
		} else {
			if ($id_tabla == 'dl') {
				$a_Clases[] = array('clase'=>'AsistenteOut','get'=>'getAsistentesOut');
			} else {
				$a_Clases[] = array('clase'=>'AsistenteDl','get'=>'getAsistentesDl');
				$a_Clases[] = array('clase'=>'AsistenteIn','get'=>'getAsistentesIn');
			}
		}
		$namespace = __NAMESPACE__;
		return $this->getConjunt($a_Clases,$namespace,$aWhere,$aOperators);
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
?>
