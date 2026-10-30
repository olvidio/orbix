<?php

namespace src\casas\infrastructure\persistence\postgresql;

use PDO;
use src\casas\domain\contracts\GrupoCasaRepositoryInterface;
use src\casas\domain\criteria\GrupoCasaField;
use src\casas\domain\entity\GrupoCasa;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\RunsCriteriaQueries;

/**
 * Clase que adapta la tabla du_grupos_dl a la interfaz del repositorio
 */
class PgGrupoCasaRepository extends ClaseRepository implements GrupoCasaRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $this->setoDbl(GlobalPdo::get('oDBC'));
        $this->setoDbl_select(GlobalPdo::get('oDBC_Select'));
        $this->setNomTabla('du_grupos_dl');
    }

    /**
     * Grupos de casas que cumplen el criterio.
     *
     * @return list<GrupoCasa>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(GrupoCasaField::class, self::COLUMNAS);
        $grupoCasas = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $grupoCasas[] = GrupoCasa::fromArray($aDatos);
        }

        return $grupoCasas;
    }

    public function Eliminar(GrupoCasa $GrupoCasa): bool
    {
        $id_item = $GrupoCasa->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item = $id_item";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    public function Guardar(GrupoCasa $GrupoCasa): bool
    {
        $id_item = $GrupoCasa->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item);

        $aDatos = $GrupoCasa->toArrayForDatabase();
        if ($bInsert === false) {
            unset($aDatos['id_item']);
            $update = "
					id_ubi_padre             = :id_ubi_padre,
					id_ubi_hijo              = :id_ubi_hijo";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item = $id_item";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            $campos = '(id_item,id_ubi_padre,id_ubi_hijo)';
            $valores = '(:id_item,:id_ubi_padre,:id_ubi_hijo)';
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
            return true;
        }
        return false;
    }

    /**
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

    public function findById(int $id_item): ?GrupoCasa
    {
        $aDatos = $this->datosById($id_item);
        if ($aDatos === false) {
            return null;
        }
        return GrupoCasa::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('du_grupos_dl_id_item_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}
