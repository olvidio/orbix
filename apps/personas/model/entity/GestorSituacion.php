<?php
namespace personas\model\entity;

use core;
use web;

/**
 * GestorSituacion
 *
 * Classe per gestionar la llista d'objectes de la clase Situacion
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 18/03/2014
 */
class GestorSituacion extends core\ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */
    /* CONSTRUCTOR -------------------------------------------------------------- */


    
    function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('xp_situacion');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna un objecte del tipus Desplegable
     * Els posibles situacions.
     *
     * @return array Una Llista
     */
    function getListaSituaciones($traslado = FALSE)
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $Condicion = '';
        if ($traslado) {
            $Condicion = " WHERE situacion IN ('A','D','E','L','T','X')";
        }
        $sQuery = "SELECT situacion,nombre_situacion FROM $nom_tabla $Condicion ORDER BY situacion";
        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorSituacion.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aOpciones = array();
        foreach ($oDbl->query($sQuery) as $aClave) {
            $clave = $aClave[0];
            $val = $aClave[1];
            $aOpciones[$clave] = $val;
        }
        return new web\Desplegable('', $aOpciones, '', true);
    }

    /**
     * retorna l'array d'objectes de tipus Situacion
     *
     * @param string sQuery la query a executar.
     * @return array Una col·lecció d'objectes de tipus Situacion
     */
    function getSituacionesQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $oSituacionSet = new core\Set();
        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorSituacion.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('situacion' => $aDades['situacion']);
            $oSituacion = new Situacion($a_pkey);
            $oSituacionSet->add($oSituacion);
        }
        return $oSituacionSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus Situacion
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array Una col·lecció d'objectes de tipus Situacion
     */
    function getSituaciones($aWhere = array(), $aOperators = array())
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $oSituacionSet = new core\Set();
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
        if (($oDblSt = $oDbl->prepare($sQry)) === false) {
            $sClauError = 'GestorSituacion.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClauError = 'GestorSituacion.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('situacion' => $aDades['situacion']);
            $oSituacion = new Situacion($a_pkey);
            $oSituacionSet->add($oSituacion);
        }
        return $oSituacionSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}

?>
