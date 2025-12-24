<?php

namespace src\cambios\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use PDOException;

use src\cambios\domain\entity\CambioUsuarioPropiedadPref;
use src\cambios\domain\contracts\CambioUsuarioPropiedadPrefRepositoryInterface;
use src\shared\traits\HandlesPdoErrors;


use function core\is_true;
/**
 * Clase que adapta la tabla av_cambios_usuario_propiedades_pref a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/12/2025
 */
class PgCambioUsuarioPropiedadPrefRepository extends ClaseRepository implements CambioUsuarioPropiedadPrefRepositoryInterface
{
    use HandlesPdoErrors;
    public function __construct()
    {
        $oDbl = $GLOBALS['oDBE'];
        $this->setoDbl($oDbl); 
        $oDbl_Select = $GLOBALS['oDBE_Select'];
        $this->setoDbl_select($oDbl_Select); 
        $this->setNomTabla('av_cambios_usuario_propiedades_pref');
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo CambioUsuarioPropiedadPref
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|false Una colección de objetos de tipo CambioUsuarioPropiedadPref
	
	 */
	public function getCambioUsuarioPropiedadPrefs(array $aWhere=[], array $aOperators=[]): array|false
	{
        $oDbl = $this->getoDbl_Select();
		$nom_tabla = $this->getNomTabla();
		$CambioUsuarioPropiedadPrefSet = new Set();
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
		$stmt = $this->prepareAndExecute( $oDbl, $sQry, $aWhere,__METHOD__, __FILE__, __LINE__);
		
		$filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            $CambioUsuarioPropiedadPref = new CambioUsuarioPropiedadPref();
            $CambioUsuarioPropiedadPref->setAllAttributes($aDatos);
			$CambioUsuarioPropiedadPrefSet->add($CambioUsuarioPropiedadPref);
		}
		return $CambioUsuarioPropiedadPrefSet->getTot();
	}

/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(CambioUsuarioPropiedadPref $CambioUsuarioPropiedadPref): bool
    {
        $id_item = $CambioUsuarioPropiedadPref->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item = $id_item";
        return $this->pdoExec( $oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

	
	/**
	 * Si no existe el registro, hace un insert, si existe, se hace el update.
	
	 */
	public function Guardar(CambioUsuarioPropiedadPref $CambioUsuarioPropiedadPref): bool
    {
        $id_item = $CambioUsuarioPropiedadPref->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item);

		$aDatos = [];
		$aDatos['id_item_usuario_objeto'] = $CambioUsuarioPropiedadPref->getId_item_usuario_objeto();
		$aDatos['propiedad'] = $CambioUsuarioPropiedadPref->getPropiedad();
		$aDatos['operador'] = $CambioUsuarioPropiedadPref->getOperador();
		$aDatos['valor'] = $CambioUsuarioPropiedadPref->getValor();
		$aDatos['valor_old'] = $CambioUsuarioPropiedadPref->isValor_old();
		$aDatos['valor_new'] = $CambioUsuarioPropiedadPref->isValor_new();
		array_walk($aDatos, 'core\poner_null');
		//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if ( is_true($aDatos['valor_old']) ) { $aDatos['valor_old']='true'; } else { $aDatos['valor_old']='false'; }
		if ( is_true($aDatos['valor_new']) ) { $aDatos['valor_new']='true'; } else { $aDatos['valor_new']='false'; }

		if ($bInsert === false) {
			//UPDATE
			$update="
					id_item_usuario_objeto   = :id_item_usuario_objeto,
					propiedad                = :propiedad,
					operador                 = :operador,
					valor                    = :valor,
					valor_old                = :valor_old,
					valor_new                = :valor_new";
			$sql = "UPDATE $nom_tabla SET $update WHERE id_item = $id_item";
            $stmt = $this->pdoPrepare( $oDbl, $sql, __METHOD__, __FILE__, __LINE__);
		} else {
			// INSERT
			$aDatos['id_item'] = $CambioUsuarioPropiedadPref->getId_item();
			$campos="(id_item,id_item_usuario_objeto,propiedad,operador,valor,valor_old,valor_new)";
			$valores="(:id_item,:id_item_usuario_objeto,:propiedad,:operador,:valor,:valor_old,:valor_new)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare( $oDbl, $sql, __METHOD__, __FILE__, __LINE__);
		}
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
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
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item = $id_item";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        
		$aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        return $aDatos;
    }
    
	
    /**
     * Busca la clase con id_item en la base de datos .
	
     */
    public function findById(int $id_item): ?CambioUsuarioPropiedadPref
    {
        $aDatos = $this->datosById($id_item);
        if (empty($aDatos)) {
            return null;
        }
        return (new CambioUsuarioPropiedadPref())->setAllAttributes($aDatos);
    }
	
    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('av_cambios_usuario_propiedades_pref_id_item_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}