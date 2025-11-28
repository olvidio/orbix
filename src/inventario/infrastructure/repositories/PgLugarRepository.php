<?php

namespace src\inventario\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use PDOException;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\entity\Lugar;
use src\inventario\domain\value_objects\LugarId;

/**
 * Clase que adapta la tabla i_lugares_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
class PgLugarRepository extends ClaseRepository implements LugarRepositoryInterface
{
    public function __construct()
    {
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('i_lugares_dl');
    }

    public function getArrayLugares(int $id_ubi):array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
		$sQuery="SELECT id_lugar, nom_lugar
				FROM $nom_tabla
				WHERE id_ubi='$id_ubi'
				ORDER BY id_ubi,nom_lugar";
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorLugar.lista';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
       $aOpciones=[];
		foreach ($oDbl->query($sQuery) as $aClave) {
			$clave=$aClave[0];
			$val=$aClave[1];
			$aOpciones[$clave]=$val;
		}
		return $aOpciones;
	}

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Lugar
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|false Una colección de objetos de tipo Lugar
	
	 */
	public function getLugares(array $aWhere=[], array $aOperators=[]): array|false
	{
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$LugarSet = new Set();
		$oCondicion = new Condicion();
		$aCondicion = [];
		foreach ($aWhere as $camp => $val) {
			if ($camp === '_ordre') { continue; }
			if ($camp === '_limit') { continue; }
			$sOperador = $aOperators[$camp] ?? '';
			if ($a = $oCondicion->getCondicion($camp,$sOperador,$val)) { $aCondicion[]=$a; }
			// operadores que no requieren valores
			if ($sOperador === 'BETWEEN' || $sOperador === 'IS NULL' || $sOperador === 'IS NOT NULL' || $sOperador === 'OR') { unset($aWhere[$camp]); }
            if ($sOperador === 'IN' || $sOperador === 'NOT IN') { unset($aWhere[$camp]); }
            if ($sOperador === 'TXT') { unset($aWhere[$camp]); }
		}
		$sCondicion = implode(' AND ',$aCondicion);
		if ($sCondicion !=='') { $sCondicion = " WHERE ".$sCondicion; }
		$sOrdre = '';
        $sLimit = '';
		if (isset($aWhere['_ordre']) && $aWhere['_ordre'] !== '') { $sOrdre = ' ORDER BY '.$aWhere['_ordre']; }
		if (isset($aWhere['_ordre'])) { unset($aWhere['_ordre']); }
		if (isset($aWhere['_limit']) && $aWhere['_limit'] !== '') { $sLimit = ' LIMIT '.$aWhere['_limit']; }
		if (isset($aWhere['_limit'])) { unset($aWhere['_limit']); }
		$sQry = "SELECT * FROM $nom_tabla ".$sCondicion.$sOrdre.$sLimit;
		if (($oDblSt = $oDbl->prepare($sQry)) === false) {
			$sClaveError = 'PgLugarRepository.listar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
			return false;
		}
		if (($oDblSt->execute($aWhere)) === false) {
			$sClaveError = 'PgLugarRepository.listar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
			return false;
		}
		
		$filas = $oDblSt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            $Lugar = new Lugar();
            $Lugar->setAllAttributes($aDatos);
			$LugarSet->add($Lugar);
		}
		return $LugarSet->getTot();
	}

/* -------------------- ENTIDAD --------------------------------------------- */

 public function Eliminar(Lugar $Lugar): bool
    {
        $id_lugar = $Lugar->getIdLugarVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_lugar = $id_lugar")) === false) {
            $sClaveError = 'PgLugarRepository.eliminar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return false;
        }
        return TRUE;
    }

	
	/**
	 * Si no existe el registro, hace un insert, si existe, se hace el update.
	
	 */
 public function Guardar(Lugar $Lugar): bool
    {
        $id_lugar = $Lugar->getIdLugarVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_lugar);

        $aDatos = [];
        $aDatos['id_ubi'] = $Lugar->getIdUbiVo()->value();
        $aDatos['nom_lugar'] = $Lugar->getNomLugarVo()?->value();
        array_walk($aDatos, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update="
                    id_ubi                   = :id_ubi,
                    nom_lugar                = :nom_lugar";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_lugar = $id_lugar")) === false) {
                $sClaveError = 'PgLugarRepository.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
                return false;
            }
            
            try {
                $oDblSt->execute($aDatos);
            } catch ( PDOException $e) {
                $err_txt=$e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgLugarRepository.update.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return false;
            }
        } else {
            // INSERT
            $aDatos['id_lugar'] = $Lugar->getIdLugarVo()->value();
            $campos="(id_lugar,id_ubi,nom_lugar)";
            $valores="(:id_lugar,:id_ubi,:nom_lugar)";        
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClaveError = 'PgLugarRepository.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
                return false;
            }
            try {
                $oDblSt->execute($aDatos);
            } catch ( PDOException $e) {
                $err_txt=$e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgLugarRepository.insertar.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return false;
            }
        }
        return TRUE;
    }
	
    private function isNew(int $id_lugar): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_lugar = $id_lugar")) === false) {
			$sClaveError = 'PgLugarRepository.isNew';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return false;
        }
        if (!$oDblSt->rowCount()) {
            return TRUE;
        }
        return false;
    }
	
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param LugarId $id_lugar
     * @return array|bool
	
     */
    public function datosById(LugarId $id_lugar): array|bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $id = $id_lugar->value();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_lugar = $id")) === false) {
			$sClaveError = 'PgLugarRepository.getDatosById';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return false;
        }
		$aDatos = $oDblSt->fetch(PDO::FETCH_ASSOC);
        return $aDatos;
    }
    
    
    /**
     * Busca la clase con id_lugar en la base de datos .
	
     */
    public function findById(LugarId $id_lugar): ?Lugar
    {
        $aDatos = $this->datosById($id_lugar);
        if (empty($aDatos)) {
            return null;
        }
        return (new Lugar())->setAllAttributes($aDatos);
    }
	
    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('i_lugares_dl_id_lugar_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}