<?php
namespace cambios\model\entity;
use core;
use core\ConfigGlobal;
/**
 * GestorCambio
 *
 * Classe per gestionar la llista d'objectes de la clase Cambio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 17/4/2019
 */

class GestorCambio Extends core\ClaseGestor {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */


	/**
	 * Constructor de la classe.
	 *
	 * @return $gestor
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS['oDBPC'];
		$this->setoDbl($oDbl);
		$this->setNomTabla('av_cambios');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/
	
	/**
	 * 
	 * Cambios: dl y public
	 *   Si sólo hago los de dl, al final en public quedarán los de las dl que no tienen el módulo de cambios.
	 *   Hay que borrar todo desde public. El primero que borre puede tener los ids para eliminar en las otras tablas,
	 *   pero los que lo hagan a continuación no van a saber lo que se ha borrado.
	 *
	 * cambios_anotados(dl)
	 *   Por lo dicho arriba, hay que borrar con un LEFT JOIN con los ids que no existan
	 *
	 * cambios_usuario(dl)
	 *   idem.
	 *   
	 * @param string $str_interval
	 */
	public function borrarCambios($str_interval='P1Y') {
	    $this->borrarCambiosP($str_interval);
	    $this->borrarCambiosAnotados();
	    $this->borrarCambiosUsuario();
	    
	}
	
