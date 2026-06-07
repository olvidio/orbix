<?php

namespace src\pasarela\infrastructure\persistence\postgresql;

use PDO;
use src\pasarela\domain\contracts\PasarelaConfigRepositoryInterface;
use src\pasarela\domain\entity\PasarelaConfig;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\ConverterJson;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\postgresql\Set;
use src\shared\traits\HandlesPdoErrors;

class PgPasarelaConfigRepository extends ClaseRepository implements PasarelaConfigRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $this->setoDbl(GlobalPdo::get('oDBC'));
        $this->setoDbl_Select(GlobalPdo::get('oDBC_Select'));
        $this->setNomTabla('pasarela_dl');
    }

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<PasarelaConfig>
     */
    public function getPasarelaConfigs(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $ConfigSchemaSet = new Set();
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
            $sCondicion = ' WHERE ' . $sCondicion;
        }
        $sOrdre = '';
        $sLimit = '';
        if (isset($aWhere['_ordre']) && is_string($aWhere['_ordre']) && $aWhere['_ordre'] !== '') {
            $sOrdre = ' ORDER BY ' . $aWhere['_ordre'];
        }
        if (isset($aWhere['_ordre'])) {
            unset($aWhere['_ordre']);
        }
        if (isset($aWhere['_limit']) && (is_string($aWhere['_limit']) || is_int($aWhere['_limit'])) && (string)$aWhere['_limit'] !== '') {
            $sLimit = ' LIMIT ' . $aWhere['_limit'];
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
            $aDatos['json_valor'] = (new ConverterJson($aDatos['json_valor'], false))->fromPg();
            $ConfigSchema = PasarelaConfig::fromArray($aDatos);
            $ConfigSchemaSet->add($ConfigSchema);
        }

        /** @var list<PasarelaConfig> $result */
        $result = $ConfigSchemaSet->getTot();

        return $result;
    }

    public function Eliminar(PasarelaConfig $PasarelaConfig): bool
    {
        $nom_parametro = $PasarelaConfig->getNomParametroVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE nom_parametro = '$nom_parametro'";

        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    public function Guardar(PasarelaConfig $PasarelaConfig): bool
    {
        $nom_parametro = $PasarelaConfig->getNomParametroVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($nom_parametro);

        $aDatos = $PasarelaConfig->toArrayForDatabase([
            'json_valor' => fn($v) => (new ConverterJson($v, false))->toPg(false),
        ]);

        if ($bInsert === false) {
            unset($aDatos['nom_parametro']);
            $update = '
					json_valor              = :json_valor';
            $sql = "UPDATE $nom_tabla SET $update WHERE nom_parametro = '$nom_parametro'";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            $campos = '(nom_parametro,json_valor)';
            $valores = '(:nom_parametro,:json_valor)';
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }

        if ($stmt === false) {
            return false;
        }

        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(string $nom_parametro): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE nom_parametro = '$nom_parametro'";
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
    public function datosById(string $nom_parametro): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE nom_parametro = '$nom_parametro'";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return false;
        }
        /** @var array<string, mixed> $row */

        return $row;
    }

    public function findById(string $nom_parametro): ?PasarelaConfig
    {
        $aDatos = $this->datosById($nom_parametro);
        if ($aDatos === false) {
            return null;
        }

        return PasarelaConfig::fromArray($aDatos);
    }
}
