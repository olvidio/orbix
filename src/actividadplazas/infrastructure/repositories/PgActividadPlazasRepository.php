<?php

namespace src\actividadplazas\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\ConverterDate;
use core\ConverterJson;
use core\Set;
use JsonException;
use PDO;
use src\actividadplazas\domain\contracts\ActividadPlazasRepositoryInterface;
use src\actividadplazas\domain\entity\ActividadPlazas;
use src\shared\traits\HandlesPdoErrors;


/**
 * Clase que adapta la tabla da_plazas a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 16/12/2025
 */
class PgActividadPlazasRepository extends ClaseRepository implements ActividadPlazasRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBP'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('da_plazas');
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ActividadPlazas
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo ActividadPlazas
     * @throws JsonException
     */
    public function getActividadesPlazas(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $ActividadPlazasSet = new Set();
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
            // para los json
            $aDatos['cedidas'] = (new ConverterJson($aDatos['cedidas'], true))->fromPg();
            $ActividadPlazas = ActividadPlazas::fromArray($aDatos);
            $ActividadPlazasSet->add($ActividadPlazas);
        }
        return $ActividadPlazasSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ActividadPlazas $ActividadPlazas): bool
    {
        $id_activ = $ActividadPlazas->getId_activ();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_activ = $id_activ";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     * @throws JsonException
     */
    public function Guardar(ActividadPlazas $ActividadPlazas): bool
    {
        $id_activ = $ActividadPlazas->getId_activ();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_activ);

        $aDatos = $ActividadPlazas->toArrayForDatabase([
            'cedidas' => fn($v) => (new ConverterJson($ActividadPlazas->getCedidas(), false))->toPg(false),
        ]);

        /*
        $aDatos = [];
        $aDatos['id_dl'] = $ActividadPlazas->getId_dl();
        $aDatos['plazas'] = $ActividadPlazas->getPlazasVo()->value();
        $aDatos['cl'] = $ActividadPlazas->getClVo()->value();
        $aDatos['dl_tabla'] = $ActividadPlazas->getDlTablaVo()->value();
        // para los json
        $aDatos['cedidas'] = (new ConverterJson($ActividadPlazas->getCedidas(), false))->toPg(false);
        array_walk($aDatos, 'core\poner_null');
        */

        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_activ']);
            $update = "
					id_dl                    = :id_dl,
					plazas                   = :plazas,
					cl                       = :cl,
					dl_tabla                 = :dl_tabla,
					cedidas                  = :cedidas";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_activ = $id_activ";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(id_activ,id_dl,plazas,cl,dl_tabla,cedidas)";
            $valores = "(:id_activ,:id_dl,:plazas,:cl,:dl_tabla,:cedidas)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_activ): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_activ = $id_activ";
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
     * @param int $id_activ
     * @return array|bool
     * @throws JsonException
     */
    public function datosById(int $id_activ): array|bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_activ = $id_activ";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        // para los json
        if ($aDatos !== false) {
            $aDatos['cedidas'] = (new ConverterJson($aDatos['cedidas'], true))->fromPg();
        }
        return $aDatos;
    }


    /**
     * Busca la clase con id_activ en la base de datos .
     * @throws JsonException
     */
    public function findById(int $id_activ): ?ActividadPlazas
    {
        $aDatos = $this->datosById($id_activ);
        if (empty($aDatos)) {
            return null;
        }
        return ActividadPlazas::fromArray($aDatos);
    }
}