<?php

namespace src\personas\infrastructure\persistence\postgresql;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\personas\domain\criteria\SituacionField;
use PDO;
use src\personas\domain\contracts\SituacionRepositoryInterface;
use src\personas\domain\entity\Situacion;
use src\shared\infrastructure\GlobalPdo;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\RunsCriteriaQueries;

/**
 * Clase que adapta la tabla xp_situacion a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 17/11/2025
 */
class PgSituacionRepository extends ClaseRepository implements SituacionRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $this->setoDbl(GlobalPdo::get('oDBPC'));
        $this->setoDbl_select(GlobalPdo::get('oDBPC_Select'));
        $this->setNomTabla('xp_situacion');
    }

    /**
     * @return array<string, string>
     */
    public function getArraySituaciones(bool $traslado = false): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $Condicion = '';
        if ($traslado) {
            $Condicion = " WHERE situacion IN ('A','D','E','L','T','X')";
        }
        $sQuery = "SELECT situacion,nombre_situacion FROM $nom_tabla $Condicion ORDER BY situacion";
        $stmt = $this->PdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aOpciones = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $aClave) {
            if (!is_array($aClave) || !isset($aClave['situacion'], $aClave['nombre_situacion'])) {
                continue;
            }
            $aOpciones[(string)$aClave['situacion']] = (string)$aClave['nombre_situacion'];
        }

        return $aOpciones;
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Situacion que cumplen el criterio.
     *
     * @return list<Situacion>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(SituacionField::class, self::COLUMNAS);
        $result = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $result[] = Situacion::fromArray($aDatos);
        }

        return $result;
    }

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<Situacion>
     */
    public function getSituaciones(array $aWhere = [], array $aOperators = []): array
    {
        $adapter = new LegacyWhereAdapter(SituacionField::class, self::COLUMNAS);
        return $this->matching($adapter->toCriteria($aWhere, $aOperators));
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Situacion $Situacion): bool
    {
        $situacion = $Situacion->getSituacionVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE situacion = '$situacion'";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Situacion $Situacion): bool
    {
        $situacion = $Situacion->getSituacionVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($situacion);

        $aDatos = $Situacion->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['situacion']);
            $update = "
					nombre_situacion         = :nombre_situacion";
            $sql = "UPDATE $nom_tabla SET $update WHERE situacion = '$situacion'";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            //INSERT
            $campos = "(situacion,nombre_situacion)";
            $valores = "(:situacion,:nombre_situacion)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(string $situacion): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE situacion = '$situacion'";
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
     * @param string $situacion
     * @return array<string, mixed>|false
     */
    public function datosById(string $situacion): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE situacion = '$situacion'";
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
     * Busca la clase con situacion en la base de datos .
     */
    public function findById(string $situacion): ?Situacion
    {
        $aDatos = $this->datosById($situacion);
        if ($aDatos === false) {
            return null;
        }
        return Situacion::fromArray($aDatos);
    }
}