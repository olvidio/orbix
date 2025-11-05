<?php
namespace actividades\model\entity;

use core\ClaseGestor;
use core\Condicion;
use core\Set;
use web\Desplegable;

/**
 * Fitxer amb la Classe que gestiona la taula xa_nivel_stgr
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/12/2010
 */

/**
 * GestorNivelStgr
 *
 * Classe per gestionar la llista d'objectes de la clase NivelStgr
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/12/2010
 */
class GestorNivelStgr extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     * @return GestorNivelStgr
     *
     */
    function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('xa_nivel_stgr');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna un Array amb els nivells d'stgr
     *
     * @return array|false
     */
    function getArrayNivelesStgr()
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $sQuery = "SELECT nivel_stgr,desc_breve || '(' || desc_nivel || ')' FROM $nom_tabla ORDER BY orden";
        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorNivelStgr.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aOpciones = [];
        foreach ($oDbl->query($sQuery) as $aClave) {
            $clave = $aClave[0];
            $val = $aClave[1];
            $aOpciones[$clave] = $val;
        }
        return $aOpciones;
    }

    /**
     * retorna un objecte del tipus Desplegable
     * Els posibles nivells de stgr.
     *
     * @return array|false
     */
    function getListaNivelesStgr()
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT nivel_stgr,desc_breve || '(' || desc_nivel || ')' FROM $nom_tabla ORDER BY orden";
        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorNivelStgr.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aOpciones = [];
        foreach ($oDbl->query($sQuery) as $aClave) {
            $clave = $aClave[0];
            $val = $aClave[1];
            $aOpciones[$clave] = $val;
        }
        return new Desplegable('', $aOpciones, '', true);
    }

    /**
     * retorna l'array d'objectes de tipus NivelStgr
     *
     * @param string sQuery la query a executar.
     * @return array|false
     */
    function getNivelesStgrQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $oNivelStgrSet = new Set();
        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorNivelStgr.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('nivel_stgr' => $aDades['nivel_stgr']);
            $oNivelStgr = new NivelStgr($a_pkey);
            $oNivelStgrSet->add($oNivelStgr);
        }
        return $oNivelStgrSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus NivelStgr
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array|void
     */
    function getNivelesStgr($aWhere = [], $aOperators = array())
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oNivelStgrSet = new Set();
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
            $sClauError = 'GestorNivelStgr.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClauError = 'GestorNivelStgr.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('nivel_stgr' => $aDades['nivel_stgr']);
            $oNivelStgr = new NivelStgr($a_pkey);
            $oNivelStgrSet->add($oNivelStgr);
        }
        return $oNivelStgrSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/

}
