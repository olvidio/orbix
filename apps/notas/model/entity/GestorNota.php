<?php
namespace notas\model\entity;

use core\ClaseGestor;
use core\Condicion;
use core\Set;
use web\Desplegable;

/**
 * GestorNota
 *
 * Classe per gestionar la llista d'objectes de la clase Nota
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
class GestorNota extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    
    function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('e_notas_situacion');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/


    /**
     * retorna un array amb
     * Els posibles tipus de nota superada
     *
     * @param string sWhere condicion con el WHERE.
     * @return false llista de id_situacion
     */
    function getArrayNotasSuperadas($bsuperada = 't')
    {
        $oDbl = $this->getoDbl_Select();
        $sQuery = "SELECT id_situacion
				FROM e_notas_situacion 
				WHERE superada = '$bsuperada'
				ORDER BY id_situacion";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorNota.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDbl->query($sQuery) as $row) {
            $aDades[] = $row['id_situacion'];
        }
        return $aDades;
    }

    /**
     * retorna un objecte del tipus Desplegable
     * Els posibles tipus de nota
     *
     * @param string sWhere condicion con el WHERE.
     * @return false Una Llista
     */
    function getListaNotas($sWhere = '')
    {
        $oDbl = $this->getoDbl_Select();
        $sQuery = "SELECT id_situacion, descripcion
				FROM e_notas_situacion $sWhere
				ORDER BY id_situacion";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorNota.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        return new Desplegable('', $oDblSt, '', true);
    }


    /**
     * retorna l'array d'objectes de tipus Nota
     *
     * @param string sQuery la query a executar.
     * @return false Una col·lecció d'objectes de tipus Nota
     */
    function getNotasQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $oNotaSet = new Set();
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorNota.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('id_situacion' => $aDades['id_situacion']);
            $oNota = new Nota($a_pkey);
            $oNotaSet->add($oNota);
        }
        return $oNotaSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus Nota
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return void Una col·lecció d'objectes de tipus Nota
     */
    function getNotas($aWhere = [], $aOperators = array())
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oNotaSet = new Set();
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
            $sClauError = 'GestorNota.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClauError = 'GestorNota.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_situacion' => $aDades['id_situacion']);
            $oNota = new Nota($a_pkey);
            $oNotaSet->add($oNota);
        }
        return $oNotaSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}
