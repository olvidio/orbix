<?php

namespace devel\model\entity;

use core\ClaseGestor;
use core\Condicion;
use core\Set;

/**
 * GestorMapId
 *
 * Classe per gestionar la llista d'objectes de la clase MapId
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 9/3/2020
 */
class GestorMapId extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    
    function __construct()
    {
        $oDbl = $GLOBALS['oDBRC'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('map_id');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna l'array d'objectes de tipus MapId
     *
     * @param string sQuery la query a executar.
     * @return array|false
     */
    function getMapIdsQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $oMapIdSet = new Set();
        if (($oDbl->query($sQuery)) === FALSE) {
            $sClauError = 'GestorMapId.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('objeto' => $aDades['objeto'],
                'id_resto' => $aDades['id_resto']);
            $oMapId = new MapId($a_pkey);
            $oMapIdSet->add($oMapId);
        }
        return $oMapIdSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus MapId
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array|void
     */
    function getMapIds($aWhere = [], $aOperators = array())
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $oMapIdSet = new Set();
        $oCondicion = new Condicion();
        $aCondi = [];
        foreach ($aWhere as $camp => $val) {
            if ($camp === '_ordre') continue;
            $sOperador = isset($aOperators[$camp]) ? $aOperators[$camp] : '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) $aCondi[] = $a;
            // operadores que no requieren valores
            if ($sOperador === 'BETWEEN' || $sOperador === 'IS NULL' || $sOperador === 'IS NOT NULL' || $sOperador === 'OR') unset($aWhere[$camp]);
            if ($sOperador === 'IN' || $sOperador === 'NOT IN') unset($aWhere[$camp]);
            if ($sOperador === 'TXT') unset($aWhere[$camp]);
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
            $sClauError = 'GestorMapId.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        if (($oDblSt->execute($aWhere)) === FALSE) {
            $sClauError = 'GestorMapId.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('objeto' => $aDades['objeto'],
                'id_resto' => $aDades['id_resto']);
            $oMapId = new MapId($a_pkey);
            $oMapIdSet->add($oMapId);
        }
        return $oMapIdSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}
