<?php

namespace src\personas\infrastructure\persistence\postgresql;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\personas\domain\criteria\UltimaAsistenciaField;
use src\shared\infrastructure\persistence\ConverterDate;
use PDO;
use src\personas\domain\contracts\UltimaAsistenciaRepositoryInterface;
use src\personas\domain\entity\UltimaAsistencia;
use src\shared\infrastructure\GlobalPdo;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\RunsCriteriaQueries;

/**
 * Clase que adapta la tabla d_ultima_asistencia a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 1/1/2026
 */
class PgUltimaAsistenciaRepository extends ClaseRepository implements UltimaAsistenciaRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $this->setoDbl(GlobalPdo::get('oDB'));
        $this->setoDbl_select(GlobalPdo::get('oDB'));
        $this->setNomTabla('d_ultima_asistencia');
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * UltimaAsistencia que cumplen el criterio.
     *
     * @return list<UltimaAsistencia>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(UltimaAsistenciaField::class, self::COLUMNAS);
        $result = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $result[] = UltimaAsistencia::fromArray($this->hidratarFila($aDatos));
        }

        return $result;
    }

    /**
     * Fechas de PG (texto iso) a sus tipos PHP.
     *
     * @param array<string, mixed> $aDatos
     * @return array<string, mixed>
     */
    private function hidratarFila(array $aDatos): array
    {
        $aDatos['f_ini'] = (new ConverterDate('date', $aDatos['f_ini']))->fromPg();

        return $aDatos;
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(UltimaAsistencia $UltimaAsistencia): bool
    {
        $id_item = $UltimaAsistencia->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item = $id_item";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(UltimaAsistencia $UltimaAsistencia): bool
    {
        $id_item = $UltimaAsistencia->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item);

        $aDatos = $UltimaAsistencia->toArrayForDatabase([
            'f_ini' => fn($v) => (new ConverterDate('date', $v))->toPg(),
        ]);

        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_item']);
            $update = "
					id_nom                   = :id_nom,
					id_tipo_activ            = :id_tipo_activ,
					f_ini                    = :f_ini,
					descripcion              = :descripcion,
					cdr                      = :cdr";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item = $id_item";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(id_item,id_nom,id_tipo_activ,f_ini,descripcion,cdr)";
            $valores = "(:id_item,:id_nom,:id_tipo_activ,:f_ini,:descripcion,:cdr)";
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
            return TRUE;
        }
        return false;
    }

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_item
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_item): array|false
    {
        $oDbl = $this->getoDbl();
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
        $aDatos['f_ini'] = (new ConverterDate('date', $aDatos['f_ini']))->fromPg();
        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string)$key] = $value;
        }

        return $result;
    }


    /**
     * Busca la clase con id_item en la base de datos .
     */
    public function findById(int $id_item): ?UltimaAsistencia
    {
        $aDatos = $this->datosById($id_item);
        if ($aDatos === false) {
            return null;
        }
        return UltimaAsistencia::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('d_ultima_asistencia_id_item_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}