<?php

namespace misas\infrastructure;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use PDOException;

use misas\domain\entity\Plantilla;
use misas\domain\repositories\PlantillaRepositoryInterface;
use web\Desplegable;


/**
 * Clase que adapta la tabla misa_plantillas_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 13/3/2023
 */
class PgPlantillaRepository extends ClaseRepository implements PlantillaRepositoryInterface
{
    public function __construct()
    {
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('misa_plantillas_dl');
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Plantilla
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo Plantilla
	
	 */
	public function getPlantillas(array $aWhere=[], array $aOperators=[]): array|FALSE
	{
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$PlantillaSet = new Set();
		$oCondicion = new Condicion();
		$aCondicion = array();
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
            $Plantilla = new Plantilla();
            $Plantilla->setAllAttributes($aDatos);
			$PlantillaSet->add($Plantilla);
		}
		return $PlantillaSet->getTot();
	}

/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Plantilla $Plantilla): bool
    {
        $id_item = $Plantilla->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item = $id_item")) === FALSE) {
            $sClaveError = 'PgPlantillaRepository.eliminar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        return TRUE;
    }

	
	/**
	 * Si no existe el registro, hace un insert, si existe, se hace el update.
	
	 */
	public function Guardar(Plantilla $Plantilla): bool
    {
        $id_item = $Plantilla->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item);

		$aDatos = [];
		$aDatos['id_ctr'] = $Plantilla->getId_ctr();
		$aDatos['que'] = $Plantilla->getQue();
		$aDatos['dia'] = $Plantilla->getDia();
		$aDatos['semana'] = $Plantilla->getSemana();
		$aDatos['id_nom'] = $Plantilla->getId_nom();
		$aDatos['t_start'] = $Plantilla->getT_start();
		$aDatos['t_end'] = $Plantilla->getT_end();
		$aDatos['observ'] = $Plantilla->getObserv();
		array_walk($aDatos, 'core\poner_null');

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					id_ctr                   = :id_ctr,
					que                      = :que,
					dia                      = :dia,
					semana                   = :semana,
					id_nom                   = :id_nom,
					t_start                  = :t_start,
					t_end                    = :t_end,
					observ                   = :observ";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item = $id_item")) === FALSE) {
				$sClaveError = 'PgPlantillaRepository.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
				return FALSE;
			}
				
            try {
                $oDblSt->execute($aDatos);
            } catch ( PDOException $e) {
                $err_txt=$e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgPlantillaRepository.update.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }
		} else {
			// INSERT
			$aDatos['id_item'] = $Plantilla->getId_item();
			$campos="(id_item,id_ctr,que,dia,semana,id_nom,t_start,t_end,observ)";
			$valores="(:id_item,:id_ctr,:que,:dia,:semana,:id_nom,:t_start,:t_end,:observ)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClaveError = 'PgPlantillaRepository.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
				return FALSE;
			}
            try {
                $oDblSt->execute($aDatos);
            } catch ( PDOException $e) {
                $err_txt=$e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgPlantillaRepository.insertar.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return FALSE;
			}
		}
		return TRUE;
	}
	
    private function isNew(int $id_item): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item = $id_item")) === FALSE) {
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
     * @param int $id_item
     * @return array|bool
	
     */
    public function datosById(int $id_item): array|bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item = $id_item")) === FALSE) {
			$sClaveError = 'PgPlantillaRepository.getDatosById';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
		$aDatos = $oDblSt->fetch(PDO::FETCH_ASSOC);
        return $aDatos;
    }
    
	
    /**
     * Busca la clase con id_item en la base de datos .
	
     */
    public function findById(int $id_item): ?Plantilla
    {
        $aDatos = $this->datosById($id_item);
        if (empty($aDatos)) {
            return null;
        }
        return (new Plantilla())->setAllAttributes($aDatos);
    }
	
    public function getNewId_item()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('misa_plantillas_dl_id_item_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}