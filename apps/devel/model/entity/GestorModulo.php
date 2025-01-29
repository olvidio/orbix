<?php

namespace devel\model\entity;

use core\ClaseGestor;
use core\Condicion;
use core\Set;
use web\Desplegable;

/**
 * GestorModulo
 *
 * Classe per gestionar la llista d'objectes de la clase Modulo
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 15/12/2014
 */
class GestorModulo extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    
    function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('m0_modulos');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna un objecte del tipus Desplegable
     * Els posibles modulos
     *
     * @return array|false
     */
    function getListaModulos()
    {
        $oDbl = $this->getoDbl();
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
     * retorna l'array d'objectes de tipus Modulo
     *
     * @param string sQuery la query a executar.
     * @return array|false
     */
    function getModulosQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $oModuloSet = new Set();
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorModulo.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('id_mod' => $aDades['id_mod']);
            $oModulo = new Modulo($a_pkey);
            $oModuloSet->add($oModulo);
        }
        return $oModuloSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus Modulo
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array|void
     */
    function getModulos($aWhere = array(), $aOperators = array())
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $oModuloSet = new Set();
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
        if ($sLimit === false) return;
        $sOrdre = '';
        if (isset($aWhere['_ordre']) && $aWhere['_ordre'] != '') $sOrdre = ' ORDER BY ' . $aWhere['_ordre'];
        if (isset($aWhere['_ordre'])) unset($aWhere['_ordre']);
        $sQry = "SELECT * FROM $nom_tabla " . $sCondi . $sOrdre . $sLimit;
        if (($oDblSt = $oDbl->prepare($sQry)) === false) {
            $sClauError = 'GestorModulo.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClauError = 'GestorModulo.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_mod' => $aDades['id_mod']);
            $oModulo = new Modulo($a_pkey);
            $oModuloSet->add($oModulo);
        }
        return $oModuloSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}

?>