<?php
namespace asignaturas\model\entity;

use core\ClaseGestor;
use core\Condicion;
use core\Set;
use web\Desplegable;

/**
 * GestorSector
 *
 * Classe per gestionar la llista d'objectes de la clase Sector
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/12/2010
 */
class GestorSector extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     * @return GestorSector
     *
     */
    function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('xe_sectores');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna un array
     * Els posibles sectors per departament.
     *
     * @return array|false
     */
    function getArraySectores()
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_sector,id_departamento FROM $nom_tabla ORDER BY id_departamento";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorSector.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aOpciones = [];
        foreach ($oDbl->query($sQuery) as $aClave) {
            $id_sector = $aClave[0];
            $id_departamento = $aClave[1];
            $a_1 = isset($aOpciones[$id_departamento]) ? $aOpciones[$id_departamento] : [];
            $aOpciones[$id_departamento] = array_merge($a_1, array($id_sector));
        }
        return $aOpciones;
    }

    /**
     * retorna un objecte del tipus Desplegable
     * Els posibles sectors.
     *
     * @return array|false
     */
    function getListaSectores()
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_sector,sector FROM $nom_tabla ORDER BY sector";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorSector.lista';
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
     * retorna l'array d'objectes de tipus Sector
     *
     * @param string sQuery la query a executar.
     * @return array|false
     */
    function getSectoresQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $oSectorSet = new Set();
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorSector.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('id_sector' => $aDades['id_sector']);
            $oSector = new Sector($a_pkey);
            $oSectorSet->add($oSector);
        }
        return $oSectorSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus Sector
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array|void
     */
    function getSectores($aWhere = [], $aOperators = array())
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oSectorSet = new Set();
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
            $sClauError = 'GestorSector.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClauError = 'GestorSector.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_sector' => $aDades['id_sector']);
            $oSector = new Sector($a_pkey);
            $oSectorSet->add($oSector);
        }
        return $oSectorSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/

}
