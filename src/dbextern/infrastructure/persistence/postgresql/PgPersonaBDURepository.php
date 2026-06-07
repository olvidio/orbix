<?php

namespace src\dbextern\infrastructure\persistence\postgresql;

use PDO;
use PDOStatement;
use src\dbextern\domain\contracts\PersonaBDURepositoryInterface;
use src\dbextern\domain\entity\PersonaBDU;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\postgresql\Set;
use src\shared\traits\HandlesPdoErrors;

class PgPersonaBDURepository extends ClaseRepository implements PersonaBDURepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBP');
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl);
        $this->setNomTabla('tmp_bdu');
    }

    /**
     * @return list<PersonaBDU>
     */
    public function getPersonaBDUQuery(string $sQuery = ''): array
    {
        $oDbl = $this->getoDbl();
        $oPersonaBDUSet = new Set();
        $stmt = $oDbl->query($sQuery);
        if ($stmt instanceof PDOStatement) {
            foreach ($stmt as $aDatos) {
                if (!is_array($aDatos)) {
                    continue;
                }
                $oPersonaBDUSet->add(PersonaBDU::fromArray($aDatos));
            }
        }

        return array_values($oPersonaBDUSet->getTot());
    }

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<PersonaBDU>
     */
    public function getIdMatchPersonas(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $PersonaBDUSet = new Set();
        $oCondicion = new Condicion();
        $aCondicion = [];
        foreach ($aWhere as $camp => $val) {
            if ($camp === '_ordre' || $camp === '_limit') {
                continue;
            }
            $sOperador = $aOperators[$camp] ?? '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) {
                $aCondicion[] = $a;
            }
            if ($sOperador === 'BETWEEN' || $sOperador === 'IS NULL' || $sOperador === 'IS NOT NULL' || $sOperador === 'OR') {
                unset($aWhere[$camp]);
            }
            if ($sOperador === 'IN' || $sOperador === 'NOT IN' || $sOperador === 'TXT') {
                unset($aWhere[$camp]);
            }
        }
        $sCondicion = implode(' AND ', $aCondicion);
        if ($sCondicion !== '') {
            $sCondicion = " WHERE " . $sCondicion;
        }
        $sOrdre = '';
        $sLimit = '';
        if (isset($aWhere['_ordre']) && is_string($aWhere['_ordre']) && $aWhere['_ordre'] !== '') {
            $sOrdre = ' ORDER BY ' . $aWhere['_ordre'];
        }
        unset($aWhere['_ordre']);
        if (isset($aWhere['_limit']) && is_string($aWhere['_limit']) && $aWhere['_limit'] !== '') {
            $sLimit = ' LIMIT ' . $aWhere['_limit'];
        }
        unset($aWhere['_limit']);
        $sQry = "SELECT * FROM $nom_tabla " . $sCondicion . $sOrdre . $sLimit;
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $aWhere, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            $PersonaBDUSet->add(PersonaBDU::fromArray($aDatos));
        }

        return array_values($PersonaBDUSet->getTot());
    }

    public function Eliminar(PersonaBDU $PersonaBDU): bool
    {
        return false;
    }

    public function Guardar(PersonaBDU $PersonaBDU): bool
    {
        return false;
    }

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_listas): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE Identif = $id_listas";
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

    public function findById(int $id_listas): ?PersonaBDU
    {
        $aDatos = $this->datosById($id_listas);
        if ($aDatos === false) {
            return null;
        }

        return PersonaBDU::fromArray($aDatos);
    }
}
