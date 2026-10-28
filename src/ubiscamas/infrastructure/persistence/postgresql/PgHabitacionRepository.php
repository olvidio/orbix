<?php

namespace src\ubiscamas\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\ubiscamas\domain\criteria\HabitacionField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\RunsCriteriaQueries;
use src\shared\infrastructure\persistence\postgresql\Set;
use PDO;
use src\shared\traits\HandlesPdoErrors;
use src\ubiscamas\domain\contracts\HabitacionRepositoryInterface;
use src\ubiscamas\domain\entity\Habitacion;

/**
 * Clase que adapta la tabla du_habitaciones a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage ubiscamas
 * @author Daniel Serrabou
 * @version 1.0
 * @created 12/03/2026
 */
class PgHabitacionRepository extends ClaseRepository implements HabitacionRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBPC');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBPC_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('du_habitaciones');
    }

    /**
     * @return array<int|string, string>
     */
public function getArrayHabitaciones(string $sCondicion = ''): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $orden = 'orden';
        if (empty($sCondicion))
            $sCondicion = "";
        $sQuery = "SELECT id_habitacion, nombre FROM $nom_tabla $sCondicion ORDER BY $orden";
        $stmt = $this->prepareAndExecute($oDbl, $sQuery, [], __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }
        $aHabitaciones = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $id_habitacion = $row['id_habitacion'];
            $nombre = $row['nombre'];

            $aHabitaciones[$id_habitacion] = $nombre;
        }

        return $aHabitaciones;
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Habitacion que cumplen el criterio.
     *
     * @return list<Habitacion>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(HabitacionField::class, self::COLUMNAS);
        return array_map(Habitacion::fromArray(...), $this->rowsMatching($criteria, $conv));
    }

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<Habitacion>
     */
    public function getHabitaciones(array $aWhere = [], array $aOperators = []): array
    {
        $adapter = new LegacyWhereAdapter(HabitacionField::class, self::COLUMNAS);
        return $this->matching($adapter->toCriteria($aWhere, $aOperators));
    }

    /**
     * devuelve una colección (array) de objetos de tipo Habitacion para un id_ubi específico
     *
     * @param int $id_ubi
     * @return list<Habitacion> Una colección de objetos de tipo Habitacion
     */
    public function getHabitacionesByUbi(int $id_ubi): array
    {
        return $this->matching(
            Criteria::create()->where(Filter::eq(HabitacionField::IdUbi, $id_ubi))->orderBy(HabitacionField::Orden)
        );
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Habitacion $Habitacion): bool
    {
        $id_habitacion = $Habitacion->getIdHabitacionVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_habitacion = :id_habitacion";
        $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        $aDatos = ['id_habitacion' => $id_habitacion];
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Habitacion $Habitacion): bool
    {
        $id_habitacion = $Habitacion->getIdHabitacionVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_habitacion);

        $aDatos = $Habitacion->toArrayForDatabase();

        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_habitacion']);
            $update = "
                    id_ubi               = :id_ubi,
                    orden                = :orden,
                    nombre               = :nombre,
                    numero_camas         = :numero_camas,
                    numero_camas_vip     = :numero_camas_vip,
                    planta               = :planta,
                    sillon               = :sillon,
                    adaptada             = :adaptada,
                    observaciones        = :observaciones,
                    tipoLavabo           = :tipoLavabo,
                    despacho             = :despacho";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_habitacion = :id_habitacion_where";
            $aDatos['id_habitacion_where'] = $id_habitacion;
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        else {
            //INSERT
            $campos = "(id_ubi,id_habitacion,orden,nombre,numero_camas,numero_camas_vip,planta,sillon,adaptada,observaciones,tipoLavabo,despacho)";
            $valores = "(:id_ubi,:id_habitacion,:orden,:nombre,:numero_camas,:numero_camas_vip,:planta,:sillon,:adaptada,:observaciones,:tipoLavabo,:despacho)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    protected function isNew(string $id_habitacion): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_habitacion = :id_habitacion";
        $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return true;
        }
        $stmt->execute(['id_habitacion' => $id_habitacion]);
        if (!$stmt->rowCount()) {
            return TRUE;
        }
        return false;
    }

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param string $id_habitacion
     * @return array<string, mixed>|false
     */
    public function datosById(string $id_habitacion): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_habitacion = :id_habitacion";
        $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        $stmt->execute(['id_habitacion' => $id_habitacion]);
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
     * Busca la clase con id_habitacion en la base de datos .
     */
    public function findById(string $id_habitacion): ?Habitacion
    {
        $aDatos = $this->datosById($id_habitacion);
        if ($aDatos === false) {
            return null;
        }
        return Habitacion::fromArray($aDatos);
    }

    public function getNewId(): string|false
    {
        $oDbl = $this->getoDbl();
        $sQuery = "SELECT gen_random_uuid()";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return false;
        }
        $col = $stmt->fetchColumn();
        return is_string($col) ? $col : false;
    }
}