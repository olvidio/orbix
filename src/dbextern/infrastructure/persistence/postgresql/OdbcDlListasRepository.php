<?php

namespace src\dbextern\infrastructure\persistence\postgresql;

use PDO;
use RuntimeException;
use src\dbextern\domain\entity\DlListas;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\postgresql\Set;
use src\shared\traits\HandlesPdoErrors;

class OdbcDlListasRepository extends ClaseRepository
{
    use HandlesPdoErrors;

    public function __construct()
    {
        try {
            $oDbl = GlobalPdo::get('oDBListas');
        } catch (RuntimeException) {
            exit(_("no se puede conectar con la base de datos de Listas"));
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl);
        $this->setNomTabla('dbo.q_Aux_Dl');
    }

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<DlListas>
     */
    public function getDlListas(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $DlListaSet = new Set();
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
            $DlListas = DlListas::fromArray($aDatos);
            $DlListaSet->add($DlListas);
        }

        return array_values($DlListaSet->getTot());
    }

    public function Eliminar(DlListas $DlListas): bool
    {
        $numero_dl = $DlListas->getNumero_dl();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE numero_dl = $numero_dl";

        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    public function Guardar(DlListas $DlListas): bool
    {
        $numero_dl = $DlListas->getNumero_dl();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($numero_dl);

        $aDatos = $DlListas->toArrayForDatabase();
        if ($bInsert === false) {
            unset($aDatos['nuemro_dl']);
            $update = "
					dl                  = :dl,
					nombre_dl           = :nombre_dl,
					abr_r               = :abr_r,
					numero_r            = :numero_r";
            $sql = "UPDATE $nom_tabla SET $update WHERE nuemro_dl = $numero_dl";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            $campos = "(nuemro_dl,dl,nombre_dl,abr_r,numero_r)";
            $valores = "(:nuemro_dl,:dl,:nombre_dl,:abr_r,:numero_r)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }

        if ($stmt === false) {
            return false;
        }

        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $numero_dl): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE numero_dl = $numero_dl";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false || !$stmt->rowCount()) {
            return true;
        }

        return false;
    }

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $numero_dl): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE numero_dl = $numero_dl";
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

    public function findById(int $numero_dl): ?DlListas
    {
        $aDatos = $this->datosById($numero_dl);
        if (empty($aDatos)) {
            return null;
        }

        return DlListas::fromArray($aDatos);
    }
}
