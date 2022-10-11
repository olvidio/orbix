<?php

namespace encargossacd\model\entity;

use core;

/**
 * GestorEncargoSacdHorarioExcepcion
 *
 * Classe per gestionar la llista d'objectes de la clase EncargoSacdHorarioExcepcion
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/01/2019
 */
class GestorEncargoSacdHorarioExcepcion extends core\ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    
    function __construct()
    {
        $oDbl = $GLOBALS['oDBE'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('encargo_sacd_horario_excepcion');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna l'array d'objectes de tipus EncargoSacdHorarioExcepcion
     *
     * @param string sQuery la query a executar.
     * @return array Una col·lecció d'objectes de tipus EncargoSacdHorarioExcepcion
     */
    function getEncargoSacdHorarioExcepcionesQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $oEncargoSacdHorarioExcepcionSet = new core\Set();
        if (($oDbl->query($sQuery)) === FALSE) {
            $sClauError = 'GestorEncargoSacdHorarioExcepcion.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('id_item_ex' => $aDades['id_item_ex'],
                'id_enc' => $aDades['id_enc']);
            $oEncargoSacdHorarioExcepcion = new EncargoSacdHorarioExcepcion($a_pkey);
            $oEncargoSacdHorarioExcepcionSet->add($oEncargoSacdHorarioExcepcion);
        }
        return $oEncargoSacdHorarioExcepcionSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus EncargoSacdHorarioExcepcion
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array Una col·lecció d'objectes de tipus EncargoSacdHorarioExcepcion
     */
    function getEncargoSacdHorarioExcepciones($aWhere = array(), $aOperators = array())
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $oEncargoSacdHorarioExcepcionSet = new core\Set();
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
            $sClauError = 'GestorEncargoSacdHorarioExcepcion.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        if (($oDblSt->execute($aWhere)) === FALSE) {
            $sClauError = 'GestorEncargoSacdHorarioExcepcion.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_item_ex' => $aDades['id_item_ex'],
                'id_enc' => $aDades['id_enc']);
            $oEncargoSacdHorarioExcepcion = new EncargoSacdHorarioExcepcion($a_pkey);
            $oEncargoSacdHorarioExcepcionSet->add($oEncargoSacdHorarioExcepcion);
        }
        return $oEncargoSacdHorarioExcepcionSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}
