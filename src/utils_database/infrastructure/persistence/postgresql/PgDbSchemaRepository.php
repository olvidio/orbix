<?php

namespace src\utils_database\infrastructure\persistence\postgresql;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use PDO;
use src\shared\infrastructure\GlobalPdo;
use src\shared\traits\HandlesPdoErrors;
use src\utils_database\domain\contracts\DbSchemaRepositoryInterface;
use src\utils_database\domain\entity\DbSchema;
use src\utils_database\domain\value_objects\DbSchemaCode;
use src\utils_database\domain\value_objects\DbSchemaId;

/**
 * Clase que adapta la tabla db_idschema a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 13/11/2025
 */
class PgDbSchemaRepository extends ClaseRepository implements DbSchemaRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBPC');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBPC_Select');
        $this->setoDbl_select($oDbl_Select);
        // Siempre en esquema public (sv/sv-e usan search_path hacia publicv; nombre sin calificar no resuelve).
        $this->setNomTabla('public.db_idschema');
    }

    /* -------------------- Otras Funciones ---------------------------------------- */

    /**
     * cambiar el nombre de un esquema existente: mantener el número.
     *     las tablas de las tres bases de datos (comun, sv, sf)
     */
    public function cambiarNombre(string $old, string $new, string $database): void
    {
        $this->DBCambiarNombre($old, $new, $database);
        $this->DBCambiarNombre($old . 'f', $new . 'f', $database);
        $this->DBCambiarNombre($old . 'v', $new . 'v', $database);
    }

    /**
     * llenar con los nuevos id, las tablas de las tres bases de datos (comun, sv, sf)
     */
    public function llenarNuevo(string $schema, string $database): void
    {
        $Id = $this->getNext($schema);
        $newId = new DbSchemaId($Id);
        $newIdSf = new DbSchemaId($Id - 1000);
        $newIdSv = new DbSchemaId($Id - 2000);

        // No se puede, porque e posible que todavía no exista el esquema
        // foreach (['comun','sv','sf'] as $database) {

        $oDbl = $this->connectar($database);
        $this->setoDbl($oDbl);

        // comun
        $oDbSchema = new DbSchema();
        $oDbSchema->setIdVo($newId);
        $oDbSchema->setSchemaVo(DbSchemaCode::fromString($schema));
        $this->Guardar($oDbSchema);

        // sf
        $oDbSchema = new DbSchema();
        $oDbSchema->setIdVo($newIdSf);
        $oDbSchema->setSchemaVo(DbSchemaCode::fromString($schema . 'f'));
        $this->Guardar($oDbSchema);

        // sv
        $oDbSchema = new DbSchema();
        $oDbSchema->setIdVo($newIdSv);
        $oDbSchema->setSchemaVo(DbSchemaCode::fromString($schema . 'v'));
        $this->Guardar($oDbSchema);
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo DbSchema
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<DbSchema>
     */
    public function getDbSchemas(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
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
        if (isset($aWhere['_ordre']) && is_string($aWhere['_ordre']) && $aWhere['_ordre'] !== '') {
            $sOrdre = ' ORDER BY ' . $aWhere['_ordre'];
        }
        if (isset($aWhere['_ordre'])) {
            unset($aWhere['_ordre']);
        }
        if (isset($aWhere['_limit']) && is_scalar($aWhere['_limit']) && (string) $aWhere['_limit'] !== '') {
            $sLimit = ' LIMIT ' . $aWhere['_limit'];
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
        $dbSchemas = [];
        foreach ($filas as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            $normalized = [];
            foreach ($aDatos as $key => $value) {
                $normalized[(string) $key] = $value;
            }
            $dbSchemas[] = DbSchema::fromArray($normalized);
        }

        return $dbSchemas;
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(DbSchema $DbSchema): bool
    {
        $schema = $DbSchema->getSchemaVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE schema = '$schema'";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(DbSchema $DbSchema): bool
    {
        $schema = $DbSchema->getSchemaVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($schema);

        $aDatos = $DbSchema->toArrayForDatabase();
        if ($bInsert === false) {
            unset($aDatos['schema']);
            //UPDATE
            $update = "
					id                       = :id";
            $sql = "UPDATE $nom_tabla SET $update WHERE schema = '$schema'";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        else {
            //INSERT
            $campos = "(schema,id)";
            $valores = "(:schema,:id)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(string $schema): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE schema = '$schema'";
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
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param string $schema
     * @return array<string, mixed>|false
     */
    public function datosById(string $schema): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE schema = '$schema'";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return false;
        }

        $row = [];
        foreach ($aDatos as $key => $value) {
            $row[(string) $key] = $value;
        }

        return $row;
    }

    /**
     * Busca la clase con schema en la base de datos .
     */
    public function findById(string $schema): ?DbSchema
    {
        $aDatos = $this->datosById($schema);
        if (!is_array($aDatos)) {
            return null;
        }
        return DbSchema::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT COALESCE(MAX(id), 2999) + 1 FROM $nom_tabla";
        $stmt = $this->pdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            throw new \RuntimeException(_('Error obteniendo nuevo id de db_idschema.'));
        }
        return (int) $stmt->fetchColumn();
    }

    /* -------------------- MÉTODOS PRIVADOS ---------------------------------------- */

    /**
     * Conecta con la base de datos: comun, sf, sv
     *
     * @param string $database
     * @return \PDO
     */
    private function connectar(string $database)
    {
        $oDB = null;
        switch ($database) {
            case 'comun':
                $oDB = $this->getoDbl();
                break;
            case 'sv':
                $oConfigDB = new ConfigDB('sv');
                $config = $oConfigDB->getEsquema('public');
                $oConexion = new DBConnection($config);
                $oDB = $oConexion->getPDO();
                break;
            case 'sv-e':
                $oConfigDB = new ConfigDB('sv-e');
                $config = $oConfigDB->getEsquema('public');
                $oConexion = new DBConnection($config);
                $oDB = $oConexion->getPDO();
                break;
            case 'sf':
                // Conectar Db df
                $oConfigDB = new ConfigDB('sf');
                $config = $oConfigDB->getEsquema('public');
                $oConexion = new DBConnection($config);
                $oDB = $oConexion->getPDO();
                $this->setoDbl($oDB);
                break;
            default:
                throw new \InvalidArgumentException(sprintf(_('Base de datos no soportada: %s'), $database));
        }
        return $oDB;
    }

    /**
     *  retorna el id_schema següent per un esquema.
     *  primer mira si ja hi és
     *
     * @param string $schema
     * @return int id_schema
     */
    private function getNext(string $schema): int
    {
        // comprobar si existe
        $cSchema = $this->getDbSchemas(['schema' => $schema]);
        if (empty($cSchema)) {
            $netxId = $this->getLast() + 1;
        }
        else {
            $oDbSchema = $cSchema[0];
            $netxId = $oDbSchema->getIdVo()->value();
        }
        return $netxId;
    }

    /**
     *  retorna el id_schema ultim del comun.
     *  primer mira si ja hi és
     *
     * @return integer id_schema
     */
    private function getLast(): int
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sQry = "SELECT id FROM $nom_tabla 
            WHERE id BETWEEN 3000 AND 4000
            ORDER BY id DESC
            LIMIT 1";
        $stmt = $this->pdoQuery($oDbl, $sQry, __METHOD__, __FILE__, __LINE__);
        $lastId = null;
        if ($stmt !== false) {
            foreach ($stmt as $aDades) {
                if (is_array($aDades) && isset($aDades['id']) && is_numeric($aDades['id'])) {
                    $lastId = (int) $aDades['id'];
                }
            }
        }
        if ($lastId === null) {
            throw new \RuntimeException(_('No se encuentra ningún id_schema en el rango 3000-4000'));
        }
        return $lastId;
    }

    /**
     * Cambiar nombre: `schema` es PK; no puede haber dos filas con el mismo nombre.
     *
     * Idempotente para reintentos de renombre:
     * - Si no existe la fila antigua (ya renombrada o no existía): no-op.
     * - Si existen antigua y nueva (estado incoherente tras corte a medias): borra la fila antigua
     *   para no violar la PK (un UPDATE antigua→nueva fallaría con «duplicate key»).
     * - Si solo existe la antigua: UPDATE a `new`.
     */
    private function DBCambiarNombre(string $old, string $new, string $database): bool
    {
        if ($old === $new || $old === '' || $new === '') {
            return true;
        }
        $oDbl = $this->connectar($database);
        $nom_tabla = $this->getNomTabla();
        $qOld = $oDbl->quote((string) $old);
        $qNew = $oDbl->quote((string) $new);

        $sqlOld = "SELECT 1 FROM $nom_tabla WHERE schema = $qOld LIMIT 1";
        $stmtOld = $this->pdoQuery($oDbl, $sqlOld, __METHOD__, __FILE__, __LINE__);
        $hasOld = $stmtOld !== false && $stmtOld->fetchColumn() !== false;

        if (!$hasOld) {
            return true;
        }

        $sqlNew = "SELECT 1 FROM $nom_tabla WHERE schema = $qNew LIMIT 1";
        $stmtNew = $this->pdoQuery($oDbl, $sqlNew, __METHOD__, __FILE__, __LINE__);
        $hasNew = $stmtNew !== false && $stmtNew->fetchColumn() !== false;

        if ($hasNew) {
            $delete = "DELETE FROM $nom_tabla WHERE schema = $qOld";

            return $this->pdoExec($oDbl, $delete, __METHOD__, __FILE__, __LINE__);
        }

        $update = "UPDATE $nom_tabla SET schema=$qNew WHERE schema=$qOld";

        return $this->pdoExec($oDbl, $update, __METHOD__, __FILE__, __LINE__);
    }

}