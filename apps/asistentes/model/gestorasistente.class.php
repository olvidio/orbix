<?php
namespace asistentes\model;
use core;
use web;
use actividades\model as actividades;
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
		$oDbl = $GLOBALS['oDB'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('d_asistentes_dl');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/

	
	/**
	 * retorna l'array d'objectes de tipus Asistente
	 *
	 * @param integre id_nom. de la persona
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus Asistente
	 */
	function getActividadesDeAsistente($id_nom,$aWhere=array(),$aOperators=array()) {
		// todas las actividades de la persona
		$a_Clases[] = array('clase'=>'AsistenteDl','get'=>'getAsistentesDl');
		$a_Clases[] = array('clase'=>'AsistenteOut','get'=>'getAsistentesOut');

		$namespace = __NAMESPACE__;
		$cAsistencias = $this->getConjunt($a_Clases,$namespace,array('id_nom'=>$id_nom), array());
		// seleccionar las actividades segun los criterios de búsqueda.
		$GesActividades = new actividades\GestorActividad();
		$aListaIds = $GesActividades->getArrayIds($aWhere,$aOperators);
		// descarto los uqe no estan.
		$cActividadesOk = array();
		foreach ($cAsistencias as $oAsistente) {
			$id_activ = $oAsistente->getId_activ();
			if (in_array($id_activ,$aListaIds)) {
				$oActividad = new actividades\Actividad($id_activ);
				$f_ini = $oActividad->getF_ini();
				$oFini= \DateTime::createFromFormat('j/m/Y', $f_ini);
				$f_ini_iso = $oFini->format('Y-m-d'); 
				$cActividadesOk[$f_ini_iso] = $oAsistente;
			}
		}
		ksort($cActividadesOk);
		return $cActividadesOk;
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
		$oDbl = $this->getoDbl();
		if (empty($sOrder)) $sOrder='apellido1,apellido2,nom';

		/* Mirar si la actividad es mia o no */
		$oActividad = new actividades\Actividad($iid_activ);
		$id_tabla = $oActividad->getId_tabla();
		switch($id_tabla) {
			case 'dl': // AsistentesDl + ASistentesIn
				$gesAsistenteDl = new GestorAsistenteDl();
				$cAsistentesDl = $gesAsistenteDl->getAsistentesDl(array('id_activ'=>$iid_activ));
		$a_Clases = array('AsistenteDl','AsistenteIn','AsistenteOut');
		$namespace = __NAMESPACE__;
		return $this->getConjunt($a_Clases,$namespace,$aWhere,$aOperators);

				break;
			case 'ex': // asistemntesOut

				break;
		}



		$oAsistenteSet = new core\Set();
		$sQry = "SELECT * FROM av_asistentes a JOIN pv_personas USING (id_nom)
				WHERE id_activ=$iid_activ
				ORDER BY ".$sOrder;
		if (($oDblSt = $oDbl->query($sQry)) === false) {
			$sClauError = 'GestorActividadAsistente.query_order';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDbl->query($sQry) as $aDades) {
			$a_pkey = array('id_activ' => $aDades['id_activ'],
							'id_nom' => $aDades['id_nom']);
			$oAsistente= new Asistente($a_pkey);
			$oAsistente->setAllAtributes($aDades);
			$oAsistenteSet->add($oAsistente);
		}
		return $oAsistenteSet->getTot();
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
		$oDbl = $this->getoDbl();
		if (empty($sOrder)) $sOrder='id_nom';
		$a_Lista = array();
		$sQry = "SELECT a.* FROM d_asistentes_activ a JOIN personas p USING (id_nom)
		   		WHERE a.id_activ=$iid_activ
				ORDER BY ".$sOrder;
		if (($oDblSt = $oDbl->query($sQry)) === false) {
			$sClauError = 'GestorActividadAsistente.query_order';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
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
		/* Buscar en los tres tipos de asistente: Dl, IN y Out. */
		$a_Clases[] = array('clase'=>'AsistenteDl','get'=>'getAsistentesDl');
		$a_Clases[] = array('clase'=>'AsistenteIn','get'=>'getAsistentesIn');
		$a_Clases[] = array('clase'=>'AsistenteOut','get'=>'getAsistentesOut');
		$namespace = __NAMESPACE__;
		return $this->getConjunt($a_Clases,$namespace,$aWhere,$aOperators);
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
?>
