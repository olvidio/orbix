<?php

namespace src\ubis\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use PDOException;
use src\shared\traits\HandlesPdoErrors;

use src\ubis\domain\entity\DescTeleco;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;

use function core\is_true;
/**
 * Clase que adapta la tabla xd_desc_teleco a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 17/11/2025
 */
class PgDescTelecoRepository extends ClaseRepository implements DescTelecoRepositoryInterface
{
    use HandlesPdoErrors;
    public function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl); 
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl_select($oDbl_Select); 
        $this->setNomTabla('xd_desc_teleco');
    }

    public function getArrayDescTelecoPersonas($sdepende): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_item, desc_teleco FROM $nom_tabla WHERE persona='t' AND id_tipo_teleco='$sdepende' ORDER BY orden";
        $sQry = "SELECT * FROM $nom_tabla " . $sCondicion . $sOrdre . $sLimit;
        $stmt = $this->pdoPrepare($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) { return []; }
        if (!$this->pdoExecute($stmt, [], __METHOD__, __FILE__, __LINE__)) { return []; }
        $aOpciones = [];
        foreach ($stmt->fetchAll(PDO::FETCH_NUM) as $aClave) {
            $aOpciones[$aClave[0]] = $aClave[1];
        }
        return $aOpciones;
    }

    public function getArrayDescTelecoUbis($sdepende): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_item, desc_teleco FROM $nom_tabla WHERE ubi='t' AND id_tipo_teleco='$sdepende' ORDER BY orden";
        $sQry = "SELECT * FROM $nom_tabla " . $sCondicion . $sOrdre . $sLimit;
        $stmt = $this->pdoPrepare($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) { return []; }
        if (!$this->pdoExecute($stmt, [], __METHOD__, __FILE__, __LINE__)) { return []; }
        $aOpciones = [];
        foreach ($stmt->fetchAll(PDO::FETCH_NUM) as $aClave) {
            $aOpciones[$aClave[0]] = $aClave[1];
        }
        return $aOpciones;
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo DescTeleco
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|false Una colección de objetos de tipo DescTeleco
	
	 */
	public function getDescsTeleco(array $aWhere=[], array $aOperators=[]): array|false
	{
        $oDbl = $this->getoDbl_Select();
		$nom_tabla = $this->getNomTabla();
		$DescTelecoSet = new Set();
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
		$sQry = "SELECT * FROM $nom_tabla " . $sCondicion . $sOrdre . $sLimit;
		$stmt = $this->pdoPrepare($oDbl, $sQry, __METHOD__, __FILE__, __LINE__);
		if ($stmt === false) { return false; }
		if (!$this->pdoExecute($stmt, $aWhere, __METHOD__, __FILE__, __LINE__)) { return false; }
		
		$filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            $DescTeleco = new DescTeleco();
            $DescTeleco->setAllAttributes($aDatos);
			$DescTelecoSet->add($DescTeleco);
		}
		return $DescTelecoSet->getTot();
	}

/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(DescTeleco $DescTeleco): bool
    {
        $id_item = $DescTeleco->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item = $id_item";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

	
	/**
	 * Si no existe el registro, hace un insert, si existe, se hace el update.
	
	 */
	public function Guardar(DescTeleco $DescTeleco): bool
    {
        $id_item = $DescTeleco->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item);

  $aDatos = [];
  // Usar la API basada en Value Objects
  $aDatos['orden'] = $DescTeleco->getOrdenVo()?->value();
  $aDatos['id_tipo_teleco'] = $DescTeleco->getIdTipoTelecoVo()?->value();
  $aDatos['desc_teleco'] = $DescTeleco->getDescTelecoVo()?->value();
  $aDatos['ubi'] = $DescTeleco->isUbi();
  $aDatos['persona'] = $DescTeleco->isPersona();
		array_walk($aDatos, 'core\poner_null');
		//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if ( is_true($aDatos['ubi']) ) { $aDatos['ubi']='true'; } else { $aDatos['ubi']='false'; }
		if ( is_true($aDatos['persona']) ) { $aDatos['persona']='true'; } else { $aDatos['persona']='false'; }

		if ($bInsert === false) {
			//UPDATE
			$update="
					orden                    = :orden,
					id_tipo_teleco           = :id_tipo_teleco,
					desc_teleco              = :desc_teleco,
					ubi                      = :ubi,
					persona                  = :persona";
			$sQry = "SELECT * FROM $nom_tabla " . $sCondicion . $sOrdre . $sLimit;
			$stmt = $this->pdoPrepare($oDbl, "UPDATE $nom_tabla SET $update WHERE id_item = $id_item", __METHOD__, __FILE__, __LINE__);
			if ($stmt === false) { return false; }
			if (!$this->pdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__)) { return false; }
		} else {
			// INSERT
			$aDatos['id_item'] = $DescTeleco->getId_item();
			$campos="(id_item,orden,id_tipo_teleco,desc_teleco,ubi,persona)";
			$valores="(:id_item,:orden,:id_tipo_teleco,:desc_teleco,:ubi,:persona)";
			$sQry = "SELECT * FROM $nom_tabla " . $sCondicion . $sOrdre . $sLimit;
			$stmt = $this->pdoPrepare($oDbl, "INSERT INTO $nom_tabla $campos VALUES $valores", __METHOD__, __FILE__, __LINE__);
			if ($stmt === false) { return false; }
			if (!$this->pdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__)) { return false; }
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
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item = $id_item";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
		return $stmt->fetch(PDO::FETCH_ASSOC);

    }
    
	
    /**
     * Busca la clase con id_item en la base de datos .
	
     */
    public function findById(int $id_item): ?DescTeleco
    {
        $aDatos = $this->datosById($id_item);
        if (empty($aDatos)) {
            return null;
        }
        return (new DescTeleco())->setAllAttributes($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('xd_desc_teleco_id_item_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}