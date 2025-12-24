<?php

namespace src\cartaspresentacion\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use src\cartaspresentacion\domain\contracts\CartaPresentacionRepositoryInterface;
use src\cartaspresentacion\domain\entity\CartaPresentacion;
use src\shared\traits\HandlesPdoErrors;


/**
 * Clase que adapta la tabla du_presentacion_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/12/2025
 */
class PgCartaPresentacionRepository extends ClaseRepository implements CartaPresentacionRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBP'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('du_presentacion');
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo CartaPresentacion
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo CartaPresentacion
     */
    public function getCartasPresentacion(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $CartaPresentacionSet = new Set();
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
            $CartaPresentacion = new CartaPresentacion();
            $CartaPresentacion->setAllAttributes($aDatos);
            $CartaPresentacionSet->add($CartaPresentacion);
        }
        return $CartaPresentacionSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(CartaPresentacion $CartaPresentacion): bool
    {
        $id_ubi = $CartaPresentacion->getId_ubi();
        $id_direccion = $CartaPresentacion->getId_direccion();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_ubi = $id_ubi AND id_direccion = $id_direccion";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(CartaPresentacion $CartaPresentacion): bool
    {
        $id_ubi = $CartaPresentacion->getId_ubi();
        $id_direccion = $CartaPresentacion->getId_direccion();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_ubi, $id_direccion);

        $aDatos = [];
        $aDatos['pres_nom'] = $CartaPresentacion->getPres_nom();
        $aDatos['pres_telf'] = $CartaPresentacion->getPres_telf();
        $aDatos['pres_mail'] = $CartaPresentacion->getPres_mail();
        $aDatos['zona'] = $CartaPresentacion->getZona();
        $aDatos['observ'] = $CartaPresentacion->getObserv();
        array_walk($aDatos, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					pres_nom                 = :pres_nom,
					pres_telf                = :pres_telf,
					pres_mail                = :pres_mail,
					zona                     = :zona,
					observ                   = :observ";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_ubi = $id_ubi AND id_direccion = $id_direccion";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $aDatos['id_ubi'] = $id_ubi;
            $aDatos['id_direccion'] = $id_direccion;
            $campos = "(id_direccion,id_ubi,pres_nom,pres_telf,pres_mail,zona,observ)";
            $valores = "(:id_direccion,:id_ubi,:pres_nom,:pres_telf,:pres_mail,:zona,:observ)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_ubi, int $id_direccion): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_ubi = $id_ubi AND id_direccion = $id_direccion";
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
     * @param int $id_ubi
     * @param int $id_direccion
     * @return array|bool
     */
    public function datosById(int $id_ubi, int $id_direccion): array|bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_ubi = $id_ubi AND id_direccion = $id_direccion";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        return $aDatos;
    }


    /**
     * Busca la clase con id_direccion en la base de datos .
     */
    public function findById(int $id_ubi, int $id_direccion): ?CartaPresentacion
    {
        $aDatos = $this->datosById($id_ubi, $id_direccion);
        if (empty($aDatos)) {
            return null;
        }
        return (new CartaPresentacion())->setAllAttributes($aDatos);
    }
}