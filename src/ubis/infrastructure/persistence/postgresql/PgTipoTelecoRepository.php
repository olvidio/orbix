<?php

namespace src\ubis\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\ubis\domain\criteria\TipoTelecoField;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\RunsCriteriaQueries;
use src\shared\infrastructure\persistence\postgresql\Set;
use PDO;
use src\shared\traits\HandlesPdoErrors;
use src\ubis\domain\contracts\TipoTelecoRepositoryInterface;
use src\ubis\domain\entity\TipoTeleco;

/**
 * Clase que adapta la tabla xd_tipo_teleco a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 17/11/2025
 */
class PgTipoTelecoRepository extends ClaseRepository implements TipoTelecoRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBPC');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBPC_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('xd_tipo_teleco');
    }

    /**
     * @return array<int|string, string>
     */
    public function getArrayTiposTelecoPersona(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oTipoCentroSet = new Set();
        $sQuery = "SELECT id, nombre_teleco
				FROM $nom_tabla
				WHERE persona='t'
				ORDER BY nombre_teleco";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aOpciones = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (!is_array($row) || !isset($row['id'], $row['nombre_teleco'])) {
                continue;
            }
            $aOpciones[(string) $row['id']] = (string) $row['nombre_teleco'];
        }

        return $aOpciones;
    }

    /**
     * @return array<int|string, string>
     */
    public function getArrayTiposTelecoUbi(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oTipoCentroSet = new Set();
        $sQuery = "SELECT id, nombre_teleco
				FROM $nom_tabla
				WHERE ubi='t'
				ORDER BY nombre_teleco";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aOpciones = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (!is_array($row) || !isset($row['id'], $row['nombre_teleco'])) {
                continue;
            }
            $aOpciones[(string) $row['id']] = (string) $row['nombre_teleco'];
        }

        return $aOpciones;
    }

    /**
     * @return array<int|string, string>
     */
    public function getArrayTiposTeleco(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oTipoCentroSet = new Set();
        $sQuery = "SELECT id, nombre_teleco
				FROM $nom_tabla
				ORDER BY nombre_teleco";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aOpciones = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (!is_array($row) || !isset($row['id'], $row['nombre_teleco'])) {
                continue;
            }
            $aOpciones[(string) $row['id']] = (string) $row['nombre_teleco'];
        }

        return $aOpciones;
    }

    /**
     * TipoTeleco que cumplen el criterio.
     *
     * @return list<TipoTeleco>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(TipoTelecoField::class, self::COLUMNAS);
        return array_map(TipoTeleco::fromArray(...), $this->rowsMatching($criteria, $conv));
    }

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<TipoTeleco>
     */
    public function getTiposTeleco(array $aWhere = [], array $aOperators = []): array
    {
        $adapter = new LegacyWhereAdapter(TipoTelecoField::class, self::COLUMNAS);
        return $this->matching($adapter->toCriteria($aWhere, $aOperators));
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(TipoTeleco $TipoTeleco): bool
    {
        $id = $TipoTeleco->getId();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id = $id";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(TipoTeleco $TipoTeleco): bool
    {
        $id = $TipoTeleco->getId();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id);

        $aDatos = $TipoTeleco->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id']);
            $update = "
					tipo_teleco              = :tipo_teleco,
					nombre_teleco            = :nombre_teleco,
					ubi                      = :ubi,
					persona                  = :persona";
            $sql = "UPDATE $nom_tabla SET $update WHERE id = $id";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        else {
            //INSERT
            $campos = "(tipo_teleco,nombre_teleco,ubi,persona,id)";
            $valores = "(:tipo_teleco,:nombre_teleco,:ubi,:persona,:id)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id = $id";
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
     * @param int $id
     * @return array<string, mixed>|false
     */
    public function datosById(int $id): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id = $id";
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
     * Busca la clase con id en la base de datos .
     */
    public function findById(int $id): ?TipoTeleco
    {
        $aDatos = $this->datosById($id);
        if ($aDatos === false) {
            return null;
        }
        return TipoTeleco::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('xd_tipo_teleco_id_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}