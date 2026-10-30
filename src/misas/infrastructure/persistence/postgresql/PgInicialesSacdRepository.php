<?php

namespace src\misas\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\misas\domain\criteria\InicialesSacdField;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\RunsCriteriaQueries;
use PDO;
use PDOException;
use src\misas\domain\contracts\InicialesSacdRepositoryInterface;
use src\misas\domain\entity\InicialesSacd;
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
class PgInicialesSacdRepository extends ClaseRepository implements InicialesSacdRepositoryInterface
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
        $this->setNomTabla('misa_iniciales_dl');
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * InicialesSacd que cumplen el criterio.
     *
     * @return list<InicialesSacd>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(InicialesSacdField::class, self::COLUMNAS);
        return array_map(InicialesSacd::fromArray(...), $this->rowsMatching($criteria, $conv));
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(InicialesSacd $InicialesSacd): bool
    {
        $id_nom = $InicialesSacd->getId_nom();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_nom = $id_nom";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(InicialesSacd $InicialesSacd): bool
    {
        $id_nom = $InicialesSacd->getId_nom();
        if ($id_nom === null) {
            return false;
        }
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_nom);

        $aDatos = $InicialesSacd->toArrayForDatabase();
        if ($bInsert === FALSE) {
            unset($aDatos['id_nom']);
            //UPDATE
            $update = "
					iniciales               = :iniciales,
					color                   = :color";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_nom = $id_nom";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(id_nom,iniciales,color)";
            $valores = "(:id_nom,:iniciales,:color)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->pdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_nom): bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_nom = $id_nom";
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
     */
    public function datosById(int $id_nom): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_nom = $id_nom";
        $stmt = $this->pdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
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
    public function findById(int $id_nom): ?InicialesSacd
    {
        $aDatos = $this->datosById($id_nom);
        if ($aDatos === false) {
            return null;
        }
        return InicialesSacd::fromArray($aDatos);
    }

}