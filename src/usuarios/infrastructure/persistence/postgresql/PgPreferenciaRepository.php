<?php

namespace src\usuarios\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\usuarios\domain\criteria\PreferenciaField;
use PDO;
use PDOException;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\RunsCriteriaQueries;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use src\usuarios\domain\entity\Preferencia;

/**
 * Clase que adapta la tabla web_preferencias a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/4/2025
 */
class PgPreferenciaRepository extends ClaseRepository implements PreferenciaRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBE');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBE_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('web_preferencias');
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Preferencia que cumplen el criterio.
     *
     * @return list<Preferencia>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(PreferenciaField::class, self::COLUMNAS);
        $result = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $result[] = Preferencia::fromArray($aDatos);
        }

        return $result;
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Preferencia $Preferencia): bool
    {
        $id_usuario = $Preferencia->getId_usuario();
        $tipo = $Preferencia->getTipoVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_usuario = $id_usuario AND tipo = '$tipo'";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Preferencia $Preferencia): bool
    {
        $id_usuario = $Preferencia->getId_usuario();
        $tipo = $Preferencia->getTipoVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_usuario, $tipo);

        $aDatos = $Preferencia->toArrayForDatabase();
        if ($bInsert === false) {
            unset($aDatos['id_usuario']);
            unset($aDatos['tipo']);
            //UPDATE
            $update = "
					preferencia              = :preferencia
					";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_usuario = $id_usuario AND tipo = '$tipo'";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        else {
            //INSERT
            $campos = "(tipo,preferencia,id_usuario)";
            $valores = "(:tipo,:preferencia,:id_usuario)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_usuario, string $tipo): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_usuario = $id_usuario AND tipo = '$tipo'";
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
     * @param string $tipo
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_usuario, string $tipo): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_usuario = $id_usuario AND  tipo = '$tipo'";
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
     * Busca la clase con tipo en la base de datos .
     */
    public function findById(int $id_usuario, string $tipo): ?Preferencia
    {
        $aDatos = $this->datosById($id_usuario, $tipo);
        if ($aDatos === false) {
            return null;
        }
        return Preferencia::fromArray($aDatos);
    }
}