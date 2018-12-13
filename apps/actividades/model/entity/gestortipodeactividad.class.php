<?php
namespace actividades\model\entity;
use core;
/**
 * GestorTipoDeActividad
 *
 * Classe per gestionar la llista d'objectes de la clase TipoDeActividad
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 09/11/2018
 */

class GestorTipoDeActividad Extends core\ClaseGestor {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */


	/**
	 * Constructor de la classe.
	 *
	 * @return $gestor
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDBC'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('a_tipos_actividad');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/
	
	/**
	 * retorna l'array de tipos de procesos posibles per el tipus d'activitat.
	 *
	 * @param string sid_tipo_activ
	 * @return array Una llista de id_tipo_proceso
	 */
	function getTiposDeProcesos($sid_tipo_activ='......',$bdl_propia='t') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
	    $aTiposDeProcesos = array();
	    if ($bdl_propia == 't') {
	        $sQry="SELECT id_tipo_proceso FROM $nom_tabla WHERE id_tipo_activ::text ~ '^$sid_tipo_activ' GROUP BY id_tipo_proceso";
	    } else {
	        $sQry="SELECT id_tipo_proceso_ex as id_tipo_proceso FROM $nom_tabla WHERE id_tipo_activ::text ~ '^$sid_tipo_activ' GROUP BY id_tipo_proceso_ex";
	    }
	    if (($oDbl->query($sQry)) === false) {
	        $sClauError = 'GestorTipoDeActividad.query';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return false;
	    }
	    foreach ($oDbl->query($sQry) as $aDades) {
	        if (!empty($aDades['id_tipo_proceso'])) {
	            $aTiposDeProcesos[]=$aDades['id_tipo_proceso'];
	        }
	    }
	    return $aTiposDeProcesos;
	}
	
	public function getId_tipoPosibles($regexp, $expr_txt) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
	    $a_id_tipos = [];
	    $query="SELECT substring(id_tipo_activ::text from '".$regexp."')
		   	FROM $nom_tabla  where id_tipo_activ::text ~'".$expr_txt."' order by id_tipo_activ";
	    //echo $query;
	    $oDBPCASt_id=$oDbl->query($query);
	    foreach ($oDBPCASt_id->fetchAll() as $row) {
	        $id_tipo = $row[0];
	        $a_id_tipos[$id_tipo] = true;
	    }
	    return $a_id_tipos;
	}
	
	public function getNom_tipoPosibles($expr_txt) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$tipo_nom = [];
		$nom_tipo = [];
	    $query="SELECT * FROM $nom_tabla where id_tipo_activ::text ~'$expr_txt' order by id_tipo_activ";
	    //echo $query;
	    $oDBPCASt_id=$oDbl->query($query);
	    $i=0;
	    foreach ($oDBPCASt_id->fetchAll() as $row) {
	        $i++;
	        $nom_tipo[$i] = $row['nombre'].'#'.$row['id_tipo_activ'];
	        $num=substr($row['id_tipo_activ'],3,3);
	        $tipo_nom[$num] = $row['nombre'];
	    }
	    return ['tipo_nom' => $tipo_nom,
	           'nom_tipo' => $nom_tipo];
	}
	public function getAsistentesPosibles($aText,$regexp) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$asistentes = [];
	    $query_ta="select substr(id_tipo_activ::text,2,1) as ta2
			from $nom_tabla where id_tipo_activ::text ~'".$regexp."' group by ta2 order by ta2";
	    //echo "query: $query_ta<br>";
	    $oDBPCASt_q_ta=$oDbl->query($query_ta);
	    foreach ($oDBPCASt_q_ta->fetchAll() as $row) {
	        $asistentes[$row[0]]=$aText[$row[0]];
	    }
	    return $asistentes;
	}
	public function getActividadesPosibles($aText,$expr_txt) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$actividades = [];
	    $query_ta="select substr(id_tipo_activ::text,3,1) as ta3
			from $nom_tabla where id_tipo_activ::text ~'$expr_txt' group by ta3 order by ta3";
	    $oDBPCASt_q_ta=$oDbl->query($query_ta);
	    foreach ($oDBPCASt_q_ta->fetchAll() as $row) {
	        $actividades[$row[0]]=$aText[$row[0]];
	    }
	    return $actividades;
	}

	public function getSfsvPosibles($aText) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$sfsv = [];
	    $query_ta="select substr(id_tipo_activ::text,1,1) as ta1 from $nom_tabla where id_tipo_activ::text ~'' group by ta1 order by ta1";
	    $oDBPCASt_q_ta=$oDbl->query($query_ta);
	    foreach ($oDBPCASt_q_ta->fetchAll() as $row) {
	        $sfsv[$row[0]]=$aText[$row[0]];
	    }
	    return $sfsv;
	}
	
	/**
	 * retorna l'array d'objectes de tipus TipoDeActividad
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d'objectes de tipus TipoDeActividad
	 */
	function getTiposDeActividadesQuery($sQuery='') {
		$oDbl = $this->getoDbl();
		$oTipoDeActividadSet = new core\Set();
		if (($oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorTipoDeActividad.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDbl->query($sQuery) as $aDades) {
			$a_pkey = array('id_tipo_activ' => $aDades['id_tipo_activ']);
			$oTipoDeActividad= new TipoDeActividad($a_pkey);
			$oTipoDeActividad->setAllAtributes($aDades);
			$oTipoDeActividadSet->add($oTipoDeActividad);
		}
		return $oTipoDeActividadSet->getTot();
	}

	/**
	 * retorna l'array d'objectes de tipus TipoDeActividad
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus TipoDeActividad
	 */
	function getTiposDeActividades($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oTipoDeActividadSet = new core\Set();
		$oCondicion = new core\Condicion();
		$aCondi = array();
		foreach ($aWhere as $camp => $val) {
			if ($camp == '_ordre') continue;
			$sOperador = isset($aOperators[$camp])? $aOperators[$camp] : '';
			if ($a = $oCondicion->getCondicion($camp,$sOperador,$val)) $aCondi[]=$a;
			// operadores que no requieren valores
			if ($sOperador == 'BETWEEN' || $sOperador == 'IS NULL' || $sOperador == 'IS NOT NULL' || $sOperador == 'OR') unset($aWhere[$camp]);
		}
		$sCondi = implode(' AND ',$aCondi);
		if ($sCondi!='') $sCondi = " WHERE ".$sCondi;
		if (isset($GLOBALS['oGestorSessioDelegación'])) {
		   	$sLimit = $GLOBALS['oGestorSessioDelegación']->getLimitPaginador('a_actividades',$sCondi,$aWhere);
		} else {
			$sLimit='';
		}
		if ($sLimit===false) return;
		$sOrdre = '';
		if (isset($aWhere['_ordre']) && $aWhere['_ordre']!='') $sOrdre = ' ORDER BY '.$aWhere['_ordre'];
		if (isset($aWhere['_ordre'])) unset($aWhere['_ordre']);
		$sQry = "SELECT * FROM $nom_tabla ".$sCondi.$sOrdre.$sLimit;
		if (($oDblSt = $oDbl->prepare($sQry)) === false) {
			$sClauError = 'GestorTipoDeActividad.llistar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		if (($oDblSt->execute($aWhere)) === false) {
			$sClauError = 'GestorTipoDeActividad.llistar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDblSt as $aDades) {
			$a_pkey = array('id_tipo_activ' => $aDades['id_tipo_activ']);
			$oTipoDeActividad= new TipoDeActividad($a_pkey);
			$oTipoDeActividad->setAllAtributes($aDades);
			$oTipoDeActividadSet->add($oTipoDeActividad);
		}
		return $oTipoDeActividadSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
