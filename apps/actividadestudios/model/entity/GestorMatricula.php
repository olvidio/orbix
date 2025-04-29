<?php

namespace actividadestudios\model\entity;

use core\ClaseGestor;
use core\Condicion;
use core\Set;
use ubis\model\entity\GestorDelegacion;

/**
 * GestorMatricula
 *
 * Classe per gestionar la llista d'objectes de la clase Matricula
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 18/11/2014
 */
class GestorMatricula extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    
    function __construct()
    {
        $oDbl = $GLOBALS['oDBP'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('d_matriculas_activ');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna l'array d'objectes de tipus Matricula
     *
     * @param integer id_nom
     * @return array|false
     */
    function getMatriculasPendientes($id_nom = '')
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $oMatriculaSet = new Set();

        if (!empty($id_nom)) {
            $sQry = "SELECT * FROM $nom_tabla Where id_nom = $id_nom AND id_situacion IS NULL";
        } else {
            $sQry = "SELECT * FROM $nom_tabla Where id_situacion IS NULL";
        }

        if (($oDbl->query($sQry)) === false) {
            $sClauError = 'GestorMatricula.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDbl->query($sQry) as $aDades) {
            $a_pkey = array('id_activ' => $aDades['id_activ'],
                'id_asignatura' => $aDades['id_asignatura'],
                'id_nom' => $aDades['id_nom']);
            $oMatricula = new Matricula($a_pkey);
            $oMatriculaSet->add($oMatricula);
        }
        return $oMatriculaSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus Matricula
     *
     * @param string sQuery la query a executar.
     * @return array|false
     */
    function getMatriculasQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $clasename = get_class($this);
        $nomClase = join('', array_slice(explode('\\', $clasename), -1));

        $oMatriculaSet = new Set();
        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorMatricula.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('id_activ' => $aDades['id_activ'],
                'id_asignatura' => $aDades['id_asignatura'],
                'id_nom' => $aDades['id_nom']);
            switch ($nomClase) {
                case 'GestorMatricula':
                    $oMatricula = new Matricula($a_pkey);
                    break;
                case 'GestorMatriculaDl':
                    $oMatricula = new MatriculaDl($a_pkey);
                    break;
            }
            $oMatriculaSet->add($oMatricula);
        }
        return $oMatriculaSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus Matricula
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array|void
     */
    function getMatriculas($aWhere = [], $aOperators = array())
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $clasename = get_class($this);
        $nomClase = join('', array_slice(explode('\\', $clasename), -1));

        $oMatriculaSet = new Set();
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
            $sClauError = 'GestorMatricula.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClauError = 'GestorMatricula.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $a_pkey = array('id_activ' => $aDades['id_activ'],
                'id_asignatura' => $aDades['id_asignatura'],
                'id_nom' => $aDades['id_nom']);
            switch ($nomClase) {
                case 'GestorMatricula':
                    $oMatricula = new Matricula($a_pkey);
                    break;
                case 'GestorMatriculaDl':
                    $oMatricula = new MatriculaDl($a_pkey);
                    break;
            }
            $oMatriculaSet->add($oMatricula);
        }
        return $oMatriculaSet->getTot();
    }

    public function getMatriculasOtroStgr(false|array $a_IdActividades)
    {
        $oDbl = $this->getoDbl();
        $str_actividades = implode(', ', $a_IdActividades);
        /*
         $str_actividades = "{" . implode(', ', $a_IdActividades) . "}";
        $aWhere = ['id_activ' => $str_actividades];
        $aOperador = ['id_activ' => 'ANY'];
        */

        // Buscar dl y r dependientes de la actual región del stgr:
        $schema = $_SESSION['session_auth']['esquema'];
        $a_reg = explode('-', $schema);
        $RegionStgr = $a_reg[0];
        $gesDl = new GestorDelegacion();
        $a_dl_de_la_region_stgr = $gesDl->getArrayDlRegionStgr([$RegionStgr]);
        $str_dl = "'" . implode("', '",$a_dl_de_la_region_stgr) ."'";

        // Personas de paso asistentes a las actividades
        $sqlA  = "SELECT m.id_nom, m.id_activ 
                    FROM publicv.d_matriculas_activ m LEFT JOIN global.personas p USING (id_nom)
                     WHERE p.id_nom IS NULL AND m.id_activ IN ( $str_actividades)
                     GROUP BY id_nom, id_activ
                ";

        $sqlB = "SELECT m.id_nom, m.id_activ 
                    FROM publicv.d_matriculas_activ m 
                    WHERE  m.id_activ IN ($str_actividades) 
                    AND NOT EXISTS (SELECT id_nom FROM global.personas p WHERE p.id_nom = m.id_nom) 
                    GROUP BY id_nom, id_activ
                ";

        // Personas de otras regiones del stgr
        $sqlC = "SELECT m.id_nom, m.id_activ 
                    FROM publicv.d_matriculas_activ m LEFT JOIN global.personas p USING (id_nom) 
                    WHERE p.dl NOT IN ($str_dl) 
                    AND m.id_activ IN ($str_actividades) 
                    GROUP BY id_nom, id_activ
                  ";

        $sQry = "$sqlA  UNION  $sqlC ORDER BY id_nom";
        if (($oDblSt = $oDbl->query($sQry)) === false) {
            $sClauError = 'GestorMatricula.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aDades = $oDblSt->fetchAll(\PDO::FETCH_ASSOC);

        return $aDades;
        /*
        foreach ($oDbl as $aDades) {

        }
        */
    }
}
