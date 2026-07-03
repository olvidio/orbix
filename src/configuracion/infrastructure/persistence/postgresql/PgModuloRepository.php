<?php

namespace src\configuracion\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\ConverterDate;
use src\shared\infrastructure\persistence\postgresql\Set;
use PDO;
use src\configuracion\domain\contracts\ModuloRepositoryInterface;
use src\configuracion\domain\entity\Modulo;
use src\shared\traits\HandlesPdoErrors;

/**
 * Clase que adapta la tabla m0_modulos a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 15/4/2025
 */
class PgModuloRepository extends ClaseRepository implements ModuloRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBPC');
        $this->setoDbl($oDbl);
        $this->setNomTabla('m0_modulos');
    }

    public function getArrayModulos(): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $aOpciones = [];

        $sQuery = "SELECT id_mod, nom FROM $nom_tabla ORDER BY nom";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }
        foreach ($stmt->fetchAll(PDO::FETCH_NUM) as $aClave) {
            $clave = $aClave[0];
            $val = $aClave[1];
            $aOpciones[$clave] = $val;
        }
        return $aOpciones;
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Modulo
     *
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<Modulo> Una colección de objetos de tipo Modulo
     */
    public function getModulos(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
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
        $modulos = [];
        foreach ($filas as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            if (is_string($aDatos['mods_req'] ?? null)) {
                $aDatos['mods_req'] = \src\shared\domain\helpers\FuncTablasSupport::arrayPgInteger2php($aDatos['mods_req']);
            }
            if (is_string($aDatos['apps_req'] ?? null)) {
                $aDatos['apps_req'] = \src\shared\domain\helpers\FuncTablasSupport::arrayPgInteger2php($aDatos['apps_req']);
            }
            $normalized = [];
            foreach ($aDatos as $key => $value) {
                $normalized[(string) $key] = $value;
            }
            $modulos[] = Modulo::fromArray($normalized);
        }
        return $modulos;
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Modulo $Modulo): bool
    {
        $id_mod = $Modulo->getIdModVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_mod = $id_mod";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Modulo $Modulo): bool
    {
        $id_mod = $Modulo->getIdModVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_mod);

        $aDatos = $Modulo->toArrayForDatabase([
            'mods_req' => fn($v) => \src\shared\domain\helpers\FuncTablasSupport::arrayPhp2pg($Modulo->getModsReqVo()?->toArray() ?? []),
            'apps_req' => fn($v) => \src\shared\domain\helpers\FuncTablasSupport::arrayPhp2pg($Modulo->getAppsReqVo()?->toArray() ?? []),
        ]);
        /*
        $aDatos = [];
        $aDatos['nom'] = $Modulo->getNomVo()->value();
        $aDatos['descripcion'] = $Modulo->getDescripcionVo()?->value();
        // para los array
        $aDatos['mods_req'] = \src\shared\domain\helpers\FuncTablasSupport::arrayPhp2pg($Modulo->getModsReqVo()?->toArray());
        $aDatos['apps_req'] = \src\shared\domain\helpers\FuncTablasSupport::arrayPhp2pg($Modulo->getAppsReqVo()?->toArray());
        array_walk($aDatos, [\src\shared\domain\helpers\FuncTablasSupport::class, 'ponerNull']);
        */

        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_mod']);
            $update = "
					nom                      = :nom,
					descripcion              = :descripcion,
					mods_req                 = :mods_req,
					apps_req                 = :apps_req";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_mod = $id_mod";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            //INSERT
            $campos = "(id_mod,nom,descripcion,mods_req,apps_req)";
            $valores = "(:id_mod,:nom,:descripcion,:mods_req,:apps_req)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_mod): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_mod = $id_mod";
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
     * @param int $id_mod
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_mod): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_mod = $id_mod";
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
        $aDatos = $result;
        // para los array del postgres
        if (is_string($aDatos['mods_req'] ?? null)) {
            $aDatos['mods_req'] = \src\shared\domain\helpers\FuncTablasSupport::arrayPgInteger2php($aDatos['mods_req']);
        }
        if (is_string($aDatos['apps_req'] ?? null)) {
            $aDatos['apps_req'] = \src\shared\domain\helpers\FuncTablasSupport::arrayPgInteger2php($aDatos['apps_req']);
        }
        return $aDatos;
    }

    /**
     * Busca la clase con id_mod en la base de datos .
     */
    public function findById(int $id_mod): ?Modulo
    {
        $aDatos = $this->datosById($id_mod);
        if ($aDatos === false) {
            return null;
        }
        return Modulo::fromArray($aDatos);
    }

    /**
     * @return int|string
     */
    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('m0_modulos_id_mod_seq'::regclass)";
        $queryResult = $oDbl->query($sQuery);
        if ($queryResult === false) {
            return 0;
        }
        $result = $queryResult->fetchColumn();
        if ($result === false || $result === null) {
            return 0;
        }

        return $result;
    }
}