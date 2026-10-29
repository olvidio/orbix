<?php

namespace src\inventario\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\inventario\domain\criteria\UbiInventarioField;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\RunsCriteriaQueries;
use src\shared\infrastructure\persistence\postgresql\Set;
use PDO;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\inventario\domain\entity\UbiInventario;
use src\shared\traits\HandlesPdoErrors;

/**
 * Clase que adapta la tabla i_ubis_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
class PgUbiInventarioRepository extends ClaseRepository implements UbiInventarioRepositoryInterface
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
        $this->setNomTabla('i_ubis_dl');
    }

    /**
     * @return list<UbiInventario>
     */
    public function getUbisInventarioLugar(bool $bLugar): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $nom_tabla_lugares = 'i_lugares_dl';
        $UbiInventarioSet = new Set();
        if ($bLugar === true) {
            $sQry = "SELECT DISTINCT u.* FROM $nom_tabla u JOIN $nom_tabla_lugares
			    USING (id_ubi) ORDER BY nom_ubi";
        } else {
            $sQry = "SELECT u.* FROM $nom_tabla u LEFT JOIN $nom_tabla_lugares 
			        USING (id_ubi) WHERE id_lugar IS NULL ORDER BY nom_ubi";
        }
        $stmt = $this->pdoQuery($oDbl, $sQry, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            $aDatos = $this->normalizeAssocRow($aDatos);
            $UbiInventario = UbiInventario::fromArray($aDatos);
            $UbiInventarioSet->add($UbiInventario);
        }
        /** @var list<UbiInventario> $result */
        $result = array_values($UbiInventarioSet->getTot());
        return $result;
    }

    public function getArrayUbisInventario(): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_ubi,nom_ubi FROM $nom_tabla ORDER BY nom_ubi";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aOpciones = [];
        foreach ($stmt as $aClave) {
            if (!is_array($aClave)) {
                continue;
            }
            $clave = $aClave[0];
            $val = $aClave[1];
            if ((!is_int($clave) && !is_string($clave)) || (!is_scalar($val) && $val !== null)) {
                continue;
            }
            $aOpciones[(int) $clave] = (string) $val;
        }
        return $aOpciones;
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * UbiInventario que cumplen el criterio.
     *
     * @return list<UbiInventario>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(UbiInventarioField::class, self::COLUMNAS);
        return array_map(UbiInventario::fromArray(...), $this->rowsMatching($criteria, $conv));
    }

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<UbiInventario>
     */
    public function getUbisInventario(array $aWhere = [], array $aOperators = []): array
    {
        $adapter = new LegacyWhereAdapter(UbiInventarioField::class, self::COLUMNAS);
        return $this->matching($adapter->toCriteria($aWhere, $aOperators));
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(UbiInventario $UbiInventario): bool
    {
        $id_ubi = $UbiInventario->getId_ubi();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_ubi = $id_ubi";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(UbiInventario $UbiInventario): bool
    {
        $id_ubi = $UbiInventario->getId_ubi();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_ubi);

        $aDatos = $UbiInventario->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_ubi']);
            $update = "
                    nom_ubi                  = :nom_ubi,
                    id_ubi_activ             = :id_ubi_activ";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_ubi = $id_ubi";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            //INSERT
            $campos = "(id_ubi,nom_ubi,id_ubi_activ)";
            $valores = "(:id_ubi,:nom_ubi,:id_ubi_activ)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
    }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_ubi): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_ubi = $id_ubi";
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
     * @param int $id_ubi
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_ubi): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_ubi = $id_ubi";
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
     * Busca la clase con id_ubi en la base de datos .
     */
    public function findById(int $id_ubi): ?UbiInventario
    {
        $aDatos = $this->datosById($id_ubi);
        if ($aDatos === false) {
            return null;
        }
        return UbiInventario::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('i_ubis_dl_id_ubi_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}