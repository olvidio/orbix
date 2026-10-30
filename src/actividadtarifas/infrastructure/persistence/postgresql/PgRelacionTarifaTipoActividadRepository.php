<?php

namespace src\actividadtarifas\infrastructure\persistence\postgresql;

use PDO;
use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;
use src\actividadtarifas\domain\entity\RelacionTarifaTipoActividad;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\actividadtarifas\domain\criteria\RelacionTarifaTipoActividadField;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\RunsCriteriaQueries;
use src\shared\infrastructure\persistence\postgresql\Set;
use src\shared\traits\HandlesPdoErrors;

class PgRelacionTarifaTipoActividadRepository extends ClaseRepository implements RelacionTarifaTipoActividadRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $this->setoDbl(GlobalPdo::get('oDBC'));
        $this->setoDbl_select(GlobalPdo::get('oDBC_Select'));
        $this->setNomTabla('xa_tipo_activ_tarifa');
    }

    /**
     * RelacionTarifaTipoActividad que cumplen el criterio.
     *
     * @return list<RelacionTarifaTipoActividad>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(RelacionTarifaTipoActividadField::class, self::COLUMNAS);
        return array_map(RelacionTarifaTipoActividad::fromArray(...), $this->rowsMatching($criteria, $conv));
    }

    public function Eliminar(RelacionTarifaTipoActividad $RelacionTarifaTipoActividad): bool
    {
        $id_item = $RelacionTarifaTipoActividad->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item = $id_item";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    public function Guardar(RelacionTarifaTipoActividad $RelacionTarifaTipoActividad): bool
    {
        $id_item = $RelacionTarifaTipoActividad->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item);

        $aDatos = $RelacionTarifaTipoActividad->toArrayForDatabase();
        if ($bInsert === false) {
            unset($aDatos['id_item']);
            $update = "
					id_tarifa                = :id_tarifa,
					id_tipo_activ            = :id_tipo_activ,
					id_serie                 = :id_serie";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item = $id_item";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            $campos = '(id_item,id_tarifa,id_tipo_activ,id_serie)';
            $valores = '(:id_item,:id_tarifa,:id_tipo_activ,:id_serie)';
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

    public function findById(int $id_item): ?RelacionTarifaTipoActividad
    {
        $aDatos = $this->datosById($id_item);
        if ($aDatos === false) {
            return null;
        }
        return RelacionTarifaTipoActividad::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('xa_tipo_activ_tarifa_id_item_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}
