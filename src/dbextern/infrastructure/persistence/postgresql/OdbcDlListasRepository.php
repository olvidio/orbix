<?php

namespace src\dbextern\infrastructure\persistence\postgresql;

use PDO;
use src\dbextern\domain\entity\DlListas;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\postgresql\Set;
use src\shared\traits\HandlesPdoErrors;

/**
 * GestorDlListas
 *
 * Classe per gestionar la llista d'objectes de la clase DlListas
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 5/12/2019
 */
class OdbcDlListasRepository extends ClaseRepository
{
    use HandlesPdoErrors;

    function __construct()
    {
        if (!empty($GLOBALS['oDBListas']) && $GLOBALS['oDBListas'] === 'error') {
            exit(_("no se puede conectar con la base de datos de Listas"));
        }
        $oDbl = $GLOBALS['oDBListas'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl);
        $this->setNomTabla('dbo.q_Aux_Dl');
    }


    public function getDlListas(array $aWhere = [], array $aOperators = []): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $DlListaSet = new Set();
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
            $DlListas = DlListas::fromArray($aDatos);
            $DlListaSet->add($DlListas);
        }
        return $DlListaSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(DlListas $DlListas): bool
    {
        $numero_dl = $DlListas->getNumero_dl();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE numero_dl = $numero_dl";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(DlListas $DlListas): bool
    {
        $numero_dl = $DlListas->getNumero_dl();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($numero_dl);

        $aDatos = $DlListas->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['nuemro_dl']);
            $update = "
					dl                  = :dl,
					nombre_dl           = :nombre_dl,
					abr_r               = :abr_r,
					numero_r            = :numero_r";
            $sql = "UPDATE $nom_tabla SET $update WHERE nuemro_dl = $numero_dl";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(nuemro_dl,dl,nombre_dl,abr_r,numero_r)";
            $valores = "(:nuemro_dl,:dl,:nombre_dl,:abr_r,:numero_r)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $numero_dl): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE numero_dl = $numero_dl";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if (!$stmt->rowCount()) {
            return TRUE;
        }
        return false;
    }

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @param int $numero_dl
     * @return array|bool
     */
    public function datosById(int $numero_dl): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE numero_dl = $numero_dl";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        return $aDatos;
    }


    /**
     * Busca la clase con id_enc en la base de datos .
     */
    public function findById(int $numero_dl): ?DlListas
    {
        $aDatos = $this->datosById($numero_dl);
        if (empty($aDatos)) {
            return null;
        }
        return DlListas::fromArray($aDatos);
    }
}
