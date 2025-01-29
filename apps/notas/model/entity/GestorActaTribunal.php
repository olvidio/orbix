<?php

namespace notas\model\entity;

use core\ClaseGestor;
use core\Condicion;
use core\ConfigGlobal;
use core\Set;

/**
 * GestorActaTribunal
 *
 * Classe per gestionar la llista d'objectes de la clase ActaTribunal
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
class GestorActaTribunal extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    
    function __construct()
    {
        // Si es cr, se mira en todas:
        if (ConfigGlobal::mi_ambito() === 'rstgr') {
            $oDbl = $GLOBALS['oDBP'];
            $this->setoDbl($oDbl);
            $this->setNomTabla('e_actas_tribunal');
        } else {
            $oDbl = $GLOBALS['oDB'];
            $this->setoDbl($oDbl);
            $this->setNomTabla('e_actas_tribunal_dl');
        }

    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/


    /**
     * retorna JSON llista d'examinadors
     * des del 2020 (perque els d'abans són amb llatí)
     *
     * @param string sQuery la query a executar.
     * @return false Json
     */
    function getJsonExaminadores($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (!empty($sQuery)) {
            $sCondi = "WHERE public.sin_acentos(examinador::text)  ~* public.sin_acentos('$sQuery'::text)
                        AND substring(acta, '\/(\d{2})')::integer > 19 ";
        } else {
            $sCondi = "WHERE substring(acta, '\/(\d{2})')::integer > 19 ";
        }
        $sOrdre = " ORDER BY examinador";
        $sLimit = " LIMIT 25";
        $sQry = "SELECT DISTINCT examinador FROM $nom_tabla " . $sCondi . $sOrdre . $sLimit;
        //echo "qry: $sQry<br>";
        if (($oDbl->query($sQry)) === false) {
            $sClauError = 'GestorActaTribunalDl.examinador';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $json = '[';
        $i = 0;
        foreach ($oDbl->query($sQry) as $aDades) {
            $i++;
            $json .= ($i > 1) ? ',' : '';
            $json .= "{\"label\":\"" . $aDades['examinador'] . "\"}";
        }
        $json .= ']';
        return $json;
    }


    /**
     * retorna l'array d'objectes de tipus ActaTribunal
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return false Una col·lecció d'objectes de tipus ActaTribunal
     */
    function getActasTribunales($aWhere = array(), $aOperators = array())
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $oActaTribunalSet = new Set();
        $oCondicion = new Condicion();
        $aCondi = array();
        foreach ($aWhere as $camp => $val) {
            if ($camp === '_ordre') {
                continue;
            }
            $sOperador = isset($aOperators[$camp]) ? $aOperators[$camp] : '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) {
                $aCondi[] = $a;
            }
            // operadores que no requieren valores
            if ($sOperador === 'BETWEEN' || $sOperador === 'IS NULL' || $sOperador === 'IS NOT NULL' || $sOperador === 'OR') {
                unset($aWhere[$camp]);
            }
            if ($sOperador === 'IN' || $sOperador === 'NOT IN') {
                unset($aWhere[$camp]);
            }
            if ($sOperador === 'TXT') {
                unset($aWhere[$camp]);
            }
        }
        $sCondi = implode(' AND ', $aCondi);
        if ($sCondi != '') {
            $sCondi = " WHERE " . $sCondi;
        }
        if (isset($GLOBALS['oGestorSessioDelegación'])) {
            $sLimit = $GLOBALS['oGestorSessioDelegación']->getLimitPaginador('a_actividades', $sCondi, $aWhere);
        } else {
            $sLimit = '';
        }
        if ($sLimit === FALSE) {
            return FALSE;
        }
        $sOrdre = '';
        if (isset($aWhere['_ordre']) && $aWhere['_ordre'] != '') {
            $sOrdre = ' ORDER BY ' . $aWhere['_ordre'];
        }
        if (isset($aWhere['_ordre'])) {
            unset($aWhere['_ordre']);
        }
        $sQry = "SELECT * FROM $nom_tabla " . $sCondi . $sOrdre . $sLimit;
        if (($oDblSt = $oDbl->prepare($sQry)) === FALSE) {
            $sClauError = 'GestorActaTribunal.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        if (($oDblSt->execute($aWhere)) === FALSE) {
            $sClauError = 'GestorActaTribunal.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = ['id_schema' => $aDades['id_schema'],
                'id_item' => $aDades['id_item'],
            ];
            // Si es cr, se mira en todas:
            if (ConfigGlobal::mi_ambito() === 'rstgr') {
                $oActaTribunal = new ActaTribunal($a_pkey);
            } else {
                $oActaTribunal = new ActaTribunalDl($a_pkey);
            }
            $oActaTribunalSet->add($oActaTribunal);
        }
        return $oActaTribunalSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}