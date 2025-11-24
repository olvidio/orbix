<?php

namespace ubis\model\entity;

use core\ClaseGestor;
use core\Condicion;
use core\Set;

/**
 * GestorTarifa
 *
 * Classe per gestionar la llista d'objectes de la clase Tarifa
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 6/10/2022
 */
class GestorTarifaUbi extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    /**
     * Constructor de la classe.
     *
     * @return $gestor
     *
     */
    function __construct()
    {
        $oDbl = $GLOBALS['oDBC'];
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('du_tarifas');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    public function copiar($year, $id_ubi)
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $this->delete($year, $id_ubi);

        $any_anterior = $year - 1;
        $sWhere = "WHERE id_ubi = $id_ubi AND year = $any_anterior";
        $sQry = "SELECT * FROM $nom_tabla $sWhere";
        foreach ($oDbl->query($sQry) as $row) {
            $oTarifa = new TarifaUbi();
            $oTarifa->setId_ubi($id_ubi);
            $oTarifa->setId_tarifa($row['id_tarifa']);
            $oTarifa->setYear($year);
            $oTarifa->setId_serie($row['id_serie']);
            $oTarifa->setCantidad($row['cantidad']);
            $oTarifa->setObserv($row['observ']);
            if ($oTarifa->DBGuardar() === false) {
                echo _("hay un error, no se ha guardado");
                echo "\n" . $oTarifa->getErrorTxt();
            }
        }
    }

    private function delete($year, $id_ubi)
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sWhere = "WHERE id_ubi = $id_ubi AND year = $year";
        $sQry = "DELETE FROM $nom_tabla " . $sWhere;
        if (($oDblSt = $oDbl->query($sQry)) === FALSE) {
            $sClauError = 'GestorTarifa.delete';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        return TRUE;
    }

    /**
     * retorna l'array d'objectes de tipus Tarifa
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array|void
     */
    function getTarifas($aWhere = [], $aOperators = array())
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oTarifaSet = new Set();
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
            $sClauError = 'GestorTarifa.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        if (($oDblSt->execute($aWhere)) === FALSE) {
            $sClauError = 'GestorTarifa.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        foreach ($oDblSt as $aDades) {
            $id_item = $aDades['id_item'];
            $oTarifa = new TarifaUbi($id_item);
            $oTarifa->setAllAttributes($aDades);
            $oTarifaSet->add($oTarifa);
        }
        return $oTarifaSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}
