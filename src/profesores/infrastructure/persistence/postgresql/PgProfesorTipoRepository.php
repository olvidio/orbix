<?php

namespace src\profesores\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\profesores\domain\criteria\ProfesorTipoField;
use PDO;
use src\profesores\domain\contracts\ProfesorTipoRepositoryInterface;
use src\profesores\domain\entity\ProfesorTipo;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\RunsCriteriaQueries;

/**
 * Clase que adapta la tabla xe_tipo_profesor_stgr a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 17/11/2025
 */
class PgProfesorTipoRepository extends ClaseRepository implements ProfesorTipoRepositoryInterface
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
        $this->setNomTabla('xe_tipo_profesor_stgr');
    }

    /**
     * @return array<int|string, string>
     */
public function getArrayProfesorTipos(): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_tipo_profesor,tipo_profesor FROM $nom_tabla ORDER BY tipo_profesor";
        $stmt = $this->PdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aOpciones = [];
        foreach ($stmt as $aClave) {
            if (!is_array($aClave)) {
                continue;
            }
            $clave = $aClave[0] ?? null;
            $val = $aClave[1] ?? '';
            if (!is_numeric($clave)) {
                continue;
            }
            $aOpciones[(int) $clave] = is_scalar($val) ? (string) $val : '';
        }

        return $aOpciones;
    }
    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * ProfesorTipo que cumplen el criterio.
     *
     * @return list<ProfesorTipo>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(ProfesorTipoField::class, self::COLUMNAS);
        $result = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $result[] = ProfesorTipo::fromArray($this->normalizeAssocRow($aDatos));
        }

        return $result;
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ProfesorTipo $ProfesorTipo): bool
    {
        $id_tipo_profesor = $ProfesorTipo->getIdTipoProfesorVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_tipo_profesor = $id_tipo_profesor";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(ProfesorTipo $ProfesorTipo): bool
    {
        $id_tipo_profesor = $ProfesorTipo->getIdTipoProfesorVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_tipo_profesor);

        $aDatos = $ProfesorTipo->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_tipo_profesor']);
            $update = "
					tipo_profesor            = :tipo_profesor";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_tipo_profesor = $id_tipo_profesor";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            //INSERT
            $campos = "(id_tipo_profesor,tipo_profesor)";
            $valores = "(:id_tipo_profesor,:tipo_profesor)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_tipo_profesor): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_tipo_profesor = $id_tipo_profesor";
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
     * @param int $id_tipo_profesor
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_tipo_profesor): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_tipo_profesor = $id_tipo_profesor";
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
     * Busca la clase con id_tipo_profesor en la base de datos .
     */
    public function findById(int $id_tipo_profesor): ?ProfesorTipo
    {
        $aDatos = $this->datosById($id_tipo_profesor);
        if ($aDatos === false) {
            return null;
        }
        return ProfesorTipo::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('xe_tipo_profe_id_tipo_profe_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}