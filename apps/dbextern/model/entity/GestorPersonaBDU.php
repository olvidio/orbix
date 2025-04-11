<?php

namespace dbextern\model\entity;

use core\ClaseGestor;
use core\Condicion;
use core\Set;

/**
 * GestorPersonaBDU
 *
 * Classe per gestionar la llista d'objectes de la clase PersonaBDU
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/02/2017
 */
class GestorPersonaBDU extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     * @return GestorPersonaBDU
     *
     */
    function __construct()
    {
        $oDbl = $GLOBALS['oDBP'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('tmp_bdu');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna l'array d'objectes de tipus PersonaBDU
     *
     * @param string sQuery la query a executar.
     * @return array Una col·lecció d'objectes de tipus PersonaBDU
     */
    function getPersonaBDUQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $oPersonaBDUSet = new Set();

        /*
            if (($oDblSt = $oDbl->query($sQuery)) === false) {
                $sClauError = 'GestorPersonaBDU.query';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
         *
         */
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('identif' => $aDades['identif']);
            $oPersonaBDU = new PersonaBDU($a_pkey);
            $oPersonaBDUSet->add($oPersonaBDU);
        }
        return $oPersonaBDUSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus PersonaBDU
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array|void
     */
    function getPersonaBDU($aWhere = array(), $aOperators = array())
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $oPersonaBDUSet = new Set();
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
        //echo "qry: $sQry<br>";
        if (($oDblSt = $oDbl->prepare($sQry)) === false) {
            $sClauError = 'GestorPersonaBDU.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClauError = 'GestorPersonaBDU.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('identif' => $aDades['identif']);
            $oPersonaBDU = new PersonaBDU($a_pkey);
            $oPersonaBDUSet->add($oPersonaBDU);
        }
        return $oPersonaBDUSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/


}
