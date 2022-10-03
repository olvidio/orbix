<?php

namespace cambios\model\entity;

use core;

/**
 * GestorCambioUsuarioObjetoPref
 *
 * Classe per gestionar la llista d'objectes de la clase CambioUsuarioObjetoPref
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 17/4/2019
 */
class GestorCambioUsuarioObjetoPref extends core\ClaseGestor
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
        $oDbl = $GLOBALS['oDBE'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('av_cambios_usuario_objeto_pref');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna l'array d'objectes de tipus CambioUsuarioObjetoPref
     *
     * @param string sQuery la query a executar.
     * @return array Una col·lecció d'objectes de tipus CambioUsuarioObjetoPref
     */
    function getCambioUsuarioObjetosPrefsQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $oCambioUsuarioObjetoPrefSet = new core\Set();
        if (($oDbl->query($sQuery)) === FALSE) {
            $sClauError = 'GestorCambioUsuarioObjetoPref.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('id_item_usuario_objeto' => $aDades['id_item_usuario_objeto']);
            $oCambioUsuarioObjetoPref = new CambioUsuarioObjetoPref($a_pkey);
            $oCambioUsuarioObjetoPrefSet->add($oCambioUsuarioObjetoPref);
        }
        return $oCambioUsuarioObjetoPrefSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus CambioUsuarioObjetoPref
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array Una col·lecció d'objectes de tipus CambioUsuarioObjetoPref
     */
    function getCambioUsuarioObjetosPrefs($aWhere = array(), $aOperators = array())
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $oCambioUsuarioObjetoPrefSet = new core\Set();
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
        if ($sLimit === FALSE) return;
        $sOrdre = '';
        if (isset($aWhere['_ordre']) && $aWhere['_ordre'] != '') $sOrdre = ' ORDER BY ' . $aWhere['_ordre'];
        if (isset($aWhere['_ordre'])) unset($aWhere['_ordre']);
        $sQry = "SELECT * FROM $nom_tabla " . $sCondi . $sOrdre . $sLimit;
        if (($oDblSt = $oDbl->prepare($sQry)) === FALSE) {
            $sClauError = 'GestorCambioUsuarioObjetoPref.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        if (($oDblSt->execute($aWhere)) === FALSE) {
            $sClauError = 'GestorCambioUsuarioObjetoPref.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_item_usuario_objeto' => $aDades['id_item_usuario_objeto']);
            $oCambioUsuarioObjetoPref = new CambioUsuarioObjetoPref($a_pkey);
            $oCambioUsuarioObjetoPrefSet->add($oCambioUsuarioObjetoPref);
        }
        return $oCambioUsuarioObjetoPrefSet->getTot();
    }

    /* METODES PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}
