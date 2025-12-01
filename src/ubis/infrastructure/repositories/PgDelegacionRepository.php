<?php

namespace src\ubis\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\ConfigGlobal;
use core\Set;
use PDO;
use RuntimeException;
use src\shared\traits\HandlesPdoErrors;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\entity\Delegacion;
use function core\is_true;

/**
 * Clase que adapta la tabla xu_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 5/11/2025
 */
class PgDelegacionRepository extends ClaseRepository implements DelegacionRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('xu_dl');
    }

    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    public function soy_region_stgr($dele = ''): bool
    {
        if (empty($dele)) {
            $dele = ConfigGlobal::mi_dele();
        }

        // caso especial de H:
        if ($dele === 'H') {
            return true;
        }
        // caso especial de M:
        if ($dele === 'M') {
            return true;
        }

        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $sQuery = "SELECT region_stgr, region FROM $nom_tabla WHERE dl = '$dele'";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        $aDades = $stmt->fetch(\PDO::FETCH_ASSOC);
        $region_stgr = 'cr' . $aDades['region_stgr'];
        if (empty($aDades['region_stgr'])) {
            $message = sprintf(_("falta indicar a que región del stgr pertenece la dl: %s"), $dele);
            throw new RunTimeException($message);
        }

        return $dele === $region_stgr;
    }

    public function mi_region_stgr($dele = ''): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        if (empty($dele)) {
            $dele = ConfigGlobal::mi_dele();
        }
        // caso especial de H y M:
        if ($dele === 'H' || $dele === 'M') {
            $region_dele = $dele;
            $region_stgr = $dele;
        } else {
            $sQuery = "SELECT region_stgr, region FROM $nom_tabla WHERE dl = '$dele'";
            $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

            $aDades = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($aDades === false || empty($aDades)) {
                $message = sprintf(_("No se encuentra información de la dl: %s"), $dele);
                throw new RunTimeException($message);
            }
            $region_dele = $aDades['region'];
            $region_stgr = $aDades['region_stgr'];
            if (empty($aDades['region_stgr'])) {
                $message = sprintf(_("falta indicar a que región del stgr pertenece la dl: %s"), $dele);
                throw new RunTimeException($message);
            }
        }
        // nombre del esquema
        $esquema_dele = $region_dele . '-' . $dele;
        $esquema_region_stgr = $region_stgr . '-cr' . $region_stgr;
        // caso especial de H:
        if ($region_stgr === 'H') {
            $esquema_region_stgr = 'H-H';
        }
        // caso especial de M:
        if ($region_stgr === 'M') {
            $esquema_region_stgr = 'M-M';
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
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        foreach ($stmt as $aDades) {
            if ($aDades === false) {
                $message = sprintf(_("No se encuentra el id del esquema: %s"), $esquema_region_stgr);
                throw new RunTimeException($message);
            }
            if ($aDades['schema'] === $esquema_region_stgr) {
                //$id_esquema_region_stgr = $aDades['id'];
            }
            if ($aDades['schema'] === $esquema_dele) {
                $id_esquema_dele = $aDades['id'];
            }
        }

        return ['region_stgr' => $region_stgr,
            'esquema_region_stgr' => $esquema_region_stgr,
            //'id_esquema_region_stgr' => $id_esquema_region_stgr,
            'mi_id_schema' => $id_esquema_dele,
            'esquema_dl' => $esquema_dele,
        ];
    }

    public function getArrayIdSchemaRegionStgr($sRegionStgr, $mi_sfsv): array
    {
        $mi_sfsv_text = $mi_sfsv === 1 ? 'v' : 'f';
        $oDbl = $this->getoDbl_Select();
        $a_schemas = $this->getArraySchemasRegionStgr($sRegionStgr, $mi_sfsv);
        // añadir la propia:
        $a_schemas[] = $sRegionStgr . '-' . $sRegionStgr . $mi_sfsv_text;

        $list_dl = '';
        foreach ($a_schemas as $schema) {
            $list_dl .= empty($list_dl) ? '' : ", ";
            $list_dl .= "'$schema'::character varying";
        }
        $where = "(db_idschema.schema)::text = any ((array[$list_dl])::text[])";

        $sQuery = "SELECT schema, id FROM db_idschema 
                 WHERE $where
                ";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        $a_idschema = [];
        foreach ($stmt as $row) {
            $schema = $row['schema'];
            $id = $row['id'];
            $a_idschema[$schema] = $id;
        }
        return $a_idschema;
    }

    /**
     * retorna un Array [id_dl => "region-dl"], els esquemes d'una regió del stgr
     *
     * @param string region.
     * @return array Una Llista d'esquemes.
     */
    public function getArraySchemasRegionStgr($sRegionStgr, $mi_sfsv): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $sQuery = "SELECT u.id_dl, u.region, u.dl FROM $nom_tabla u 
                 WHERE active = 't' AND region_stgr = '$sRegionStgr'
                 ORDER BY region,dl";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        $a_schema = [];
        foreach ($stmt as $row) {
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
     * retorna un Array, les dl d'una regió del stgr
     *
     * @param array optional lista de regions.
     * @return array Una Llista de delegacions.
     */
    public function getArrayDlRegionStgr($aRegiones = array()): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $num_regiones = count($aRegiones);
        if ($num_regiones > 0) {
            $sCondicion = "WHERE active = 't' AND region_stgr = ";
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
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        $a_dl = [];
        foreach ($stmt as $row) {
            $id_dl = $row['id_dl'];
            $dl = $row['dl'];
            $a_dl[$id_dl] = $dl;
        }
        return $a_dl;
    }

    /**
     * devuelve una colección (array) de objetos de tipo Delegacion
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo Delegacion
     */
    public function getDelegaciones(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $DelegacionSet = new Set();
        $oCondicion = new Condicion();
        $aCondicion = [];
        foreach ($aWhere as $camp => $val) {
            if ($camp === '_ordre') {
                continue;
            }
            if ($camp === '_limit') {
                continue;
            }
            $sOperador = $aOperators[$camp] ?? '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) {
                $aCondicion[] = $a;
            }
            // operadores que no requieren valores
            if ($sOperador === 'BETWEEN' || $sOperador === 'IS NULL' || $sOperador === 'IS NOT NULL' || $sOperador === 'OR') {
                unset($aWhere[$camp]);
            }
            if ($sOperador === 'IN' || $sOperador === 'NOT IN') {
                unset($aWhere[$camp]);
            }
            if ($sOperador === 'TXT') {
                unset($aWhere[$camp]);
            }
        }
        $sCondicion = implode(' AND ', $aCondicion);
        if ($sCondicion !== '') {
            $sCondicion = " WHERE " . $sCondicion;
        }
        $sOrdre = '';
        $sLimit = '';
        if (isset($aWhere['_ordre']) && $aWhere['_ordre'] !== '') {
            $sOrdre = ' ORDER BY ' . $aWhere['_ordre'];
        }
        if (isset($aWhere['_ordre'])) {
            unset($aWhere['_ordre']);
        }
        if (isset($aWhere['_limit']) && $aWhere['_limit'] !== '') {
            $sLimit = ' LIMIT ' . $aWhere['_limit'];
        }
        if (isset($aWhere['_limit'])) {
            unset($aWhere['_limit']);
        }
        $sQry = "SELECT * FROM $nom_tabla " . $sCondicion . $sOrdre . $sLimit;
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $aWhere, __METHOD__, __FILE__, __LINE__);

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            $Delegacion = new Delegacion();
            $Delegacion->setAllAttributes($aDatos);
            $DelegacionSet->add($Delegacion);
        }
        return $DelegacionSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Delegacion $Delegacion): bool
    {
        $dl = $Delegacion->getDlVo()?->value() ?? '';
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE dl = '$dl'";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Delegacion $Delegacion): bool
    {
        $id_dl = $Delegacion->getIdDlVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_dl);

        $aDatos = [];
        $aDatos['dl'] = $Delegacion->getDlVo()?->value();
        $aDatos['region'] = $Delegacion->getRegionVo()?->value();
        $aDatos['nombre_dl'] = $Delegacion->getNombreDlVo()?->value();
        $aDatos['active'] = $Delegacion->isActive();
        $aDatos['grupo_estudios'] = $Delegacion->getGrupoEstudiosVo()?->value();
        $aDatos['region_stgr'] = $Delegacion->getRegionStgrVo()?->value();
        array_walk($aDatos, 'core\poner_null');
        //para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDatos['active'])) {
            $aDatos['active'] = 'true';
        } else {
            $aDatos['active'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					dl                       = :dl,
					region                   = :region,
					nombre_dl                = :nombre_dl,
					active                   = :active,
					grupo_estudios           = :grupo_estudios,
					region_stgr              = :region_stgr";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_dl = $id_dl ";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            //INSERT
            $aDatos['id_dl'] = $Delegacion->getIdDlVo()->value();
            $campos = "(id_dl,dl,region,nombre_dl,active,grupo_estudios,region_stgr)";
            $valores = "(:id_dl,:dl,:region,:nombre_dl,:active,:grupo_estudios,:region_stgr)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_dl): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_dl = $id_dl ";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if (!$stmt->rowCount()) {
            return TRUE;
        }
        return false;
    }

    public function datosById(int $id_dl): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_dl = $id_dl ";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }


    /**
     * Busca la clase con dl en la base de datos .
     */
    public function findById(int $id_dl): ?Delegacion
    {
        $aDatos = $this->datosById($id_dl);
        if (empty($aDatos)) {
            return null;
        }
        return (new Delegacion())->setAllAttributes($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('xu_dl_id_dl_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}