<?php

namespace src\inventario\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\inventario\domain\criteria\EgmField;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\RunsCriteriaQueries;
use PDO;
use src\inventario\domain\contracts\EgmRepositoryInterface;
use src\inventario\domain\entity\Egm;
use src\inventario\domain\value_objects\EgmItemId;
use src\shared\traits\HandlesPdoErrors;

/**
 * Clase que adapta la tabla i_egm_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
class PgEgmRepository extends ClaseRepository implements EgmRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];
    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDB');
        $this->setoDbl($oDbl);
        $this->setoDbl_select($oDbl);
        $this->setNomTabla('i_egm_dl');
    }

    public function getUltimoGrupo(int $id_equipaje): int
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $sQuery = "SELECT MAX(id_grupo) FROM $nom_tabla WHERE id_equipaje = $id_equipaje";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);

        if ($stmt === false) {
            return 0;
        }
        $value = $stmt->fetchColumn();
        return is_numeric($value) ? (int) $value : 0;
    }

    /**
     * @return list<string>
     */
    public function getArrayIdFromIdEquipajes(array $aEquipajes, int|string $lugar = ''): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $sQuery = "SELECT id_item,id_equipaje,id_lugar FROM $nom_tabla ORDER BY id_equipaje";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aOpciones = [];
        foreach ($stmt as $aClave) {
            if (!is_array($aClave) || !isset($aClave[0], $aClave[1], $aClave[2])) {
                continue;
            }
            if (in_array($aClave[1], $aEquipajes, true)) {
                $aOpciones[] = (string) (!empty($lugar) ? $aClave[2] : $aClave[0]);
            }
        }
        return $aOpciones;
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Egm que cumplen el criterio.
     *
     * @return list<Egm>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(EgmField::class, self::COLUMNAS);
        return array_map(Egm::fromArray(...), $this->rowsMatching($criteria, $conv));
    }

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<Egm>
     */
    public function getEgmes(array $aWhere = [], array $aOperators = []): array
    {
        $adapter = new LegacyWhereAdapter(EgmField::class, self::COLUMNAS);
        return $this->matching($adapter->toCriteria($aWhere, $aOperators));
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Egm $Egm): bool
    {
        $id_item = $Egm->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item = $id_item";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Egm $Egm): bool
    {
        $id_item = $Egm->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item);

        $aDatos = $Egm->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_item']);
            $update = "
                    id_equipaje              = :id_equipaje,
                    id_grupo                 = :id_grupo,
                    id_lugar                 = :id_lugar,
                    texto                    = :texto";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item = $id_item";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        } else {
            //INSERT
            $campos = "(id_item,id_equipaje,id_grupo,id_lugar,texto)";
            $valores = "(:id_item,:id_equipaje,:id_grupo,:id_lugar,:texto)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
    }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_item): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item = $id_item";
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
     * @param int $id_item
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_item): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item = $id_item";
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
     * Busca la clase con id_item en la base de datos .
     */
    public function findById(int $id_item): ?Egm
    {
        $aDatos = $this->datosById($id_item);
        if ($aDatos === false) {
            return null;
        }
        return Egm::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('i_egm_dl_id_item_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}