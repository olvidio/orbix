<?php

namespace src\cambios\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use PDOException;

use src\cambios\domain\entity\CambioUsuarioObjetoPref;
use src\cambios\domain\contracts\CambioUsuarioObjetoPrefRepositoryInterface;
use src\shared\traits\HandlesPdoErrors;


use function core\is_true;
/**
 * Clase que adapta la tabla av_cambios_usuario_objeto_pref a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/12/2025
 */
class PgCambioUsuarioObjetoPrefRepository extends ClaseRepository implements CambioUsuarioObjetoPrefRepositoryInterface
{
    use HandlesPdoErrors;
    public function __construct()
    {
        $oDbl = $GLOBALS['oDBE'];
        $this->setoDbl($oDbl); 
        $oDbl_Select = $GLOBALS['oDBE_Select'];
        $this->setoDbl_select($oDbl_Select); 
        $this->setNomTabla('av_cambios_usuario_objeto_pref');
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo CambioUsuarioObjetoPref
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|false Una colección de objetos de tipo CambioUsuarioObjetoPref
	
	 */
	public function getCambioUsuarioObjetoPrefs(array $aWhere=[], array $aOperators=[]): array|false
	{
        $oDbl = $this->getoDbl_Select();
		$nom_tabla = $this->getNomTabla();
		$CambioUsuarioObjetoPrefSet = new Set();
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
            $CambioUsuarioObjetoPref = new CambioUsuarioObjetoPref();
            $CambioUsuarioObjetoPref->setAllAttributes($aDatos);
			$CambioUsuarioObjetoPrefSet->add($CambioUsuarioObjetoPref);
		}
		return $CambioUsuarioObjetoPrefSet->getTot();
	}

/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(CambioUsuarioObjetoPref $CambioUsuarioObjetoPref): bool
    {
        $id_item_usuario_objeto = $CambioUsuarioObjetoPref->getId_item_usuario_objeto();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item_usuario_objeto = $id_item_usuario_objeto";
        return $this->pdoExec( $oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

	
	/**
	 * Si no existe el registro, hace un insert, si existe, se hace el update.
	
	 */
	public function Guardar(CambioUsuarioObjetoPref $CambioUsuarioObjetoPref): bool
    {
        $id_item_usuario_objeto = $CambioUsuarioObjetoPref->getId_item_usuario_objeto();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item_usuario_objeto);

		$aDatos = [];
		$aDatos['id_usuario'] = $CambioUsuarioObjetoPref->getId_usuario();
		$aDatos['dl_org'] = $CambioUsuarioObjetoPref->getDl_org();
		$aDatos['id_tipo_activ_txt'] = $CambioUsuarioObjetoPref->getId_tipo_activ_txt();
		$aDatos['id_fase_ref'] = $CambioUsuarioObjetoPref->getId_fase_ref();
		$aDatos['aviso_off'] = $CambioUsuarioObjetoPref->isAviso_off();
		$aDatos['aviso_on'] = $CambioUsuarioObjetoPref->isAviso_on();
		$aDatos['aviso_outdate'] = $CambioUsuarioObjetoPref->isAviso_outdate();
		$aDatos['objeto'] = $CambioUsuarioObjetoPref->getObjeto();
		$aDatos['aviso_tipo'] = $CambioUsuarioObjetoPref->getAviso_tipo();
		$aDatos['id_pau'] = $CambioUsuarioObjetoPref->getId_pau();
		array_walk($aDatos, 'core\poner_null');
		//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if ( is_true($aDatos['aviso_off']) ) { $aDatos['aviso_off']='true'; } else { $aDatos['aviso_off']='false'; }
		if ( is_true($aDatos['aviso_on']) ) { $aDatos['aviso_on']='true'; } else { $aDatos['aviso_on']='false'; }
		if ( is_true($aDatos['aviso_outdate']) ) { $aDatos['aviso_outdate']='true'; } else { $aDatos['aviso_outdate']='false'; }

		if ($bInsert === false) {
			//UPDATE
			$update="
					id_usuario               = :id_usuario,
					dl_org                   = :dl_org,
					id_tipo_activ_txt        = :id_tipo_activ_txt,
					id_fase_ref              = :id_fase_ref,
					aviso_off                = :aviso_off,
					aviso_on                 = :aviso_on,
					aviso_outdate            = :aviso_outdate,
					objeto                   = :objeto,
					aviso_tipo               = :aviso_tipo,
					id_pau                   = :id_pau";
			$sql = "UPDATE $nom_tabla SET $update WHERE id_item_usuario_objeto = $id_item_usuario_objeto";
            $stmt = $this->pdoPrepare( $oDbl, $sql, __METHOD__, __FILE__, __LINE__);
		} else {
			// INSERT
			$aDatos['id_item_usuario_objeto'] = $CambioUsuarioObjetoPref->getId_item_usuario_objeto();
			$campos="(id_item_usuario_objeto,id_usuario,dl_org,id_tipo_activ_txt,id_fase_ref,aviso_off,aviso_on,aviso_outdate,objeto,aviso_tipo,id_pau)";
			$valores="(:id_item_usuario_objeto,:id_usuario,:dl_org,:id_tipo_activ_txt,:id_fase_ref,:aviso_off,:aviso_on,:aviso_outdate,:objeto,:aviso_tipo,:id_pau)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare( $oDbl, $sql, __METHOD__, __FILE__, __LINE__);
		}
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
	}
	
    private function isNew(int $id_item_usuario_objeto): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item_usuario_objeto = $id_item_usuario_objeto";
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
     * @param int $id_item_usuario_objeto
     * @return array|bool
	
     */
    public function datosById(int $id_item_usuario_objeto): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item_usuario_objeto = $id_item_usuario_objeto";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        
		$aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        return $aDatos;
    }
    
	
    /**
     * Busca la clase con id_item_usuario_objeto en la base de datos .
	
     */
    public function findById(int $id_item_usuario_objeto): ?CambioUsuarioObjetoPref
    {
        $aDatos = $this->datosById($id_item_usuario_objeto);
        if (empty($aDatos)) {
            return null;
        }
        return (new CambioUsuarioObjetoPref())->setAllAttributes($aDatos);
    }
	
    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('av_cambios_usuario_objeto_pref_id_item_usuario_objeto_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}