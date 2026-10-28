<?php

namespace src\utils_database\infrastructure\persistence\postgresql;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\RunsCriteriaQueries;
use src\utils_database\domain\criteria\MapIdField;
use PDO;
use PDOException;
use src\shared\infrastructure\GlobalPdo;
use src\shared\traits\HandlesPdoErrors;
use src\utils_database\domain\contracts\MapIdRepositoryInterface;
use src\utils_database\domain\entity\MapId;

/**
 * Clase que adapta la tabla map_id a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 13/11/2025
 */
class PgMapIdRepository extends ClaseRepository implements MapIdRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBRC');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBRC_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('map_id');
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * MapId que cumplen el criterio.
     *
     * @return list<MapId>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(MapIdField::class, self::COLUMNAS);
        $mapIdes = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $mapIdes[] = MapId::fromArray($aDatos);
        }
        return $mapIdes;
    }

    /**
     * devuelve una colección (array) de objetos de tipo MapId
     *
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<MapId>
     */
    public function getMapIdes(array $aWhere = [], array $aOperators = []): array
    {
        $adapter = new LegacyWhereAdapter(MapIdField::class, self::COLUMNAS);
        return $this->matching($adapter->toCriteria($aWhere, $aOperators));
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(MapId $MapId): bool
    {
        $objeto = $MapId->getObjetoVo()->value();
        $id_resto = $MapId->getIdRestoVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE objeto = '$objeto' AND id_resto = $id_resto ";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(MapId $MapId): bool
    {
        $objeto = $MapId->getObjetoVo()->value();
        $id_resto = $MapId->getIdRestoVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($objeto, $id_resto);

        $aDatos = $MapId->toArrayForDatabase();
        // Hydratable excluye `id_dl` (nombre reservado de otras entidades), pero aquí es una columna normal.
        $aDatos['id_dl'] = $MapId->getId_dl();
        if ($bInsert === false) {
            unset($aDatos['objeto']);
            unset($aDatos['id_resto']);
            //UPDATE
            $update = "
					id_dl                    = :id_dl";
            $sql = "UPDATE $nom_tabla SET $update WHERE objeto = '$objeto' AND id_resto = $id_resto ";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        else {
            //INSERT
            $campos = "(objeto,id_resto,id_dl)";
            $valores = "(:objeto,:id_resto,:id_dl)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(string $objeto, int $id_resto): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE objeto = '$objeto' AND id_resto = $id_resto";
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
     * @param string $objeto
     * @return array<string, mixed>|false
     */
    public function datosById(string $objeto, int $id_resto): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE objeto = '$objeto' AND id_resto = $id_resto";
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
     * Busca la clase con objeto en la base de datos .
     */
    public function findById(string $objeto, int $id_resto): ?MapId
    {
        $aDatos = $this->datosById($objeto, $id_resto);
        if (!is_array($aDatos)) {
            return null;
        }
        return MapId::fromArray($aDatos);
    }
}