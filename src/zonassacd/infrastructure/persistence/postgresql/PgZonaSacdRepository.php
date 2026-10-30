<?php

namespace src\zonassacd\infrastructure\persistence\postgresql;

use PDO;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\RunsCriteriaQueries;
use src\zonassacd\domain\contracts\ZonaSacdRepositoryInterface;
use src\zonassacd\domain\criteria\ZonaSacdField;
use src\zonassacd\domain\entity\ZonaSacd;

class PgZonaSacdRepository extends ClaseRepository implements ZonaSacdRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBC');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBC_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('zonas_sacd');
    }

    /**
     * @return list<int>
     */
    public function getIdSacdsDeZona(int $iid_zona): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_nom
				FROM $nom_tabla
				WHERE id_zona=$iid_zona
				ORDER BY id_nom";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aLista = [];
        foreach ($stmt as $aDades) {
            if (!is_array($aDades) || !isset($aDades['id_nom'])) {
                continue;
            }
            $aLista[] = is_numeric($aDades['id_nom']) ? (int) $aDades['id_nom'] : 0;
        }
        return $aLista;
    }

    /**
     * Relaciones zona - sacd que cumplen el criterio.
     *
     * @return list<ZonaSacd>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(ZonaSacdField::class, self::COLUMNAS);
        $zonasSacds = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $zonasSacds[] = ZonaSacd::fromArray($aDatos);
        }

        return $zonasSacds;
    }

    public function Eliminar(ZonaSacd $ZonaSacd): bool
    {
        $id_item = $ZonaSacd->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item = $id_item";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    public function Guardar(ZonaSacd $ZonaSacd): bool
    {
        $id_item = $ZonaSacd->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item);

        $aDatos = $ZonaSacd->toArrayForDatabase();
        if ($bInsert === false) {
            unset($aDatos['id_item']);
            $update = "
					id_nom                   = :id_nom,
					id_zona                  = :id_zona,
					propia                   = :propia,
					dw1                      = :dw1,
					dw2                      = :dw2,
					dw3                      = :dw3,
					dw4                      = :dw4,
					dw5                      = :dw5,
					dw6                      = :dw6,
					dw7                      = :dw7";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item = $id_item";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            $campos = '(id_item,id_nom,id_zona,propia,dw1,dw2,dw3,dw4,dw5,dw6,dw7)';
            $valores = '(:id_item,:id_nom,:id_zona,:propia,:dw1,:dw2,:dw3,:dw4,:dw5,:dw6,:dw7)';
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

    public function findById(int $id_item): ?ZonaSacd
    {
        $aDatos = $this->datosById($id_item);
        if ($aDatos === false) {
            return null;
        }
        return ZonaSacd::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('zonas_sacd_id_item_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}
