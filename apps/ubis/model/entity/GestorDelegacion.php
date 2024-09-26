<?php

namespace ubis\model\entity;

use core\ClaseGestor;
use core\Condicion;
use core\ConfigGlobal;
use core\Set;
use Exception;
use web;
use function core\is_true;

/**
 * GestorDelegacion
 *
 * Classe per gestionar la llista d'objectes de la clase Delegacion
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 20/11/2010
 */
class GestorDelegacion extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     * @return GestorDelegacion
     *
     */
    function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('xu_dl');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    public static function getDlFromSchema(string $esquema): string
    {
        $a_reg = explode('-', $esquema);
        $dl = $a_reg[1];
        // quito la v o la f.
        if (substr($dl, -1) === 'v' || substr($dl, -1) === 'f') {
            $dl = substr($a_reg[1], 0, -1);
        }
        return $dl;
    }

    /**
     * @throws Exception
     */
    public function mi_region_stgr($dele = '')
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        if (empty($dele)) {
            $dele = ConfigGlobal::mi_dele();
        }

        $sQuery = "SELECT region_stgr, region
                        FROM $nom_tabla
                        WHERE dl = '$dele'";

        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorDelegacion.region_stgr';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
        if ($aDades === FALSE || empty($aDades)) {
            $message = sprintf(_("No se encuentra información de la dl: %s"), $dele);
            throw new \RuntimeException($message);
        }
        $region_dele = $aDades['region'];
        $region_stgr = $aDades['region_stgr'];
        if (empty($aDades['region_stgr'])) {
            $message = sprintf(_("falta indicar a que región del stgr pertenece la dl: %s"), $dele);
            throw new \RuntimeException($message);
        }
        // nombre del esquema
        $esquema_dele = $region_dele . '-' . $dele;
        $esquema_region_stgr = $region_stgr . '-cr' . $region_stgr;
        // caso especial de H:
        if ($region_stgr === 'H') {
            $esquema_region_stgr = 'H-H';
        }
        if (ConfigGlobal::mi_sfsv() === 2) {
            $esquema_region_stgr .= 'f';
            $esquema_dele .= 'f';
        } else {
            $esquema_region_stgr .= 'v';
            $esquema_dele .= 'v';
        }

        // buscar el id_schema de $esquema_region_stgr y de $dele
        $sQuery = "SELECT schema, id 
                        FROM db_idschema
                        WHERE schema = '$esquema_region_stgr' OR schema = '$esquema_dele'";

        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorDelegacion.region_stgr';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            if ($aDades === FALSE) {
                $message = sprintf(_("No se encuentra el id del esquema: %s"), $esquema_region_stgr);
                throw new \RuntimeException($message);
            }
            if ($aDades['schema'] === $esquema_region_stgr) {
                $id_esquema_region_stgr = $aDades['id'];
            }
            if ($aDades['schema'] === $esquema_dele) {
                $id_esquema_dele = $aDades['id'];
            }
        }
        return ['region_stgr' => $region_stgr,
            'esquema_region_stgr' => $esquema_region_stgr,
            'id_esquema_region_stgr' => $id_esquema_region_stgr,
            'mi_id_schema' => $id_esquema_dele,
        ];
    }

    public
    function getArrayIdSchemaRegionStgr($sRegionStgr, $mi_sfsv)
    {
        $oDbl = $this->getoDbl_Select();
        $a_schemas = $this->getArraySchemasRegionStgr($sRegionStgr, $mi_sfsv);

        $list_dl = '';
        foreach ($a_schemas as $schema) {
            $list_dl .= empty($list_dl) ? '' : ", ";
            $list_dl .= "'$schema'::character varying";
        }
        $where = "(db_idschema.schema)::text = any ((array[$list_dl])::text[])";

        $sQuery = "SELECT schema, id FROM db_idschema 
                 WHERE $where
                ";
        //echo "query: $sQuery";
        $a_idschema = [];
        foreach ($oDbl->query($sQuery) as $row) {
            $schema = $row['schema'];
            $id = $row['id'];
            $a_idschema[$schema] = $id;
        }
        return $a_idschema;
    }

    /**
     * retorna un objecte del tipus Array, els esquemes d'una regió del stgr
     *
     * @param string region.
     * @return array Una Llista d'esquemes.
     */
    function getArraySchemasRegionStgr($sRegionStgr, $mi_sfsv)
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $sQuery = "SELECT u.id_dl, u.region, u.dl FROM $nom_tabla u 
                 WHERE status = 't' AND region_stgr = '$sRegionStgr'
                 ORDER BY region,dl";
        //echo "query: $sQuery";
        $a_schema = [];
        foreach ($oDbl->query($sQuery) as $row) {
            $id_dl = $row['id_dl'];
            $region = $row['region'];
            $dl = $row['dl'];
            if ($mi_sfsv === 1) {
                $dl .= 'v';
            } elseif ($mi_sfsv === 2) {
                $dl .= 'f';
            }
            $a_schema[$id_dl] = "$region-$dl";
        }
        return $a_schema;
    }

    /**
     * retorna un objecte del tipus Array, les dl d'una regió del stgr
     *
     * @param array optional lista de regions.
     * @return array Una Llista de delegacions.
     */
    function getArrayDlRegionStgr($aRegiones = array())
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $num_regiones = count($aRegiones);
        if ($num_regiones > 0) {
            $sCondicion = "WHERE status = 't' AND region_stgr = ";
            $sReg = implode("'OR region_stgr = '", $aRegiones);
            $sReg = "'" . $sReg . "'";
            $sCondicion .= $sReg;
            $sQuery = "SELECT u.id_dl,u.dl FROM $nom_tabla u 
					$sCondicion
					ORDER BY dl";
        } else {
            $sQuery = "SELECT id_dl, dl
					FROM $nom_tabla
					ORDER BY dl";
        }
        //echo "query: $sQuery";
        $a_dl = array();
        foreach ($oDbl->query($sQuery) as $row) {
            $id_dl = $row['id_dl'];
            $dl = $row['dl'];
            $a_dl[$id_dl] = $dl;
        }
        return $a_dl;
    }

    /**
     * retorna un objecte del tipus Desplegable
     *
     * @param array optional lista de regions.
     * @return object Una Llista de delegacions i regions per filtrar.
     */
    function getListaDlURegionesFiltro($isfsv = '')
    {
        switch ($isfsv) {
            case 1:
                $sf = '';
                break;
            case 2:
                $sf = 'f';
                break;
            default:
                $sf = (ConfigGlobal::mi_sfsv() == 2) ? 'f' : '';
        }
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $sQuery = "SELECT 'dl|'||dl||'$sf', nombre_dl||' ('||dl||'$sf)'
                FROM $nom_tabla
                WHERE status = 't'
                ORDER BY 2";

        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorDelegacion.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        return new web\Desplegable('', $oDblSt, '', true);
    }

    /**
     * retorna un objecte del tipus Desplegable
     * Es fa servir ConfigGlobal::mi_dele() sense la 'f' perque es global a les dues seccions.
     *
     * @param boolean si se incluye la dl o no
     * @return object Una Llista de delegacions (amb el nom de la regió).
     */
    function getListaRegDele($bdl = 't')
    {
        $sf = (ConfigGlobal::mi_sfsv() == 2) ? 'f' : '';
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        if (is_true($bdl)) {
            $sQuery = "SELECT region||'-'||dl||'$sf', nombre_dl||' ('||dl||'$sf)'
					FROM $nom_tabla
                    WHERE status = 't'
					ORDER BY 2";
        } else {
            $sQuery = "SELECT region||'-'||dl||'$sf', nombre_dl||' ('||dl||'$sf)'
					FROM $nom_tabla 
                    WHERE dl != '" . ConfigGlobal::mi_dele() . "'
                        AND status = 't'
					ORDER BY 2";
        }
        //echo "sql: $sQuery<br>";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorDelegacion.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        return new web\Desplegable('', $oDblSt, '', true);
    }

    /**
     * retorna un objecte del tipus Desplegable
     *
     * @param integer $sfsv 1 para sv, 2 para sf
     * @param boolean si se incluye la dl o no
     * @return object Una Llista de delegacions i regions.
     */
    function getListaDelegacionesURegiones($isfsv = 0, $bdl = 't')
    {
        if (empty($isfsv)) {
            $isfsv = ConfigGlobal::mi_sfsv();
        }
        $sf = ($isfsv == 2) ? 'f' : '';
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        // Ahora pongo las regiones que pueden organizar (todas) en la tabla de las dl.
        if (is_true($bdl)) {
            $sQuery = "SELECT dl||'$sf', nombre_dl||' ('||region||'-'||dl||'$sf)'
					FROM $nom_tabla
                    WHERE status = 't'
					ORDER BY 2";
        } else {
            $sQuery = "SELECT dl||'$sf', nombre_dl||' ('||region||'-'||dl||'$sf)'
					FROM $nom_tabla WHERE dl != '" . ConfigGlobal::mi_dele() . "'
                    WHERE status = 't'
					ORDER BY 2";
        }

        //echo "sql: $sQuery<br>";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorDelegacion.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        return new web\Desplegable('', $oDblSt, '', true);
    }

    /**
     * retorna un objecte del tipus Desplegable
     *
     * @param array optional lista de regions.
     * @return object Una Llista de delegacions.
     */
    function getListaDelegaciones($aRegiones = array())
    {
        $isfsv = ConfigGlobal::mi_sfsv();
        $sf = ($isfsv == 2) ? 'f' : '';
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $num_regiones = count($aRegiones);
        if ($num_regiones > 0) {
            $sCondicion = "WHERE status = 't' AND region = ";
            $sReg = implode("'OR region = '", $aRegiones);
            $sReg = "'" . $sReg . "'";
            $sCondicion .= $sReg;
            $sQuery = "SELECT dl, nombre_dl||' ('||region||'-'||dl||'$sf)'
                    FROM $nom_tabla 
					$sCondicion
					ORDER BY nombre_dl";
        } else {
            $sQuery = "SELECT dl, nombre_dl||' ('||region||'-'||dl||'$sf)'
					FROM $nom_tabla
                    WHERE status = 't'
					ORDER BY nombre_dl";
        }
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorDelegacion.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        return new web\Desplegable('', $oDblSt, '', true);
    }

    /**
     * retorna un objecte del tipus Array
     *
     * @param array optional lista de regions.
     * @return array Una Llista de delegacions.
     */
    function getArrayDelegaciones($aRegiones = array())
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $num_regiones = count($aRegiones);
        if ($num_regiones > 0) {
            $sCondicion = "WHERE status = 't' AND region = ";
            $sReg = implode("'OR region = '", $aRegiones);
            $sReg = "'" . $sReg . "'";
            $sCondicion .= $sReg;
            $sQuery = "SELECT u.id_dl,u.dl FROM $nom_tabla u 
					$sCondicion
					ORDER BY dl";
        } else {
            $sQuery = "SELECT id_dl, dl
					FROM $nom_tabla
					ORDER BY dl";
        }
        //echo "query: $sQuery";
        $a_dl = array();
        foreach ($oDbl->query($sQuery) as $row) {
            $id_dl = $row['id_dl'];
            $dl = $row['dl'];
            $a_dl[$id_dl] = $dl;
        }
        return $a_dl;
    }

    function getArrayDelegacionesActuales()
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $isfsv = ConfigGlobal::mi_sfsv();
        $sf = ($isfsv == 2) ? 'f' : '';

        $sQuery = "SELECT dl, nombre_dl||' ('||region||'-'||dl||'$sf)'
                   FROM $nom_tabla u 
					WHERE status = 't'
					ORDER BY nombre_dl";
        //echo "query: $sQuery";
        $a_dl = array();
        foreach ($oDbl->query($sQuery) as $row) {
            $dl_sigla = $row[0];
            $dl_nom = $row[1];
            $a_dl[$dl_sigla] = $dl_nom;
        }
        return $a_dl;
    }

    /**
     * retorna l'array d'objectes de tipus Delegacion
     *
     * @param string sQuery la query a executar.
     * @return array Una col·lecció d'objectes de tipus Delegacion
     */
    function getDelegacionesQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $oDelegacionSet = new Set();
        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorDelegacion.query';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('dl' => $aDades['dl'],
                'region' => $aDades['region']);
            $oDelegacion = new Delegacion($a_pkey);
            $oDelegacionSet->add($oDelegacion);
        }
        return $oDelegacionSet->getTot();
    }

    /**
     * retorna l'array d'objectes de tipus Delegacion
     *
     * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
     * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
     * @return array Una col·lecció d'objectes de tipus Delegacion
     */
    function getDelegaciones($aWhere = array(), $aOperators = array())
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oDelegacionSet = new Set();
        $oCondicion = new Condicion();
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
        $sQry = "SELECT * FROM $nom_tabla " . $sCondi . $sOrdre . $sLimit;
        if (($oDblSt = $oDbl->prepare($sQry)) === false) {
            $sClauError = 'GestorDelegacion.llistar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        if (($oDblSt->execute($aWhere)) === false) {
            $sClauError = 'GestorDelegacion.llistar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
            return false;
        }
        foreach ($oDblSt as $aDades) {
            $oDelegacion = new Delegacion($aDades['id_dl']);
            $oDelegacionSet->add($oDelegacion);
        }
        return $oDelegacionSet->getTot();
    }

    /* MÉTODOS PROTECTED --------------------------------------------------------*/

    /* MÉTODOS GET y SET --------------------------------------------------------*/

}