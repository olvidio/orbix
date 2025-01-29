<?php

namespace personas\model\entity;

use core\ClaseGestor;
use core\Condicion;
use core\Set;

/**
 * GestorTelecoPersona
 *
 * Classe per gestionar la llista d'objectes de la clase TelecoPersona
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */
class GestorTelecoPersona extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     * @return GestorTelecoPersona
     *
     */
    function __construct()
    {
        $oDbl = $GLOBALS['oDBP'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('d_teleco_personas');
    }
    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna l'array d'objectes de tipus TelecoPersona
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array|void
     */
    function getTelecos($aWhere = array(), $aOperators = array())
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $oTelecoPersonaSet = new Set();
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
            $sClauError = 'GestorTelecoPersona.llistar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClauError = 'GestorTelecoPersona.llistar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_item' => $aDades['id_item'],
                'id_nom' => $aDades['id_nom']);
            switch ($nom_tabla) {
                case 'd_teleco_personas':
                    $sClauError = 'GestorTelecoPersona.llistar';
                    exit("$sClauError, __LINE__, __FILE__");
                    break;
                case 'd_teleco_personas_dl':
                    $oTelecoPersona = new TelecoPersonaDl($a_pkey);
                    break;
                case 'd_teleco_personas_ex':
                    $oTelecoPersona = new TelecoPersonaEx($a_pkey);
                    break;
            }
            $oTelecoPersonaSet->add($oTelecoPersona);
        }
        return $oTelecoPersonaSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/
    /* MÉTODOS GET y SET --------------------------------------------------------*/
}
