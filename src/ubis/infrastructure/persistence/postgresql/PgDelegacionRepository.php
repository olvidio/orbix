<?php

namespace src\ubis\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use PDO;
use src\shared\config\ConfigGlobal;
use src\ubis\domain\RegionStgrAviso;
use src\ubis\domain\RegionStgrConfigException;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\postgresql\Set;
use src\shared\traits\HandlesPdoErrors;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\entity\Delegacion;

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
        $oDbl = GlobalPdo::get('oDBPC');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBPC_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('xu_dl');
    }

    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    public function soy_region_stgr(string $dele = ''): bool
    {
        if ($dele === '') {
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
        if ($stmt === false) {
            return false;
        }

        $aDades = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!is_array($aDades) || !isset($aDades['region_stgr'])) {
            throw new RegionStgrConfigException(RegionStgrAviso::TIPO_REGION_STGR_FALTA, $dele);
        }
        $regionStgrRaw = $aDades['region_stgr'];
        $regionStgr = is_scalar($regionStgrRaw) ? (string) $regionStgrRaw : '';
        if ($regionStgr === '') {
            throw new RegionStgrConfigException(RegionStgrAviso::TIPO_REGION_STGR_FALTA, $dele);
        }
        $region_stgr = 'cr' . $regionStgr;

        return $dele === $region_stgr;
    }

    public function mi_region_stgr(string $dele = ''): array
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
        if ($stmt === false) {
            return [];
        }

            $aDades = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!is_array($aDades) || $aDades === []) {
                throw new RegionStgrConfigException(RegionStgrAviso::TIPO_DL_NO_ENCONTRADA, $dele);
            }
            if (!isset($aDades['region'], $aDades['region_stgr'])) {
                throw new RegionStgrConfigException(RegionStgrAviso::TIPO_DL_NO_ENCONTRADA, $dele);
            }
            $region_dele = (string) $aDades['region'];
            $region_stgr = (string) $aDades['region_stgr'];
            if ($region_stgr === '') {
                throw new RegionStgrConfigException(RegionStgrAviso::TIPO_REGION_STGR_FALTA, $dele);
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
        if ($stmt === false) {
            throw new RegionStgrConfigException(
                RegionStgrAviso::TIPO_ESQUEMA_NO_ENCONTRADO,
                $dele,
                $esquema_region_stgr,
            );
        }

        $id_esquema_dele = null;
        foreach ($stmt as $aDades) {
            if (!is_array($aDades) || !isset($aDades['schema'], $aDades['id'])) {
                continue;
            }
            if ((string) $aDades['schema'] === $esquema_region_stgr) {
                //$id_esquema_region_stgr = $aDades['id'];
            }
            if ((string) $aDades['schema'] === $esquema_dele) {
                $id_esquema_dele = $aDades['id'];
            }
        }
        if ($id_esquema_dele === null) {
            throw new RegionStgrConfigException(
                RegionStgrAviso::TIPO_ESQUEMA_NO_ENCONTRADO,
                $dele,
                $esquema_dele,
            );
        }

        return ['region_stgr' => $region_stgr,
            'esquema_region_stgr' => $esquema_region_stgr,
            //'id_esquema_region_stgr' => $id_esquema_region_stgr,
            'mi_id_schema' => $id_esquema_dele,
            'esquema_dl' => $esquema_dele,
        ];
    }

    /**
     * @return array<int|string, string>
     */
    public function getArrayIdSchemaRegionStgr(string $sRegionStgr, int $mi_sfsv): array
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
        if ($stmt === false) {
            return [];
        }

        $a_idschema = [];
        foreach ($stmt as $row) {
            if (!is_array($row) || !isset($row['schema'], $row['id'])) {
                continue;
            }
            $a_idschema[(string) $row['schema']] = (string) $row['id'];
        }

        return $a_idschema;
    }

    /**
     * retorna un Array [id_dl => "region-dl"], els esquemes d'una regió del stgr
     *
     * @return array<int|string, string>
     */
    public function getArraySchemasRegionStgr(string $sRegionStgr, ?int $mi_sfsv = null): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $sQuery = "SELECT u.id_dl, u.region, u.dl FROM $nom_tabla u 
                 WHERE active = 't' AND region_stgr = '$sRegionStgr'
                 ORDER BY region,dl";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $a_schema = [];
        foreach ($stmt as $row) {
            if (!is_array($row) || !isset($row['id_dl'], $row['region'], $row['dl'])) {
                continue;
            }
            $id_dl = $row['id_dl'];
            $region = (string) $row['region'];
            $dl = (string) $row['dl'];
            if ($mi_sfsv === 1) {
                $dl .= 'v';
            } elseif ($mi_sfsv === 2) {
                $dl .= 'f';
            }
            $a_schema[$id_dl] = $region . '-' . $dl;
        }

        return $a_schema;
    }

    /**
     * retorna un Array, les dl d'una regió del stgr
     *
     * @param list<string> $aRegiones
     * @return array<int|string, string>
     */
    public function getArrayDlRegionStgr(array $aRegiones = []): array
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
        if ($stmt === false) {
            return [];
        }

        $a_dl = [];
        foreach ($stmt as $row) {
            if (!is_array($row) || !isset($row['id_dl'], $row['dl'])) {
                continue;
            }
            $a_dl[$row['id_dl']] = (string) $row['dl'];
        }

        return $a_dl;
    }

    /**
     * devuelve una colección (array) de objetos de tipo Delegacion
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<Delegacion> Una colección de objetos de tipo Delegacion
     */
    public function getDelegaciones(array $aWhere = [], array $aOperators = []): array
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
        $ordreVal = $aWhere['_ordre'] ?? null;
        if (is_string($ordreVal) && $ordreVal !== '') {
            $sOrdre = ' ORDER BY ' . $ordreVal;
        }
        if (isset($aWhere['_ordre'])) {
            unset($aWhere['_ordre']);
        }
        $limitVal = $aWhere['_limit'] ?? null;
        if ((is_string($limitVal) || is_int($limitVal)) && (string) $limitVal !== '') {
            $sLimit = ' LIMIT ' . $limitVal;
        }
        if (isset($aWhere['_limit'])) {
            unset($aWhere['_limit']);
        }
        $sQry = "SELECT * FROM $nom_tabla " . $sCondicion . $sOrdre . $sLimit;
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $aWhere, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $delegaciones = [];
        foreach ($filas as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            $normalized = [];
            foreach ($aDatos as $key => $value) {
                $normalized[(string) $key] = $value;
            }
            $delegaciones[] = Delegacion::fromArray($normalized);
        }
        return $delegaciones;
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Delegacion $Delegacion): bool
    {
        $dl = $Delegacion->getDlVo()->value() ?? '';
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

        $aDatos = $Delegacion->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_dl']);
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
            $campos = "(id_dl,dl,region,nombre_dl,active,grupo_estudios,region_stgr)";
            $valores = "(:id_dl,:dl,:region,:nombre_dl,:active,:grupo_estudios,:region_stgr)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_dl): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_dl = $id_dl ";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return true;
        }
        if (!$stmt->rowCount()) {
            return TRUE;
        }
        return false;
    }

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_dl): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_dl = $id_dl ";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return false;
        }
        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string) $key] = $value;
        }
        return $result;

    }


    /**
     * Busca la clase con dl en la base de datos .
     */
    public function findById(int $id_dl): ?Delegacion
    {
        $aDatos = $this->datosById($id_dl);
        if ($aDatos === false) {
            return null;
        }
        return Delegacion::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('xu_dl_id_dl_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}