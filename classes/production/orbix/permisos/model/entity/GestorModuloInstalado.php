<?php

namespace permisos\model\entity;

use core\ClaseGestor;
use core\Condicion;
use core\Set;
use web\Desplegable;

/**
 * GestorModuloInstalado
 *
 * Classe per gestionar la llista d'objectes de la clase ModuloInstalado
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 15/12/2014
 */
class GestorModuloInstalado extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    
    function __construct()
    {
        $oDbl = $GLOBALS['oDBE'];
        $oDbl_Select = $GLOBALS['oDBE_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('m0_mods_installed_dl');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/


    /**
     * retorna un objecte del tipus Desplegable
     * Els posibles modulos
     *
     * @return array|false
     */
    function getListaModulosInstalados()
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_mod, nom
				FROM $nom_tabla
				ORDER BY nom";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorTipoCasa.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        return new Desplegable('', $oDblSt, '', true);
    }

    /**
     * retorna l'array d'objectes de tipus ModuloInstalado
     *
     * @param string sQuery la query a executar.
     * @return array|false
     */
    function getModulosInstaladosQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $oModuloInstaladoSet = new Set();
        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorModuloInstalado.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('id_mod' => $aDades['id_mod']);
            $oModuloInstalado = new ModuloInstalado($a_pkey);
            $oModuloInstaladoSet->add($oModuloInstalado);
        }
        return $oModuloInstaladoSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus ModuloInstalado
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array|void
     */
    function getModulosInstalados($aWhere = [], $aOperators = array())
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oModuloInstaladoSet = new Set();
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
        if ($sLimit === false) return;
        $sOrdre = '';
        if (isset($aWhere['_ordre']) && $aWhere['_ordre'] != '') $sOrdre = ' ORDER BY ' . $aWhere['_ordre'];
        if (isset($aWhere['_ordre'])) unset($aWhere['_ordre']);
        $sQry = "SELECT * FROM $nom_tabla " . $sCondi . $sOrdre . $sLimit;
        if (($oDblSt = $oDbl->prepare($sQry)) === false) {
            $sClauError = 'GestorModuloInstalado.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClauError = 'GestorModuloInstalado.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_mod' => $aDades['id_mod']);
            $oModuloInstalado = new ModuloInstalado($a_pkey);
            $oModuloInstaladoSet->add($oModuloInstalado);
        }
        return $oModuloInstaladoSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}