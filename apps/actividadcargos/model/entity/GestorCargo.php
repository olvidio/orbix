<?php
namespace actividadcargos\model\entity;

use core;
use core\Condicion;
use core\Set;
use web;

/**
 * GestorCargo
 *
 * Classe per gestionar la llista d'objectes de la clase cargo
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 19/11/2014
 */
class GestorCargo extends core\ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    
    function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('xd_orden_cargo');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * Retorna un array amb els id_cargo de un tipo de cargo.
     *
     * @param string $tipo_cargo
     * @return array $aIdCargo[$id_cargo] = $cargo;
     */
    public function getArrayCargosDeTipo($tipo_cargo = 'sacd')
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_cargo,cargo 
                FROM $nom_tabla
                WHERE tipo_cargo = '$tipo_cargo' 
                ORDER BY orden_cargo";
        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorCargo.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aIdCargo = array();
        foreach ($oDbl->query($sQuery) as $aDades) {
            $id_cargo = $aDades['id_cargo'];
            $cargo = $aDades['cargo'];
            $aIdCargo[$id_cargo] = $cargo;
        }
        return $aIdCargo;

    }

    /**
     * retorna un objecte del tipus Desplegable
     * Els posibles cargos.
     *
     * @return array Una Llista
     */
    function getListaCargos()
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_cargo,cargo FROM $nom_tabla ORDER BY orden_cargo";
        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorCargo.lista';
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
     * retorna l'array d'objectes de tipus cargo
     *
     * @param string sQuery la query a executar.
     * @return array Una col·lecció d'objectes de tipus cargo
     */
    function getCargosQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $ocargoSet = new Set();
        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorCargo.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('id_cargo' => $aDades['id_cargo']);
            $ocargo = new Cargo($a_pkey);
            $ocargoSet->add($ocargo);
        }
        return $ocargoSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus cargo
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array Una col·lecció d'objectes de tipus cargo
     */
    function getCargos($aWhere = array(), $aOperators = array())
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $ocargoSet = new Set();
        $oCondicion = new Condicion();
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
            $sClauError = 'GestorCargo.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClauError = 'GestorCargo.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_cargo' => $aDades['id_cargo']);
            $ocargo = new Cargo($a_pkey);
            $ocargoSet->add($ocargo);
        }
        return $ocargoSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}
