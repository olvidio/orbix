<?php

namespace src\dbextern\infrastructure\persistence\postgresql;

use PDO;
use src\dbextern\domain\contracts\PersonaBDURepositoryInterface;
use src\dbextern\domain\entity\PersonaBDU;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\postgresql\Set;
use src\shared\traits\HandlesPdoErrors;

class PgPersonaBDURepository extends ClaseRepository implements PersonaBDURepositoryInterface
{

    use HandlesPdoErrors;

    function __construct()
    {
        $oDbl = $GLOBALS['oDBP'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('tmp_bdu');
    }

    function getPersonaBDUQuery(string $sQuery = ''): array
    {
        $oDbl = $this->getoDbl();
        $oPersonaBDUSet = new Set();

        foreach ($oDbl->query($sQuery) as $aDatos) {
            $oPersonaBDU = PersonaBDU::fromArray($aDatos);
            $oPersonaBDUSet->add($oPersonaBDU);
        }
        return $oPersonaBDUSet->getTot();
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    public function getIdMatchPersonas(array $aWhere = [], array $aOperators = []): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $PersonaBDUSet = new Set();
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
            $PersonaBDU = PersonaBDU::fromArray($aDatos);
            $PersonaBDUSet->add($PersonaBDU);
        }
        return $PersonaBDUSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(PersonaBDU $PersonaBDU): bool
    {
        // no tiene sentido, es solamente de consulta
        return false;
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(PersonaBDU $PersonaBDU): bool
    {
        // no tiene sentido, es solamente de consulta
        return false;
    }

    private function isNew(int $id_listas): bool
    {
        // no tiene sentido, es solamente de consulta
        return false;
    }

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $id_listas
     * @return array|bool
     */
    public function datosById(int $id_listas): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE Identif = $id_listas";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        return $aDatos;
    }


    /**
     * Busca la clase con id_listas en la base de datos .
     */
    public function findById(int $id_listas): ?PersonaBDU
    {
        $aDatos = $this->datosById($id_listas);
        if (empty($aDatos)) {
            return null;
        }
        return PersonaBDU::fromArray($aDatos);
    }
}