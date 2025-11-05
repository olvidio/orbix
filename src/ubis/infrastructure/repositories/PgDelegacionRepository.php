<?php

namespace src\ubis\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use PDOException;

use src\ubis\domain\entity\Delegacion;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;


use function core\is_true;
/**
 * Clase que adapta la tabla xu_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 5/11/2025
 */
class PgDelegacionRepository extends ClaseRepository implements DelegacionRepositoryInterface
{
    public function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl); 
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl_select($oDbl_Select); 
        $this->setNomTabla('xu_dl');
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Delegacion
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo Delegacion
	
	 */
	public function getDelegaciones(array $aWhere=[], array $aOperators=[]): array|FALSE
	{
        $oDbl = $this->getoDbl_Select();
		$nom_tabla = $this->getNomTabla();
		$DelegacionSet = new Set();
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
			$sClaveError = 'PgDelegacionRepository.listar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
			return FALSE;
		}
		if (($oDblSt->execute($aWhere)) === FALSE) {
			$sClaveError = 'PgDelegacionRepository.listar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
			return FALSE;
		}
		
		$filas = $oDblSt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            $Delegacion = new Delegacion();
            $Delegacion->setAllAttributes($aDatos);
			$DelegacionSet->add($Delegacion);
		}
		return $DelegacionSet->getTot();
	}

/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Delegacion $Delegacion): bool
    {
        $dl = $Delegacion->getDl();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE dl = '$dl'")) === FALSE) {
            $sClaveError = 'PgDelegacionRepository.eliminar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        return TRUE;
    }

	
	/**
	 * Si no existe el registro, hace un insert, si existe, se hace el update.
	
	 */
	public function Guardar(Delegacion $Delegacion): bool
    {
        $id_dl = $Delegacion->getId_dl();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_dl);

		$aDatos = [];
		$aDatos['dl'] = $Delegacion->getDl();
		$aDatos['region'] = $Delegacion->getRegion();
		$aDatos['nombre_dl'] = $Delegacion->getNombre_dl();
		$aDatos['status'] = $Delegacion->isStatus();
		$aDatos['grupo_estudios'] = $Delegacion->getGrupo_estudios();
		$aDatos['region_stgr'] = $Delegacion->getRegion_stgr();
		array_walk($aDatos, 'core\poner_null');
		//para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if ( is_true($aDatos['status']) ) { $aDatos['status']='true'; } else { $aDatos['status']='false'; }

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					dl                       = :dl,
					region                   = :region,
					nombre_dl                = :nombre_dl,
					status                   = :status,
					grupo_estudios           = :grupo_estudios,
					region_stgr              = :region_stgr";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_dl = $id_dl ")) === FALSE) {
				$sClaveError = 'PgDelegacionRepository.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
				return FALSE;
			}
				
            try {
                $oDblSt->execute($aDatos);
            } catch ( PDOException $e) {
                $err_txt=$e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgDelegacionRepository.update.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }
		} else {
			// INSERT
			$aDatos['id_dl'] = $Delegacion->getId_dl();
			$campos="(id_dl,dl,region,nombre_dl,status,grupo_estudios,region_stgr)";
			$valores="(:id_dl,:dl,:region,:nombre_dl,:status,:grupo_estudios,:region_stgr)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClaveError = 'PgDelegacionRepository.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
				return FALSE;
			}
            try {
                $oDblSt->execute($aDatos);
            } catch ( PDOException $e) {
                $err_txt=$e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgDelegacionRepository.insertar.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return FALSE;
			}
		}
		return TRUE;
	}
	
    private function isNew(int $id_dl): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_dl = $id_dl ")) === FALSE) {
			$sClaveError = 'PgDelegacionRepository.isNew';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        if (!$oDblSt->rowCount()) {
            return TRUE;
        }
        return FALSE;
    }
	
    public function datosById(int $id_dl): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_dl = $id_dl ")) === FALSE) {
			$sClaveError = 'PgDelegacionRepository.getDatosById';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
		$aDatos = $oDblSt->fetch(PDO::FETCH_ASSOC);
        return $aDatos;
    }
    
	
    /**
     * Busca la clase con dl en la base de datos .
	
     */
    public function findById(int $id_dl): ?Delegacion
    {
        $aDatos = $this->datosById($id_dl);
        if (empty($aDatos)) {
            return null;
        }
        return (new Delegacion())->setAllAttributes($aDatos);
    }
}