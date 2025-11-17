<?php

namespace src\profesores\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use PDOException;

use src\profesores\domain\entity\ProfesorTipo;
use src\profesores\domain\contracts\ProfesorTipoRepositoryInterface;


/**
 * Clase que adapta la tabla xe_tipo_profesor_stgr a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 17/11/2025
 */
class PgProfesorTipoRepository extends ClaseRepository implements ProfesorTipoRepositoryInterface
{
    public function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl); 
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl_select($oDbl_Select); 
        $this->setNomTabla('xe_tipo_profesor_stgr');
    }

    public function getArrayProfesorTipos(): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_tipo_profesor,tipo_profesor FROM $nom_tabla ORDER BY tipo_profesor";
        if (($oDblSt = $oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorProfesorTipo.lista';
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
	 * devuelve una colección (array) de objetos de tipo ProfesorTipo
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo ProfesorTipo
	
	 */
	public function getProfesorTipos(array $aWhere=[], array $aOperators=[]): array|FALSE
	{
        $oDbl = $this->getoDbl_Select();
		$nom_tabla = $this->getNomTabla();
		$ProfesorTipoSet = new Set();
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
			$sClaveError = 'PgProfesorTipoRepository.listar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
			return FALSE;
		}
		if (($oDblSt->execute($aWhere)) === FALSE) {
			$sClaveError = 'PgProfesorTipoRepository.listar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
			return FALSE;
		}
		
		$filas = $oDblSt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            $ProfesorTipo = new ProfesorTipo();
            $ProfesorTipo->setAllAttributes($aDatos);
			$ProfesorTipoSet->add($ProfesorTipo);
		}
		return $ProfesorTipoSet->getTot();
	}

/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(ProfesorTipo $ProfesorTipo): bool
    {
        $id_tipo_profesor = $ProfesorTipo->getIdTipoProfesorVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_tipo_profesor = $id_tipo_profesor")) === FALSE) {
            $sClaveError = 'PgProfesorTipoRepository.eliminar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        return TRUE;
    }

	
	/**
	 * Si no existe el registro, hace un insert, si existe, se hace el update.
	
	 */
	public function Guardar(ProfesorTipo $ProfesorTipo): bool
    {
        $id_tipo_profesor = $ProfesorTipo->getIdTipoProfesorVo()->value();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_tipo_profesor);

		$aDatos = [];
		$aDatos['tipo_profesor'] = $ProfesorTipo->getTipoProfesorVo()?->value();
		array_walk($aDatos, 'core\poner_null');

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					tipo_profesor            = :tipo_profesor";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_tipo_profesor = $id_tipo_profesor")) === FALSE) {
				$sClaveError = 'PgProfesorTipoRepository.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
				return FALSE;
			}
				
            try {
                $oDblSt->execute($aDatos);
            } catch ( PDOException $e) {
                $err_txt=$e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgProfesorTipoRepository.update.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }
		} else {
			// INSERT
			$aDatos['id_tipo_profesor'] = $ProfesorTipo->getIdTipoProfesorVo()->value();
			$campos="(id_tipo_profesor,tipo_profesor)";
			$valores="(:id_tipo_profesor,:tipo_profesor)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClaveError = 'PgProfesorTipoRepository.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
				return FALSE;
			}
            try {
                $oDblSt->execute($aDatos);
            } catch ( PDOException $e) {
                $err_txt=$e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgProfesorTipoRepository.insertar.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return FALSE;
			}
		}
		return TRUE;
	}
	
    private function isNew(int $id_tipo_profesor): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_tipo_profesor = $id_tipo_profesor")) === FALSE) {
			$sClaveError = 'PgProfesorTipoRepository.isNew';
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
     * @param int $id_tipo_profesor
     * @return array|bool
	
     */
    public function datosById(int $id_tipo_profesor): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_tipo_profesor = $id_tipo_profesor")) === FALSE) {
			$sClaveError = 'PgProfesorTipoRepository.getDatosById';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
		$aDatos = $oDblSt->fetch(PDO::FETCH_ASSOC);
        return $aDatos;
    }
    
	
    /**
     * Busca la clase con id_tipo_profesor en la base de datos .
	
     */
    public function findById(int $id_tipo_profesor): ?ProfesorTipo
    {
        $aDatos = $this->datosById($id_tipo_profesor);
        if (empty($aDatos)) {
            return null;
        }
        return (new ProfesorTipo())->setAllAttributes($aDatos);
    }
	
    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('xe_tipo_profe_id_tipo_profe_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}