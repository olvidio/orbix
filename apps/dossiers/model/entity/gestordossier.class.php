<?php
namespace dossiers\model\entity;

use core;

/**
 * GestorDossier
 *
 * Classe per gestionar la llista d'objectes de la clase Dossier
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 25/11/2014
 */
class GestorDossier extends core\ClaseGestor
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
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('d_dossiers_abiertos');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna l'array d'objectes de tipus Dossier
     *
     * @param string sQuery la query a executar.
     * @return array Una col·lecció d'objectes de tipus Dossier
     */
    function DossiersNotEmpty($pau = '', $id = '')
    {
        $esquema = core\ConfigGlobal::mi_region_dl();
        $oDbl = $this->getoDbl();
        $oDossierSet = new core\Set();
        $gesTipoDossier = new GestorTipoDossier();
        $cTiposDossier = $gesTipoDossier->getTiposDossiers(array('tabla_from' => $pau));
        $db_anterior = 0;
        foreach ($cTiposDossier as $oTipoDossier) {
            $id_tipo_dossier = $oTipoDossier->getId_tipo_dossier();
            $tabla_to = $oTipoDossier->getTabla_to();
            $campo_to = $oTipoDossier->getCampo_to();
            $db = $oTipoDossier->getDb();
            // Cambiar la conexión a la DB si está en otra:
            if ($db != $db_anterior) {
                $this->cambiarConexion($db);
                $oDbl = $this->getoDbl();
            }
            //comprobar que la tabla existe
            if (empty($tabla_to)) {
                continue;
            }
            $sQry = "SELECT to_regclass('\"$esquema\".$tabla_to')";
            $exist = $oDbl->query($sQry)->fetchColumn();
            if (empty($exist)) {
                $db_anterior = $db;
                continue;
            }
            //miro si tiene contenido
            $sQuery = "SELECT * FROM $tabla_to WHERE $campo_to = $id LIMIT 2";
            if (($oDblSt = $oDbl->query($sQuery)) === false) {
                $sClauError = 'GestorDossier.comprobar.query';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
            if ($oDblSt->rowCount() > 0) {
                $a_pkey = array('tabla' => $pau,
                    'id_pau' => $id,
                    'id_tipo_dossier' => $id_tipo_dossier);
                $oDossier = new Dossier($a_pkey);
                $oDossier->DBCarregar();
                $oDossierSet->add($oDossier);
            }
            $db_anterior = $db;
        }
        // Volver la conexión al orignal, por si acaso.
        $this->cambiarConexion(TipoDossier::DB_INTERIOR);

        return $oDossierSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus Dossier
     *
     * @param string sQuery la query a executar.
     * @return array Una col·lecció d'objectes de tipus Dossier
     */
    function getDossiersQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $oDossierSet = new core\Set();
        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorDossier.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('tabla' => $aDades['tabla'],
                'id_pau' => $aDades['id_pau'],
                'id_tipo_dossier' => $aDades['id_tipo_dossier']);
            $oDossier = new Dossier($a_pkey);
            $oDossierSet->add($oDossier);
        }
        return $oDossierSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus Dossier
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array Una col·lecció d'objectes de tipus Dossier
     */
    function getDossiers($aWhere = array(), $aOperators = array())
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $oDossierSet = new core\Set();
        $oCondicion = new core\Condicion();
        $aCondi = array();
        foreach ($aWhere as $camp => $val) {
            if ($camp == '_ordre') continue;
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
            $sClauError = 'GestorDossier.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClauError = 'GestorDossier.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('tabla' => $aDades['tabla'],
                'id_pau' => $aDades['id_pau'],
                'id_tipo_dossier' => $aDades['id_tipo_dossier']);
            $oDossier = new Dossier($a_pkey);
            $oDossierSet->add($oDossier);
        }
        return $oDossierSet->getTot();
    }

    /* METODES PROTECTED --------------------------------------------------------*/

    private function cambiarConexion($db)
    {
        switch ($db) {
            case TipoDossier::DB_COMUN:
                $oDbl = $GLOBALS['oDBC'];
                $this->setoDbl($oDbl);
                break;
            case TipoDossier::DB_INTERIOR:
                $oDbl = $GLOBALS['oDB'];
                $this->setoDbl($oDbl);
                break;
            case TipoDossier::DB_EXTERIOR:
                $oDbl = $GLOBALS['oDBE'];
                $this->setoDbl($oDbl);
                break;
        }
    }
    /* MÉTODOS GET y SET --------------------------------------------------------*/
}

?>
