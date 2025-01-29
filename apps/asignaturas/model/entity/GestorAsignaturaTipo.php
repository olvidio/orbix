<?php
namespace asignaturas\model\entity;


use core\ClaseGestor;
use core\Condicion;
use core\Set;
use web\Desplegable;

/**
 * GestorAsignaturaTipo
 *
 * Classe per gestionar la llista d'objectes de la clase AsignaturaTipo
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/12/2010
 */
class GestorAsignaturaTipo extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     * @return GestorAsignaturaTipo
     *
     */
    function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('xa_tipo_asig');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna un objecte del tipus Desplegable
     * Els posibles tipus d'assignatures.
     *
     * @return array|false
     */
    function getListaAsignaturaTipos()
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_tipo,tipo_asignatura FROM $nom_tabla ORDER BY tipo_asignatura";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorAsignaturaTipo.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aOpciones = array();
        foreach ($oDbl->query($sQuery) as $aClave) {
            $clave = $aClave[0];
            $val = $aClave[1];
            $aOpciones[$clave] = $val;
        }
        return new Desplegable('', $aOpciones, '', true);
    }

    /**
     * retorna l'array d'objectes de tipus AsignaturaTipo
     *
     * @param string sQuery la query a executar.
     * @return array|false
     */
    function getAsignaturaTiposQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $oAsignaturaTipoSet = new Set();
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorAsignaturaTipo.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('id_tipo' => $aDades['id_tipo']);
            $oAsignaturaTipo = new AsignaturaTipo($a_pkey);
            $oAsignaturaTipoSet->add($oAsignaturaTipo);
        }
        return $oAsignaturaTipoSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus AsignaturaTipo
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array|void
     */
    function getAsignaturaTipos($aWhere = array(), $aOperators = array())
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oAsignaturaTipoSet = new Set();
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
            $sClauError = 'GestorAsignaturaTipo.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClauError = 'GestorAsignaturaTipo.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_tipo' => $aDades['id_tipo']);
            $oAsignaturaTipo = new AsignaturaTipo($a_pkey);
            $oAsignaturaTipoSet->add($oAsignaturaTipo);
        }
        return $oAsignaturaTipoSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/

}
