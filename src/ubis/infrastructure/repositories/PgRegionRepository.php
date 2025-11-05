<?php

namespace src\ubis\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use PDOException;

use src\ubis\domain\entity\Region;
use src\ubis\domain\contracts\RegionRepositoryInterface;


use function core\is_true;
/**
 * Clase que adapta la tabla xu_region a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 5/11/2025
 */
class PgRegionRepository extends ClaseRepository implements RegionRepositoryInterface
{
    public function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl); 
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl_select($oDbl_Select); 
        $this->setNomTabla('xu_region');
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Region
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo Region
	
	 */
	public function getRegiones(array $aWhere=[], array $aOperators=[]): array|FALSE
	{
        $oDbl = $this->getoDbl_Select();
		$nom_tabla = $this->getNomTabla();
		$RegionSet = new Set();
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
		if (($oDblSt = $oDbl->prepare($sQry)) === FALSE) {
			$sClaveError = 'PgRegionRepository.listar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
			return FALSE;
		}
		if (($oDblSt->execute($aWhere)) === FALSE) {
			$sClaveError = 'PgRegionRepository.listar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
			return FALSE;
		}
		
		$filas = $oDblSt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            $Region = new Region();
            $Region->setAllAttributes($aDatos);
			$RegionSet->add($Region);
		}
		return $RegionSet->getTot();
	}

/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Region $Region): bool
    {
        $region = $Region->getRegion();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE region = '$region'")) === FALSE) {
            $sClaveError = 'PgRegionRepository.eliminar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        return TRUE;
    }

	
	/**
	 * Si no existe el registro, hace un insert, si existe, se hace el update.
	
	 */
	public function Guardar(Region $Region): bool
    {
        $id_region = $Region->getId_region();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_region);

		$aDatos = [];
        $aDatos['region'] = $Region->getRegion();
		$aDatos['nombre_region'] = $Region->getNombre_region();
		$aDatos['status'] = $Region->isStatus();
		array_walk($aDatos, 'core\poner_null');
		//para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if ( is_true($aDatos['status']) ) { $aDatos['status']='true'; } else { $aDatos['status']='false'; }

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					region                   = :region,
					nombre_region            = :nombre_region,
					status                   = :status";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_region = $id_region ")) === FALSE) {
				$sClaveError = 'PgRegionRepository.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
				return FALSE;
			}
				
            try {
                $oDblSt->execute($aDatos);
            } catch ( PDOException $e) {
                $err_txt=$e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgRegionRepository.update.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }
		} else {
			// INSERT
			$aDatos['id_region'] = $Region->getId_region();
			$campos="(id_region,region,nombre_region,status)";
			$valores="(:id_region,:region,:nombre_region,:status)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClaveError = 'PgRegionRepository.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
				return FALSE;
			}
            try {
                $oDblSt->execute($aDatos);
            } catch ( PDOException $e) {
                $err_txt=$e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgRegionRepository.insertar.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return FALSE;
			}
		}
		return TRUE;
	}
	
    private function isNew(int $id_region): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_region = $id_region ")) === FALSE) {
			$sClaveError = 'PgRegionRepository.isNew';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        if (!$oDblSt->rowCount()) {
            return TRUE;
        }
        return FALSE;
    }
	
    public function datosById(int $id_region): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_region = $id_region ")) === FALSE) {
			$sClaveError = 'PgRegionRepository.getDatosById';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
		$aDatos = $oDblSt->fetch(PDO::FETCH_ASSOC);
        return $aDatos;
    }
    
	
    public function findById(int $id_region): ?Region
    {
        $aDatos = $this->datosById($id_region);
        if (empty($aDatos)) {
            return null;
        }
        return (new Region())->setAllAttributes($aDatos);
    }
	
    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('xu_region_id_region_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}