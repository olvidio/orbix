<?php

namespace src\profesores\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use PDOException;

use src\profesores\domain\entity\ProfesorDocenciaStgr;
use src\profesores\domain\contracts\ProfesorDocenciaStgrRepositoryInterface;


/**
 * Clase que adapta la tabla d_docencia_stgr a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/11/2025
 */
class PgProfesorDocenciaStgrRepository extends ClaseRepository implements ProfesorDocenciaStgrRepositoryInterface
{
    public function __construct()
    {
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl); 
        $this->setNomTabla('d_docencia_stgr');
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo ProfesorDocenciaStgr
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|false Una colección de objetos de tipo ProfesorDocenciaStgr
	
	 */
	public function getProfesorDocenciasStgr(array $aWhere=[], array $aOperators=[]): array|false
	{
        $oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$ProfesorDocenciaStgrSet = new Set();
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
			$sClaveError = 'PgProfesorDocenciaStgrRepository.listar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
			return false;
		}
		if (($oDblSt->execute($aWhere)) === false) {
			$sClaveError = 'PgProfesorDocenciaStgrRepository.listar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
			return false;
		}
		
		$filas =$stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            $ProfesorDocenciaStgr = new ProfesorDocenciaStgr();
            $ProfesorDocenciaStgr->setAllAttributes($aDatos);
			$ProfesorDocenciaStgrSet->add($ProfesorDocenciaStgr);
		}
		return $ProfesorDocenciaStgrSet->getTot();
	}

/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(ProfesorDocenciaStgr $ProfesorDocenciaStgr): bool
    {
        $id_item = $ProfesorDocenciaStgr->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item = $id_item";
 return $this->pdoExec( $oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

	
	/**
	 * Si no existe el registro, hace un insert, si existe, se hace el update.
	
	 */
	public function Guardar(ProfesorDocenciaStgr $ProfesorDocenciaStgr): bool
    {
        $id_item = $ProfesorDocenciaStgr->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item);

		$aDatos = [];
		$aDatos['id_nom'] = $ProfesorDocenciaStgr->getId_nom();
		$aDatos['id_asignatura'] = $ProfesorDocenciaStgr->getId_asignatura();
		$aDatos['id_activ'] = $ProfesorDocenciaStgr->getId_activ();
		$aDatos['tipo'] = $ProfesorDocenciaStgr->getTipo();
		$aDatos['curso_inicio'] = $ProfesorDocenciaStgr->getCurso_inicio();
		$aDatos['acta'] = $ProfesorDocenciaStgr->getActa();
		array_walk($aDatos, 'core\poner_null');

		if ($bInsert === false) {
			//UPDATE
			$update="
					id_nom                   = :id_nom,
					id_asignatura            = :id_asignatura,
					id_activ                 = :id_activ,
					tipo                     = :tipo,
					curso_inicio             = :curso_inicio,
					acta                     = :acta";
			$sql = "UPDATE $nom_tabla SET $update WHERE id_item = $id_item";
			$stmt = $this->pdoPrepare( $oDbl, $sql, __METHOD__, __FILE__, __LINE__);
				
            try {
                $oDblSt->execute($aDatos);
            } catch ( PDOException $e) {
                $err_txt=$e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgProfesorDocenciaStgrRepository.update.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return false;
            }
		} else {
			// INSERT
			$aDatos['id_item'] = $ProfesorDocenciaStgr->getId_item();
			$campos="(id_item,id_nom,id_asignatura,id_activ,tipo,curso_inicio,acta)";
			$valores="(:id_item,:id_nom,:id_asignatura,:id_activ,:tipo,:curso_inicio,:acta)";		
			$sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
			$stmt = $this->pdoPrepare( $oDbl, $sql, __METHOD__, __FILE__, __LINE__);
            try {
                $oDblSt->execute($aDatos);
            } catch ( PDOException $e) {
                $err_txt=$e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgProfesorDocenciaStgrRepository.insertar.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return false;
			}
		}
		return TRUE;
	}
	
    private function isNew(int $id_item): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item = $id_item";
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
     * @param int $id_item
     * @return array|bool
	
     */
    public function datosById(int $id_item): array|bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item = $id_item";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
		return $stmt->fetch(PDO::FETCH_ASSOC);

    }
    
	
    /**
     * Busca la clase con id_item en la base de datos .
	
     */
    public function findById(int $id_item): ?ProfesorDocenciaStgr
    {
        $aDatos = $this->datosById($id_item);
        if (empty($aDatos)) {
            return null;
        }
        return (new ProfesorDocenciaStgr())->setAllAttributes($aDatos);
    }
	
    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('d_docencia_stgr_id_item_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}