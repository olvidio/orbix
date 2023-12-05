<?php
namespace ubis\model\entity;

use core;
use web;
use core\ConfigGlobal;

/**
 * GestorCentro
 *
 * Classe per gestionar la llista d'objectes de la clase Centro
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/09/2010
 */
class GestorCentro extends core\ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     * @return GestorCentro
     *
     */
    function __construct()
    {
        if (ConfigGlobal::is_dmz()) {
            $oDbl = $GLOBALS['oDBEP'];
            $oDbl_Select = $GLOBALS['oDBEP_Select'];
            $this->setoDbl($oDbl);
            $this->setoDbl_Select($oDbl_Select);
            $this->setNomTabla('u_centros');
        } else {
            $oDbl = $GLOBALS['oDBP'];
            $this->setoDbl($oDbl);
            $this->setoDbl_Select($oDbl);
            $this->setNomTabla('u_centros');
        }
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna un array Els posibles centres
     *
     * @return array
     */
    function getArrayCentros()
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $orden = 'nombre_ubi';
        $sCondicion = "WHERE status = 't'";
        $sQuery = "SELECT id_ubi, nombre_ubi
				FROM $nom_tabla
				$sCondicion
				ORDER BY $orden";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorCentro.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aCentros = [];
        foreach ($oDbl->query($sQuery) as $row) {
            $id_ubi = $row['id_ubi'];
            $nombre_ubi = $row['nombre_ubi'];

            $aCentros[$id_ubi] = $nombre_ubi;
        }

        return $aCentros;
    }

    /**
     * retorna un objecte del tipus Desplegable
     * Els posibles centres
     *
     * @return object Desplegable
     */
    function getListaCentros($sCondicion = '', $orden = '')
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        if (empty($orden)) {
            $orden = 'nombre_ubi';
        }
        if (empty($sCondicion)) $sCondicion = "WHERE status = 't'";
        $sQuery = "SELECT id_ubi, nombre_ubi
				FROM $nom_tabla
				$sCondicion
				ORDER BY $orden";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorCentro.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        return new web\Desplegable('', $oDblSt, '', true);
    }

    /**
     * retorna un objecte del tipus PDO (base de dades)
     * Els posibles centres
     *
     * @param string optional $sCondicion Condició de búsqueda (amb el WHERE).
     * @return object consulta PDO
     */
    function getPosiblesCentros($sCondicion = '')
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_ubi, nombre_ubi
				FROM $nom_tabla
				$sCondicion
				ORDER BY nombre_ubi";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorUbi.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        //return new web\Desplegable('',$oDblSt,'',true);
        return $oDblSt;
    }

    /**
     * retorna l'array d'objectes de tipus Centro
     *
     * @param string sQuery la query a executar.
     * @return array Una col·lecció d'objectes de tipus Centro
     */
    function getCentrosQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $oCentroSet = new core\Set();
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorCentro.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $clasename = get_class($this);
        $nomClase = join('', array_slice(explode('\\', $clasename), -1));
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('iid_ubi' => $aDades['id_ubi']);
            switch ($nomClase) {
                case 'GestorCentroDl':
                    $oCentro = new CentroDl($a_pkey);
                    break;
                case 'GestorCentroEllas':
                    $oCentro = new CentroEllas($a_pkey);
                    break;
                default:
                    $oCentro = new Centro($a_pkey);
            }
            $oCentroSet->add($oCentro);
        }
        return $oCentroSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus Centro
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array Una col·lecció d'objectes de tipus Centro
     */
    function getCentros($aWhere = array(), $aOperators = array())
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oCentroSet = new core\Set();
        $oCondicion = new core\Condicion();
        $aCondi = array();
        foreach ($aWhere as $camp => $val) {
            if ($camp === '_ordre') continue;
            $sOperador = isset($aOperators[$camp]) ? $aOperators[$camp] : '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) $aCondi[] = $a;
            // operadores que no requieren valores
            if ($sOperador == 'BETWEEN' || $sOperador == 'IS NULL' || $sOperador == 'IS NOT NULL' || $sOperador == 'OR') unset($aWhere[$camp]);
            if ($sOperador == 'IN' || $sOperador == 'NOT IN') unset($aWhere[$camp]);
            if ($sOperador == 'TXT') unset($aWhere[$camp]);
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
            $sClauError = 'GestorCentro.listar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClauError = 'GestorCentro.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }

        $clasename = get_class($this);
        $nomClase = join('', array_slice(explode('\\', $clasename), -1));
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_ubi' => $aDades['id_ubi']);
            switch ($nomClase) {
                case 'GestorCentroDl':
                    $oCentro = new CentroDl($a_pkey);
                    break;
                case 'GestorCentroEllas':
                    $oCentro = new CentroEllas($a_pkey);
                    break;
                default:
                    $oCentro = new Centro($a_pkey);
            }
            $oCentroSet->add($oCentro);
        }
        return $oCentroSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/

}
