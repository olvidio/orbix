<?php

namespace procesos\model\entity;

use core\ClaseGestor;
use core\Condicion;
use core\ConfigGlobal;
use core\Set;
use permisos\model\PermDl;
use src\usuarios\application\repositories\RoleRepository;
use src\usuarios\application\repositories\UsuarioRepository;
use web\Desplegable;

/**
 * GestorActividadFase
 *
 * Classe per gestionar la llista d'objectes de la clase ActividadFase
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/12/2018
 */
class GestorActividadFase extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    function __construct()
    {
        $oDbl = $GLOBALS['oDBC'];
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('a_fases');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/
    /**
     * retorna un array
     *
     * @param array lista de procesos.
     * @return array|false
     */
    function getTodasActividadFases($a_id_tipo_proceso = [])
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $cond = '';
        // filtro por sf/sv
        $miSfsv = ConfigGlobal::mi_sfsv();
        switch ($miSfsv) {
            case 1: // sv
                $cond = "(sv ='t') ";
                break;
            case 2: //sf
                $cond = "(sf ='t') ";
                break;
        }
        $cond .= ' AND';

        $aFases = [];
        foreach ($a_id_tipo_proceso as $idTipoProceso) {
            $sQuery = "SELECT f.id_fase, f.desc_fase
                    FROM $nom_tabla f JOIN a_tareas_proceso p USING (id_fase)
                    WHERE $cond id_tipo_proceso = $idTipoProceso
                    ";

            //echo "w: $sQuery<br>";
            if (($oDbl->query($sQuery)) === false) {
                $sClauError = 'GestorActividadFase.lista';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
            foreach ($oDbl->query($sQuery) as $row) {
                $aFases[] = $row['id_fase'];
            }
        }
        return $aFases;
    }

    /**
     * retorna un array
     *
     * @param array optional lista de procesos.
     * @return array $aFases[$desc_fase] = $id_fase;
     */
    function getArrayActividadFasesTodas($aProcesos = array())
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $UsuarioRepository = new UsuarioRepository();
        $oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
        $id_role = $oMiUsuario->getId_role();
        $miSfsv = ConfigGlobal::mi_sfsv();

        $RoleRepository = new RoleRepository();
        $aRoles = $RoleRepository->getArrayRoles();

        $cond = '';
        if (!empty($aRoles[$id_role]) && ($aRoles[$id_role] === 'SuperAdmin')) {
            $cond = "(sf = 't' OR sv ='t') ";
        } else {
            // filtro por sf/sv
            switch ($miSfsv) {
                case 1: // sv
                    $cond = "(sv ='t') ";
                    break;
                case 2: //sf
                    $cond = "(sf ='t') ";
                    break;
            }
        }

        // intentar ordenar. No se puede por que los num de orden son distintos para cada proceso
        $aDescFases = [];
        $aFasesComunes = [];
        foreach ($aProcesos as $id_tipo_proceso) {
            $sCondicion = "WHERE $cond AND id_tipo_proceso = $id_tipo_proceso";
            $sQuery = "SELECT f.id_fase, f.desc_fase
					FROM $nom_tabla f JOIN a_tareas_proceso p USING (id_fase)
					$sCondicion
					";

            $aFasesProceso = [];
            foreach ($oDbl->query($sQuery) as $row) {
                $id_fase = $row['id_fase'];
                $desc_fase = $row['desc_fase'];
                $aDescFases[$id_fase] = $desc_fase;

                $aFasesProceso[] = $id_fase;

            }
            // la primera vuelta no hay nada y hay que saltarlo:
            if (empty($aFasesComunes)) {
                $aFasesComunes = $aFasesProceso;
                continue;
            }
            $aFasesComunes = $aFasesComunes + $aFasesProceso;
        }
        // poner la descripcion de la fase en el array resultante.
        $aFasesComunesOrden = [];
        foreach ($aFasesComunes as $id_fase) {
            $desc_fase = $aDescFases[$id_fase];
            $aFasesComunesOrden[$desc_fase] = $id_fase;
        }

        return $aFasesComunesOrden;
    }

    /**
     * Para ver una cuadricula con todas las fases de un conjunto de procesos y
     * poder marcarlas. para sustituir a la funcion de getListaActividadFases.
     *
     * @param array $aProcesos
     */
    public function getArrayFasesProcesos(array $aProcesos = array())
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $miSfsv = ConfigGlobal::mi_sfsv();

        $cond = '';
        // filtro por sf/sv
        switch ($miSfsv) {
            case 1: // sv
                $cond = "(sv = 't') ";
                break;
            case 2: //sf
                $cond = "(sf = 't') ";
                break;
        }

        $num_procesos = count($aProcesos);
        if ($num_procesos !== false && $num_procesos > 0) {
            // con una sola consulta no acaba de hacerlo bien, al haber fases con varias tareas...
            //$aFasesComunes = [];
            $aFasesProcesoDesc = [];
            foreach ($aProcesos as $id_tipo_proceso) {
                $sCondicion = "WHERE $cond AND id_tipo_proceso = $id_tipo_proceso";
                $sQuery = "SELECT f.id_fase, f.desc_fase
					FROM $nom_tabla f JOIN a_tareas_proceso p USING (id_fase)
					$sCondicion
					GROUP BY f.id_fase, f.desc_fase
					ORDER BY desc_fase";
                if (($oDblSt = $oDbl->query($sQuery)) === false) {
                    $sClauError = 'GestorActividadFase.lista';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                    return false;
                }
                foreach ($oDblSt as $aDades) {
                    //$aFasesComunes[$aDades['id_fase']] = $aDades['desc_fase'];
                    $aFasesProcesoDesc[$aDades['desc_fase']] = $aDades['id_fase'];
                }
            }
        } else {
            $sQuery = "SELECT id_fase, desc_fase
					FROM $nom_tabla
					WHERE $cond
					ORDER BY desc_fase";
            if (($oDblSt = $oDbl->query($sQuery)) === false) {
                $sClauError = 'GestorActividadFase.lista';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
            foreach ($oDblSt as $aDades) {
                //$aFasesComunes[$aDades['id_fase']] = $aDades['desc_fase'];
                $aFasesProcesoDesc[$aDades['desc_fase']] = $aDades['id_fase'];
            }
        }
        return $aFasesProcesoDesc;
    }

    public function getFaseAnterior($id_tipo_proceso, $iFase)
    {
        $a_fases_proceso = $this->getArrayFasesProcesos([$id_tipo_proceso]);

        $id_fase_anterior = '';
        reset($a_fases_proceso);
        while (current($a_fases_proceso) !== $iFase) {
            $id_fase_anterior = current($a_fases_proceso);
            if ($id_fase_anterior === FALSE) return FALSE;
            next($a_fases_proceso);
        }
        return $id_fase_anterior;

    }


    /**
     * retorna un objecte del tipus Desplegable
     *
     * @param array optional lista de procesos.
     * @param boolean optional només les fases de les que sóc responsable.
     * @return false|Desplegable
     */
    function getListaActividadFases($aProcesos = [], $bresp = false)
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $UsuarioRepository = new UsuarioRepository();
        $oMiUsuario = $UsuarioRepository->findById(ConfigGlobal::mi_id_usuario());
        $id_role = $oMiUsuario->getId_role();
        $miSfsv = ConfigGlobal::mi_sfsv();

        $RoleRepository = new RoleRepository();
        $aRoles = $RoleRepository->getArrayRoles();

        if ($bresp) {
            //$miPerm=$oMiUsuario->getPerm_oficinas();
            $oPermiso = new PermDl();
        }

        $cond = '';
        if (!empty($aRoles[$id_role]) && ($aRoles[$id_role] === 'SuperAdmin')) {
            $cond = "(sf = 't' OR sv ='t') ";
        } else {
            // filtro por sf/sv
            switch ($miSfsv) {
                case 1: // sv
                    $cond = "(sv = 't') ";
                    break;
                case 2: //sf
                    $cond = "(sf = 't') ";
                    break;
            }
        }

        $num_procesos = count($aProcesos);
        if ($num_procesos !== false && $num_procesos > 0) {
            $sCondicion = "WHERE $cond AND id_tipo_proceso =";
            $sCondicion .= implode(' OR id_tipo_proceso = ', $aProcesos);
            $sQuery = "SELECT f.id_fase, f.desc_fase
					FROM $nom_tabla f JOIN a_tareas_proceso p USING (id_fase)
					$sCondicion
					GROUP BY f.id_fase, f.desc_fase
					HAVING Count(p.id_tipo_proceso) = $num_procesos
					ORDER BY desc_fase";
        } else {
            $sQuery = "SELECT id_fase, desc_fase
					FROM $nom_tabla
					WHERE $cond
					ORDER BY desc_fase";
        }
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorActividadFase.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }

        // Si no hay proceso se muestra todo.
        if (empty($aProcesos)) {
            return new Desplegable('', $oDblSt, '', true);
        } else {
            $aFasesComunes = [];
            foreach ($oDblSt as $aDades) {
                $aFasesComunes[$aDades['id_fase']] = $aDades['desc_fase'];
            }

            // Ordenar según el primer proceso (si hay más de uno).
            reset($aProcesos);
            $id_tipo_proceso = current($aProcesos);
            $oGestorProceso = new GestorTareaProceso();
            $aFasesProceso = $oGestorProceso->getFasesProceso($id_tipo_proceso);
            $aFasesProcesoDesc = [];
            foreach ($aFasesProceso as $id_item => $id_fase) {
                // compruebo que está en la lista de las fases comunes.
                if (array_key_exists($id_fase, $aFasesComunes)) {
                    $oFase = new ActividadFase($id_fase);
                    // compruebo si soy el responsable
                    if ($bresp) {
                        $oTareaProceso = new TareaProceso($id_item);
                        $of_responsable_txt = $oTareaProceso->getOf_responsable_txt();

                        // Si no hay oficina responsable, pueden todos:
                        if (empty($of_responsable_txt)) {
                            $aFasesProcesoDesc[$id_fase] = $oFase->getDesc_fase();
                        } elseif ($oPermiso->have_perm_oficina($of_responsable_txt)) {
                            $aFasesProcesoDesc[$id_fase] = $oFase->getDesc_fase();
                        }
                    } else {
                        $aFasesProcesoDesc[$id_fase] = $oFase->getDesc_fase();
                    }
                }
            }
            return new Desplegable('', $aFasesProcesoDesc, '', true);
        }
    }

    /**
     * retorna l'array d'objectes de tipus ActividadFase
     *
     * @param string sQuery la query a executar.
     * @return array|false
     */
    function getActividadFasesQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $oActividadFaseSet = new Set();
        if (($oDbl->query($sQuery)) === FALSE) {
            $sClauError = 'GestorActividadFase.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('id_fase' => $aDades['id_fase']);
            $oActividadFase = new ActividadFase($a_pkey);
            $oActividadFaseSet->add($oActividadFase);
        }
        return $oActividadFaseSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus ActividadFase
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array|void
     */
    function getActividadFases($aWhere = [], $aOperators = array())
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oActividadFaseSet = new Set();
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
            $sClauError = 'GestorActividadFase.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        if (($oDblSt->execute($aWhere)) === FALSE) {
            $sClauError = 'GestorActividadFase.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_fase' => $aDades['id_fase']);
            $oActividadFase = new ActividadFase($a_pkey);
            $oActividadFaseSet->add($oActividadFase);
        }
        return $oActividadFaseSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}
