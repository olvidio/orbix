<?php

namespace src\inventario\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\ConverterDate;
use core\Set;
use PDO;
use PDOException;
use src\inventario\domain\contracts\EquipajeRepositoryInterface;
use src\inventario\domain\entity\Equipaje;
use src\inventario\domain\value_objects\EquipajeId;


/**
 * Clase que adapta la tabla i_equipajes_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
class PgEquipajeRepository extends ClaseRepository implements EquipajeRepositoryInterface
{
    public function __construct()
    {
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('i_equipajes_dl');
    }

    function getEquipajesCoincidentes(string $f_ini_iso, string $f_fin_iso): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $sQuery = "SELECT id_equipaje,nom_equipaje FROM $nom_tabla 
			WHERE (f_ini BETWEEN '$f_ini_iso' AND '$f_fin_iso')
		   		OR (f_fin BETWEEN '$f_ini_iso' AND '$f_fin_iso')
			ORDER BY nom_equipaje";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorTipoDoc.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aOpciones = [];
        foreach ($oDbl->query($sQuery) as $aClave) {
            $aOpciones[] = $aClave[0];
        }
        return $aOpciones;
    }

    function zzgetEquipajesCoincidentes(int $id_equipaje): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $oEquipaje = new Equipaje($id_equipaje);
        $f_ini = $oEquipaje->getF_ini();
        $f_fin = $oEquipaje->getF_fin();
        $sQuery = "SELECT id_equipaje,nom_equipaje FROM $nom_tabla 
			WHERE (f_ini BETWEEN '$f_ini' AND '$f_fin')
		   		OR (f_fin BETWEEN '$f_ini' AND '$f_fin')
			ORDER BY nom_equipaje";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorTipoDoc.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aOpciones = [];
        foreach ($oDbl->query($sQuery) as $aClave) {
            $aOpciones[] = $aClave[0];
        }
        return $aOpciones;
    }

    function getArrayEquipajes(string $f_ini_iso = ''): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $where = '';
        if (!empty($f_ini_iso)) $where = "WHERE f_ini > '$f_ini_iso'";
        $sQuery = "SELECT id_equipaje,nom_equipaje FROM $nom_tabla 
			$where
			ORDER BY f_ini,nom_equipaje";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorTipoDoc.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return false;
        }
        $aOpciones = [];
        foreach ($oDbl->query($sQuery) as $aClave) {
            $clave = $aClave[0];
            $val = $aClave[1];
            $aOpciones[$clave] = $val;
        }
        return $aOpciones;
    }


    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Equipaje
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|FALSE Una colección de objetos de tipo Equipaje
     */
    public function getEquipajes(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $EquipajeSet = new Set();
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
            $sClaveError = 'PgEquipajeRepository.listar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        if (($oDblSt->execute($aWhere)) === FALSE) {
            $sClaveError = 'PgEquipajeRepository.listar.execute';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }

        $filas = $oDblSt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            // para las fechas del postgres (texto iso)
            $aDatos['f_ini'] = (new ConverterDate('date', $aDatos['f_ini']))->fromPg();
            $aDatos['f_fin'] = (new ConverterDate('date', $aDatos['f_fin']))->fromPg();
            $Equipaje = new Equipaje();
            $Equipaje->setAllAttributes($aDatos);
            $EquipajeSet->add($Equipaje);
        }
        return $EquipajeSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Equipaje $Equipaje): bool
    {
        $id_equipaje = $Equipaje->getIdEquipajeVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_equipaje = $id_equipaje")) === FALSE) {
            $sClaveError = 'PgEquipajeRepository.eliminar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        return TRUE;
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Equipaje $Equipaje): bool
    {
        $id_equipaje = $Equipaje->getIdEquipajeVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_equipaje);

        $aDatos = [];
        $aDatos['ids_activ'] = $Equipaje->getIdsActivVo()?->value();
        $aDatos['lugar'] = $Equipaje->getLugarVo()?->value();
        $aDatos['id_ubi_activ'] = $Equipaje->getIdUbiActivVo()?->value();
        $aDatos['nom_equipaje'] = $Equipaje->getNomEquipajeVo()?->value();
        $aDatos['cabecera'] = $Equipaje->getCabeceraVo()?->value();
        $aDatos['pie'] = $Equipaje->getPieVo()?->value();
        $aDatos['cabecerab'] = $Equipaje->getCabecerabVo()?->value();
        // para las fechas
        $aDatos['f_ini'] = (new ConverterDate('date', $Equipaje->getF_ini()))->toPg();
        $aDatos['f_fin'] = (new ConverterDate('date', $Equipaje->getF_fin()))->toPg();
        array_walk($aDatos, 'core\poner_null');

        if ($bInsert === FALSE) {
            //UPDATE
            $update = "
                    ids_activ                = :ids_activ,
                    lugar                    = :lugar,
                    f_ini                    = :f_ini,
                    f_fin                    = :f_fin,
                    id_ubi_activ             = :id_ubi_activ,
                    nom_equipaje             = :nom_equipaje,
                    cabecera                 = :cabecera,
                    pie                      = :pie,
                    cabecerab                = :cabecerab";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_equipaje = $id_equipaje")) === FALSE) {
                $sClaveError = 'PgEquipajeRepository.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }

            try {
                $oDblSt->execute($aDatos);
            } catch (PDOException $e) {
                $err_txt = $e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgEquipajeRepository.update.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }
        } else {
            // INSERT
            $aDatos['id_equipaje'] = $Equipaje->getIdEquipajeVo()->value();
            $campos = "(id_equipaje,ids_activ,lugar,f_ini,f_fin,id_ubi_activ,nom_equipaje,cabecera,pie,cabecerab)";
            $valores = "(:id_equipaje,:ids_activ,:lugar,:f_ini,:f_fin,:id_ubi_activ,:nom_equipaje,:cabecera,:pie,:cabecerab)";
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
                $sClaveError = 'PgEquipajeRepository.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }
            try {
                $oDblSt->execute($aDatos);
            } catch (PDOException $e) {
                $err_txt = $e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgEquipajeRepository.insertar.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }
        }
        return TRUE;
    }

    private function isNew(int $id_equipaje): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_equipaje = $id_equipaje")) === FALSE) {
            $sClaveError = 'PgEquipajeRepository.isNew';
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
     * @param EquipajeId $id_equipaje
     * @return array|bool
     */
    public function datosById(EquipajeId $id_equipaje): array|bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $id = $id_equipaje->value();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_equipaje = $id")) === FALSE) {
            $sClaveError = 'PgEquipajeRepository.getDatosById';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        $aDatos = $oDblSt->fetch(PDO::FETCH_ASSOC);
        // para las fechas del postgres (texto iso)
        if ($aDatos !== FALSE) {
            $aDatos['f_ini'] = (new ConverterDate('date', $aDatos['f_ini']))->fromPg();
            $aDatos['f_fin'] = (new ConverterDate('date', $aDatos['f_fin']))->fromPg();
        }
        return $aDatos;
    }


    /**
     * Busca la clase con id_equipaje en la base de datos .
     */
    public function findById(EquipajeId $id_equipaje): ?Equipaje
    {
        $aDatos = $this->datosById($id_equipaje);
        if (empty($aDatos)) {
            return null;
        }
        return (new Equipaje())->setAllAttributes($aDatos);
    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('i_equipajes_dl_id_equipaje_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}