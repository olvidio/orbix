<?php
namespace profesores\model\entity;

use core\ClaseGestor;
use core\Condicion;
use core\Set;
use personas\model\entity\PersonaDl;

/**
 * GestorProfesorAmpliacion
 *
 * Classe per gestionar la llista d'objectes de la clase ProfesorAmpliacion
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 08/04/2014
 */
class GestorProfesorAmpliacion extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    
    function __construct()
    {
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('d_profesor_ampliacion');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna un objecte del tipus Array
     * Els posibles professors per una asignatura
     *
     * @return array Una Llista
     */
    function getListaProfesoresAsignatura($id_asignatura)
    {
        $gesProfesores = $this->getProfesorAmpliaciones(array('id_asignatura' => $id_asignatura, 'f_cese' => ''), array('f_cese' => 'IS NULL'));
        $aProfesores = [];
        $aAp1 = [];
        $aAp2 = [];
        $aNom = [];
        foreach ($gesProfesores as $oProfesor) {
            $id_nom = $oProfesor->getId_nom();
            $oPersonaDl = new PersonaDl($id_nom);
            $ap_nom = $oPersonaDl->getPrefApellidosNombre();
            $aProfesores[] = array('id_nom' => $id_nom, 'ap_nom' => $ap_nom);
            $aAp1[] = $oPersonaDl->getApellido1();
            $aAp2[] = $oPersonaDl->getApellido2();
            $aNom[] = $oPersonaDl->getNom();
        }
        $multisort_args = [];
        $multisort_args[] = $aAp1;
        $multisort_args[] = SORT_ASC;
        $multisort_args[] = SORT_STRING;
        $multisort_args[] = $aAp2;
        $multisort_args[] = SORT_ASC;
        $multisort_args[] = SORT_STRING;
        $multisort_args[] = $aNom;
        $multisort_args[] = SORT_ASC;
        $multisort_args[] = SORT_STRING;
        $multisort_args[] = &$aProfesores;   // finally add the source array, by reference
        call_user_func_array("array_multisort", $multisort_args);
        $aOpciones = [];
        foreach ($aProfesores as $aClave) {
            $clave = $aClave['id_nom'];
            $val = $aClave['ap_nom'];
            $aOpciones[$clave] = $val;
        }
        return $aOpciones;
    }

    /**
     * retorna l'array d'objectes de tipus ProfesorAmpliacion
     *
     * @param string sQuery la query a executar.
     * @return array|false
     */
    function getProfesorAmpliacionesQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $oProfesorAmpliacionSet = new Set();
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorProfesorAmpliacion.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('id_item' => $aDades['id_item'],
                'id_nom' => $aDades['id_nom']);
            $oProfesorAmpliacion = new ProfesorAmpliacion($a_pkey);
            $oProfesorAmpliacionSet->add($oProfesorAmpliacion);
        }
        return $oProfesorAmpliacionSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus ProfesorAmpliacion
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array|void
     */
    function getProfesorAmpliaciones($aWhere = [], $aOperators = array())
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $oProfesorAmpliacionSet = new Set();
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
            $sClauError = 'GestorProfesorAmpliacion.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClauError = 'GestorProfesorAmpliacion.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_item' => $aDades['id_item'],
                'id_nom' => $aDades['id_nom']);
            $oProfesorAmpliacion = new ProfesorAmpliacion($a_pkey);
            $oProfesorAmpliacionSet->add($oProfesorAmpliacion);
        }
        return $oProfesorAmpliacionSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}

?>
