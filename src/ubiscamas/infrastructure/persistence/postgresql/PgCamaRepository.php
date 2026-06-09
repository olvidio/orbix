<?php

namespace src\ubiscamas\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\postgresql\Set;
use PDO;
use src\shared\domain\value_objects\Uuid;
use src\shared\traits\HandlesPdoErrors;
use src\ubiscamas\domain\contracts\CamaRepositoryInterface;
use src\ubiscamas\domain\entity\Cama;
use src\ubiscamas\domain\value_objects\HabitacionId;

/**
 * Clase que adapta la tabla du_camas a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage ubiscamas
 * @author Daniel Serrabou
 * @version 1.0
 * @created 12/03/2026
 */
class PgCamaRepository extends ClaseRepository implements CamaRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBPC');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBPC_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('du_camas');
    }

    /**
     * @return array<int|string, string>
     */
public function getArrayCamas(string $sCondicion = ''): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        if (empty($sCondicion))
            $sCondicion = "";
        $sQuery = "SELECT id_cama, descripcion FROM $nom_tabla $sCondicion ORDER BY descripcion";
        $stmt = $this->prepareAndExecute($oDbl, $sQuery, [], __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }
        $aCamas = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $id_cama = $row['id_cama'];
            $descripcion = $row['descripcion'];

            $aCamas[$id_cama] = $descripcion;
        }

        return $aCamas;
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Cama
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<Cama> Una colección de objetos de tipo Cama
     */
    public function getCamas(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $CamaSet = new Set();
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
            $normalized = [];
            foreach ($aDatos as $key => $value) {
                $normalized[(string) $key] = $value;
            }
            $CamaSet->add(Cama::fromArray($normalized));
        }
        /** @var list<Cama> $result */
        $result = array_values($CamaSet->getTot());

        return $result;
    }

    /**
     * devuelve una colección (array) de objetos de tipo Cama para una habitación específica
     *
     * @return list<Cama>
     */
    public function getCamasByHabitacion(HabitacionId $id_habitacion): array
    {
        $id_habitacion_val = $id_habitacion->value();
        return $this->getCamas(['id_habitacion' => $id_habitacion_val, '_ordre' => 'descripcion']);
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Cama $Cama): bool
    {
        $id_cama = $Cama->getIdCamaVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_cama = :id_cama";
        $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        $aDatos = ['id_cama' => $id_cama];
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Cama $Cama): bool
    {
        $id_cama = $Cama->getIdCamaVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_cama);

        $aDatos = $Cama->toArrayForDatabase();

        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_cama']);
            $update = "
                    id_habitacion        = :id_habitacion,
                    descripcion          = :descripcion,
                    larga                = :larga,
                    vip                  = :vip";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_cama = :id_cama_where ";
            $aDatos['id_cama_where'] = $id_cama;
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        else {
            //INSERT (du_camas exige id_schema NOT NULL; Hydratable no serializa id_schema)
            $campos = "(id_schema,id_cama,id_habitacion,descripcion,larga,vip)";
            $valores = "(:id_schema,:id_cama,:id_habitacion,:descripcion,:larga,:vip)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }

        if ($bInsert) {
            $idSchema = ConfigGlobal::mi_id_schema();
            if ($idSchema < 1) {
                throw new \RuntimeException(_('Falta id_schema de sesión (mi_id_schema) para persistir en du_camas.'));
            }
            $aDatos['id_schema'] = $idSchema;
        }

        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    protected function isNew(string $id_cama): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_cama = :id_cama";
        $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return true;
        }
        $stmt->execute(['id_cama' => $id_cama]);
        if (!$stmt->rowCount()) {
            return TRUE;
        }
        return false;
    }

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param string $id_cama
     * @return array<string, mixed>|false
     */
    public function datosById(string $id_cama): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_cama = :id_cama";
        $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        $stmt->execute(['id_cama' => $id_cama]);
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
     * Busca la clase con id_cama en la base de datos .
     */
    public function findById(string $id_cama): ?Cama
    {
        $aDatos = $this->datosById($id_cama);
        if ($aDatos === false) {
            return null;
        }
        return Cama::fromArray($aDatos);
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