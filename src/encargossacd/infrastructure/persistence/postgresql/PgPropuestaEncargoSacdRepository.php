<?php

namespace src\encargossacd\infrastructure\persistence\postgresql;

use PDO;
use src\encargossacd\domain\contracts\PropuestaEncargoSacdRepositoryInterface;
use src\encargossacd\domain\entity\PropuestaEncargoSacd;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\ConverterDate;
use src\encargossacd\domain\criteria\PropuestaEncargoSacdField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\RunsCriteriaQueries;
use src\shared\traits\HandlesPdoErrors;

class PgPropuestaEncargoSacdRepository extends ClaseRepository implements PropuestaEncargoSacdRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $this->setoDbl(GlobalPdo::get('oDBE'));
        $this->setoDbl_select(GlobalPdo::get('oDBE_Select'));
        $this->setNomTabla('propuesta_encargos_sacd');
    }

    /**
     * PropuestaEncargoSacd que cumplen el criterio.
     *
     * @return list<PropuestaEncargoSacd>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(PropuestaEncargoSacdField::class, self::COLUMNAS);
        $items = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $aDatos['f_ini'] = (new ConverterDate('date', $aDatos['f_ini']))->fromPg();
            $aDatos['f_fin'] = (new ConverterDate('date', $aDatos['f_fin']))->fromPg();
            $items[] = PropuestaEncargoSacd::fromArray($aDatos);
        }
        return $items;
    }

    public function findById(int $id_item): ?PropuestaEncargoSacd
    {
        $rows = $this->matching(Criteria::create()->where(Filter::eq(PropuestaEncargoSacdField::IdItem, $id_item)));

        return $rows[0] ?? null;
    }

    public function existenLasTablas(): bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $schema_name = ConfigGlobal::mi_region_dl();
        $sql = "SELECT EXISTS (
            SELECT FROM information_schema.tables
            WHERE table_schema = '$schema_name'
            AND table_name = '$nom_tabla'
        )";
        $stmt = $this->pdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return is_array($row) && filter_var($row['exists'] ?? false, FILTER_VALIDATE_BOOLEAN);
    }

    public function Eliminar(PropuestaEncargoSacd $propuesta): bool
    {
        $id_item = $propuesta->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item = $id_item";

        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    public function Guardar(PropuestaEncargoSacd $propuesta): bool
    {
        $id_item = $propuesta->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $id_item <= 0 || $this->isNew($id_item);

        if ($bInsert && $id_item <= 0) {
            $id_item = $this->getNewId();
            $propuesta->setId_item($id_item);
        }

        $aDatos = $propuesta->toArrayForDatabase([
            'f_ini' => fn($v) => (new ConverterDate('date', $v))->toPg(),
            'f_fin' => fn($v) => (new ConverterDate('date', $v))->toPg(),
        ]);

        if ($bInsert === false) {
            unset($aDatos['id_item']);
            $update = "
					id_enc                   = :id_enc,
					id_nom                   = :id_nom,
					modo                     = :modo,
					f_ini                    = :f_ini,
					f_fin                    = :f_fin,
					id_nom_new               = :id_nom_new";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item = $id_item";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            $campos = '(id_item,id_enc,id_nom,modo,f_ini,f_fin,id_nom_new)';
            $valores = '(:id_item,:id_enc,:id_nom,:modo,:f_ini,:f_fin,:id_nom_new)';
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }

        return $this->pdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_item): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item = $id_item";
        $stmt = $this->pdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return true;
        }

        return !$stmt->rowCount();
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('propuesta_encargos_sacd_id_item_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}
