<?php

namespace src\configuracion\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use PDOException;
use src\configuracion\domain\contracts\AppRepositoryInterface;
use src\configuracion\domain\entity\App;

/**
 * Clase que adapta la tabla m0_apps a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 10/11/2025
 */
class PgAppRepository extends ClaseRepository implements AppRepositoryInterface
{
    public function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('m0_apps');
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo App
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo App
     */
    public function getApps(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $AppSet = new Set();
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
            $sClaveError = 'PgAppRepository.listar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        if (($oDblSt->execute($aWhere)) === FALSE) {
            $sClaveError = 'PgAppRepository.listar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }

        $filas = $oDblSt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            $App = new App();
            $App->setAllAttributes($aDatos);
            $AppSet->add($App);
        }
        return $AppSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(App $App): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $id_app = $App->getIdAppVo()->value();

        if ($oDbl->exec("DELETE FROM $nom_tabla WHERE id_app=$id_app") === FALSE) {
            $sClaveError = 'PgAppRepository.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        return TRUE;
    }

    public function Guardar(App $App): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $id_app = $App->getIdAppVo()->value();
        $nom = $App->getNombreAppVo()->value();
        $bInsert = $this->isNew($id_app);

        $aDades = [];
        $aDades['nom'] = $nom;

        if ($this->isNew($id_app) === TRUE) {
            array_unshift($aDades, $id_app);
            $aClauPrimaria = '(id_app,nom)';
            $aClaus = '(:id_app,:nom)';
            $bInsert = TRUE;
        } else {
            $update = " nom=:nom";
            $sClau = " WHERE id_app='$id_app'";
        }
        if ($bInsert === FALSE) {
            $sQry = "UPDATE $nom_tabla SET  $update $sClau";
            if (($oDblSt = $oDbl->prepare($sQry)) === FALSE) {
                $sClaveError = 'PgAppRepository.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }
        } else {
            $sQry = "INSERT INTO $nom_tabla $aClauPrimaria VALUES $aClaus";
            if (($oDblSt = $oDbl->prepare($sQry)) === FALSE) {
                $sClaveError = 'PgAppRepository.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }
        }
        try {
            $oDblSt->execute($aDades);
        } catch (PDOException $e) {
            $err_txt = $e->errorInfo[2];
            $this->setErrorTxt($err_txt);
            $sClaveError = 'PgAppRepository.update.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        return TRUE;
    }

    private function isNew(int $id_app): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_app = $id_app")) === FALSE) {
            $sClaveError = 'PgAppRepository.isNew';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        if (!$oDblSt->rowCount()) {
            return TRUE;
        }
        return FALSE;
    }

    public function datosById(int $id_app): array|bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT * FROM $nom_tabla WHERE id_app = $id_app";
        if (($oDblSt = $oDbl->query($sQuery)) === FALSE) {
            $sClaveError = 'PgAppRepository.getDatosById';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        $aDades = $oDblSt->fetch(PDO::FETCH_ASSOC);
        if ($aDades === FALSE) {
            return FALSE;
        }
        return $aDades;
    }

    public function findById(int $id_app): ?App
    {
        $aDades = $this->datosById($id_app);
        if (!is_array($aDades)) {
            return null;
        }
        $App = new App();
        $App->setAllAttributes($aDades);
        return $App;
    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('m0_apps_id_app_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}
