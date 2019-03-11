<?php
namespace encargossacd\model\entity;
use core;
use encargossacd\model\EncargoFuncionesTrait;
/**
 * GestorEncargoTipo
 *
 * Classe per gestionar la llista d'objectes de la clase EncargoTipo
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/01/2019
 */

class GestorEncargoTipo Extends core\ClaseGestor {
    use EncargoFuncionesTrait;
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
		$this->setNomTabla('encargo_tipo');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/
	
	/**
	 * Devuelve el número del tipo de encargo para hacer una selección SQL.
	 *
	 *	 En función de los parámetros que se le pasan:
	 *		$grupo		ctr,cgi,igl,otros,personales
	 *		$nom tipo	(el encargo en concreto)
	 *	Si un parámetro se omite, se pone un punto (.) para que la búsqueda sea qualquier número
	 *	ejemplo: 12....
	 */
	 public function id_tipo_encargo($grupo,$nom_tipo) {
        $oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
         
        $condta1='.';
        $condta2='.';
        $condta3='..';
        $condta=$condta1 . $condta2 . $condta3 ;
        
        if ($nom_tipo and $nom_tipo!="...") {
            $condicion="id_tipo_enc::text ~ '" . $condta. "'";
            $query="SELECT * FROM $nom_tabla where $condicion";
            if (($oDblSt=$oDbl->query($query)) === FALSE) {
                $sClauError = 'GestorEncargoTipo.id_tipo_encargo.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
                return FALSE;
            }
            $row = $oDblSt->fetch();
            $id_tipo_enc =$row["id_tipo_enc"];
            $condta=$id_tipo_enc;
        }
         
        return $condta;
	 }
	
	/**
	 * Devuelve los parámetros de un encargo en función del tipo de encargo.
	 *
	 * Es la función inversa de "id_tipo_encargo()".
	 * Se le pasa el id_tipo_encargo, y devuelve un array ($tipo) con los siguientes valores:
	 *
	 *		grupo		ctr,cgi,igl,otros,personales
	 *		nom_tipo	(el encargo en concreto)
	 *
	 *@author	Daniel Serrabou
	 *@since		28/2/06.
	 *
	 */
	 public function encargo_de_tipo($id_tipo_enc){
        $oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
        $t_grupo = EncargoTipo::GRUPO;
         
        //transpongo los vectores para buscar por números y no por el texto
        $ft_grupo = array_flip ($t_grupo);
        
        $ta1=substr($id_tipo_enc,0,1);
        $ta2=substr($id_tipo_enc,1,3);
         
        if ($ta1==".") {
            ksort($ft_grupo);
            $grupo=$ft_grupo;
        } else {
            $grupo=$ft_grupo[$ta1];
        }
         
        $query="SELECT * FROM $nom_tabla where id_tipo_enc::text ~ '^".$id_tipo_enc."' order by tipo_enc";
        //echo $query;
		if (($oDblSt=$oDbl->query($query)) === FALSE) {
			$sClauError = 'GestorEncargoTipo.encargo_de_tipo.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
         
		$nom_tipo = [];
        if ($ta2=="...") {
            $i=0;
            foreach ($oDblSt->fetchAll() as $row) {
                $nom_tipo[$row["id_tipo_enc"]] = $row["tipo_enc"];
                $i++;
            }
        } else {
            $row=$oDblSt->fetch(\PDO::FETCH_ASSOC);
            $nom_tipo=$row["tipo_enc"];
        }
        
        $tipo = array(
            "grupo" => $grupo,
            "nom_tipo" => $nom_tipo
        );
         
        return $tipo;
	 }
	 
	/**
	 * retorna l'array d'objectes de tipus EncargoTipo
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d'objectes de tipus EncargoTipo
	 */
	function getEncargoTiposQuery($sQuery='') {
		$oDbl = $this->getoDbl();
		$oEncargoTipoSet = new core\Set();
		if (($oDbl->query($sQuery)) === FALSE) {
			$sClauError = 'GestorEncargoTipo.query';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		foreach ($oDbl->query($sQuery) as $aDades) {
			$a_pkey = array('id_tipo_enc' => $aDades['id_tipo_enc']);
			$oEncargoTipo= new EncargoTipo($a_pkey);
			$oEncargoTipo->setAllAtributes($aDades);
			$oEncargoTipoSet->add($oEncargoTipo);
		}
		return $oEncargoTipoSet->getTot();
	}

	/**
	 * retorna l'array d'objectes de tipus EncargoTipo
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d'objectes de tipus EncargoTipo
	 */
	function getEncargoTipos($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$oEncargoTipoSet = new core\Set();
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
		if ($sLimit === FALSE) return;
		$sOrdre = '';
		if (isset($aWhere['_ordre']) && $aWhere['_ordre']!='') $sOrdre = ' ORDER BY '.$aWhere['_ordre'];
		if (isset($aWhere['_ordre'])) unset($aWhere['_ordre']);
		$sQry = "SELECT * FROM $nom_tabla ".$sCondi.$sOrdre.$sLimit;
		if (($oDblSt = $oDbl->prepare($sQry)) === FALSE) {
			$sClauError = 'GestorEncargoTipo.llistar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		if (($oDblSt->execute($aWhere)) === FALSE) {
			$sClauError = 'GestorEncargoTipo.llistar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		foreach ($oDblSt as $aDades) {
			$a_pkey = array('id_tipo_enc' => $aDades['id_tipo_enc']);
			$oEncargoTipo= new EncargoTipo($a_pkey);
			$oEncargoTipo->setAllAtributes($aDades);
			$oEncargoTipoSet->add($oEncargoTipo);
		}
		return $oEncargoTipoSet->getTot();
	}

	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
