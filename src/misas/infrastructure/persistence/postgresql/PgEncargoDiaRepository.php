<?php

namespace src\misas\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\misas\domain\criteria\EncargoDiaField;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\RunsCriteriaQueries;
use src\shared\infrastructure\persistence\ConverterDate;
use PDO;
use PDOException;
use src\misas\domain\contracts\EncargoDiaRepositoryInterface;
use src\misas\domain\entity\EncargoDia;
use src\misas\domain\value_objects\EncargoDiaId;
use src\shared\traits\HandlesPdoErrors;

/**
 * Clase que adapta la tabla misa_plantillas_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/3/2023
 */
class PgEncargoDiaRepository extends ClaseRepository implements EncargoDiaRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBC');
        $oDbl_Select = GlobalPdo::get('oDBC_Select');
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('misa_cuadricula_dl');
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * EncargoDia que cumplen el criterio.
     *
     * @return list<EncargoDia>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(EncargoDiaField::class, self::COLUMNAS);
        $items = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $aDatos['tstart'] = (new ConverterDate('timestamp', $aDatos['tstart']))->fromPg();
            $aDatos['tend'] = (new ConverterDate('timestamp', $aDatos['tend']))->fromPg();
            $items[] = EncargoDia::fromArray($aDatos);
        }
        return $items;
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(EncargoDia $EncargoDia): bool
    {
        $uuid_item = $EncargoDia->getUuid_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE uuid_item = '$uuid_item'";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(EncargoDia $EncargoDia): bool
    {
        $uuid_item = $EncargoDia->getUuidItemVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($EncargoDia->getUuidItemVo());

        $aDatos = $EncargoDia->toArrayForDatabase([
            'tstart' => fn($v) => (new ConverterDate('timestamp', $v))->toPg(),
            'tend' => fn($v) => (new ConverterDate('timestamp', $v))->toPg(),
        ]);

        if ($bInsert === FALSE) {
            unset($aDatos['uuid_item']);
            //UPDATE
            $update = "
					id_enc                   = :id_enc,
					tstart                  = :tstart,
					tend                    = :tend,
					id_nom                   = :id_nom,
					observ                   = :observ,
                    status                   = :status";
            $sql = "UPDATE $nom_tabla SET $update WHERE uuid_item = '$uuid_item'";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        } else {
            // INSERT
            $campos = "(uuid_item,id_enc,tstart,tend,id_nom,observ,status)";
            $valores = "(:uuid_item,:id_enc,:tstart,:tend,:id_nom,:observ,:status)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        }
        return $this->pdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(EncargoDiaId $vo): bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $uuid_item = $vo->value();
        $sql = " SELECT * FROM $nom_tabla WHERE uuid_item = '$uuid_item'";
        $stmt = $this->pdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return true;
        }
        if (!$stmt->rowCount()) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @return array<string, mixed>|false
     */
    public function datosById(EncargoDiaId $vo): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $uuid_item = $vo->value();
        $sql = "SELECT * FROM $nom_tabla WHERE uuid_item = '$uuid_item'";
        $stmt = $this->pdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return false;
        }
        $aDatos['tstart'] = (new ConverterDate('timestamp', $aDatos['tstart']))->fromPg();
        $aDatos['tend'] = (new ConverterDate('timestamp', $aDatos['tend']))->fromPg();

        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string) $key] = $value;
        }

        return $result;
    }


    /**
     * Busca la clase con id_item en la base de datos .
     */
    public function findById(EncargoDiaId $vo): ?EncargoDia
    {
        $aDatos = $this->datosById($vo);
        if ($aDatos === false) {
            return null;
        }
        return EncargoDia::fromArray($aDatos);
    }

}