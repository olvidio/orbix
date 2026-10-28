<?php

namespace src\dossiers\infrastructure\persistence\postgresql;

use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\RunsCriteriaQueries;
use src\dossiers\domain\criteria\DossierField;
use src\shared\infrastructure\persistence\ConverterDate;
use src\shared\infrastructure\persistence\postgresql\Set;
use PDO;
use src\dossiers\domain\contracts\DossierRepositoryInterface;
use src\dossiers\domain\entity\Dossier;
use src\dossiers\domain\value_objects\DossierPk;
use src\shared\traits\HandlesPdoErrors;


/**
 * Clase que adapta la tabla d_dossiers_abiertos a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 2/12/2025
 */
class PgDossierRepository extends ClaseRepository implements DossierRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $this->setoDbl(GlobalPdo::get('oDB'));
        $this->setoDbl_select(GlobalPdo::get('oDB'));
        $this->setNomTabla('d_dossiers_abiertos');
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Dossiers que cumplen el criterio.
     *
     * @return list<Dossier>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(DossierField::class, self::COLUMNAS);
        $dossieres = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $aDatos['f_ini'] = (new ConverterDate('date', $aDatos['f_ini']))->fromPg();
            $aDatos['f_camb_dossier'] = (new ConverterDate('date', $aDatos['f_camb_dossier']))->fromPg();
            $aDatos['f_active'] = (new ConverterDate('date', $aDatos['f_active']))->fromPg();
            $dossieres[] = Dossier::fromArray($aDatos);
        }
        return $dossieres;
    }

    /**
     * devuelve una colección (array) de objetos de tipo Dossier
     *
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<Dossier> Una colección de objetos de tipo Dossier
     */
    public function getDossieres(array $aWhere = [], array $aOperators = []): array
    {
        $adapter = new LegacyWhereAdapter(DossierField::class, self::COLUMNAS);
        return $this->matching($adapter->toCriteria($aWhere, $aOperators));
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Dossier $Dossier): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $id_tipo_dossier = $Dossier->getId_tipo_dossier();
        $id_pau = $Dossier->getId_pau();
        $tabla = $Dossier->getTablaVo()->value();

        $sql = "DELETE FROM $nom_tabla WHERE id_pau = $id_pau AND id_tipo_dossier = $id_tipo_dossier AND tabla = '$tabla' ";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Dossier $Dossier): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $id_tipo_dossier = $Dossier->getId_tipo_dossier();
        $id_pau = $Dossier->getId_pau();
        $tabla = $Dossier->getTablaVo()->value();
        $bInsert = $this->isNew($id_tipo_dossier, $id_pau, $tabla);

        $aDatos = $Dossier->toArrayForDatabase([
            'f_ini' => fn($v) => (new ConverterDate('date', $v))->toPg(),
            'f_camb_dossier' => fn($v) => (new ConverterDate('date', $v))->toPg(),
            'f_active' => fn($v) => (new ConverterDate('date', $v))->toPg(),
        ]);

        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_tipo_dossier']);
            unset($aDatos['id_pau']);
            unset($aDatos['tabla']);
            $update = "
					f_ini                    = :f_ini,
					f_camb_dossier           = :f_camb_dossier,
					active                   = :active,
					f_active                 = :f_active";
            $sql = "UPDATE $nom_tabla SET $update WHERE tabla = '$tabla'";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(id_tipo_dossier,id_pau,tabla,f_ini,f_camb_dossier,active,f_active)";
            $valores = "(:id_tipo_dossier,:id_pau,:tabla,:f_ini,:f_camb_dossier,:active,:f_active)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_tipo_dossier, int $id_pau, string $tabla): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $sql = "SELECT * FROM $nom_tabla WHERE id_pau = $id_pau AND id_tipo_dossier = $id_tipo_dossier AND tabla = '$tabla' ";
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
    public function datosById(int $id_tipo_dossier, int $id_pau, string $tabla): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_pau = $id_pau AND id_tipo_dossier = $id_tipo_dossier AND tabla = '$tabla' ";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        if ($stmt === false) {
            return false;
        }

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return false;
        }
        $aDatos['f_ini'] = (new ConverterDate('date', $aDatos['f_ini']))->fromPg();
        $aDatos['f_camb_dossier'] = (new ConverterDate('date', $aDatos['f_camb_dossier']))->fromPg();
        $aDatos['f_active'] = (new ConverterDate('date', $aDatos['f_active']))->fromPg();
        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string) $key] = $value;
        }
        return $result;
    }

    /**
     * @return array<string, mixed>|false
     */
    public function datosByPk(DossierPk $pk): array|false
    {
        return $this->datosById($pk->idTipoDossier(), $pk->idPau(), $pk->tabla());
    }

    public function crearDossier(DossierPk $pk): Dossier
    {
        $aDatos['tabla'] = $pk->tabla();
        $aDatos['id_pau'] = $pk->idPau();
        $aDatos['id_tipo_dossier'] = $pk->idTipoDossier();

        return Dossier::fromArray($aDatos);
    }

    /**
     * Busca la clase con tabla en la base de datos .
     */
    public function findById(int $id_tipo_dossier, int $id_pau, string $tabla): ?Dossier
    {
        $aDatos = $this->datosById($id_tipo_dossier, $id_pau, $tabla);
        if (empty($aDatos)) {
            return null;
        }
        return Dossier::fromArray($aDatos);
    }

    public function findByPk(DossierPk $pk): ?Dossier
    {
        return $this->findById($pk->idTipoDossier(), $pk->idPau(), $pk->tabla());
    }

}