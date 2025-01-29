<?php

namespace procesos\model\entity;

use core\ClaseGestor;
use core\Condicion;
use core\ConfigGlobal;
use core\Set;
use web\Desplegable;

/**
 * GestorProcesoTipo
 *
 * Classe per gestionar la llista d'objectes de la clase ProcesoTipo
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/12/2018
 */
class GestorProcesoTipo extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */



    function __construct()
    {
        $oDbl = $GLOBALS['oDBC'];
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('a_tipos_proceso');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna un objecte del tipus Desplegable
     * Els posibles Proceso Tipos.
     *
     * @return false|Desplegable
     */
    function getListaProcesoTipos()
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $mi_sfsv = ConfigGlobal::mi_sfsv();
        $sQuery = "SELECT id_tipo_proceso, nom_proceso 
                FROM $nom_tabla
                WHERE sfsv = $mi_sfsv 
                ORDER BY nom_proceso";
        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorProcesoTipo.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aOpciones = array();
        foreach ($oDbl->query($sQuery) as $aClave) {
            $clave = $aClave[0];
            $val = $aClave[1];
            $aOpciones[$clave] = $val;
        }
        return new Desplegable('', $aOpciones, '', true);
    }

    /**
     * retorna l'array d'objectes de tipus ProcesoTipo
     *
     * @param string sQuery la query a executar.
     * @return array|false
     */
    function getProcesoTiposQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $oProcesoTipoSet = new Set();
        if (($oDbl->query($sQuery)) === FALSE) {
            $sClauError = 'GestorProcesoTipo.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('id_tipo_proceso' => $aDades['id_tipo_proceso']);
            $oProcesoTipo = new ProcesoTipo($a_pkey);
            $oProcesoTipoSet->add($oProcesoTipo);
        }
        return $oProcesoTipoSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus ProcesoTipo
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array|void
     */
    function getProcesoTipos($aWhere = array(), $aOperators = array())
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oProcesoTipoSet = new Set();
        $oCondicion = new Condicion();
        $aCondi = array();
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
            $sClauError = 'GestorProcesoTipo.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        if (($oDblSt->execute($aWhere)) === FALSE) {
            $sClauError = 'GestorProcesoTipo.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_tipo_proceso' => $aDades['id_tipo_proceso']);
            $oProcesoTipo = new ProcesoTipo($a_pkey);
            $oProcesoTipoSet->add($oProcesoTipo);
        }
        return $oProcesoTipoSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}
