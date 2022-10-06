<?php

namespace casas\model\entity;

use core;
use web\DateTimeLocal;

/**
 * GestorUbiGasto
 *
 * Classe per gestionar la llista d'objectes de la clase UbiGasto
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 26/6/2019
 */
class GestorUbiGasto extends core\ClaseGestor
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
        $oDbl = $GLOBALS['oDBC'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('du_gastos_dl');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna un integer amb la suma de les despeses en el periode.
     *
     * @param integer $id_ubi
     * @param DateTimeLocal $oInicio
     * @param DateTimeLocal $oFin
     * @param integer $tipo
     * @return integer suma dels valors
     */
    function getSumaGastos($id_ubi, $tipo, $oInicio, $oFin)
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $inicioIso = $oInicio->getIso();
        $finIso = $oFin->getIso();
        $sQry = "SELECT SUM(cantidad) FROM $nom_tabla
                WHERE id_ubi=$id_ubi AND tipo=$tipo AND f_gasto BETWEEN '$inicioIso' AND '$finIso' ";
        if (($oDblSt = $oDbl->query($sQry)) === false) {
            $sClauError = 'GestorUbiGasto.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        return $oDblSt->fetchColumn();
    }

    /**
     * retorna l'array d'objectes de tipus UbiGasto
     *
     * @param string sQuery la query a executar.
     * @return array Una col·lecció d'objectes de tipus UbiGasto
     */
    function getUbiGastosQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $oUbiGastoSet = new core\Set();
        if (($oDbl->query($sQuery)) === FALSE) {
            $sClauError = 'GestorUbiGasto.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('id_item' => $aDades['id_item']);
            $oUbiGasto = new UbiGasto($a_pkey);
            $oUbiGastoSet->add($oUbiGasto);
        }
        return $oUbiGastoSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus UbiGasto
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array Una col·lecció d'objectes de tipus UbiGasto
     */
    function getUbiGastos($aWhere = array(), $aOperators = array())
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $oUbiGastoSet = new core\Set();
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
            $sClauError = 'GestorUbiGasto.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        if (($oDblSt->execute($aWhere)) === FALSE) {
            $sClauError = 'GestorUbiGasto.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_item' => $aDades['id_item']);
            $oUbiGasto = new UbiGasto($a_pkey);
            $oUbiGastoSet->add($oUbiGasto);
        }
        return $oUbiGastoSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}
