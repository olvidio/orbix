<?php

namespace cambios\model\entity;

use core\ClaseGestor;
use core\Condicion;
use core\Set;

/**
 * GestorCambioAnotado
 *
 * Classe per gestionar la llista d'objectes de la clase CambioAnotado
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 2/5/2019
 */
class GestorCambioAnotado extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */
    /**
     * corresponde a :sv, sf
     *
     * @var string
     */
    private $ubicacion;

    /* CONSTRUCTOR -------------------------------------------------------------- */


    
    function __construct()
    {
        $oDbl = $GLOBALS['oDBC'];
        $this->setoDbl($oDbl);
        //$this->setNomTabla('av_cambios_anotados_dl');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * Se añade esta funcion para cambiar de tabla. Si se tienen una instalación en
     * la dmz, hay desfases en la sincronización de la tabla y ocasiona algunos problemas.
     * Se tiene una tabla distinta para sv y sf.
     *
     * @param integer $server
     */
    public function setTabla($ubicacion)
    {
        $this->ubicacion = $ubicacion;
        if ($ubicacion === 'sv') {
            $this->setNomTabla('av_cambios_anotados_dl');
        }
        if ($ubicacion === 'sf') {
            $this->setNomTabla('av_cambios_anotados_dl_sf');
        }
    }

    /**
     * retorna l'array d'objectes de tipus CambioAnotado
     *
     * @param string sQuery la query a executar.
     * @return array|false
     */
    function getCambiosAnotadosQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $oCambioAnotadoSet = new Set();
        if (($oDbl->query($sQuery)) === FALSE) {
            $sClauError = 'GestorCambioAnotado.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('id_item' => $aDades['id_item']);
            $oCambioAnotado = new CambioAnotado($a_pkey);
            $oCambioAnotado->setTabla($this->ubicacion);
            $oCambioAnotadoSet->add($oCambioAnotado);
        }
        return $oCambioAnotadoSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus CambioAnotado
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array|void
     */
    function getCambiosAnotados($aWhere = [], $aOperators = array())
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $oCambioAnotadoSet = new Set();
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
            $sClauError = 'GestorCambioAnotado.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        if (($oDblSt->execute($aWhere)) === FALSE) {
            $sClauError = 'GestorCambioAnotado.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_item' => $aDades['id_item']);
            $oCambioAnotado = new CambioAnotado($a_pkey);
            $oCambioAnotado->setTabla($this->ubicacion);
            $oCambioAnotadoSet->add($oCambioAnotado);
        }
        return $oCambioAnotadoSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}
