<?php

namespace misas\infrastructure;

use core\ClaseRepository;
use core\Condicion;
use core\ConverterDate;
use core\Set;
use misas\domain\EncargoCtrId;
use misas\domain\entity\EncargoCtr;
use misas\domain\repositories\EncargoCtrRepositoryInterface;
use PDO;
use PDOException;

/**
 * Clase que adapta la tabla misa_plantillas_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/3/2023
 */
class PgEncargoCtrRepository extends ClaseRepository implements EncargoCtrRepositoryInterface
{
    public function __construct()
    {
        $oDbl = $GLOBALS['oDBC'];
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('misa_rel_encargo_ctr');
    }


    public function getEncargosCentro(int $id_ubi): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $EncargoCtrSet = new Set();

        $aWhere = ['id_ubi' => $id_ubi];
        $sQry = "SELECT * FROM $nom_tabla WHERE id_ubi = :id_ubi ";
        if (($oDblSt = $oDbl->prepare($sQry)) === FALSE) {
            $sClaveError = 'PgEncargoCtrRepository.listar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        if (($oDblSt->execute($aWhere)) === FALSE) {
            $sClaveError = 'PgEncargoCtrRepository.listar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }

        $filas = $oDblSt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            $EncargoCtr = new EncargoCtr();
            $EncargoCtr->setAllAttributes($aDatos);
            $EncargoCtrSet->add($EncargoCtr);
        }
        return $EncargoCtrSet->getTot();
    }

    public function getCentrosEncargo(int $id_enc): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $EncargoCtrSet = new Set();

        $aWhere = ['id_enc' => $id_enc];
        $sQry = "SELECT * FROM $nom_tabla WHERE id_enc = :id_enc ";
        if (($oDblSt = $oDbl->prepare($sQry)) === FALSE) {
            $sClaveError = 'PgEncargoCtrRepository.listar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        if (($oDblSt->execute($aWhere)) === FALSE) {
            $sClaveError = 'PgEncargoCtrRepository.listar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }

        $filas = $oDblSt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            $EncargoCtr = new EncargoCtr();
            $EncargoCtr->setAllAttributes($aDatos);
            $EncargoCtrSet->add($EncargoCtr);
        }
        return $EncargoCtrSet->getTot();
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */
    /**
     * devuelve una colección (array) de objetos de tipo EncargoCtr
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo EncargoCtr
     */
    public function getEncargosCentros(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $EncargoCtrSet = new Set();
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
        if (($oDblSt = $oDbl->prepare($sQry)) === FALSE) {
            $sClaveError = 'PgEncargoCtrRepository.listar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        if (($oDblSt->execute($aWhere)) === FALSE) {
            $sClaveError = 'PgEncargoCtrRepository.listar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }

        $filas = $oDblSt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            $EncargoCtr = new EncargoCtr();
            $EncargoCtr->setAllAttributes($aDatos);
            $EncargoCtrSet->add($EncargoCtr);
        }
        return $EncargoCtrSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(EncargoCtr $EncargoCtr): bool
    {
        $uuid_item = $EncargoCtr->getUuid_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE uuid_item = '$uuid_item'")) === FALSE) {
            $sClaveError = 'PgEncargoCtrRepository.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        return TRUE;
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(EncargoCtr $EncargoCtr): bool
    {
        $uuid_item = $EncargoCtr->getUuid_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($uuid_item);

        $aDatos = $EncargoCtr->toArrayForDatabase();
        if ($bInsert === FALSE) {
            //UPDATE
            $update = "
					id_enc                   = :id_enc,
					id_ubi                   = :id_ubi";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE uuid_item = '$uuid_item'")) === FALSE) {
                $sClaveError = 'PgEncargoCtrRepository.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }

            try {
                $oDblSt->execute($aDatos);
            } catch (PDOException $e) {
                $err_txt = $e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgEncargoCtrRepository.update.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }
        } else {
            // INSERT
            $aDatos['uuid_item'] = $EncargoCtr->getUuid_item();
            $campos = "(uuid_item,id_enc,id_ubi)";
            $valores = "(:uuid_item,:id_enc,:id_ubi)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClaveError = 'PgEncargoCtrRepository.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }
            try {
                $oDblSt->execute($aDatos);
            } catch (PDOException $e) {
                $err_txt = $e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgEncargoCtrRepository.insertar.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }
        }
        return TRUE;
    }

    private function isNew(EncargoCtrId $uuid_item): bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE uuid_item = '$uuid_item'")) === FALSE) {
            $sClaveError = 'PgEncargoCtrRepository.isNew';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        if (!$oDblSt->rowCount()) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     */
    public function datosById(EncargoCtrId $uuid_item): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $uuid_item = $uuid_item->value();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE uuid_item = '$uuid_item'")) === FALSE) {
            $sClaveError = 'PgEncargoCtrRepository.getDatosById';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        $aDatos = $oDblSt->fetch(PDO::FETCH_ASSOC);
        return $aDatos;
    }


    /**
     * Busca la clase con id_item en la base de datos .
     */
    public function findById(EncargoCtrId $uuid_item): ?EncargoCtr
    {
        $aDatos = $this->datosById($uuid_item);
        if (empty($aDatos)) {
            return null;
        }
        return (new EncargoCtr())->setAllAttributes($aDatos);
    }

}