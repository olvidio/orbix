<?php

namespace src\profesores\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\profesores\domain\criteria\ProfesorLatinField;
use PDO;
use src\profesores\domain\contracts\ProfesorLatinRepositoryInterface;
use src\profesores\domain\entity\ProfesorLatin;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\RunsCriteriaQueries;

/**
 * Clase que adapta la tabla d_profesor_latin a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/11/2025
 */
class PgProfesorLatinRepository extends ClaseRepository implements ProfesorLatinRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDB');
        $this->setoDbl($oDbl);
        $this->setoDbl_select($oDbl);
        $this->setNomTabla('d_profesor_latin');
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * ProfesorLatin que cumplen el criterio.
     *
     * @return list<ProfesorLatin>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(ProfesorLatinField::class, self::COLUMNAS);
        $result = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $result[] = ProfesorLatin::fromArray($this->normalizeAssocRow($aDatos));
        }

        return $result;
    }

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<ProfesorLatin>
     */
    public function getProfesoresLatin(array $aWhere = [], array $aOperators = []): array
    {
        $adapter = new LegacyWhereAdapter(ProfesorLatinField::class, self::COLUMNAS);
        return $this->matching($adapter->toCriteria($aWhere, $aOperators));
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ProfesorLatin $ProfesorLatin): bool
    {
        $id_nom = $ProfesorLatin->getId_nom();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_nom = $id_nom";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(ProfesorLatin $ProfesorLatin): bool
    {
        $id_nom = $ProfesorLatin->getId_nom();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_nom);

        $aDatos = $ProfesorLatin->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_nom']);
            $update = "
					latin                    = :latin";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_nom = $id_nom";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            //INSERT
            $campos = "(id_nom,latin)";
            $valores = "(:id_nom,:latin)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_nom): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_nom = $id_nom";
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
     * @param int $id_nom
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_nom): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_nom = $id_nom";
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
     * Busca la clase con id_nom en la base de datos .
     */
    public function findById(int $id_nom): ?ProfesorLatin
    {
        $aDatos = $this->datosById($id_nom);
        if ($aDatos === false) {
            return null;
        }
        return ProfesorLatin::fromArray($aDatos);
    }
}