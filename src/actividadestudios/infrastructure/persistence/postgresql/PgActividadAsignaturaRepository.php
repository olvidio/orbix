<?php

namespace src\actividadestudios\infrastructure\persistence\postgresql;

use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\actividadestudios\domain\criteria\ActividadAsignaturaField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\RunsCriteriaQueries;
use src\shared\infrastructure\persistence\ConverterDate;
use src\shared\infrastructure\persistence\postgresql\Set;
use PDO;
use src\actividadestudios\domain\contracts\ActividadAsignaturaRepositoryInterface;
use src\actividadestudios\domain\entity\ActividadAsignatura;
use src\actividadestudios\domain\value_objects\ActividadAsignaturaPk;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\shared\traits\HandlesPdoErrors;


/**
 * Clase que adapta la tabla d_asignaturas_activ_all a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/12/2025
 */
class PgActividadAsignaturaRepository extends ClaseRepository implements ActividadAsignaturaRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct(
        private AsignaturaRepositoryInterface $asignaturaRepository,
    ) {
        $oDbl = GlobalPdo::get('oDBP');
        $this->setoDbl($oDbl);
        $this->setoDbl_select($oDbl);
        $this->setNomTabla('d_asignaturas_activ_all');
    }

    /**
     * @return array<int, array{nombre_asignatura: mixed, creditos: mixed}>
     */
    public function getAsignaturasCa(int $id_activ, string $tipo = ''): array
    {
        /**
         * Array con  id_asignatura => array(nombre_asignatura, creditos)
         * para no tener que consultar cada vez a la base de datos.
         *
         */
        $aAsigDatos = $this->asignaturaRepository->getArrayAsignaturasCreditos();

        // por cada ca creo un array con las asignaturas y los créditos.
        $criteria = Criteria::create()->where(Filter::eq(ActividadAsignaturaField::IdActiv, $id_activ));
        $criteria = empty($tipo)
            ? $criteria->andWhere(Filter::isNull(ActividadAsignaturaField::Tipo))
            : $criteria->andWhere(Filter::eq(ActividadAsignaturaField::Tipo, $tipo));
        $cActividadAsignaturas = $this->matching($criteria);
        $aAsignaturasCa = [];
        foreach ($cActividadAsignaturas as $oActividadAsignatura) {
            $id_asignatura = $oActividadAsignatura->getIdAsignaturaVo()->value();
            if (empty($aAsigDatos[$id_asignatura])) {
                $aAsignaturasCa[$id_asignatura] = array('nombre_asignatura' => '??', 'creditos' => '??');
            } else {
                $aAsignaturasCa[$id_asignatura] = $aAsigDatos[$id_asignatura];
            }
        }
        return $aAsignaturasCa;
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * ActividadAsignatura que cumplen el criterio.
     *
     * @return list<ActividadAsignatura>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(ActividadAsignaturaField::class, self::COLUMNAS);
        $items = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            // para las fechas del postgres (texto iso)
            $aDatos['f_ini'] = (new ConverterDate('date', $aDatos['f_ini']))->fromPg();
            $aDatos['f_fin'] = (new ConverterDate('date', $aDatos['f_fin']))->fromPg();
            $items[] = ActividadAsignatura::fromArray($aDatos);
        }
        return $items;
    }

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<ActividadAsignatura>
     */
    public function getActividadAsignaturas(array $aWhere = [], array $aOperators = []): array
    {
        $adapter = new LegacyWhereAdapter(ActividadAsignaturaField::class, self::COLUMNAS);
        return $this->matching($adapter->toCriteria($aWhere, $aOperators));
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ActividadAsignatura $ActividadAsignatura): bool
    {
        $id_activ = $ActividadAsignatura->getId_activ();
        $id_asignatura = $ActividadAsignatura->getIdAsignaturaVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_activ=$id_activ AND id_asignatura=$id_asignatura";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(ActividadAsignatura $ActividadAsignatura): bool
    {
        $id_activ = $ActividadAsignatura->getId_activ();
        $id_asignatura = $ActividadAsignatura->getIdAsignaturaVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_activ, $id_asignatura);

        $aDatos = $ActividadAsignatura->toArrayForDatabase([
            'f_ini' => fn($v) => (new ConverterDate('date', $v))->toPg(),
            'f_fin' => fn($v) => (new ConverterDate('date', $v))->toPg(),
        ]);

        /*
        $aDatos = [];
        $aDatos['id_profesor'] = $ActividadAsignatura->getId_profesor();
        $aDatos['avis_profesor'] = $ActividadAsignatura->getAvis_profesor();
        $aDatos['tipo'] = $ActividadAsignatura->getTipoActividadAsignaturaVo()->value();
        // para las fechas
        $aDatos['f_ini'] = (new ConverterDate('date', $ActividadAsignatura->getF_ini()))->toPg();
        $aDatos['f_fin'] = (new ConverterDate('date', $ActividadAsignatura->getF_fin()))->toPg();
        array_walk($aDatos, [\src\shared\domain\helpers\FuncTablasSupport::class, 'ponerNull']);
        */

        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_activ']);
            unset($aDatos['id_asignatura']);
            $update = "
					id_profesor              = :id_profesor,
					avis_profesor            = :avis_profesor,
					tipo                     = :tipo,
					f_ini                    = :f_ini,
					f_fin                    = :f_fin";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_activ = $id_activ AND id_asignatura=$id_asignatura";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT (d_asignaturas_activ_all exige id_schema NOT NULL; Hydratable no serializa id_schema)
            $campos = "(id_schema,id_activ,id_asignatura,id_profesor,avis_profesor,tipo,f_ini,f_fin)";
            $valores = "(:id_schema,:id_activ,:id_asignatura,:id_profesor,:avis_profesor,:tipo,:f_ini,:f_fin)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }

        if ($bInsert) {
            $sid = ConfigGlobal::mi_id_schema();
            $idSchema = $sid;
            if ($idSchema < 1) {
                throw new \RuntimeException(_("Falta id_schema de sesión (mi_id_schema) para persistir actividad-asignatura."));
            }
            $aDatos['id_schema'] = $idSchema;
        }

        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_activ, int $id_asignatura): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_activ=$id_activ AND id_asignatura=$id_asignatura";
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
    public function datosById(int $id_activ, int $id_asignatura): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_activ=$id_activ AND id_asignatura=$id_asignatura";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return false;
        }
        // para las fechas del postgres (texto iso)
        $aDatos['f_ini'] = (new ConverterDate('date', $aDatos['f_ini']))->fromPg();
        $aDatos['f_fin'] = (new ConverterDate('date', $aDatos['f_fin']))->fromPg();
        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string) $key] = $value;
        }
        return $result;
    }

    /**
     * @return array<string, mixed>|false
     */
    public function datosByPk(ActividadAsignaturaPk $pk): array|false
    {
        return $this->datosById($pk->IdActiv(), $pk->IdAsignatura());
    }

    /**
     * Busca la clase con id_activ en la base de datos .
     */
    public function findById(int $id_activ, int $id_asignatura): ?ActividadAsignatura
    {
        $aDatos = $this->datosById($id_activ, $id_asignatura);
        if (empty($aDatos)) {
            return null;
        }
        return ActividadAsignatura::fromArray($aDatos);
    }

    public function findByPk(ActividadAsignaturaPk $pk): ?ActividadAsignatura
    {
        return $this->findById($pk->IdActiv(), $pk->IdAsignatura());
    }
}