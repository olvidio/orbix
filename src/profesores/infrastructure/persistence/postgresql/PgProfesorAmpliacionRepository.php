<?php

namespace src\profesores\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\profesores\domain\criteria\ProfesorAmpliacionField;
use src\shared\infrastructure\persistence\ConverterDate;
use PDO;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\profesores\domain\contracts\ProfesorAmpliacionRepositoryInterface;
use src\profesores\domain\entity\ProfesorAmpliacion;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\RunsCriteriaQueries;

/**
 * Clase que adapta la tabla d_profesor_ampliacion a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/11/2025
 */
class PgProfesorAmpliacionRepository extends ClaseRepository implements ProfesorAmpliacionRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct(
        private PersonaDlRepositoryInterface $personaDlRepository,
    ) {
        $oDbl = GlobalPdo::get('oDB');
        $this->setoDbl($oDbl);
        $this->setoDbl_select($oDbl);
        $this->setNomTabla('d_profesor_ampliacion');
    }

    /**
     * @deprecated Usar getArrayProfesoresAsignaturaVo(AsignaturaId $id_asignatura)
     * @return array<int, string>
     */
    public function getArrayProfesoresAsignatura(int $id_asignatura): array
    {
        $gesProfesores = $this->matching(
            Criteria::create()
                ->where(Filter::eq(ProfesorAmpliacionField::IdAsignatura, $id_asignatura))
                ->andWhere(Filter::isNull(ProfesorAmpliacionField::FCese))
        );
        $aProfesores = [];
        foreach ($gesProfesores as $oProfesor) {
            $id_nom = $oProfesor->getId_nom();
            $oPersonaDl = $this->personaDlRepository->findById($id_nom);
            if ($oPersonaDl === null) {
                continue;
            }
            $aProfesores[] = [
                'id_nom' => $id_nom,
                'ap_nom' => $oPersonaDl->getPrefApellidosNombre(),
                'ap1' => $oPersonaDl->getApellido1Vo()->value(),
                'ap2' => $oPersonaDl->getApellido2Vo()?->value() ?? '',
                'nom' => $oPersonaDl->getNomVo()?->value() ?? '',
            ];
        }
        \src\shared\domain\helpers\FuncTablasSupport::usortProfesoresPorApellidos($aProfesores);

        return \src\shared\domain\helpers\FuncTablasSupport::profesoresOpcionesFromFilas($aProfesores);
    }

    /**
     * @return array<int|string, string>
     */
public function getArrayProfesoresAsignaturaVo(AsignaturaId $id_asignatura): array
    {
        return $this->getArrayProfesoresAsignatura($id_asignatura->value());
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * ProfesorAmpliacion que cumplen el criterio.
     *
     * @return list<ProfesorAmpliacion>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(ProfesorAmpliacionField::class, self::COLUMNAS);
        $result = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $result[] = ProfesorAmpliacion::fromArray($this->hidratarFila($aDatos));
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
        $aDatos['f_nombramiento'] = (new ConverterDate('date', $aDatos['f_nombramiento']))->fromPg();
        $aDatos['f_cese'] = (new ConverterDate('date', $aDatos['f_cese']))->fromPg();

        return $aDatos;
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ProfesorAmpliacion $ProfesorAmpliacion): bool
    {
        $id_item = $ProfesorAmpliacion->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item = $id_item";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(ProfesorAmpliacion $ProfesorAmpliacion): bool
    {
        $id_item = $ProfesorAmpliacion->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item);

        $aDatos = $ProfesorAmpliacion->toArrayForDatabase([
            'f_nombramiento' => fn($v) => (new ConverterDate('date', $v))->toPg(),
            'f_cese' => fn($v) => (new ConverterDate('date', $v))->toPg(),
        ]);

        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_item']);
            $update = "
					id_nom                   = :id_nom,
					id_asignatura            = :id_asignatura,
					escrito_nombramiento     = :escrito_nombramiento,
					f_nombramiento           = :f_nombramiento,
					escrito_cese             = :escrito_cese,
					f_cese                   = :f_cese";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item = $id_item";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        } else {
            //INSERT
            $campos = "(id_item,id_nom,id_asignatura,escrito_nombramiento,f_nombramiento,escrito_cese,f_cese)";
            $valores = "(:id_item,:id_nom,:id_asignatura,:escrito_nombramiento,:f_nombramiento,:escrito_cese,:f_cese)";
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
        $aDatos['f_nombramiento'] = (new ConverterDate('date', $aDatos['f_nombramiento']))->fromPg();
        $aDatos['f_cese'] = (new ConverterDate('date', $aDatos['f_cese']))->fromPg();
        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string) $key] = $value;
        }
        return $result;
    }


    /**
     * Busca la clase con id_item en la base de datos .
     */
    public function findById(int $id_item): ?ProfesorAmpliacion
    {
        $aDatos = $this->datosById($id_item);
        if ($aDatos === false) {
            return null;
        }
        return ProfesorAmpliacion::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('d_profesor_ampliacion_id_item_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}