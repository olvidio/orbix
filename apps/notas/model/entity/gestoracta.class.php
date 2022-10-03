<?php
namespace notas\model\entity;

use core;

/**
 * GestorActa
 *
 * Classe per gestionar la llista d'objectes de la clase Acta
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
class GestorActa extends core\ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    /**
     * Constructor de la classe.
     *
     * @return $gestor
     *
     */
    function __construct()
    {
        $oDbl = $GLOBALS['oDBP'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('e_actas');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/


    /**
     * retorna l'última acta d'una regió.
     *
     * @param string regió/dl/? en el que buscar la últim número d'acta.
     * @return integer
     */
    function getUltimaActa($sRegion = '?', $any)
    {
        $sRegion = ($sRegion == '?') ? "\\" . $sRegion : $sRegion;
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT  (regexp_matches(acta, '^\w{1,6}\s+(\d+)/$any'))::numeric[] as num
			FROM $nom_tabla WHERE acta ~* '^$sRegion\s+.*/$any'
			ORDER BY num DESC
			LIMIT 1";
        //echo "ss: $sQuery<br>";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorActa.UltimaActa';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $num = $oDblSt->fetchColumn();
        if ($num != false) {
            // Quitar los {}.
            $num = (integer)trim($num, '{}');
        } else {
            $num = 0;
        }
        return $num;
    }

    /**
     * retorna l'última linea del llibre.
     *
     * @param integer iLibro libro en el que buscar la últmia linea.
     * @return integer
     */
    function getUltimaLinea($iLibro = 1)
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $ult_pag = $this->getUltimaPagina($iLibro);
        $sQuery = "SELECT max(linea) FROM $nom_tabla WHERE libro='$iLibro' AND pagina='$ult_pag' GROUP BY COALESCE(linea,0) ";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorActa.UltimoLibro';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        return $oDblSt->fetchColumn();
    }

    /**
     * retorna l'última pàgina del llibre.
     *
     * @param integer iLibro libro en el que buscar la últmia pàgina.
     * @return integer
     */
    function getUltimaPagina($iLibro = 1)
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT max(pagina) FROM $nom_tabla WHERE libro=$iLibro GROUP BY libro";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorActa.UltimoLibro';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        return $oDblSt->fetchColumn();
    }

    /**
     * retorna l'últim llibre d'actes.
     *
     * @return integer
     */
    function getUltimoLibro()
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT max(libro) FROM $nom_tabla";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorActa.UltimoLibro';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        return $oDblSt->fetchColumn();
    }


    /**
     * retorna l'array d'objectes de tipus Acta
     *
     * @param string sQuery la query a executar.
     * @return array Una col·lecció d'objectes de tipus Acta
     */
    function getActasQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $oActaSet = new core\Set();
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorActa.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('acta' => $aDades['acta']);
            $oActa = new Acta($a_pkey);
            $oActaSet->add($oActa);
        }
        return $oActaSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus Acta
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array Una col·lecció d'objectes de tipus Acta
     */
    function getActas($aWhere = array(), $aOperators = array())
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $oActaSet = new core\Set();
        $oCondicion = new core\Condicion();
        $aCondi = array();
        foreach ($aWhere as $camp => $val) {
            if ($camp == '_ordre') continue;
            $sOperador = isset($aOperators[$camp]) ? $aOperators[$camp] : '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) $aCondi[] = $a;
            // operadores que no requieren valores
            if ($sOperador == 'BETWEEN' || $sOperador == 'IS NULL' || $sOperador == 'IS NOT NULL' || $sOperador == 'OR') unset($aWhere[$camp]);
            if ($sOperador == 'IN' || $sOperador == 'NOT IN') unset($aWhere[$camp]);
            if ($sOperador == 'TXT') unset($aWhere[$camp]);
        }
        $sCondi = implode(' AND ', $aCondi);
        if ($sCondi != '') $sCondi = " WHERE " . $sCondi;
        if (isset($GLOBALS['oGestorSessioDelegación'])) {
            $sLimit = $GLOBALS['oGestorSessioDelegación']->getLimitPaginador('a_actividades', $sCondi, $aWhere);
        } else {
            $sLimit = '';
        }
        if ($sLimit === false) return;
        $sOrdre = '';
        if (isset($aWhere['_ordre']) && $aWhere['_ordre'] != '') $sOrdre = ' ORDER BY ' . $aWhere['_ordre'];
        if (isset($aWhere['_ordre'])) unset($aWhere['_ordre']);
        $sQry = "SELECT * FROM $nom_tabla " . $sCondi . $sOrdre . $sLimit;
        //echo "Query: $sQry<br>";
        //print_r($aWhere);
        if (($oDblSt = $oDbl->prepare($sQry)) === false) {
            $sClauError = 'GestorActa.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClauError = 'GestorActa.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('acta' => $aDades['acta']);
            $oActa = new Acta($a_pkey);
            $oActaSet->add($oActa);
        }
        return $oActaSet->getTot();
    }

    /* METODES PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}

?>
