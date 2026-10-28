<?php

namespace src\profesores\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\profesores\domain\criteria\ProfesorDocenciaStgrField;
use PDO;
use src\profesores\domain\contracts\ProfesorDocenciaStgrRepositoryInterface;
use src\profesores\domain\entity\ProfesorDocenciaStgr;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\RunsCriteriaQueries;


/**
 * Clase que adapta la tabla d_docencia_stgr a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/11/2025
 */
class PgProfesorDocenciaStgrRepository extends ClaseRepository implements ProfesorDocenciaStgrRepositoryInterface
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
        $this->setNomTabla('d_docencia_stgr');
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * ProfesorDocenciaStgr que cumplen el criterio.
     *
     * @return list<ProfesorDocenciaStgr>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(ProfesorDocenciaStgrField::class, self::COLUMNAS);
        $result = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $result[] = ProfesorDocenciaStgr::fromArray($this->normalizeAssocRow($aDatos));
        }

        return $result;
    }

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<ProfesorDocenciaStgr>
     */
    public function getProfesorDocenciasStgr(array $aWhere = [], array $aOperators = []): array
    {
        $adapter = new LegacyWhereAdapter(ProfesorDocenciaStgrField::class, self::COLUMNAS);
        return $this->matching($adapter->toCriteria($aWhere, $aOperators));
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ProfesorDocenciaStgr $ProfesorDocenciaStgr): bool
    {
        $id_item = $ProfesorDocenciaStgr->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item = $id_item";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(ProfesorDocenciaStgr $ProfesorDocenciaStgr): bool
    {
        $id_item = $ProfesorDocenciaStgr->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item);

        $aDatos = $ProfesorDocenciaStgr->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_item']);
            $update = "
					id_nom                   = :id_nom,
					id_asignatura            = :id_asignatura,
					id_activ                 = :id_activ,
					tipo                     = :tipo,
					curso_inicio             = :curso_inicio,
					acta                     = :acta";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item = $id_item";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            //INSERT
            $campos = "(id_item,id_nom,id_asignatura,id_activ,tipo,curso_inicio,acta)";
            $valores = "(:id_item,:id_nom,:id_asignatura,:id_activ,:tipo,:curso_inicio,:acta)";
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
        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string) $key] = $value;
        }
        return $result;

    }


    /**
     * Busca la clase con id_item en la base de datos .
     */
    public function findById(int $id_item): ?ProfesorDocenciaStgr
    {
        $aDatos = $this->datosById($id_item);
        if ($aDatos === false) {
            return null;
        }
        return ProfesorDocenciaStgr::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('d_docencia_stgr_id_item_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}