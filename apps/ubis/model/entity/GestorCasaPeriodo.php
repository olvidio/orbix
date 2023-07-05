<?php

namespace ubis\model\entity;

use core;

/**
 * GestorCasaPeriodo
 *
 * Classe per gestionar la llista d'objectes de la clase CasaPeriodo
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 12/11/2018
 */
class GestorCasaPeriodo extends core\ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    
    function __construct()
    {
        $oDbl = $GLOBALS['oDBC'];
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('du_periodos');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna un array per fer cerques més rapid.
     * Si la casa és només de sf o sv, no mira el dossier.
     *
     * @param integer id_ubi
     * @param web\DatetimeLocal inicio data d'inici del periode a comptar.
     * @param web\DatetimeLocal fin data de fi del periode a comptar.
     * @return array
     */
    function getArrayCasaPeriodos($id_ubi, $oInicio, $oFin)
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $inicio_iso = $oInicio->getIso();
        $fin_iso = $oFin->getIso();
        $sQuery = "SELECT to_char(f_ini,'YYYYMMDD') as iso_ini,to_char(f_fin,'YYYYMMDD') as iso_fin, sfsv
			FROM $nom_tabla
			WHERE id_ubi=$id_ubi AND f_fin > '$inicio_iso' AND f_ini <= '$fin_iso'
			ORDER BY f_ini
			";
        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorCasaPeriodo.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $a_periodos = array();
        foreach ($oDbl->query($sQuery) as $row) {
            $a_periodos[] = array('iso_ini' => $row['iso_ini'], 'iso_fin' => $row['iso_fin'], 'sfsv' => $row['sfsv']);
        }
        // si no hi ha resultat miro que l'ubi sigui només sf o sv.
        if (count($a_periodos) == 0) {
            $oCasa = new CasaDl($id_ubi);
            $sf = $oCasa->getSf();
            $sv = $oCasa->getSv();
            $oInicio->setTime(0, 0, 0);
            $isoIni = $oInicio->format('Ymd');
            $oFin->setTime(23, 59, 59);
            $isoFin = $oFin->format('Ymd');

            if ($sf === true && $sv === false) {
                $a_periodos[] = array('iso_ini' => $isoIni, 'iso_fin' => $isoFin, 'sfsv' => 2);
            }
            if ($sf === false && $sv === true) {
                $a_periodos[] = array('iso_ini' => $isoIni, 'iso_fin' => $isoFin, 'sfsv' => 1);
            }
        }
        return $a_periodos;
    }

    /**
     * retorna la suma dels dies d'ocupació d'una secció.
     *
     * @param integer iseccion 1=sv, 2= sf.
     * @param integer id_ubi
     * @param web\DateTimeLocal inicio data d'inici del periode a comptar.
     * @param web\DateTimeLocal fin data de fi del periode a comptar.
     * @return integer número de dies.
     */
    function getCasaPeriodosDias($iseccion, $id_ubi, $oInicio, $oFin)
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $inicio_iso = $oInicio->getIso();
        $fin_iso = $oFin->getIso();

        $sQuery = "SELECT SUM((date(f_fin)-date(f_ini))+1 )
			FROM $nom_tabla
			WHERE id_ubi=$id_ubi AND f_ini BETWEEN '$inicio_iso' AND '$fin_iso' AND sfsv=$iseccion
			";

        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorCasaPeriodo.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $num_dias = $oDblSt->fetchColumn();
        $num_dias = empty($num_dias) ? 0 : $num_dias;
        return $num_dias;
    }

    /**
     * retorna l'array d'objectes de tipus CasaPeriodo
     *
     * @param string sQuery la query a executar.
     * @return array Una col·lecció d'objectes de tipus CasaPeriodo
     */
    function getCasaPeriodosQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $oCasaPeriodoSet = new core\Set();
        if (($oDbl->query($sQuery)) === FALSE) {
            $sClauError = 'GestorCasaPeriodo.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('id_item' => $aDades['id_item']);
            $oCasaPeriodo = new CasaPeriodo($a_pkey);
            $oCasaPeriodoSet->add($oCasaPeriodo);
        }
        return $oCasaPeriodoSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus CasaPeriodo
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array Una col·lecció d'objectes de tipus CasaPeriodo
     */
    function getCasaPeriodos($aWhere = array(), $aOperators = array())
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oCasaPeriodoSet = new core\Set();
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
            $sClauError = 'GestorCasaPeriodo.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        if (($oDblSt->execute($aWhere)) === FALSE) {
            $sClauError = 'GestorCasaPeriodo.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_item' => $aDades['id_item']);
            $oCasaPeriodo = new CasaPeriodo($a_pkey);
            $oCasaPeriodoSet->add($oCasaPeriodo);
        }
        return $oCasaPeriodoSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}
