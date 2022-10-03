<?php
namespace actividades\model\entity;

use core;
use web;
use web\Desplegable;

/**
 * GestorActividadAll
 *
 * Classe per gestionar la llista d'objectes de la clase Actividad
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */
class GestorActividadAll extends core\ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     * @return GestorActividadAll
     *
     */
    function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('a_actividades_all');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * Devuelve un array con las actividades de una casa en un periodo.
     * Se modifican las fechas de inicio (si es anterior al periodo),
     *  para que empieze en el inicio del periodo
     * Se requiere del array $_SESSION['oPermActividades'] para saber si se tiene permisos para ver...
     *
     * @param integer $id_ubi
     * @param web\DateTimeLocal $oFini
     * @param web\DateTimeLocal $oFfin
     * @return string[][]|boolean array con las actividades o false
     */
    public function actividadesDeUnaCasa($id_ubi, $oFini, $oFfin, $cdc_sel = 0)
    {
        $oIniPlanning = $oFini;
        $a = 0;
        $a_cdc = array();
        $aWhere = array();
        $aOperador = array();
        if (empty($id_ubi) || $id_ubi == 1) { // en estos casos sólo miro las actividades de cada sección.
            if (empty($id_ubi)) {
                $aOperador['id_ubi'] = 'IS NULL';
            }
            switch ($cdc_sel) {
                case 11:
                    $aWhere['id_tipo_activ'] = '^1';
                    $aOperador['id_tipo_activ'] = '~';
                    break;
                case 12:
                    $aWhere['id_tipo_activ'] = '^2';
                    $aOperador['id_tipo_activ'] = '~';
                    break;
            }
        }
        $aWhere['f_ini'] = "'" . $oFfin->format('Y-m-d') . "'";
        $aOperador['f_ini'] = '<=';
        $aWhere['f_fin'] = "'" . $oFini->format('Y-m-d') . "'";
        $aOperador['f_fin'] = '>=';
        $aWhere['id_ubi'] = $id_ubi;
        $aWhere['status'] = 4;
        $aOperador['status'] = '<';
        $oActividades = $this->getActividades($aWhere, $aOperador);
        foreach ($oActividades as $oActividad) {
            $id_activ = $oActividad->getId_activ();
            $id_tipo_activ = $oActividad->getId_tipo_activ();
            $oF_ini_act = $oActividad->getF_ini();
            $h_ini = $oActividad->getH_ini();
            $oF_fin_act = $oActividad->getF_fin();
            $h_fin = $oActividad->getH_fin();
            $dl_org = $oActividad->getDl_org();
            $nom_activ = $oActividad->getNom_activ();

            $oTipoActividad = new web\TiposActividades($id_tipo_activ);
            $ssfsv = $oTipoActividad->getSfsvText();

            //para el caso de que la actividad comience antes
            //del periodo de inicio obligo a que tome una hora de inicio
            //en el entorno de las primeras del día (a efectos del planning
            //ya es suficiente con la 1:16 de la madrugada)
            if ($oIniPlanning > $oF_ini_act) {
                $ini = $oFini->getFromLocal();
                $hini = "1:16";
            } else {
                $ini = (string)$oF_ini_act->getFromLocal();
                $hini = (string)$h_ini;
            }
            $fi = (string)$oF_fin_act->getFromLocal();
            $hfi = (string)$h_fin;

            // mirar permisos.
            $_SESSION['oPermActividades']->setActividad($id_activ, $id_tipo_activ, $dl_org);
            $oPermActiv = $_SESSION['oPermActividades']->getPermisoActual('datos');

            if ($oPermActiv->have_perm_activ('ocupado') === false) {
                $a++;
                continue;
            } // no tiene permisos ni para ver.
            if ($oPermActiv->have_perm_activ('ver') === false) { // sólo puede ver que està ocupado
                $nom_curt = $ssfsv;
                $nom_llarg = "$ssfsv ($ini-$fi)";
            } else {
                $nom_curt = $oTipoActividad->getAsistentesText() . " " . $oTipoActividad->getActividadText();
                $nom_llarg = $nom_activ;
            }

            if ($oPermActiv->have_perm_activ('modificar')) { // puede modificar
                // en realidad creo que simplemente tiene que haber algo. Activa la funcion de javascript: cambiar_activ.
                $pagina = 'programas/actividad_ver.php';
            } else {
                $pagina = '';
            }

            $a_cdc[] = array(
                'nom_curt' => $nom_curt,
                'nom_llarg' => $nom_llarg,
                'f_ini' => $ini,
                'h_ini' => $hini,
                'f_fi' => $fi,
                'h_fi' => $hfi,
                'id_tipo_activ' => $id_tipo_activ,
                'pagina' => $pagina,
                'id_activ' => $id_activ
            );
            $a++;
        }
        // En caso de que todas=0, si no hay actividad, no pongo la casa
        if ($a > 0) {
            return $a_cdc;
        } else {
            return false;
        }
    }

    /**
     * retorna si hi ha una activitat coincident en dates de l'altre secció.
     *
     * @param object Actividad
     * @param string salida. 'bool' para que retorne true/false, 'array' para que retorne la lista.
     * @return bool,array una llista de id_activ.
     */
    function getCoincidencia($oActividad, $salida = 'bool')
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $iTolerancia = 1;
        $interval = "P$iTolerancia" . "D";
        $id_tipo_activ = $oActividad->getId_tipo_activ();
        $id = (string)$id_tipo_activ; // para convertir id_tipo_activ en un string.
        $seccion = ($id[0] == "1") ? 2 : 1;
        $oFini0 = $oActividad->getF_ini();
        $oFini1 = clone $oFini0;
        $oFfin0 = $oActividad->getF_fin();
        $oFfin1 = clone $oFfin0;
        $oFini0->sub(new \DateInterval($interval));
        $oFini1->add(new \DateInterval($interval));
        $oFfin0->sub(new \DateInterval($interval));
        $oFfin1->add(new \DateInterval($interval));
        $sql_ini = "f_ini between '" . $oFini0->format('Y-m-d') . "' and '" . $oFini1->format('Y-m-d') . "'";
        $sql_fin = "f_fin between '" . $oFfin0->format('Y-m-d') . "' and '" . $oFfin1->format('Y-m-d') . "'";
        if ($salida == 'array') {
            $sql = "SELECT id_activ";
        } else {
            $sql = "SELECT count(*)";
        }
        $sql .= " FROM $nom_tabla";
        $sql .= " WHERE id_tipo_activ::text ~ '^" . $seccion . "[45]' ";
        $sql .= " AND $sql_ini";
        $sql .= " AND $sql_fin";

        //echo "sql: $sql<br>";

        if (($oDblSt = $oDbl->query($sql)) === false) {
            $sClauError = 'GestorActividadAll.getCoincidencia';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if ($salida == 'array') {
            $aActiv = [];
            foreach ($oDblSt as $aDades) {
                $aActiv[] = $aDades['id_activ'];
            }
            return $aActiv;
        } else {
            return $oDblSt->fetchColumn();
        }
    }

    /**
     * retorna l'array amb el id_ubi de les activitats sel·leccionades
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array una llista de id_ubi.
     */
    function getUbis($aWhere = array(), $aOperators = array())
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $oCondicion = new core\Condicion();
        $aCondi = array();
        $aUbis = array();
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
            $sLimit = $GLOBALS['oGestorSessioDelegación']->getLimitPaginador("$nom_tabla", $sCondi, $aWhere);
        } else {
            $sLimit = '';
        }
        if ($sLimit === false) return;
        $sOrdre = '';
        if (isset($aWhere['_ordre']) && $aWhere['_ordre'] != '') $sOrdre = ' ORDER BY ' . $aWhere['_ordre'];
        if (isset($aWhere['_ordre'])) unset($aWhere['_ordre']);
        $sQry = "SELECT id_ubi FROM $nom_tabla " . $sCondi . " GROUP BY id_ubi" . $sOrdre;
        if (($oDblSt = $oDbl->prepare($sQry)) === false) {
            $sClauError = 'GestorActividadAll.llistar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClauError = 'GestorActividadAll.llistar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $aUbis[] = $aDades['id_ubi'];
        }
        return $aUbis;
    }

    /**
     * retorna una llista id_activ=>Nom_activ
     *
     * @param string sTipo
     * @param string scondicion Condicion adicional a sTipo (debe empezar con AND).
     * @return Desplegable
     */
    function getListaActividadesDeTipo($sid_tipo = '......', $scondicion = '')
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_activ, nom_activ
		   FROM $nom_tabla
	   	   WHERE id_tipo_activ::text ~ '" . $sid_tipo . "' $scondicion
		   ORDER by f_ini";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorActividadAll.ListaDeTipo';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        return new web\Desplegable('', $oDblSt, '', true);
    }

    /**
     * retorna un Desplegable d'activitats
     *
     * @param string scondicion (debe empezar con AND)
     * @return array Una Llista.
     */
    function getListaActividadesEstudios($scondicion = '')
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $cond_nivel_stgr = "(nivel_stgr < 6 OR nivel_stgr=11)";
        if (empty($scondicion)) {
            $any = $_SESSION['oConfig']->any_final_curs('est') - 2;
            $inicurs = core\curso_est("inicio", $any, "est")->format('Y-m-d');
            $scondicion = "AND f_ini > '$inicurs'";
        }
        $sQuery = "SELECT id_activ, nom_activ
		   FROM $nom_tabla
	   	   WHERE " . $cond_nivel_stgr . " $scondicion
		   ORDER by f_ini";
        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorActividadAll.ListaActividadesEstudios';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aOpciones = array();
        foreach ($oDbl->query($sQuery) as $aClave) {
            $clave = $aClave[0];
            $val = $aClave[1];
            $aOpciones[$clave] = $val;
        }
        return new web\Desplegable('', $aOpciones, '', true);
    }

    /**
     * retorna l'array de id d'Actividad
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array de id_actividad intger
     */
    function getArrayIds($aWhere = array(), $aOperators = array())
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $aListaId = array();
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
        $sQry = "SELECT * FROM $nom_tabla" . $sCondi . $sOrdre . $sLimit;
        //echo "<br>query: $sQry<br>";
        if (($oDblSt = $oDbl->prepare($sQry)) === false) {
            $sClauError = 'GestorActividadAll.llistar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClauError = 'GestorActividadAll.llistar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $aListaId[] = $aDades['id_activ'];
        }
        return $aListaId;
    }

    /**
     * retorna l'array d'objectes de tipus Actividad
     *
     * @param string sQuery la query a executar.
     * @return array Una col·lecció d'objectes de tipus Actividad
     */
    function getActividadesQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $oActividadSet = new core\Set();
        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorActividadAll.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        /* Creo que no tiene sentido. Sólo si la consulta es SELECT (No DELETE...)
         * y Requiere los campos id_activ, dl_org, id_tabla.
         */
        /*
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('id_activ' => $aDades['id_activ']);
            $dl = $aDades['dl_org'];
            $id_tabla = $aDades['id_tabla'];
            if ($dl == core\ConfigGlobal::mi_delef()) {
                $oActividad = new ActividadDl($a_pkey);
            } else {
                if ($id_tabla == 'dl') {
                    $oActividad = new ActividadPub($a_pkey);
                } else {
                    $oActividad = new ActividadEx($a_pkey);
                }
            }
            $oActividadSet->add($oActividad);
        }
        */
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('id_tipo_activ' => $aDades['id_activ']);
            $oActividad = new Actividad($a_pkey);
            $oActividad->setAllAtributes($aDades);
            $oActividadSet->add($oActividad);
        }
        return $oActividadSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus Actividad
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array Una col·lecció d'objectes de tipus Actividad
     */
    function getActividades($aWhere = array(), $aOperators = array())
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $oActividadSet = new core\Set();
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
        $sQry = "SELECT * FROM $nom_tabla" . $sCondi . $sOrdre . $sLimit;
        //echo "<br>query: $sQry<br>";
        if (($oDblSt = $oDbl->prepare($sQry)) === false) {
            $sClauError = 'GestorActividadAll.llistar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClauError = 'GestorActividadAll.llistar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_activ' => $aDades['id_activ']);
            $id_tabla = $aDades['id_tabla'];
            $dl_org = $aDades['dl_org'];
            // para dl y dlf:
            $dl_org_no_f = preg_replace('/(\.*)f$/', '\1', $dl_org);
            if ($dl_org_no_f == core\ConfigGlobal::mi_dele()) {
                $oActividad = new ActividadDl($a_pkey);
            } else {
                if ($id_tabla == 'dl') {
                    $oActividad = new ActividadPub($a_pkey);
                } else {
                    $oActividad = new ActividadEx($a_pkey);
                }
            }
            $oActividad->setAllAtributes($aDades, FALSE);
            $oActividadSet->add($oActividad);
        }
        return $oActividadSet->getTot();
    }

    /* METODES PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/

}

?>
