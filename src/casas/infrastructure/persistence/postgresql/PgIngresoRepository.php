<?php

namespace src\casas\infrastructure\persistence\postgresql;

use PDO;
use src\casas\domain\contracts\IngresoRepositoryInterface;
use src\casas\domain\criteria\IngresoField;
use src\casas\domain\entity\Ingreso;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\RunsCriteriaQueries;

/**
 * Clase que adapta la tabla da_ingresos_dl a la interfaz del repositorio
 */
class PgIngresoRepository extends ClaseRepository implements IngresoRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $this->setoDbl(GlobalPdo::get('oDBC'));
        $this->setoDbl_select(GlobalPdo::get('oDBC_Select'));
        $this->setNomTabla('da_ingresos_dl');
    }

    /**
     * Ingresos de actividad que cumplen el criterio.
     *
     * @return list<Ingreso>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(IngresoField::class, self::COLUMNAS);
        $ingresos = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $ingresos[] = Ingreso::fromArray($aDatos);
        }

        return $ingresos;
    }


    public function Eliminar(Ingreso $Ingreso): bool
    {
        $id_activ = $Ingreso->getId_activ();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_activ = $id_activ";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    public function Guardar(Ingreso $Ingreso): bool
    {
        $id_activ = $Ingreso->getId_activ();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_activ);

        $aDatos = $Ingreso->toArrayForDatabase();
        if ($bInsert === false) {
            unset($aDatos['id_activ']);
            $update = "
					ingresos                 = :ingresos,
					num_asistentes           = :num_asistentes,
					ingresos_previstos       = :ingresos_previstos,
					num_asistentes_previstos = :num_asistentes_previstos,
					observ                   = :observ";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_activ = $id_activ";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            $campos = '(id_activ,ingresos,num_asistentes,ingresos_previstos,num_asistentes_previstos,observ)';
            $valores = '(:id_activ,:ingresos,:num_asistentes,:ingresos_previstos,:num_asistentes_previstos,:observ)';
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_activ): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_activ = $id_activ";
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
    public function datosById(int $id_activ): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_activ = $id_activ";
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

    public function findById(int $id_activ): ?Ingreso
    {
        $aDatos = $this->datosById($id_activ);
        if ($aDatos === false) {
            return null;
        }
        return Ingreso::fromArray($aDatos);
    }
}
