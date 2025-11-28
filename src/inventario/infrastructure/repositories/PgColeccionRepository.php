<?php

namespace src\inventario\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use PDOException;
use src\inventario\domain\contracts\ColeccionRepositoryInterface;
use src\inventario\domain\entity\Coleccion;
use src\inventario\domain\value_objects\ColeccionId;
use function core\is_true;

/**
 * Clase que adapta la tabla i_colecciones_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
class PgColeccionRepository extends ClaseRepository implements ColeccionRepositoryInterface
{
    public function __construct()
    {
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('i_colecciones_dl');
    }

    public function getArrayColecciones(): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
		$sQuery="SELECT id_coleccion,nom_coleccion FROM $nom_tabla ORDER BY nom_coleccion";
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = 'GestorColeccion.lista';
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
	 * devuelve una colección (array) de objetos de tipo Coleccion
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|false Una colección de objetos de tipo Coleccion
	
	 */
	public function getColecciones(array $aWhere=[], array $aOperators=[]): array|false
	{
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$ColeccionSet = new Set();
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
			$sClaveError = 'PgColeccionRepository.listar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
			return false;
		}
		if (($oDblSt->execute($aWhere)) === false) {
			$sClaveError = 'PgColeccionRepository.listar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
			return false;
		}
		
		$filas = $oDblSt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            $Coleccion = new Coleccion();
            $Coleccion->setAllAttributes($aDatos);
			$ColeccionSet->add($Coleccion);
		}
		return $ColeccionSet->getTot();
	}

/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Coleccion $Coleccion): bool
    {
        $id_coleccion = $Coleccion->getIdColeccionVo()?->value() ?? $Coleccion->getId_coleccion();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_coleccion = $id_coleccion")) === false) {
            $sClaveError = 'PgColeccionRepository.eliminar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return false;
        }
        return TRUE;
    }

	
	/**
	 * Si no existe el registro, hace un insert, si existe, se hace el update.
	
	 */
	public function Guardar(Coleccion $Coleccion): bool
    {
        $id_coleccion = $Coleccion->getIdColeccionVo()?->value() ?? $Coleccion->getId_coleccion();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_coleccion);

        $aDatos = [];
        $aDatos['nom_coleccion'] = $Coleccion->getNomColeccionVo()?->value();
        $aDatos['agrupar'] = $Coleccion->getAgruparVo()?->value();
        array_walk($aDatos, 'core\poner_null');
        //para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if ( is_true($aDatos['agrupar']) ) { $aDatos['agrupar']='true'; } else { $aDatos['agrupar']='false'; }

        if ($bInsert === false) {
            //UPDATE
            $update="
                    nom_coleccion            = :nom_coleccion,
                    agrupar                  = :agrupar";
            if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_coleccion = $id_coleccion")) === false) {
                $sClaveError = 'PgColeccionRepository.update.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
                return false;
            }
            
            try {
                $oDblSt->execute($aDatos);
            } catch ( PDOException $e) {
                $err_txt=$e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgColeccionRepository.update.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return false;
            }
        } else {
            // INSERT
            $aDatos['id_coleccion'] = $Coleccion->getIdColeccionVo()?->value() ?? $Coleccion->getId_coleccion();
            $campos="(id_coleccion,nom_coleccion,agrupar)";
            $valores="(:id_coleccion,:nom_coleccion,:agrupar)";        
            if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
                $sClaveError = 'PgColeccionRepository.insertar.prepare';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
                return false;
            }
            try {
                $oDblSt->execute($aDatos);
            } catch ( PDOException $e) {
                $err_txt=$e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgColeccionRepository.insertar.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return false;
            }
        }
        return TRUE;
    }
	
    private function isNew(int $id_coleccion): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_coleccion = $id_coleccion")) === false) {
			$sClaveError = 'PgColeccionRepository.isNew';
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
     * @param ColeccionId $id_coleccion
     * @return array|bool
	
     */
    public function datosById(ColeccionId $id_coleccion): array|bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $id = $id_coleccion->value();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_coleccion = $id")) === false) {
			$sClaveError = 'PgColeccionRepository.getDatosById';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return false;
        }
		$aDatos = $oDblSt->fetch(PDO::FETCH_ASSOC);
        return $aDatos;
    }
    
    
    /**
     * Busca la clase con id_coleccion en la base de datos .
	
     */
    public function findById(ColeccionId $id_coleccion): ?Coleccion
    {
        $aDatos = $this->datosById($id_coleccion);
        if (empty($aDatos)) {
            return null;
        }
        return (new Coleccion())->setAllAttributes($aDatos);
    }
	
    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('i_colecciones_dl_id_coleccion_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}