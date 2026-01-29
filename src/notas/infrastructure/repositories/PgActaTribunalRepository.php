<?php

namespace src\notas\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\ConfigGlobal;
use core\Set;
use PDO;
use src\notas\domain\contracts\ActaTribunalRepositoryInterface;
use src\notas\domain\entity\ActaTribunal;
use src\shared\traits\HandlesPdoErrors;


/**
 * Clase que adapta la tabla e_actas_tribunal_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 27/12/2025
 */
class PgActaTribunalRepository extends ClaseRepository implements ActaTribunalRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        // Si es cr, se mira en todas:
        if (ConfigGlobal::mi_ambito() === 'rstgr') {
            $oDbl = $GLOBALS['oDBP'];
            $this->setoDbl($oDbl);
            $this->setNomTabla('e_actas_tribunal');
        } else {
            $oDbl = $GLOBALS['oDB'];
            $this->setoDbl($oDbl);
            $this->setNomTabla('e_actas_tribunal_dl');
        }
    }

    /**
     * retorna JSON llista d'examinadors
     * des del 2020 (perque els d'abans són amb llatí)
     *
     * @param string sQuery la query a executar.
     * @return false Json
     */
    public function getJsonExaminadores($sQuery = ''): string
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (!empty($sQuery)) {
            $sCondi = "WHERE public.sin_acentos(examinador::text)  ~* public.sin_acentos('$sQuery'::text)
                        AND substring(acta, '\/(\d{2})')::integer > 19 ";
        } else {
            $sCondi = "WHERE substring(acta, '\/(\d{2})')::integer > 19 ";
        }
        $sOrdre = " ORDER BY examinador";
        $sLimit = " LIMIT 25";
        $sQry = "SELECT DISTINCT examinador FROM $nom_tabla " . $sCondi . $sOrdre . $sLimit;
        //echo "qry: $sQry<br>";
        $stmt = $this->pdoQuery($oDbl, $sQry, __METHOD__, __FILE__, __LINE__);

        $json = '[';
        $i = 0;
        foreach ($oDbl->query($sQry) as $aDades) {
            $i++;
            $json .= ($i > 1) ? ',' : '';
            $json .= "{\"label\":\"" . $aDades['examinador'] . "\"}";
        }
        $json .= ']';
        return $json;
    }


    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ActaTribunalDl
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo ActaTribunalDl
     */
    public function getActasTribunales(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $ActaTribunalDlSet = new Set();
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
            $ActaTribunalDl = ActaTribunal::fromArray($aDatos);
            $ActaTribunalDlSet->add($ActaTribunalDl);
        }
        return $ActaTribunalDlSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ActaTribunal $ActaTribunal): bool
    {
        $id_item = $ActaTribunal->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item = $id_item";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(ActaTribunal $ActaTribunal): bool
    {
        $id_item = $ActaTribunal->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item);

        $aDatos = $ActaTribunal->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_item']);
            $update = "
					acta                     = :acta,
					examinador               = :examinador,
					orden                    = :orden";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item = $id_item";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(acta,examinador,orden,id_item)";
            $valores = "(:acta,:examinador,:orden,:id_item)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_item): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item = $id_item";
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
     * @param int $id_item
     * @return array|bool
     */
    public function datosById(int $id_item): array|bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item = $id_item";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        return $aDatos;
    }


    /**
     * Busca la clase con id_item en la base de datos .
     */
    public function findById(int $id_item): ?ActaTribunal
    {
        $aDatos = $this->datosById($id_item);
        if (empty($aDatos)) {
            return null;
        }
        return ActaTribunal::fromArray($aDatos);
    }

}