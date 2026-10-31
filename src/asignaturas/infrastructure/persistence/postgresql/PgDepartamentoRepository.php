<?php

namespace src\asignaturas\infrastructure\persistence\postgresql;

use PDO;
use src\asignaturas\domain\contracts\DepartamentoRepositoryInterface;
use src\asignaturas\domain\entity\Departamento;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\asignaturas\domain\criteria\DepartamentoField;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\RunsCriteriaQueries;
use src\shared\infrastructure\persistence\postgresql\Set;
use src\shared\traits\HandlesPdoErrors;

class PgDepartamentoRepository extends ClaseRepository implements DepartamentoRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren. */
    private const COLUMNAS = ['nombre_departamento' => 'departamento'];

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBPC');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBPC_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('xe_departamentos');
    }

    /**
     * @return array<int|string, string>
     */
    public function getArrayDepartamentos(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_departamento,departamento 
				FROM $nom_tabla
				ORDER BY departamento";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aOpciones = [];
        foreach ($stmt as $aClave) {
            if (!is_array($aClave) || !isset($aClave['id_departamento'], $aClave['departamento'])) {
                continue;
            }
            $aOpciones[$aClave['id_departamento']] = (string) $aClave['departamento'];
        }
        return $aOpciones;
    }

    /**
     * Departamento que cumplen el criterio.
     *
     * @return list<Departamento>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(DepartamentoField::class, self::COLUMNAS);
        return array_map(Departamento::fromArray(...), $this->rowsMatching($criteria, $conv));
    }

    public function Eliminar(Departamento $Departamento): bool
    {
        $id_departamento = $Departamento->getIdDepartamentoVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_departamento = $id_departamento";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    public function Guardar(Departamento $Departamento): bool
    {
        $id_departamento = $Departamento->getIdDepartamentoVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_departamento);

        $aDatos = $Departamento->toArrayForDatabase();
        if ($bInsert === false) {
            unset($aDatos['id_departamento']);
            $update = "
                    departamento             = :departamento";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_departamento = $id_departamento";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            $campos = "(id_departamento,departamento)";
            $valores = "(:id_departamento,:departamento)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_departamento): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_departamento = $id_departamento";
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
    public function datosById(int $id_departamento): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_departamento = $id_departamento";
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

    public function findById(int $id_departamento): ?Departamento
    {
        $aDatos = $this->datosById($id_departamento);
        if ($aDatos === false) {
            return null;
        }
        return Departamento::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('xe_departamen_id_departamen_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}
