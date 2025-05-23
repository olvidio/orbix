<?php
namespace asistentes\model\entity;

use core\ClaseGestor;
use core\Condicion;
use core\Set;

/**
 * GestorAsistentePub
 *
 * Classe per gestionar la llista d'objectes de la clase AsistentePub
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */
class GestorAsistentePub extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    
    function __construct()
    {
        $oDbl = $GLOBALS['oDBEP'];
        $oDbl_Select = $GLOBALS['oDBEP_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('d_asistentes_de_paso');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna l'array de id_nom
     *
     * @param array id_activ de las actividades seleccionadas.
     * @return array|false
     */
    function getListaAsistentesDistintos($aId_activ = array())
    {
        $oDbl = $this->getoDbl_Select();
        $where = '';
        if (!empty($aId_activ)) {
            $where = 'WHERE id_activ=';
            $where .= implode(' OR id_activ=', $aId_activ);
        }
        $sQuery = "SELECT DISTINCT id_nom from publicv.d_asistentes_de_paso $where";
        //echo "qq: $sQuery<br>";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorAsistentePub.lista.id_nom';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aId_nom = [];
        foreach ($oDbl->query($sQuery) as $aDades) {
            $aId_nom[] = $aDades['id_nom'];
        }
        return $aId_nom;
    }

    /**
     * retorna l'array d'objectes de tipus AsistentePub
     *
     * @param string sQuery la query a executar.
     * @return array|false
     */
    function getAsistentesPubQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $oAsistentePubSet = new Set();
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorAsistentePub.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('id_activ' => $aDades['id_activ'],
                'id_nom' => $aDades['id_nom']);
            $oAsistentePub = new AsistentePub($a_pkey);
            $oAsistentePubSet->add($oAsistentePub);
        }
        return $oAsistentePubSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus AsistentePub
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array|void
     */
    function getAsistentesPub($aWhere = [], $aOperators = array())
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oAsistentePubSet = new Set();
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
            $sClauError = 'GestorAsistentePub.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClauError = 'GestorAsistentePub.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_activ' => $aDades['id_activ'],
                'id_nom' => $aDades['id_nom']);
            // Puede ser AsistenteEx, AsistenteOut o AsistenteIn.
            $oAsistentePub = new AsistentePub($a_pkey);
            $oAsistentePubSet->add($oAsistentePub);
        }
        return $oAsistentePubSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}
