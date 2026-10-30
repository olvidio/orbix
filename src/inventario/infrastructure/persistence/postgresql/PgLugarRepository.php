<?php

namespace src\inventario\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\inventario\domain\criteria\LugarField;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\RunsCriteriaQueries;
use PDO;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\entity\Lugar;
use src\shared\traits\HandlesPdoErrors;

/**
 * Clase que adapta la tabla i_lugares_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
class PgLugarRepository extends ClaseRepository implements LugarRepositoryInterface
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
        $this->setNomTabla('i_lugares_dl');
    }

    public function getArrayLugares(int $id_ubi): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_lugar, nom_lugar
				FROM $nom_tabla
				WHERE id_ubi='$id_ubi'
				ORDER BY id_ubi,nom_lugar";
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
     * Lugar que cumplen el criterio.
     *
     * @return list<Lugar>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(LugarField::class, self::COLUMNAS);
        return array_map(Lugar::fromArray(...), $this->rowsMatching($criteria, $conv));
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Lugar $Lugar): bool
    {
        $id_lugar = $Lugar->getId_lugar();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_lugar = $id_lugar";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Lugar $Lugar): bool
    {
        $id_lugar = $Lugar->getId_lugar();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_lugar);

        $aDatos = $Lugar->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_lugar']);
            $update = "
                    id_ubi                   = :id_ubi,
                    nom_lugar                = :nom_lugar";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_lugar = $id_lugar";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            //INSERT
            $campos = "(id_lugar,id_ubi,nom_lugar)";
            $valores = "(:id_lugar,:id_ubi,:nom_lugar)";
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

    private function isNew(int $id_lugar): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_lugar = $id_lugar";
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
     * @param int $id_lugar
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_lugar): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_lugar = $id_lugar";
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
     * Busca la clase con id_lugar en la base de datos .
     */
    public function findById(int $id_lugar): ?Lugar
    {
        $aDatos = $this->datosById($id_lugar);
        if ($aDatos === false) {
            return null;
        }
        return Lugar::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('i_lugares_dl_id_lugar_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}