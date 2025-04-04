<?php
namespace usuarios\model\entity;


use core\ClaseGestor;
use core\Condicion;
use core\Set;
use web\Desplegable;

/**
 * GestorRole
 *
 * Classe per gestionar la llista d'objectes de la clase Role
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 16/01/2014
 */
class GestorRole extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     * @return GestorRole
     *
     */
    function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('aux_roles');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna un objecte del tipus Desplegable
     * Els posibles roles
     *
     * @param string sWhere condicion con el WHERE.
     * @return array|false
     */
    function getListaRoles($sWhere = '')
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_role, role
				FROM $nom_tabla $sWhere
				ORDER BY role";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorRole.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        return new Desplegable('', $oDblSt, '', true);
    }

    /**
     * retorna un array els posibles roles
     * (el nom convertit a minúscules) per nom => id
     *
     * @param string sWhere condicion con el WHERE.
     * @return array|false
     */
    function getArrayRoles($sWhere = '')
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_role, role
				FROM $nom_tabla $sWhere
				ORDER BY role";
        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorRole.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aRoles = [];
        foreach ($oDbl->query($sQuery) as $aDades) {
            $nom_role = strtolower($aDades['role']?? '');
            $aRoles[$nom_role] = $aDades['id_role'];
        }
        return $aRoles;
    }

    /**
     * retorna un array les pau dels Roles
     * (el nom convertit a minúscules) per nom => id
     *
     * @param string sWhere condicion con el WHERE.
     * @return array|false
     */
    function getArrayRolesPau()
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_role, pau
				FROM $nom_tabla WHERE pau IS NOT NULL
				ORDER BY id_role";
        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorRole.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aPauRoles = [];
        foreach ($oDbl->query($sQuery) as $aDades) {
            $nom_pau = strtolower($aDades['pau']?? '');
            $id_role = strtolower($aDades['id_role']);
            $aPauRoles[$id_role] = $nom_pau;
        }
        return $aPauRoles;
    }

    /**
     * retorna l'array d'objectes de tipus Role
     *
     * @param string sQuery la query a executar.
     * @return array|false
     */
    function getRolesQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $oRoleSet = new Set();
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorRole.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('id_role' => $aDades['id_role']);
            $oRole = new Role($a_pkey);
            $oRoleSet->add($oRole);
        }
        return $oRoleSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus Role
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array|void
     */
    function getRoles($aWhere = array(), $aOperators = array())
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oRoleSet = new Set();
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
            $sClauError = 'GestorRole.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClauError = 'GestorRole.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_role' => $aDades['id_role']);
            $oRole = new Role($a_pkey);
            $oRoleSet->add($oRole);
        }
        return $oRoleSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/

}

?>
