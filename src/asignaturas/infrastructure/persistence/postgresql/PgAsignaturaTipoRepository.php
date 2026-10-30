<?php

namespace src\asignaturas\infrastructure\persistence\postgresql;

use PDO;
use src\asignaturas\domain\contracts\AsignaturaTipoRepositoryInterface;
use src\asignaturas\domain\entity\AsignaturaTipo;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\asignaturas\domain\criteria\AsignaturaTipoField;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\RunsCriteriaQueries;
use src\shared\infrastructure\persistence\postgresql\Set;
use src\shared\traits\HandlesPdoErrors;

class PgAsignaturaTipoRepository extends ClaseRepository implements AsignaturaTipoRepositoryInterface
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
        $this->setNomTabla('xa_tipo_asig');
    }

    /**
     * @return array<int|string, string>
     */
    public function getArrayAsignaturaTipos(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_tipo,tipo_asignatura FROM $nom_tabla ORDER BY tipo_asignatura";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aOpciones = [];
        foreach ($stmt as $aClave) {
            if (!is_array($aClave)) {
                continue;
            }
            if (isset($aClave['id_tipo'], $aClave['tipo_asignatura'])) {
                $aOpciones[$aClave['id_tipo']] = (string) $aClave['tipo_asignatura'];
            } elseif (isset($aClave[0], $aClave[1])) {
                $aOpciones[$aClave[0]] = (string) $aClave[1];
            }
        }
        return $aOpciones;
    }

    /**
     * AsignaturaTipo que cumplen el criterio.
     *
     * @return list<AsignaturaTipo>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(AsignaturaTipoField::class, self::COLUMNAS);
        return array_map(AsignaturaTipo::fromArray(...), $this->rowsMatching($criteria, $conv));
    }

    public function Eliminar(AsignaturaTipo $AsignaturaTipo): bool
    {
        $id_tipo = $AsignaturaTipo->getId_tipo();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_tipo = $id_tipo";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    public function Guardar(AsignaturaTipo $AsignaturaTipo): bool
    {
        $id_tipo = $AsignaturaTipo->getId_tipo();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_tipo);

        $aDatos = $AsignaturaTipo->toArrayForDatabase();
        if ($bInsert === false) {
            unset($aDatos['id_tipo']);
            $update = "
					tipo_asignatura          = :tipo_asignatura,
					tipo_breve               = :tipo_breve,
					año                      = :year,
					tipo_latin               = :tipo_latin";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_tipo = $id_tipo";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            $campos = "(id_tipo,tipo_asignatura,tipo_breve,año,tipo_latin)";
            $valores = "(:id_tipo,:tipo_asignatura,:tipo_breve,:year,:tipo_latin)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_tipo): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_tipo = $id_tipo";
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
    public function datosById(int $id_tipo): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_tipo = $id_tipo";
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

    public function findById(int $id_tipo): ?AsignaturaTipo
    {
        $aDatos = $this->datosById($id_tipo);
        if ($aDatos === false) {
            return null;
        }
        return AsignaturaTipo::fromArray($aDatos);
    }
}
