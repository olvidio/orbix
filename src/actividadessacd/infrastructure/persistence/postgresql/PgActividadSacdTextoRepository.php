<?php

namespace src\actividadessacd\infrastructure\persistence\postgresql;

use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\actividadessacd\domain\criteria\ActividadSacdTextoField;
use PDO;
use src\actividadessacd\domain\contracts\ActividadSacdTextoRepositoryInterface;
use src\actividadessacd\domain\entity\ActividadSacdTexto;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\RunsCriteriaQueries;


class PgActividadSacdTextoRepository extends ClaseRepository implements ActividadSacdTextoRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBE');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBE_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('a_sacd_textos');
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * ActividadSacdTexto que cumplen el criterio.
     *
     * @return list<ActividadSacdTexto>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(ActividadSacdTextoField::class, self::COLUMNAS);
        $result = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $result[] = ActividadSacdTexto::fromArray($aDatos);
        }

        return $result;
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ActividadSacdTexto $ActividadSacdTexto): bool
    {
        $id_item = $ActividadSacdTexto->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item = $id_item";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(ActividadSacdTexto $ActividadSacdTexto): bool
    {
        $id_item = $ActividadSacdTexto->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item);

        $aDatos = $ActividadSacdTexto->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_item']);
            $update = "
					idioma                   = :idioma,
					clave                    = :clave,
					texto                    = :texto";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item = $id_item";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        else {
            // INSERT
            $campos = "(id_item,idioma,clave,texto)";
            $valores = "(:id_item,:idioma,:clave,:texto)";
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
            return true;
        }
        return false;
    }

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_item): array|false
    {
        $oDbl = $this->getoDbl_Select();
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
        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string)$key] = $value;
        }
        return $result;
    }


    /**
     * Busca la clase con id_item en la base de datos .
     */
    public function findById(int $id_item): ?ActividadSacdTexto
    {
        $aDatos = $this->datosById($id_item);
        if ($aDatos === false) {
            return null;
        }
        return ActividadSacdTexto::fromArray($aDatos);
    }

    public function getNewId(): int|string
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('a_sacd_textos_id_item_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();
        if ($id === false) {
            return 0;
        }
        return is_numeric($id) ? (int)$id : (string)$id;
    }
}