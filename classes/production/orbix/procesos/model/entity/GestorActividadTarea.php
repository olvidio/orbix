<?php

namespace procesos\model\entity;

use core\ClaseGestor;
use core\Condicion;
use core\Set;
use web\Desplegable;

/**
 * GestorActividadTarea
 *
 * Classe per gestionar la llista d'objectes de la clase ActividadTarea
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/12/2018
 */
class GestorActividadTarea extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    
    function __construct()
    {
        $oDbl = $GLOBALS['oDBC'];
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('a_tareas');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/


    /**
     * retorna un objecte del tipus Desplegable
     * les posibles tareas d'una fase
     *
     * @param integer iid_fase la fase a la que pertany.
     * @return false|Desplegable
     */
    function getListaActividadTareas($iid_fase)
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $where_txt = '';
        $aOpciones = [];
        if (!empty($iid_fase)) {
            $where_txt = " WHERE id_fase = $iid_fase ";
            $sQuery = "SELECT  id_tarea, desc_tarea 
                           FROM $nom_tabla
                           $where_txt
                           ORDER BY id_fase,desc_tarea";
            if (($oDbl->query($sQuery)) === false) {
                $sClauError = 'GestorActividadTarea.lista';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
            foreach ($oDbl->query($sQuery) as $aClave) {
                $clave = $aClave[0];
                $val = $aClave[1];
                $aOpciones[$clave] = $val;
            }
        }
        return new Desplegable('', $aOpciones, '', true);
    }

    /**
     * retorna l'array d'objectes de tipus ActividadTarea
     *
     * @param string sQuery la query a executar.
     * @return array|false
     */
    function getActividadTareasQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $oActividadTareaSet = new Set();
        if (($oDbl->query($sQuery)) === FALSE) {
            $sClauError = 'GestorActividadTarea.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('id_tarea' => $aDades['id_tarea']);
            $oActividadTarea = new ActividadTarea($a_pkey);
            $oActividadTareaSet->add($oActividadTarea);
        }
        return $oActividadTareaSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus ActividadTarea
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array|void
     */
    function getActividadTareas($aWhere = [], $aOperators = array())
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oActividadTareaSet = new Set();
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
            $sClauError = 'GestorActividadTarea.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        if (($oDblSt->execute($aWhere)) === FALSE) {
            $sClauError = 'GestorActividadTarea.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_tarea' => $aDades['id_tarea']);
            $oActividadTarea = new ActividadTarea($a_pkey);
            $oActividadTareaSet->add($oActividadTarea);
        }
        return $oActividadTareaSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}