	/**
	 * Elimina de la tabla usuario, los registros de cambios que ya se han eliminado
	 * @return boolean
	 */
	private function borrarCambiosUsuario() {
	    $oDbl = $GLOBALS['oDBC'];
	    $this->setoDbl($oDbl);
	    $this->setNomTabla('av_cambios');
	    
	    $sQry = "DELETE FROM av_cambios_usuario USING av_cambios_usuario a
                LEFT JOIN public.av_cambios c
                 ON (a.id_schema_cambio = c.id_schema AND a.id_item_cambio = c.id_item_cambio)
                WHERE av_cambios_usuario.id_item = a.id_item
                AND c.id_item_cambio IS NULL
                ";

	    if ($oDbl->query($sQry) === FALSE) {
	        $sClauError = 'GestorCambio.llistar.prepare';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return FALSE;
	    }
	} 
	/**
	 * Elimina de la tabla anotados, los registros de cambios que ya se han eliminado
	 * @return boolean
	 */
	private function borrarCambiosAnotados() {
	    $oDbl = $GLOBALS['oDBC'];
	    $this->setoDbl($oDbl);
	    $this->setNomTabla('av_cambios');

		if ( $_SERVER['DB_SERVER'] == 1) {
		    $nom_tabla_anotados = 'av_cambios_anotados_dl';
		} else {
		    $nom_tabla_anotados = 'av_cambios_anotados_dl_sf';
		}
	    
	    $sQry = "DELETE FROM $nom_tabla_anotados USING $nom_tabla_anotados a
                LEFT JOIN public.av_cambios c
                 ON (a.id_schema_cambio = c.id_schema AND a.id_item_cambio = c.id_item_cambio)
                WHERE $nom_tabla_anotados.id_item = a.id_item
                AND c.id_item_cambio IS NULL
                ";

	    if ($oDbl->query($sQry) === FALSE) {
	        $sClauError = 'GestorCambio.llistar.prepare';
	        $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
	        return FALSE;
	    }
	} 
	/**
	 * Elimina els apunts amb un timestamp anterior en un interval 
	 *
	 * @return 
	 */
	private function borrarCambiosP($str_interval='P1Y') {
		$oDbl = $this->getoDbl();
		$oDbl = $GLOBALS['oDBC'];
		$nom_tabla = $this->getNomTabla();
		
		$interval = new \DateInterval($str_interval);
		$oDateTime = new \DateTime();
		$timestamp = $oDateTime->sub($interval)->format('Y-m-d 00:00:00');
		
		$sQry = "DELETE FROM public.$nom_tabla
                WHERE timestamp_cambio < '$timestamp'
                ";

		if ($oDbl->query($sQry) === FALSE) {
			$sClauError = 'GestorCambio.borrar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		return TRUE;
	}
	/**
	 * retorna l'array d'objectes de tipus Cambio
	 * Que no s'hagin apuntat a la dl.
	 *
	 * @return array Una col·lecció d'objectes de tipus Cambio
	 */
	function getCambiosNuevos() {
		$oDbl = $this->getoDbl();
		$oDbl = $GLOBALS['oDBC'];
		$oCambioSet = new core\Set();
		
		/*
		if ( $_SERVER['DB_SERVER'] == 1) {
		    $nom_tabla_anotados = 'av_cambios_anotados_dl';
		} else {
		    $nom_tabla_anotados = 'av_cambios_anotados_dl_sf';
		}
		*/
		// Unir los anotados en seervidor 1 y en servidor 2:
		//select * from "H-dlb".av_cambios_anotados_dl UNION SELECT * from "H-dlb".av_cambios_anotados_dl_sf
		
		// Cuando av_cambios_anotados no tiene la fila, No podemos saber si es un cambio de la dl o no.
		// Vamos a hacer dos consultas separadas y unimos.
		
		// Cambios Dl (av_cambios_dl)
		$sQry = "SELECT c.id_schema, c.id_item_cambio, c.id_tipo_cambio, c.id_activ, c.id_tipo_activ, 
                c.json_fases_sv, c.json_fases_sf, c.dl_org,
                c.objeto, c.propiedad, c.valor_old, c.valor_new, c.quien_cambia, c.sfsv_quien_cambia, c.timestamp_cambio
                FROM av_cambios_dl c LEFT JOIN $nom_tabla_anotados a
                ON (c.id_schema = a.id_schema_cambio AND c.id_item_cambio=a.id_item_cambio)
                WHERE a.anotado IS NULL OR a.anotado = 'f'
                ORDER BY dl_org,id_tipo_activ,timestamp_cambio
                ";
		if ($oDbl->query($sQry) === FALSE) {
			$sClauError = 'GestorCambio.llistar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		foreach ($oDbl->query($sQry) as $aDades) {
			$a_pkey = array('id_item_cambio' => $aDades['id_item_cambio']);
            $oCambio= new CambioDl($a_pkey);
			$oCambio->setAllAtributes($aDades);
			$oCambioSet->add($oCambio);
		}
		// Cambios NO dl (sólo public.av_cambios)
		$sQry = "SELECT c.id_schema, c.id_item_cambio, c.id_tipo_cambio, c.id_activ, c.id_tipo_activ, 
                c.json_fases_sv, c.json_fases_sf, c.dl_org,
                c.objeto, c.propiedad, c.valor_old, c.valor_new, c.quien_cambia, c.sfsv_quien_cambia, c.timestamp_cambio
                FROM ONLY public.av_cambios c LEFT JOIN $nom_tabla_anotados a
                ON (c.id_schema = a.id_schema_cambio AND c.id_item_cambio=a.id_item_cambio)
                WHERE a.anotado IS NULL OR a.anotado = 'f'
                ORDER BY dl_org,id_tipo_activ,timestamp_cambio
                ";
		if ($oDbl->query($sQry) === FALSE) {
			$sClauError = 'GestorCambio.llistar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		foreach ($oDbl->query($sQry) as $aDades) {
			$a_pkey = array('id_item_cambio' => $aDades['id_item_cambio']);
            $oCambio= new Cambio($a_pkey);
			$oCambio->setAllAtributes($aDades);
			$oCambioSet->add($oCambio);
		}
		
		return $oCambioSet->getTot();
	}
	/**
	 * retorna l'array d'objectes de tipus Cambio
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d'objectes de tipus Cambio
	 */
	function getCambiosQuery($sQuery='') {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oCambioSet = new core\Set();
		if (($oDbl->query($sQuery)) === FALSE) {
			$sClauError = 'GestorCambio.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		foreach ($oDbl->query($sQuery) as $aDades) {
			$a_pkey = array('id_item_cambio' => $aDades['id_item_cambio']);
			if ($nom_tabla == 'av_cambios_dl') {
                $oCambio= new CambioDl($a_pkey);
			} else {
                $oCambio= new Cambio($a_pkey);
			}
			$oCambio->setAllAtributes($aDades);
			$oCambioSet->add($oCambio);
		}
		return $oCambioSet->getTot();
	}

	/**
	 * retorna l'array d'objectes de tipus Cambio
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus Cambio
	 */
	function getCambios($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oCambioSet = new core\Set();
		$oCondicion = new core\Condicion();
		$aCondi = array();
		foreach ($aWhere as $camp => $val) {
			if ($camp == '_ordre') continue;
			$sOperador = isset($aOperators[$camp])? $aOperators[$camp] : '';
			if ($a = $oCondicion->getCondicion($camp,$sOperador,$val)) $aCondi[]=$a;
			// operadores que no requieren valores
			if ($sOperador == 'BETWEEN' || $sOperador == 'IS NULL' || $sOperador == 'IS NOT NULL' || $sOperador == 'OR') unset($aWhere[$camp]);
            if ($sOperador == 'IN' || $sOperador == 'NOT IN') unset($aWhere[$camp]);
            if ($sOperador == 'TXT') unset($aWhere[$camp]);
		}
		$sCondi = implode(' AND ',$aCondi);
		if ($sCondi!='') $sCondi = " WHERE ".$sCondi;
		if (isset($GLOBALS['oGestorSessioDelegación'])) {
		   	$sLimit = $GLOBALS['oGestorSessioDelegación']->getLimitPaginador('a_actividades',$sCondi,$aWhere);
		} else {
			$sLimit='';
		}
		if ($sLimit === FALSE) return;
		$sOrdre = '';
		if (isset($aWhere['_ordre']) && $aWhere['_ordre']!='') $sOrdre = ' ORDER BY '.$aWhere['_ordre'];
		if (isset($aWhere['_ordre'])) unset($aWhere['_ordre']);
		$sQry = "SELECT * FROM $nom_tabla ".$sCondi.$sOrdre.$sLimit;
		if (($oDblSt = $oDbl->prepare($sQry)) === FALSE) {
			$sClauError = 'GestorCambio.llistar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		if (($oDblSt->execute($aWhere)) === FALSE) {
			$sClauError = 'GestorCambio.llistar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		foreach ($oDblSt as $aDades) {
			$a_pkey = array('id_item_cambio' => $aDades['id_item_cambio']);
			if ($nom_tabla == 'av_cambios_dl') {
                $oCambio= new CambioDl($a_pkey);
			} else {
                $oCambio= new Cambio($a_pkey);
			}
			$oCambio->setAllAtributes($aDades);
			$oCambioSet->add($oCambio);
		}
		return $oCambioSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
