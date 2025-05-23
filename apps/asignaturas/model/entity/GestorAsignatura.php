<?php

namespace asignaturas\model\entity;

use core\ClaseGestor;
use core\Condicion;
use core\Set;
use web\Desplegable;

/**
 * GestorAsignatura
 *
 * Classe per gestionar la llista d'objectes de la clase Asignatura
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 29/11/2010
 */
class GestorAsignatura extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     * @return GestorAsignatura
     *
     */
    function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('xa_asignaturas');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna un array
     * Les posibles assignatures.
     *
     * @return array|false
     */
    function getArrayAsignaturas()
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_asignatura,nombre_corto FROM $nom_tabla ORDER BY id_asignatura";
        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorAsignatura.array';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aOpciones = [];
        foreach ($oDbl->query($sQuery) as $aClave) {
            $id_asignatura = $aClave[0];
            $nombre_corto = $aClave[1];
            $aOpciones[$id_asignatura] = $nombre_corto;
        }
        return $aOpciones;
    }

    /**
     * Devuelve una lista con los id_nivel de las opcionales.
     *
     * @param string $formato 'csv'
     * @return string
     */
    public function getListaOpGenericas(string $formato = '')
    {
        switch ($formato) {
            case 'json':
                $genericas = "[\"1230\",\"1231\",\"1232\",\"2430\",\"2431\",\"2432\",\"2433\",\"2434\"]";
                break;
            case 'csv':
            default:
                $genericas = "1230,1231,1232,2430,2431,2432,2433,2434";
        }
        return $genericas;
    }

    /**
     * retorna JSON llista d'Asignaturas
     *
     * @param string sQuery la query a executar.
     * @return false|object
     */
    function getJsonAsignaturas($aWhere)
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sCondi = '';
        foreach ($aWhere as $camp => $val) {
            if ($camp === 'nombre_asignatura' && !empty($val)) {
                $sCondi .= "WHERE status=true AND nombre_asignatura ILIKE '%$val%'";
            }
            if ($camp === 'id' && !empty($val)) {
                if (!empty($sCondi)) {
                    $sCondi .= " AND id_asignatura = $val";
                } else {
                    $sCondi .= "WHERE id_asignatura = $val";
                }
            }
        }
        $sOrdre = " ORDER BY id_nivel";
        $sLimit = " LIMIT 25";
        $sQuery = "SELECT DISTINCT id_asignatura,nombre_asignatura,id_nivel FROM $nom_tabla " . $sCondi . $sOrdre . $sLimit;
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorAsignatura.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $json = '[';
        $i = 0;
        foreach ($oDbl->query($sQuery) as $aClave) {
            $i++;
            $id_asignatura = $aClave[0];
            $nombre_asignatura = $aClave[1];
            $nombre_asignatura = str_replace('"', '\\"', $nombre_asignatura);
            $nombre_asignatura = str_replace("'", "\\'", $nombre_asignatura);
            $json .= ($i > 1) ? ',' : '';
            $json .= "{\"value\":\"$id_asignatura\",\"label\":\"$nombre_asignatura\"}";
        }
        $json .= ']';
        return $json;
    }

    /**
     * retorna un array del tipus: id_asignatura => array(nombre_asignatura, creditos)
     *
     * @return array|false
     */
    function getArrayAsignaturasCreditos()
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_asignatura, nombre_asignatura, creditos FROM $nom_tabla ORDER BY id_nivel";
        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorAsignatura.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aOpciones = [];
        foreach ($oDbl->query($sQuery) as $row) {
            $id_asignatrura = $row[0];
            $nombre_asignatura = $row[1];
            $creditos = $row[2];
            $aOpciones[$id_asignatrura] = array('nombre_asignatura' => $nombre_asignatura, 'creditos' => $creditos);
        }
        return $aOpciones;
    }

    /**
     * retorna un objecte del tipus Desplegable
     * Les posibles asignatures
     *
     * @param bool $op_genericas listar o no opcionales genéricas (opcional I...)
     * @return false|object
     */
    function getListaAsignaturas(bool $op_genericas = true)
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sWhere = "WHERE status = 't' ";
        if (!$op_genericas) {
            $genericas = $this->getListaOpGenericas('csv');
            $sWhere .= " AND id_nivel NOT IN ($genericas)";
        }
        //para hacer listados que primero salgan las normales y después las opcionales:
        //$sQuery="SELECT id_asignatura, nombre_asignatura FROM $nom_tabla $sWhere ORDER BY nombre_asignatura";
        $sQuery = "SELECT id_asignatura, nombre_asignatura, CASE WHEN id_nivel < 3000 THEN xa_asignaturas.id_nivel ELSE 3001 END AS op FROM $nom_tabla $sWhere ORDER BY op,nombre_asignatura;";
        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorAsignatura.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aOpciones = [];
        $c = 0;
        foreach ($oDbl->query($sQuery) as $aClave) {
            $clave = $aClave[0];
            $val = $aClave[1];
            $id_op = $aClave[2];
            if ($id_op > 3000 && $c < 1) {
                $aOpciones[3000] = '----------';
                $c = 1;
            }
            $aOpciones[$clave] = $val;
        }
        return new Desplegable('', $aOpciones, '', true);
    }

    /**
     * retorna l'array d'objectes de tipus Asignatura
     *
     * @param string sQuery la query a executar.
     * @return array|false
     */
    function getAsignaturasQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $oAsignaturaSet = new Set();
        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorAsignatura.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('id_asignatura' => $aDades['id_asignatura']);
            $oAsignatura = new Asignatura($a_pkey);
            $oAsignaturaSet->add($oAsignatura);
        }
        return $oAsignaturaSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus Asignatura
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array|void
     */
    function getAsignaturas($aWhere = [], $aOperators = array())
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oAsignaturaSet = new Set();
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
            $sClauError = 'GestorAsignatura.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClauError = 'GestorAsignatura.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_asignatura' => $aDades['id_asignatura']);
            $oAsignatura = new Asignatura($a_pkey);
            $oAsignaturaSet->add($oAsignatura);
        }
        return $oAsignaturaSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/
}