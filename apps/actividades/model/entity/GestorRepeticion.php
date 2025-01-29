<?php
namespace actividades\model\entity;

use core\ClaseGestor;
use core\Condicion;
use core\Set;
use web\Desplegable;

/**
 * GestorRepeticion
 *
 * Classe per gestionar la llista d'objectes de la clase Repeticion
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 04/02/2011
 */
class GestorRepeticion extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     * @return GestorRepeticion
     *
     */
    function __construct()
    {
        // constructor buit
        $oDbl = $GLOBALS['oDBPC'];
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('xa_tipo_repeticion');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna un array
     * Els posibles tipus de repetició
     *
     * @return array|false
     */
    function getArrayRepeticion()
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_repeticion, repeticion
				FROM $nom_tabla
				ORDER BY repeticion";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorRepeticion.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aRepeticion = [];
        foreach ($oDblSt as $aDades) {
            $id_repeticion = $aDades['id_repeticion'];
            $repeticion = $aDades['repeticion'];
            $aRepeticion[$id_repeticion] = $repeticion;
        }
        return $aRepeticion;
    }

    /**
     * retorna un objecte del tipus Desplegable
     * Els posibles tipus de repetició
     *
     * @return false|object
     */
    function getListaRepeticion()
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_repeticion, repeticion
				FROM $nom_tabla
				ORDER BY repeticion";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorRepeticion.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        return new Desplegable('', $oDblSt, '', true);
    }

    /**
     * retorna l'array d'objectes de tipus Repeticion
     *
     * @param string sQuery la query a executar.
     * @return array|false
     */
    function getRepeticionesQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $oRepeticionSet = new Set();
        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorRepeticion.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('id_repeticion' => $aDades['id_repeticion']);
            $oRepeticion = new Repeticion($a_pkey);
            $oRepeticionSet->add($oRepeticion);
        }
        return $oRepeticionSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus Repeticion
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array|void
     */
    function getRepeticiones($aWhere = array(), $aOperators = array())
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oRepeticionSet = new Set();
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
            $sClauError = 'GestorRepeticion.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClauError = 'GestorRepeticion.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_repeticion' => $aDades['id_repeticion']);
            $oRepeticion = new Repeticion($a_pkey);
            $oRepeticionSet->add($oRepeticion);
        }
        return $oRepeticionSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/

}
