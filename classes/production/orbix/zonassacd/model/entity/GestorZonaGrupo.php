<?php

namespace zonassacd\model\entity;

use core\ClaseGestor;
use core\Condicion;
use core\Set;
use web\Desplegable;

/**
 * GestorZonaGrupo
 *
 * Classe per gestionar la llista d'objectes de la clase ZonaGrupo
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/03/2019
 */
class GestorZonaGrupo extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    
    function __construct()
    {
        $oDbl = $GLOBALS['oDBE'];
        $oDbl_Select = $GLOBALS['oDBE_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('zonas_grupos');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna un objecte del tipus Desplegable
     * Els posibles grups de zones
     *
     * @param string optional $sCondicion Condició de búsqueda (amb el WHERE).
     * @return array|false
     */
    function getListaZonaGrupos($sCondicion = '')
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_grupo, nombre_grupo
				FROM $nom_tabla
				$sCondicion
				ORDER BY orden";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorZonaGrupo.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        return new Desplegable('', $oDblSt, '', true);
    }

    /**
     * retorna l'array d'objectes de tipus ZonaGrupo
     *
     * @param string sQuery la query a executar.
     * @return array|false
     */
    function getZonasGrupoQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $oZonaGrupoSet = new Set();
        if (($oDbl->query($sQuery)) === FALSE) {
            $sClauError = 'GestorZonaGrupo.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('id_grupo' => $aDades['id_grupo']);
            $oZonaGrupo = new ZonaGrupo($a_pkey);
            $oZonaGrupoSet->add($oZonaGrupo);
        }
        return $oZonaGrupoSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus ZonaGrupo
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array|void
     */
    function getZonasGrupo($aWhere = [], $aOperators = array())
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oZonaGrupoSet = new Set();
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
            $sClauError = 'GestorZonaGrupo.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        if (($oDblSt->execute($aWhere)) === FALSE) {
            $sClauError = 'GestorZonaGrupo.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_grupo' => $aDades['id_grupo']);
            $oZonaGrupo = new ZonaGrupo($a_pkey);
            $oZonaGrupoSet->add($oZonaGrupo);
        }
        return $oZonaGrupoSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}
