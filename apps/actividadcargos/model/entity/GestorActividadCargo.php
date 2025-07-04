<?php

namespace actividadcargos\model\entity;

use actividades\model\entity\ActividadAll;
use actividades\model\entity\GestorActividad;
use asistentes\model\entity\GestorAsistente;
use core\ClaseGestor;
use core\Condicion;
use core\ConfigGlobal;
use core\Set;
use personas\model\entity\Persona;
use personas\model\entity\PersonaSacd;

/**
 * GestorActividadCargo
 *
 * Classe per gestionar la llista d'objectes de la clase ActividadCargo
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 19/11/2014
 */
class GestorActividadCargo extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    function __construct()
    {
        $oDbl = $GLOBALS['oDBE'];
        $oDbl_Select = $GLOBALS['oDBE_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('d_cargos_activ_dl');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna l'array de id_nom dels sacd que atenen l'activitat
     *
     * @param integer iid_activ l'id de l'activitat.
     * @return array|false
     */
    function getActividadIdSacds($iid_activ = '')
    {
        // valores del id_cargo de tipo_cargo = sacd:
        $gesCargos = new GestorCargo();
        $aIdCargos_sacd = $gesCargos->getArrayCargosDeTipo('sacd');
        $txt_where_cargos = implode(',', array_keys($aIdCargos_sacd));

        // Los sacd los pongo en la base de datos comun.
        $oDbl = $GLOBALS['oDBC_Select'];
        $nom_tabla = 'c' . $this->getNomTabla();
        $aLista = [];
        $sQuery = "SELECT id_nom, id_cargo
				FROM $nom_tabla
				WHERE id_activ=" . $iid_activ . " AND id_cargo IN ($txt_where_cargos)
				ORDER BY id_cargo";
        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorActividadCargo.sacds';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $aLista[] = $aDades['id_nom'];
        }
        return $aLista;
    }

    /**
     * retorna l'array d'objectes de tipus Persona
     *
     * @param integer iid_activ l'id de l'activitat.
     * @return array|false
     */
    function getActividadSacds($iid_activ = '')
    {
        // valores del id_cargo de tipo_cargo = sacd:
        $gesCargos = new GestorCargo();
        $aIdCargos_sacd = $gesCargos->getArrayCargosDeTipo('sacd');
        $txt_where_cargos = implode(',', array_keys($aIdCargos_sacd));

        // Los sacd los pongo en la base de datos comun.
        $oDbl = $GLOBALS['oDBC_Select'];
        $nom_tabla = 'c' . $this->getNomTabla();
        $oPersonaSet = new Set();
        $sQuery = "SELECT id_nom, id_cargo
				FROM $nom_tabla
				WHERE id_activ=" . $iid_activ . " AND id_cargo IN ($txt_where_cargos)
				ORDER BY id_cargo";
        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorActividadCargo.sacds';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $id_nom = $aDades['id_nom'];
            $oPersona = new PersonaSacd($id_nom);
            $oPersona->DBCarregar();
            if (empty($oPersona->getApellido1())) {
                // si estoy dentro y soy sv, puedo mirar la tabla correcta:
                if (ConfigGlobal::is_dmz() === FALSE && ConfigGlobal::mi_sfsv() === 1) {
                    $oPersona = Persona::NewPersona($id_nom);
                    // puede contestar: "no encuentro a nadie"
                    if (is_object($oPersona)) {
                        $oPersonaSet->add($oPersona);
                    }
                } else {
                    // Si es de otra dl, ya es lo que toca: No tengo acceso a la tablas de cp_sacd.
                    // Desde dentro accedo a PersonaIn, pero desde fuera NO.
                    // nom actividad:
                    $oActividad = new ActividadAll($iid_activ);
                    $nom_activ = $oActividad->getNom_activ();
                    $msg = sprintf(_("No se tiene acceso al nombre de (es de otra dl o el sacd no está en DB-comun) id_nom: %s"), $id_nom);
                    $msg .= '<br>';
                    $msg .= sprintf(_("afecta a la actividad: %s"), $nom_activ);
                    $msg .= '<br>';
                    echo $msg;
                }
            } else {
                $oPersonaSet->add($oPersona);
            }
        }
        return $oPersonaSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus ActividadCargo
     *
     * @param integer id_nom. de la persona
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array Una col·lecció d'objectes de tipus ActividadCargo
     */
    function getActividadCargosDeAsistente($aWhereNom, $aWhere = [], $aOperators = array())
    {
        // seleccionar las actividades segun los criterios de búsqueda.
        $GesActividades = new GestorActividad();
        $aListaIds = $GesActividades->getArrayIdsWithKeyFini($aWhere, $aOperators);

        $cCargos = $this->getActividadCargos($aWhereNom);
        // descarto los que no estan.
        $cCargosOk = [];
        $i = 0;
        foreach ($cCargos as $oActividadCargo) {
            $id_activ = $oActividadCargo->getId_activ();
            if (in_array($id_activ, $aListaIds)) {
                $i++;
                $oActividad = new ActividadAll($id_activ);
                $oF_ini = $oActividad->getF_ini();
                $f_ini_iso = $oF_ini->format('Y-m-d') . '#' . $i; // Añado $i por si empezan el mismo dia.
                $oActividadCargo->DBCarregar();
                $cCargosOk[$f_ini_iso] = $oActividadCargo;
            }
        }
        ksort($cCargosOk);
        return $cCargosOk;
    }


    /**
     * retorna un array amb els asistents i el carrec (si el té):
     *        $aAsis[$id_activ] = array('id_activ','id_nom','propio','id_cargo');
     *
     * @param array $aWhere para la asistencia (id_nom y plaza)
     * @param array $aOperador para la asistencia (id_nom y plaza)
     * @param array $aWhereAct para la Actividad
     * @param array $aOperadorAct para la Actividad
     * @return array|false
     */
    function getAsistenteCargoDeActividad($aWhere, $aOperador = [], $aWhereAct = [], $aOperadorAct = [])
    {

        if (empty($aWhere['id_nom'])) {
            return FALSE;
        }
        $id_nom = $aWhere['id_nom'];

        $GesAsistente = new GestorAsistente();
        $cAsistentes = $GesAsistente->getActividadesDeAsistente($aWhere, $aOperador, $aWhereAct, $aOperadorAct);

        $cCargos = $this->getActividadCargos(array('id_nom' => $id_nom));
        // seleccionar las actividades segun los criterios de búsqueda.
        $GesActividades = new GestorActividad();
        $aListaIds = $GesActividades->getArrayIdsWithKeyFini($aWhereAct, $aOperadorAct);
        // descarto los que no estan.
        $cActividadesOk = [];
        foreach ($cCargos as $oCargo) {
            $id_activ = $oCargo->getId_activ();
            if (in_array($id_activ, $aListaIds)) {
                $cActividadesOk[$id_activ] = $oCargo;
            }
        }
        // lista de id_activ ordenados.
        $aAsis = [];
        foreach ($cAsistentes as $f_ini_iso => $oAsistente) {
            $id_activ = $oAsistente->getId_activ();
            $propio = $oAsistente->getPropio();
            $plaza = $oAsistente->getPlaza();
            $aAsis[$id_activ] = ['id_activ' => $id_activ,
                'id_nom' => $id_nom,
                'propio' => $propio,
                'plaza' => $plaza,
            ];
        }
        // Añado los cargos
        foreach ($cActividadesOk as $id_activ => $oCargo) {
            $oCargo = $cActividadesOk[$id_activ];
            $id_cargo = $oCargo->getId_cargo();
            if (array_key_exists($id_activ, $aAsis)) {
                // Añado al primero el id_cargo del segundo.
                $aAsis[$id_activ]['id_cargo'] = $id_cargo;
            } else {
                // añado la actividad
                $aAsis[$id_activ] = ['id_activ' => $id_activ,
                    'id_nom' => $id_nom,
                    'propio' => 'f',
                    'id_cargo' => $id_cargo,
                    'plaza' => 0,
                ];
            }
        }
        return $aAsis;
    }

    /**
     * retorna un array amb els carrecs (perque sigui compatible amb: getAsistenteCargoDeActividad).
     *       $aAsis[$id_activ] = array('id_activ','id_nom','propio','id_cargo');
     *
     * @param array $aWhere para la asistencia (id_nom y plaza)
     * @param array $aOperador para la asistencia (id_nom y plaza)
     * @param array $aWhereAct para la Actividad
     * @param array $aOperadorAct para la Actividad
     * @return array|false
     */
    function getCargoDeActividad($aWhere, $aOperador = [], $aWhereAct = [], $aOperadorAct = [])
    {

        if (empty($aWhere['id_nom'])) {
            return FALSE;
        }
        $id_nom = $aWhere['id_nom'];

        $cCargos = $this->getActividadCargos(array('id_nom' => $id_nom));
        // seleccionar las actividades segun los criterios de búsqueda.
        $GesActividades = new GestorActividad();
        $aListaIds = $GesActividades->getArrayIdsWithKeyFini($aWhereAct, $aOperadorAct);
        // descarto los que no están.
        $cActividadesOk = [];
        foreach ($cCargos as $oCargo) {
            $id_activ = $oCargo->getId_activ();
            if (in_array($id_activ, $aListaIds)) {
                $cActividadesOk[$id_activ] = $oCargo;
            }
        }
        // lista de id_activ ordenados.
        $aAsis = [];
        foreach ($cActividadesOk as $id_activ => $oCargo) {
            $oCargo = $cActividadesOk[$id_activ];
            $id_cargo = $oCargo->getId_cargo();
            if (array_key_exists($id_activ, $aAsis)) {
                // Añado al primero el id_cargo del segundo.
                $aAsis[$id_activ]['id_cargo'] = $id_cargo;
            } else {
                // añado la actividad
                $aAsis[$id_activ] = ['id_activ' => $id_activ,
                    'id_nom' => $id_nom,
                    'propio' => 'f',
                    'id_cargo' => $id_cargo,
                    'plaza' => 0,
                ];
            }
        }
        return $aAsis;
    }

    /**
     * retorna l'array d'objectes de tipus ActividadCargo
     *
     * @param string sQuery la query a executar.
     * @return array|false
     */
    function getActividadCargosQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $oActividadCargoSet = new Set();
        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorActividadCargo.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('id_item' => $aDades['id_item'], 'id_schema' => $aDades['id_schema']);
            $oActividadCargo = new ActividadCargo($a_pkey);
            $oActividadCargoSet->add($oActividadCargo);
        }
        return $oActividadCargoSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus ActividadCargo
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array|void
     */
    function getActividadCargos($aWhere = [], $aOperators = array())
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oActividadCargoSet = new Set();
        $oCondicion = new Condicion();
        $aCondi = [];
        foreach ($aWhere as $camp => $val) {
            if ($camp === '_ordre') continue;
            $sOperador = isset($aOperators[$camp]) ? $aOperators[$camp] : '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) $aCondi[] = 'a.' . $a;
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
        if (empty($aWhere['_ordre'])) {
            // Por defecto ordenar por orden_cargo:
            $aWhere['_ordre'] = 'orden_cargo';
            $gesOrdenCargo = new GestorCargo();
            $cOrdenCargo = $gesOrdenCargo->getCargos();
            $csvNestIdCargo = '';
            $csvNestOrdenCargo = '';
            foreach ($cOrdenCargo as $oOrdenCargo) {
                $id_cargo = $oOrdenCargo->getId_cargo();
                $orden_cargo = $oOrdenCargo->getOrden_cargo();
                $csvNestIdCargo .= empty($csvNestIdCargo) ? '' : ',';
                $csvNestIdCargo .= $id_cargo;
                $csvNestOrdenCargo .= empty($csvNestOrdenCargo) ? '' : ',';
                $csvNestOrdenCargo .= $orden_cargo;
            }
        }
        if (isset($aWhere['_ordre']) && $aWhere['_ordre'] != '') $sOrdre = ' ORDER BY ' . $aWhere['_ordre'];
        if (isset($aWhere['_ordre'])) unset($aWhere['_ordre']);

        if (!empty($csvNestIdCargo) && !empty($csvNestOrdenCargo)) {
            $sQry = "SELECT a.* 
                FROM $nom_tabla a LEFT JOIN UNNEST (ARRAY[$csvNestIdCargo], ARRAY[$csvNestOrdenCargo]) AS c (id_cargo,orden_cargo) ON (a.id_cargo = c.id_cargo) 
                " . $sCondi . $sOrdre . $sLimit;
        } else {
            $sQry = "SELECT a.* 
                FROM $nom_tabla a 
                " . $sCondi . $sOrdre . $sLimit;
        }
        //echo "query $sQry <br>";
        if (($oDblSt = $oDbl->prepare($sQry)) === false) {
            $sClauError = 'GestorActividadCargo.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClauError = 'GestorActividadCargo.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_item' => $aDades['id_item'], 'id_schema' => $aDades['id_schema']);
            $oActividadCargo = new ActividadCargo($a_pkey);
            $oActividadCargoSet->add($oActividadCargo);
        }
        return $oActividadCargoSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}
