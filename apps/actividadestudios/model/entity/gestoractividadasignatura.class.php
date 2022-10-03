<?php
namespace actividadestudios\model\entity;

use asignaturas\model\entity as asignaturas;
use core;

/**
 * GestorActividadAsignatura
 *
 * Classe per gestionar la llista d'objectes de la clase ActividadAsignatura
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 14/11/2014
 */
class GestorActividadAsignatura extends core\ClaseGestor
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
        $oDbl = $GLOBALS['oDBP'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('d_asignaturas_activ_all');
    }

    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/


    /**
     * retorna l'array amb les asignatures, credits i nivell stgr del ca
     *
     * @param biginteger id_activ
     * @param string tipo  tipo='p' para preceptor
     * @return array asignaturas es un array (id_asignatura=>Creditos);
     */
    function getAsignaturasCa($id_activ, $tipo = '')
    {
        /**
         * Array con  id_asignatura => array(nombre_asignatura, creditos)
         * para no tener que consultar cada vez a la base de datos.
         *
         */
        $GesAsignaturas = new asignaturas\GestorAsignatura();
        $aAsigDatos = $GesAsignaturas->getArrayAsignaturasCreditos();

        // por cada ca creo un array con las asignaturas y los créditos.
        $aWhere['id_activ'] = $id_activ;
        $aOperador = array();
        if (empty($tipo)) {
            $aWhere['tipo'] = 'NULL';
            $aOperador['tipo'] = 'IS NULL';
        } else {
            $aWhere['tipo'] = $tipo;
        }
        $cActividadAsignaturas = $this->getActividadAsignaturas($aWhere, $aOperador);
        $aAsignaturasCa = array();
        foreach ($cActividadAsignaturas as $oActividadAsignatura) {
            $id_asignatura = $oActividadAsignatura->getId_asignatura();
            $aAsignaturasCa[$id_asignatura] = $aAsigDatos[$id_asignatura];
        }
        return $aAsignaturasCa;
    }

    /**
     * retorna l'array d'objectes de tipus ActividadAsignatura
     *
     * @param string sQuery la query a executar.
     * @return array Una col·lecció d'objectes de tipus ActividadAsignatura
     */
    function getActividadAsignaturasQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $oActividadAsignaturaSet = new core\Set();
        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorActividadAsignatura.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('id_activ' => $aDades['id_activ'],
                'id_asignatura' => $aDades['id_asignatura']);
            $oActividadAsignatura = new ActividadAsignatura($a_pkey);
            $oActividadAsignaturaSet->add($oActividadAsignatura);
        }
        return $oActividadAsignaturaSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus ActividadAsignatura
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array Una col·lecció d'objectes de tipus ActividadAsignatura
     */
    function getActividadAsignaturas($aWhere = array(), $aOperators = array())
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $oActividadAsignaturaSet = new core\Set();
        $oCondicion = new core\Condicion();
        $aCondi = array();
        foreach ($aWhere as $camp => $val) {
            if ($camp == '_ordre') {
                continue;
            }
            $sOperador = isset($aOperators[$camp]) ? $aOperators[$camp] : '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) {
                $aCondi[] = $a;
            }
            // operadores que no requieren valores
            if ($sOperador == 'BETWEEN' || $sOperador == 'IS NULL' || $sOperador == 'IS NOT NULL' || $sOperador == 'OR') {
                unset($aWhere[$camp]);
            }
        }
        $sCondi = implode(' AND ', $aCondi);
        if ($sCondi != '') {
            $sCondi = " WHERE " . $sCondi;
        }
        if (isset($GLOBALS['oGestorSessioDelegación'])) {
            $sLimit = $GLOBALS['oGestorSessioDelegación']->getLimitPaginador('a_actividades', $sCondi, $aWhere);
        } else {
            $sLimit = '';
        }
        if ($sLimit === false) {
            return;
        }
        $sOrdre = '';
        if (isset($aWhere['_ordre']) && $aWhere['_ordre'] != '') {
            $sOrdre = ' ORDER BY ' . $aWhere['_ordre'];
        }
        if (isset($aWhere['_ordre'])) {
            unset($aWhere['_ordre']);
        }
        $sQry = "SELECT * FROM $nom_tabla " . $sCondi . $sOrdre . $sLimit;
        if (($oDblSt = $oDbl->prepare($sQry)) === false) {
            $sClauError = 'GestorActividadAsignatura.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClauError = 'GestorActividadAsignatura.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_activ' => $aDades['id_activ'],
                'id_asignatura' => $aDades['id_asignatura']);
            $oActividadAsignatura = new ActividadAsignatura($a_pkey);
            $oActividadAsignaturaSet->add($oActividadAsignatura);
        }
        return $oActividadAsignaturaSet->getTot();
    }

    /* METODES PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}

?>
