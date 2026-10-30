<?php

namespace src\dbextern\infrastructure\persistence\postgresql;

use PDO;
use src\dbextern\domain\contracts\IdMatchPersonaRepositoryInterface;
use src\dbextern\domain\criteria\IdMatchPersonaField;
use src\dbextern\domain\entity\IdMatchPersona;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\RunsCriteriaQueries;

class PgIdMatchPersonaRepository extends ClaseRepository implements IdMatchPersonaRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBP');
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl);
        $this->setNomTabla('conv_id_personas');
    }

    /**
     * IdMatch que cumplen el criterio.
     *
     * @return list<IdMatchPersona>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(IdMatchPersonaField::class, self::COLUMNAS);
        $idMatchPersonas = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $idMatchPersonas[] = IdMatchPersona::fromArray($aDatos);
        }

        return $idMatchPersonas;
    }

    public function Eliminar(IdMatchPersona $IdMatchPersona): bool
    {
        $id_listas = $IdMatchPersona->getId_listas();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_listas = $id_listas";

        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    public function Guardar(IdMatchPersona $IdMatchPersona): bool
    {
        $id_listas = $IdMatchPersona->getId_listas();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_listas);

        $aDatos = $IdMatchPersona->toArrayForDatabase();
        if ($bInsert === false) {
            unset($aDatos['id_listas']);
            $update = "
					id_orbix            = :id_orbix,
					id_tabla            = :id_tabla";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_listas = $id_listas";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            $campos = "(id_listas,id_orbix,id_tabla)";
            $valores = "(:id_listas,:id_orbix,:id_tabla)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }

        if ($stmt === false) {
            return false;
        }

        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_listas): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_listas = $id_listas";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false || !$stmt->rowCount()) {
            return true;
        }

        return false;
    }

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_listas): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_listas = $id_listas";
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

    public function findById(int $id_listas): ?IdMatchPersona
    {
        $aDatos = $this->datosById($id_listas);
        if ($aDatos === false) {
            return null;
        }

        return IdMatchPersona::fromArray($aDatos);
    }
}
