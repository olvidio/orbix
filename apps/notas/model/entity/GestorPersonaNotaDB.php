<?php
namespace notas\model\entity;

use function core\is_true;
use core;

/**
 * GestorPersonaNota
 *
 * Classe per gestionar la llista d'objectes de la clase PersonaNota
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
class GestorPersonaNotaDB extends core\ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    protected string $esquema_region_stgr;

    function __construct()
    {
        $oDbl = $GLOBALS['oDBP'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('e_notas');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna l'array d'objectes de tipus PersonaNota. Només les aprovades.
     *
     * @param integer id_nom  de la persona.
     * @return array Una col·lecció d'objectes de tipus PersonaNota
     */
    function getPersonaNotasSuperadas($id_nom, $nivel = '')
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $oPersonaNotaSet = new core\Set();

        $cond_nivel = '';
        if (is_true($nivel)) {
            $cond_nivel = "AND id_nivel >= 1100 AND id_nivel <= 2500 ";
        }

        $gesNotas = new GestorNota();
        $cNotas = $gesNotas->getNotas(array('superada' => 't'));
        $superadas_txt = '';
        foreach ($cNotas as $oNota) {
            $id_situacion = $oNota->getId_situacion();
            $superadas_txt .= !empty($superadas_txt) ? '|' : '';
            $superadas_txt .= $id_situacion;
        }

        $sQry = "SELECT * FROM  $nom_tabla
				WHERE id_nom=$id_nom $cond_nivel AND id_situacion::text ~ '$superadas_txt' 
				";
        if (($oDblSt = $oDbl->query($sQry)) === false) {
            $sClauError = 'GestorPersonaNota.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_nom' => $aDades['id_nom'],
                'id_asignatura' => $aDades['id_asignatura']);
            $oPersonaNota = $this->chooseNewObject($a_pkey);
            $oPersonaNotaSet->add($oPersonaNota);
        }
        return $oPersonaNotaSet->getTot();
    }


    /**
     * retorna l'array d'objectes de tipus PersonaNota
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array Una col·lecció d'objectes de tipus PersonaNota
     */
    public function getPersonaNotas($aWhere = array(), $aOperators = array())
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $oPersonaNotaSet = new core\Set();
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
        //echo "query: $sQry<br>";
        if (($oDblSt = $oDbl->prepare($sQry)) === false) {
            $sClauError = 'GestorPersonaNota.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }

        if (($oDblSt->execute($aWhere)) === false) {
            $sClauError = 'GestorPersonaNota.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_nom' => $aDades['id_nom'],
                'id_nivel' => $aDades['id_nivel']);
            $oPersonaNota = $this->chooseNewObject($a_pkey);
            $oPersonaNota->setoDbl($oDbl);
            $oPersonaNotaSet->add($oPersonaNota);
        }
        return $oPersonaNotaSet->getTot();
    }

    protected function chooseNewObject($a_pkey): PersonaNotaDlDB|PersonaNotaDB|PersonaNotaOtraRegionStgrDB
    {
        if ($this->sNomTabla === "e_notas") {
            $oPersonaNota = new PersonaNotaDB($a_pkey);
        }
        if ($this->sNomTabla === "e_notas_dl") {
            $oPersonaNota = new PersonaNotaDlDB($a_pkey);
        }
        if ($this->sNomTabla === "e_notas_otra_region_stgr") {
            $oPersonaNota = new PersonaNotaOtraRegionStgrDB($this->esquema_region_stgr, $a_pkey);
        }
        return $oPersonaNota;
    }

}

?>
