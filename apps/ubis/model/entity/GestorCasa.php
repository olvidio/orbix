<?php
namespace ubis\model\entity;

use core\ClaseGestor;
use core\Condicion;
use core\Set;

/**
 * GestorCasa
 *
 * Classe per gestionar la llista d'objectes de la clase Casa
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/09/2010
 */
class GestorCasa extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     * @return GestorCasa
     *
     */
    function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('u_cdc');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna un array
     * Les posibles cases
     *
     * @param string optional $sCondicion Condició de búsqueda (amb el WHERE).
     * @return array|false
     */
    function getArrayPosiblesCasas($sCondicion = '')
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_ubi, nombre_ubi
				FROM $nom_tabla
				$sCondicion
				ORDER BY nombre_ubi";
        //echo "q: $sQuery<br>";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorCasa.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }

        $a_casa = [];
        foreach ($oDbl->query($sQuery) as $row) {
            $id_ubi = $row['id_ubi'];
            $nombre_ubi = $row['nombre_ubi'];

            $a_casa[$id_ubi] = $nombre_ubi;
        }

        return $a_casa;
    }

    /**
     * retorna un objecte del tipus PDO (base de dades)
     * Les posibles cases
     *
     * @param string optional $sCondicion Condició de búsqueda (amb el WHERE).
     * @return false|object
     */
    function getPosiblesCasas($sCondicion = '')
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_ubi, nombre_ubi
				FROM $nom_tabla
				$sCondicion
				ORDER BY nombre_ubi";
        //echo "q: $sQuery<br>";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorCasa.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        //return new Desplegable('',$oDblSt,'',true);
        return $oDblSt;
    }

    /**
     * retorna l'array d'objectes de tipus Casa
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array|void
     */
    function getCasas($aWhere = [], $aOperators = array())
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oCasaSet = new Set();
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
            $sClauError = 'GestorCasa.llistar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClauError = 'GestorCasa.llistar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_ubi' => $aDades['id_ubi']);
            $oCasa = new Casa($a_pkey);
            $oCasaSet->add($oCasa);
        }
        return $oCasaSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/

}
