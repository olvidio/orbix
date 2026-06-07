<?php

namespace src\profesores\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\ConverterDate;
use src\shared\infrastructure\persistence\postgresql\Set;
use PDO;
use src\asignaturas\domain\value_objects\AsignaturaId;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\profesores\domain\contracts\ProfesorAmpliacionRepositoryInterface;
use src\profesores\domain\entity\ProfesorAmpliacion;
use src\shared\traits\HandlesPdoErrors;

use function src\shared\domain\helpers\usort_profesores_por_apellidos;


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

    public function __construct(
        private PersonaDlRepositoryInterface $personaDlRepository,
    ) {
        $oDbl = GlobalPdo::get('oDB');
        $this->setoDbl($oDbl);
        $this->setNomTabla('d_profesor_ampliacion');
    }

    /**
     * @deprecated Usar getArrayProfesoresAsignaturaVo(AsignaturaId $id_asignatura)
     * @return array<int, string>
     */
    public function getArrayProfesoresAsignatura(int $id_asignatura): array
    {
        $gesProfesores = $this->getProfesorAmpliaciones(['id_asignatura' => $id_asignatura, 'f_cese' => ''], ['f_cese' => 'IS NULL']);
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
        usort_profesores_por_apellidos($aProfesores);

        $aOpciones = [];
        foreach ($aProfesores as $aClave) {
            $aOpciones[$aClave['id_nom']] = $aClave['ap_nom'];
        }

        return $aOpciones;
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
     * devuelve una colección (array) de objetos de tipo ProfesorAmpliacion
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<ProfesorAmpliacion> Una colección de objetos de tipo ProfesorAmpliacion
     */
    public function getProfesorAmpliaciones(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $ProfesorAmpliacionSet = new Set();
        $oCondicion = new Condicion();
        $aCondicion = [];
        foreach ($aWhere as $camp => $val) {
            if ($camp === '_ordre') {
                continue;
            }
            if ($camp === '_limit') {
                continue;
            }
            $sOperador = $aOperators[$camp] ?? '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) {
                $aCondicion[] = $a;
            }
            // operadores que no requieren valores
            if ($sOperador === 'BETWEEN' || $sOperador === 'IS NULL' || $sOperador === 'IS NOT NULL' || $sOperador === 'OR') {
                unset($aWhere[$camp]);
            }
            if ($sOperador === 'IN' || $sOperador === 'NOT IN') {
                unset($aWhere[$camp]);
            }
            if ($sOperador === 'TXT') {
                unset($aWhere[$camp]);
            }
        }
        $sCondicion = implode(' AND ', $aCondicion);
        if ($sCondicion !== '') {
            $sCondicion = " WHERE " . $sCondicion;
        }
        $sOrdre = '';
        $sLimit = '';
        $ordreVal = $aWhere['_ordre'] ?? null;
        if (is_string($ordreVal) && $ordreVal !== '') {
            $sOrdre = ' ORDER BY ' . $ordreVal;
        }
        if (isset($aWhere['_ordre'])) {
            unset($aWhere['_ordre']);
        }
        $limitVal = $aWhere['_limit'] ?? null;
        if ((is_string($limitVal) || is_int($limitVal)) && (string) $limitVal !== '') {
            $sLimit = ' LIMIT ' . $limitVal;
        }
        if (isset($aWhere['_limit'])) {
            unset($aWhere['_limit']);
        }
        $sQry = "SELECT * FROM $nom_tabla " . $sCondicion . $sOrdre . $sLimit;
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $aWhere, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            // para las fechas del postgres (texto iso)
            $aDatos['f_nombramiento'] = (new ConverterDate('date', $aDatos['f_nombramiento']))->fromPg();
            $aDatos['f_cese'] = (new ConverterDate('date', $aDatos['f_cese']))->fromPg();
            $ProfesorAmpliacion = ProfesorAmpliacion::fromArray($aDatos);
            $ProfesorAmpliacionSet->add($ProfesorAmpliacion);
        }
        return array_values($ProfesorAmpliacionSet->getTot());
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