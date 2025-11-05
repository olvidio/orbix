<?php

namespace procesos\model\entity;

use core\ClaseGestor;
use core\Condicion;
use core\Set;
use function core\is_true;

/**
 * GestorTareaProceso
 *
 * Classe per gestionar la llista d'objectes de la clase TareaProceso
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/12/2018
 */
class GestorTareaProceso extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    var $aFasesArbol = [];
    var $aFases = [];

    /* CONSTRUCTOR -------------------------------------------------------------- */



    function __construct()
    {
        $oDbl = $GLOBALS['oDBC'];
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('a_tareas_proceso');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna array de fase#traea => fase_previa, tarea_previa.
     *
     * @param integer iid_tipo_proceso tipus de procés.
     * @return array|false
     */
    public function getArrayFasesDependientes($iid_tipo_proceso)
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT * FROM $nom_tabla 
                    WHERE id_tipo_proceso = $iid_tipo_proceso
                    ";

        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorTareaProceso.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aFases = [];
        foreach ($oDbl->query($sQuery) as $aDades) {
            $id_fase = $aDades['id_fase'];
            $id_tarea = $aDades['id_tarea'];
            // tarea puede ser empty en vez de 0:
            $id_tarea = empty($id_tarea) ? 0 : $id_tarea;
            $fase_tarea = $id_fase . '#' . $id_tarea;

            $aJson_fases_previas = json_decode($aDades['json_fases_previas']);
            $aFases2 = [];
            foreach ($aJson_fases_previas as $json_fase_previa) {
                $id_fase_previa = $json_fase_previa->id_fase;
                $id_tarea_previa = $json_fase_previa->id_tarea;
                $mensaje = $json_fase_previa->mensaje;
                // tarea puede ser empty en vez de 0:
                $id_tarea_previa = empty($id_tarea_previa) ? 0 : $id_tarea_previa;

                $aFases2[] = [$id_fase_previa . '#' . $id_tarea_previa => $mensaje];
            }
            $aFases[$fase_tarea] = $aFases2;
        }
        return $aFases;
    }

    /**
     * Añade una fase y su mensaje al arbolPrevio
     *
     * @param string $fase_tarea_org
     * @param array $aFase_previa
     */
    private function add(string $fase_tarea_org, array $aFase_previa)
    {
        $fase_tarea = key($aFase_previa);
        $mensaje = current($aFase_previa);
        $this->aFasesArbol[$fase_tarea_org][$fase_tarea] = $mensaje;
    }

    /**
     * añade a la fase original, las fases previas de las que depende.
     * recursivamente.
     *
     * @param string $fase_tarea_org
     * @param array $aaFase_previa
     */
    private function ar(string $fase_tarea_org, array $aaFase_previa)
    {
        foreach ($aaFase_previa as $aFase_previa) {
            $fase_tarea_previa = key($aFase_previa);
            // evitar loops infinitos:
            if ($fase_tarea_org == $fase_tarea_previa) continue;
            $this->add($fase_tarea_org, $aFase_previa);
            if (array_key_exists($fase_tarea_previa, $this->aFases)) {
                $aaFase_previa = $this->aFases[$fase_tarea_previa];
                $this->ar($fase_tarea_org, $aaFase_previa);
            }
        }
    }

    /**
     * Devuelve un array donde la clave son todas las fase_tarea del proceso.
     *     Para cada fase tarea se le pone un array con todas las fase_tareas de las que depende
     *     (con el mensaje de si no se cumple el requisito).
     *
     * @param integer $iid_tipo_proceso
     * @return array
     */
    public function arbolPrevio(int $iid_tipo_proceso)
    {
        $this->aFases = $this->getArrayFasesDependientes($iid_tipo_proceso);
        foreach ($this->aFases as $fase_tarea_org => $aaFase_previa) {
            $this->aFasesArbol[$fase_tarea_org] = [];
            $this->ar($fase_tarea_org, $aaFase_previa);
        }
        return $this->aFasesArbol;
    }

    /**
     * retorna un array
     *
     * @param integer iid_tipo_proceso tipus de procés.
     * @param integer id_fase.
     * @param integer id_tarea.
     * @param integer $f nº de fila
     * @return array|false
     */
    public function getListaFasesDependientes($iid_tipo_proceso, $id_fase, $id_tarea = 0, int $f = 0)
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT * FROM $nom_tabla 
                    WHERE id_tipo_proceso = $iid_tipo_proceso
                    ";

        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorTareaProceso.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aFases = [$f => "$id_fase#$id_tarea"];
        foreach ($oDbl->query($sQuery) as $aDades) {
            $id_fase_i = $aDades['id_fase'];
            $id_tarea_i = $aDades['id_tarea'];
            // tarea puede ser empty en vez de 0:
            $id_tarea_i = empty($id_tarea_i) ? 0 : $id_tarea_i;

            if ($id_fase == $id_fase_i && $id_tarea == $id_tarea_i) {

                $aJson_fases_previas = json_decode($aDades['json_fases_previas']);
                foreach ($aJson_fases_previas as $json_fase_previa) {
                    $id_fase_previa = $json_fase_previa->id_fase;
                    $id_tarea_previa = $json_fase_previa->id_tarea;
                    if (!empty($id_fase_previa)) {
                        $f++;
                        $aF2 = $this->getListaFasesDependientes($iid_tipo_proceso, $id_fase_previa, $id_tarea_previa, $f);
                        $aFases = $aFases + $aF2;
                    }
                }
                return $aFases;
            }
        }
    }

    /**
     * retorna integer de la id_fase anterior
     *
     * @param integer id_item la fase tarea.
     * @return integer id_true o false si hi ha un error
     */
    public function getStatusProceso($iid_tipo_proceso, $aFasesEstado)
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $sQuery = "SELECT id_fase,id_tarea,status FROM $nom_tabla
				WHERE id_tipo_proceso=$iid_tipo_proceso ";

        $aFasesOn = [];
        foreach ($oDbl->query($sQuery) as $aDades) {
            $id_fase = $aDades['id_fase'];
            $id_tarea = $aDades['id_tarea'];
            $fase_tarea = $id_fase . '#' . $id_tarea;
            $status = $aDades['status'];
            if (!array_key_exists($fase_tarea, $aFasesEstado)) {
                exit (_("Hay que regenerar el proceso de la actividad"));
            } else {
                if (is_true($aFasesEstado[$fase_tarea])) {
                    $aFasesOn[$id_fase] = $status;
                }
            }
        }
        // los status de la actividad si son ordenados. 1,2,3,4.
        asort($aFasesOn);
        $ultimo_status = end($aFasesOn);

        return $ultimo_status;
    }

    /**
     * retorna l'array amb la lista de fases ordenadas. Per utilitzar a 'a_fases_gestor.class'
     *
     * @param integer iid_tipo_proceso tipus de procés.
     * @return array|false
     */
    function getFasesProceso($iid_tipo_proceso = '')
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        if (empty($iid_tipo_proceso)) return [];
        //$sQuery = "SELECT * FROM $nom_tabla WHERE id_tipo_proceso = $iid_tipo_proceso ORDER BY n_orden";
        $sQuery = "SELECT * FROM $nom_tabla WHERE id_tipo_proceso = $iid_tipo_proceso ";

        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorTareaProceso.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aFases = [];
        foreach ($oDbl->query($sQuery) as $aDades) {
            $aFases[$aDades['id_item']] = $aDades['id_fase'];
        }
        return $aFases;
    }

    public function getFaseIndependiente($id_tipo_proceso)
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQry = "SELECT * FROM $nom_tabla 
                WHERE id_tipo_proceso = $id_tipo_proceso AND json_fases_previas::text = '[]'::text ";

        if (($oDbl->query($sQry)) === FALSE) {
            $sClauError = 'GestorTareaProceso.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        $i = 0;
        foreach ($oDbl->query($sQry) as $aDades) {
            $i++;
            $a_pkey = array('id_item' => $aDades['id_item']);
            $oTareaProceso = new TareaProceso($a_pkey);
            $oTareaProceso->setAllAtributes($aDades);
        }
        if ($i == 0) {
            $txt = _("No se puede encontrar una fase independiente (que no tenga fase previa) para el proceso: %s");
            $msg = sprintf($txt, $id_tipo_proceso);
            echo $msg;
            return false;
        }
        if ($i > 1) {
            $txt = _("No debería haber más de una fase independiente (que no tenga fase previa) en un proceso. Hay %s para el id_proceso: %s");
            $msg = sprintf($txt, $i, $id_tipo_proceso);
            echo $msg;
        }
        return $oTareaProceso;
    }

    /**
     * retorna l'array d'objectes de tipus TareaProceso
     *
     * @param string sQuery la query a executar.
     * @return array|false
     */
    function getTareasProcesosQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $oTareaProcesoSet = new Set();
        if (($oDbl->query($sQuery)) === FALSE) {
            $sClauError = 'GestorTareaProceso.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('id_item' => $aDades['id_item']);
            $oTareaProceso = new TareaProceso($a_pkey);
            $oTareaProcesoSet->add($oTareaProceso);
        }
        return $oTareaProcesoSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus TareaProceso
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array|void
     */
    function getTareasProceso($aWhere = [], $aOperators = array())
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oTareaProcesoSet = new Set();
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
            $sClauError = 'GestorTareaProceso.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        if (($oDblSt->execute($aWhere)) === FALSE) {
            $sClauError = 'GestorTareaProceso.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_item' => $aDades['id_item']);
            $oTareaProceso = new TareaProceso($a_pkey);
            $oTareaProcesoSet->add($oTareaProceso);
        }
        return $oTareaProcesoSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}
