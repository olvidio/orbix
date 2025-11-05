<?php
namespace profesores\model\entity;

use core\ClaseGestor;
use core\Condicion;
use core\Set;
use web\Desplegable;

/**
 * GestorProfesorTipo
 *
 * Classe per gestionar la llista d'objectes de la clase ProfesorTipo
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
class GestorProfesorTipo extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    
    function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('xe_tipo_profesor_stgr');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna un objecte del tipus Desplegable
     * Els posibles tipus de profesors.
     *
     * @return array|false
     */
    function getListaProfesorTipos()
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_tipo_profesor,tipo_profesor FROM $nom_tabla ORDER BY tipo_profesor";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorProfesorTipo.lista';
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
     * retorna l'array d'objectes de tipus ProfesorTipo
     *
     * @param string sQuery la query a executar.
     * @return array|false
     */
    function getProfesorTiposQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $oProfesorTipoSet = new Set();
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorProfesorTipo.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('id_tipo_profesor' => $aDades['id_tipo_profesor']);
            $oProfesorTipo = new ProfesorTipo($a_pkey);
            $oProfesorTipoSet->add($oProfesorTipo);
        }
        return $oProfesorTipoSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus ProfesorTipo
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array|void
     */
    function getProfesorTipos($aWhere = [], $aOperators = array())
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $oProfesorTipoSet = new Set();
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
            $sClauError = 'GestorProfesorTipo.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClauError = 'GestorProfesorTipo.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_tipo_profesor' => $aDades['id_tipo_profesor']);
            $oProfesorTipo = new ProfesorTipo($a_pkey);
            $oProfesorTipoSet->add($oProfesorTipo);
        }
        return $oProfesorTipoSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}

?>
