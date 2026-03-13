<?php

namespace src\ubiscamas\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
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

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('du_habitaciones');
    }

    public function getArrayHabitaciones($sCondicion = ''): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $orden = 'orden';
        if (empty($sCondicion)) $sCondicion = "";
        $sQuery = "SELECT id_habitacion, nombre FROM $nom_tabla $sCondicion ORDER BY $orden";
        $stmt = $this->prepareAndExecute($oDbl, $sQuery, [], __METHOD__, __FILE__, __LINE__);
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
     * devuelve una colección (array) de objetos de tipo Habitacion
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo Habitacion
     */
    public function getHabitaciones(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $HabitacionSet = new Set();
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
        if (isset($aWhere['_ordre']) && $aWhere['_ordre'] !== '') {
            $sOrdre = ' ORDER BY ' . $aWhere['_ordre'];
        }
        if (isset($aWhere['_ordre'])) {
            unset($aWhere['_ordre']);
        }
        if (isset($aWhere['_limit']) && $aWhere['_limit'] !== '') {
            $sLimit = ' LIMIT ' . $aWhere['_limit'];
        }
        if (isset($aWhere['_limit'])) {
            unset($aWhere['_limit']);
        }
        $sQry = "SELECT * FROM $nom_tabla " . $sCondicion . $sOrdre . $sLimit;
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $aWhere, __METHOD__, __FILE__, __LINE__);

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            $Habitacion = Habitacion::fromArray($aDatos);
            $HabitacionSet->add($Habitacion);
        }
        return $HabitacionSet->getTot();
    }

    /**
     * devuelve una colección (array) de objetos de tipo Habitacion para un id_ubi específico
     *
     * @param int $id_ubi
     * @return array|false Una colección de objetos de tipo Habitacion
     */
    public function getHabitacionesByUbi(int $id_ubi): array|false
    {
        return $this->getHabitaciones(['id_ubi' => $id_ubi], ['_ordre' => 'orden']);
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Habitacion $Habitacion): bool
    {
        $id_habitacion = $Habitacion->getIdHabitacion();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_habitacion = :id_habitacion";
        $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        $aDatos = ['id_habitacion' => $id_habitacion];
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Habitacion $Habitacion): bool
    {
        $id_habitacion = $Habitacion->getIdHabitacion();
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
                    fumador              = :fumador,
                    tipoLavabo           = :tipoLavabo,
                    despacho             = :despacho";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_habitacion = :id_habitacion_where";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            //INSERT
            $campos = "(id_ubi,id_habitacion,orden,nombre,numero_camas,numero_camas_vip,planta,sillon,adaptada,fumador,tipoLavabo,despacho)";
            $valores = "(:id_ubi,:id_habitacion,:orden,:nombre,:numero_camas,:numero_camas_vip,:planta,:sillon,:adaptada,:fumador,:tipoLavabo,:despacho)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    protected function isNew(string $id_habitacion): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_habitacion = :id_habitacion";
        $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
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
     * @return array|bool
     */
    public function datosById(string $id_habitacion): array|bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_habitacion = :id_habitacion";
        $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        $stmt->execute(['id_habitacion' => $id_habitacion]);
        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        return $aDatos;
    }

    /**
     * Busca la clase con id_habitacion en la base de datos .
     */
    public function findById(string $id_habitacion): ?Habitacion
    {
        $aDatos = $this->datosById($id_habitacion);
        if (empty($aDatos)) {
            return null;
        }
        return Habitacion::fromArray($aDatos);
    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "SELECT gen_random_uuid()";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}
