<?php

namespace src\procesos\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\procesos\domain\criteria\ProcesoTipoField;
use src\shared\config\ConfigGlobal;
use PDO;
use src\procesos\domain\contracts\ProcesoTipoRepositoryInterface;
use src\procesos\domain\entity\ProcesoTipo;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\RunsCriteriaQueries;


/**
 * Clase que adapta la tabla a_tipos_proceso a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 26/12/2025
 */
class PgProcesoTipoRepository extends ClaseRepository implements ProcesoTipoRepositoryInterface
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
        $this->setNomTabla('a_tipos_proceso');
    }

    /**
     * @return array<int|string, string>
     */
public function getArrayProcesoTipos(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $mi_sfsv = ConfigGlobal::mi_sfsv();
        $sQuery = "SELECT id_tipo_proceso, nom_proceso 
                FROM $nom_tabla
                WHERE sfsv = $mi_sfsv 
                ORDER BY nom_proceso";
        $stmt = $this->PdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aOpciones = [];
        foreach ($stmt as $aClave) {
            if (!is_array($aClave)) {
                continue;
            }
            $claveRaw = $aClave['id_tipo_proceso'] ?? ($aClave[0] ?? null);
            $valRaw = $aClave['nom_proceso'] ?? ($aClave[1] ?? null);
            if (!is_numeric($claveRaw)) {
                continue;
            }
            $aOpciones[(int) $claveRaw] = is_scalar($valRaw) ? (string) $valRaw : '';
        }
        return $aOpciones;
    }


    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * ProcesoTipo que cumplen el criterio.
     *
     * @return list<ProcesoTipo>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(ProcesoTipoField::class, self::COLUMNAS);
        $result = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $result[] = ProcesoTipo::fromArray($aDatos);
        }

        return $result;
    }

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<ProcesoTipo>
     */
    public function getProcesoTipos(array $aWhere = [], array $aOperators = []): array
    {
        $adapter = new LegacyWhereAdapter(ProcesoTipoField::class, self::COLUMNAS);
        return $this->matching($adapter->toCriteria($aWhere, $aOperators));
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ProcesoTipo $ProcesoTipo): bool
    {
        $id_tipo_proceso = $ProcesoTipo->getIdTipoProcesoVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_tipo_proceso = $id_tipo_proceso";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(ProcesoTipo $ProcesoTipo): bool
    {
        $id_tipo_proceso = $ProcesoTipo->getIdTipoProcesoVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_tipo_proceso);

        $aDatos = $ProcesoTipo->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_tipo_proceso']);
            $update = "
					nom_proceso              = :nom_proceso,
					sfsv                     = :sfsv";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_tipo_proceso = $id_tipo_proceso";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(id_tipo_proceso,nom_proceso,sfsv)";
            $valores = "(:id_tipo_proceso,:nom_proceso,:sfsv)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        /** @var \PDOStatement $stmt */
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_tipo_proceso): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_tipo_proceso = $id_tipo_proceso";
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
     * @param int $id_tipo_proceso
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_tipo_proceso): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_tipo_proceso = $id_tipo_proceso";
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
     * Busca la clase con id_tipo_proceso en la base de datos .
     */
    public function findById(int $id_tipo_proceso): ?ProcesoTipo
    {
        $aDatos = $this->datosById($id_tipo_proceso);
        if ($aDatos === false) {
            return null;
        }
        return ProcesoTipo::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('a_tipos_proceso_id_tipo_proceso_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}