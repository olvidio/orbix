<?php

namespace cambios\model\entity;

use core\ClaseGestor;
use core\Condicion;
use core\ConverterDate;
use core\Set;
use web\DateTimeLocal;

/**
 * GestorCambioUsuario
 *
 * Classe per gestionar la llista d'objectes de la clase CambioUsuario
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 17/4/2019
 */
class GestorCambioUsuario extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    
    function __construct()
    {
        $oDbl = $GLOBALS['oDBC'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('av_cambios_usuario');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * para eliminar avisos masivamente, anteriores a una fecha.
     *
     * @param DateTimeLocal|string df_fin.
     */
    public function eliminarHastaFecha($df_fin)
    {
        if (empty($df_fin)) return FALSE;
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $nom_tabla_cambios = 'public.av_cambios';

        $oConverter = new ConverterDate('date', $df_fin);
        $sf_fin = $oConverter->toPg();

        $sql = "DELETE FROM $nom_tabla u USING $nom_tabla_cambios c 
                WHERE u.id_schema_cambio=c.id_schema AND u.id_item_cambio=c.id_item_cambio 
                    AND c.timestamp_cambio < '$sf_fin'
                ";

        if ($oDbl->exec($sql) === FALSE) {
            $sClauError = 'CambioUsuario.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        return TRUE;
    }

    /**
     * retorna l'array d'objectes de tipus CambioUsuario
     *
     * @param string sQuery la query a executar.
     * @return array|false
     */
    function getCambiosUsuarioQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $oCambioUsuarioSet = new Set();
        if (($oDbl->query($sQuery)) === FALSE) {
            $sClauError = 'GestorCambioUsuario.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('id_item' => $aDades['id_item']);
            $oCambioUsuario = new CambioUsuario($a_pkey);
            $oCambioUsuarioSet->add($oCambioUsuario);
        }
        return $oCambioUsuarioSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus CambioUsuario
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array|void
     */
    function getCambiosUsuario($aWhere = [], $aOperators = array())
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $oCambioUsuarioSet = new Set();
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
            $sClauError = 'GestorCambioUsuario.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        if (($oDblSt->execute($aWhere)) === FALSE) {
            $sClauError = 'GestorCambioUsuario.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_item' => $aDades['id_item']);
            $oCambioUsuario = new CambioUsuario($a_pkey);
            $oCambioUsuarioSet->add($oCambioUsuario);
        }
        return $oCambioUsuarioSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}
