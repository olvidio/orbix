<?php

namespace src\procesos\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\procesos\domain\criteria\ActividadTareaField;
use PDO;
use src\procesos\domain\contracts\ActividadTareaRepositoryInterface;
use src\procesos\domain\entity\ActividadTarea;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\RunsCriteriaQueries;


/**
 * Clase que adapta la tabla a_tareas a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 26/12/2025
 */
class PgActividadTareaRepository extends ClaseRepository implements ActividadTareaRepositoryInterface
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
        $this->setNomTabla('a_tareas');
    }
/**
     * @return array<int|string, string>
     */
public function getArrayActividadTareas(int $iid_fase): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $sQuery = "SELECT  id_tarea, desc_tarea 
                       FROM $nom_tabla
                       WHERE id_fase = $iid_fase
                       ORDER BY id_fase,desc_tarea";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aOpciones = [];
        foreach ($stmt as $aClave) {
            if (!is_array($aClave)) {
                continue;
            }
            $claveRaw = $aClave['id_tarea'] ?? ($aClave[0] ?? null);
            $valRaw = $aClave['desc_tarea'] ?? ($aClave[1] ?? null);
            if (!is_numeric($claveRaw)) {
                continue;
            }
            $aOpciones[(int) $claveRaw] = is_scalar($valRaw) ? (string) $valRaw : '';
        }

        return $aOpciones;
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * ActividadTarea que cumplen el criterio.
     *
     * @return list<ActividadTarea>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(ActividadTareaField::class, self::COLUMNAS);
        $result = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $result[] = ActividadTarea::fromArray($aDatos);
        }

        return $result;
    }

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<ActividadTarea>
     */
    public function getActividadTareas(array $aWhere = [], array $aOperators = []): array
    {
        $adapter = new LegacyWhereAdapter(ActividadTareaField::class, self::COLUMNAS);
        return $this->matching($adapter->toCriteria($aWhere, $aOperators));
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ActividadTarea $ActividadTarea): bool
    {
        $id_tarea = $ActividadTarea->getIdTareaVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_tarea = $id_tarea";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(ActividadTarea $ActividadTarea): bool
    {
        $id_tarea = $ActividadTarea->getIdTareaVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_tarea);

        $aDatos = $ActividadTarea->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_tarea']);
            $update = "
					id_fase                  = :id_fase,
					desc_tarea               = :desc_tarea";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_tarea = $id_tarea";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(id_fase,id_tarea,desc_tarea)";
            $valores = "(:id_fase,:id_tarea,:desc_tarea)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        /** @var \PDOStatement $stmt */
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_tarea): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_tarea = $id_tarea";
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
     * @param int $id_tarea
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_tarea): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_tarea = $id_tarea";
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
     * Busca la clase con id_tarea en la base de datos .
     */
    public function findById(int $id_tarea): ?ActividadTarea
    {
        $aDatos = $this->datosById($id_tarea);
        if ($aDatos === false) {
            return null;
        }
        return ActividadTarea::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('a_tareas_id_tarea_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}