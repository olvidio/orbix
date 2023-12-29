<?php

namespace misas\infrastructure;

use core\ClaseRepository;
use core\Condicion;
use core\ConverterDate;
use core\Set;
use misas\domain\EncargoDiaId;
use misas\domain\entity\EncargoDia;
use misas\domain\repositories\EncargoDiaRepositoryInterface;
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
class PgEncargoDiaRepository extends ClaseRepository implements EncargoDiaRepositoryInterface
{
    public function __construct()
    {
        $oDbl = $GLOBALS['oDBC'];
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl_Select);
        $this->setNomTabla('misa_cuadricula_dl');
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo EncargoDia
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo EncargoDia
     */
    public function getEncargoDias(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $EncargoDiaSet = new Set();
        $oCondicion = new Condicion();
        $aCondicion = array();
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
            $sClaveError = 'PgPlantillaRepository.listar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        if (($oDblSt->execute($aWhere)) === FALSE) {
            $sClaveError = 'PgPlantillaRepository.listar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }

        $filas = $oDblSt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            // para las fechas del postgres (texto iso)
            $aDatos['tstart'] = (new ConverterDate('datetime', $aDatos['tstart']))->fromPg();
            $aDatos['tend'] = (new ConverterDate('datetime', $aDatos['tend']))->fromPg();
            $EncargoDia = new EncargoDia();
            $EncargoDia->setAllAttributes($aDatos);
            $EncargoDiaSet->add($EncargoDia);
        }
        return $EncargoDiaSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(EncargoDia $EncargoDia): bool
    {
        $uuid_item = $EncargoDia->getUuid_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE uuid_item = '$uuid_item'")) === FALSE) {
            $sClaveError = 'PgPlantillaRepository.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        return TRUE;
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(EncargoDia $EncargoDia): bool
    {
        $uuid_item = $EncargoDia->getUuid_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($uuid_item);

        $aDatos = [];
        $aDatos['id_enc'] = $EncargoDia->getId_enc();
        $aDatos['id_nom'] = $EncargoDia->getId_nom();
        $aDatos['observ'] = $EncargoDia->getObserv();
        // para las fechas
        $aDatos['tstart'] = (new ConverterDate('timestamp', $EncargoDia->getTstart()))->toPg();
        $aDatos['tend'] = (new ConverterDate('timestamp', $EncargoDia->getTend()))->toPg();

        array_walk($aDatos, 'core\poner_null');

        if ($bInsert === FALSE) {
            //UPDATE
            $update = "
					id_enc                   = :id_enc,
					tstart                  = :tstart,
					tend                    = :tend,
					id_nom                   = :id_nom,
					observ                   = :observ";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE uuid_item = '$uuid_item'")) === FALSE) {
                $sClaveError = 'PgPlantillaRepository.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }

            try {
                $oDblSt->execute($aDatos);
            } catch (PDOException $e) {
                $err_txt = $e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgPlantillaRepository.update.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }
        } else {
            // INSERT
            $aDatos['uuid_item'] = $EncargoDia->getUuid_item();
            $campos = "(uuid_item,id_enc,tstart,tend,id_nom,observ)";
            $valores = "(:uuid_item,:id_enc,:tstart,:tend,:id_nom,:observ)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClaveError = 'PgPlantillaRepository.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }
            try {
                $oDblSt->execute($aDatos);
            } catch (PDOException $e) {
                $err_txt = $e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgPlantillaRepository.insertar.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }
        }
        return TRUE;
    }

    private function isNew(EncargoDiaId $uuid_item): bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE uuid_item = '$uuid_item'")) === FALSE) {
            $sClaveError = 'PgPlantillaRepository.isNew';
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
    public function datosById(EncargoDiaId $uuid_item): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $uuid_item = $uuid_item->value();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE uuid_item = '$uuid_item'")) === FALSE) {
            $sClaveError = 'PgPlantillaRepository.getDatosById';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        $aDatos = $oDblSt->fetch(PDO::FETCH_ASSOC);
        // para las fechas del postgres (texto iso)
        if ($aDatos !== FALSE) {
            $aDatos['tstart'] = (new ConverterDate('datetime', $aDatos['tstart']))->fromPg();
            $aDatos['tend'] = (new ConverterDate('datetime', $aDatos['tend']))->fromPg();
        }
        return $aDatos;
    }


    /**
     * Busca la clase con id_item en la base de datos .
     */
    public function findById(EncargoDiaId $uuid_item): ?EncargoDia
    {
        $aDatos = $this->datosById($uuid_item);
        if (empty($aDatos)) {
            return null;
        }
        return (new EncargoDia())->setAllAttributes($aDatos);
    }

}