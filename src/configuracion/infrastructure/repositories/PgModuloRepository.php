<?php

namespace src\configuracion\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use PDOException;
use src\configuracion\domain\contracts\ModuloRepositoryInterface;
use src\configuracion\domain\entity\Modulo;
use function core\array_pgInteger2php;
use function core\array_php2pg;

/**
 * Clase que adapta la tabla m0_modulos a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 15/4/2025
 */
class PgModuloRepository extends ClaseRepository implements ModuloRepositoryInterface
{
    public function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('m0_modulos');
    }

    public function getArrayModulos(): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $aOpciones = [];

        $sQuery = "SELECT id_mod, nom FROM $nom_tabla ORDER BY nom";
        try {
            $oDblSt = $oDbl->query($sQuery);
            foreach ($oDbl->query($sQuery) as $aClave) {
                $clave = $aClave[0];
                $val = $aClave[1];
                $aOpciones[$clave] = $val;
            }
        } catch (PDOException $e) {
            $err_txt = $e->errorInfo[2];
            $this->setErrorTxt($err_txt);
            $sClaveError = 'PgModuloRepository.update.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
        }

        return $aOpciones;
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Modulo
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo Modulo
     */
    public function getModulos(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $ModuloSet = new Set();
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
            $sClaveError = 'PgModuloRepository.listar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        if (($oDblSt->execute($aWhere)) === FALSE) {
            $sClaveError = 'PgModuloRepository.listar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }

        $filas = $oDblSt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            // para los array del postgres
            $aDatos['mods_req'] = array_pgInteger2php($aDatos['mods_req']);
            $aDatos['apps_req'] = array_pgInteger2php($aDatos['apps_req']);
            $Modulo = new Modulo();
            $Modulo->setAllAttributes($aDatos);
            $ModuloSet->add($Modulo);
        }
        return $ModuloSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Modulo $Modulo): bool
    {
        $id_mod = $Modulo->getId_mod();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_mod = $id_mod")) === FALSE) {
            $sClaveError = 'PgModuloRepository.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        return TRUE;
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Modulo $Modulo): bool
    {
        $id_mod = $Modulo->getId_mod();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_mod);

        $aDatos = [];
        $aDatos['nom'] = $Modulo->getNom();
        $aDatos['descripcion'] = $Modulo->getDescripcion();
        // para los array
        $aDatos['mods_req'] = array_php2pg($Modulo->getMods_req());
        $aDatos['apps_req'] = array_php2pg($Modulo->getApps_req());
        array_walk($aDatos, 'core\poner_null');

        if ($bInsert === FALSE) {
            //UPDATE
            $update = "
					nom                      = :nom,
					descripcion              = :descripcion,
					mods_req                 = :mods_req,
					apps_req                 = :apps_req";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_mod = $id_mod")) === FALSE) {
                $sClaveError = 'PgModuloRepository.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }

            try {
                $oDblSt->execute($aDatos);
            } catch (PDOException $e) {
                $err_txt = $e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgModuloRepository.update.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }
        } else {
            // INSERT
            $aDatos['id_mod'] = $Modulo->getId_mod();
            $campos = "(id_mod,nom,descripcion,mods_req,apps_req)";
            $valores = "(:id_mod,:nom,:descripcion,:mods_req,:apps_req)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClaveError = 'PgModuloRepository.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }
            try {
                $oDblSt->execute($aDatos);
            } catch (PDOException $e) {
                $err_txt = $e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgModuloRepository.insertar.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }
        }
        return TRUE;
    }

    private function isNew(int $id_mod): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_mod = $id_mod")) === FALSE) {
            $sClaveError = 'PgModuloRepository.isNew';
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
     * @param int $id_mod
     * @return array|bool
     */
    public function datosById(int $id_mod): array|bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_mod = $id_mod")) === FALSE) {
            $sClaveError = 'PgModuloRepository.getDatosById';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        $aDatos = $oDblSt->fetch(PDO::FETCH_ASSOC);
        // para los array del postgres
        if ($aDatos !== FALSE) {
            $aDatos['mods_req'] = array_pgInteger2php($aDatos['mods_req']);
            $aDatos['apps_req'] = array_pgInteger2php($aDatos['apps_req']);
        }
        return $aDatos;
    }


    /**
     * Busca la clase con id_mod en la base de datos .
     */
    public function findById(int $id_mod): ?Modulo
    {
        $aDatos = $this->datosById($id_mod);
        if (empty($aDatos)) {
            return null;
        }
        return (new Modulo())->setAllAttributes($aDatos);
    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('m0_modulos_id_mod_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}