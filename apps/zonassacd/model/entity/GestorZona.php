<?php

namespace zonassacd\model\entity;

use core\ClaseGestor;
use core\Condicion;
use core\Set;
use web\Desplegable;

/**
 * GestorZona
 *
 * Classe per gestionar la llista d'objectes de la clase Zona
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/03/2019
 */
class GestorZona extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    function __construct()
    {
        $oDbl = $GLOBALS['oDBE'];
        $oDbl_Select = $GLOBALS['oDBE_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('zonas');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    function isJefeZona(int $id_nom): bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        //SELECT EXISTS(SELECT 1 FROM contact WHERE id=12)
        $sQuery = "SELECT EXISTS(SELECT 1
					FROM $nom_tabla
					WHERE id_nom = $id_nom)";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorZona.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }

        $row = $oDblSt->fetch();
        return $row[0];
    }


    function getArrayZonas($iid_nom_jefe = ''): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sCondicion = '';
        if (!empty($iid_nom_jefe)) {
            $sCondicion = "WHERE id_nom = $iid_nom_jefe";
        }
        $sQuery = "SELECT id_zona, nombre_zona
					FROM $nom_tabla $sCondicion
					ORDER BY orden";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorZona.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aOpciones = [];
        foreach ($oDblSt as $aDades) {
            $id_zona = $aDades['id_zona'];
            $nombre_zona = $aDades['nombre_zona'];
            $aOpciones[$id_zona] = $nombre_zona;
        }
        return $aOpciones;
    }

    /**
     * retorna l'array d'objectes de tipus Zona
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array|void
     */
    function getZonas($aWhere = [], $aOperators = array())
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oZonaSet = new Set();
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
            $sClauError = 'GestorZona.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        if (($oDblSt->execute($aWhere)) === FALSE) {
            $sClauError = 'GestorZona.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_zona' => $aDades['id_zona']);
            $oZona = new Zona($a_pkey);
            $oZonaSet->add($oZona);
        }
        return $oZonaSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}
