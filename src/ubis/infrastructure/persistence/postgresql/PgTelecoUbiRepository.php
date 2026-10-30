<?php

namespace src\ubis\infrastructure\persistence\postgresql;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\ubis\domain\criteria\TelecoUbiField;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\RunsCriteriaQueries;
use PDO;
use src\shared\traits\HandlesPdoErrors;
use src\ubis\domain\contracts\TelecoUbiRepositoryInterface;
use src\ubis\domain\entity\TelecoUbi;

/**
 * Clase que adapta la tabla d_teleco_cdc a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 19/11/2025
 */
abstract class PgTelecoUbiRepository extends ClaseRepository implements TelecoUbiRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * TelecoUbi que cumplen el criterio.
     *
     * @return list<TelecoUbi>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(TelecoUbiField::class, self::COLUMNAS);
        return array_map(TelecoUbi::fromArray(...), $this->rowsMatching($criteria, $conv));
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(TelecoUbi $TelecoCdc): bool
    {
        $id_item = $TelecoCdc->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item = $id_item";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(TelecoUbi $TelecoCdc): bool
    {
        $id_item = $TelecoCdc->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item);

        $aDatos = $TelecoCdc->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_item']);
            $update = "
					id_ubi                   = :id_ubi,
					id_tipo_teleco           = :id_tipo_teleco,
					id_desc_teleco           = :id_desc_teleco,
					num_teleco               = :num_teleco,
					observ                   = :observ";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item = $id_item";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        }
        else {
            //INSERT
            $campos = "(id_ubi,id_tipo_teleco,id_desc_teleco,num_teleco,observ,id_item)";
            $valores = "(:id_ubi,:id_tipo_teleco,:id_desc_teleco,:num_teleco,:observ,:id_item)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
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
        $oDbl = $this->getoDbl_Select();
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
    public function findById(int $id_item): ?TelecoUbi
    {
        $aDatos = $this->datosById($id_item);
        if ($aDatos === false) {
            return null;
        }
        return TelecoUbi::fromArray($aDatos);
    }
}