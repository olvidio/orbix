<?php

namespace src\inventario\infrastructure;

use core\ClaseRepository;
use core\Condicion;
use core\ConverterDate;
use core\Set;
use src\inventario\domain\repositories\DocumentoRepositoryInterface;
use PDO;
use PDOException;
use src\inventario\domain\entity\Documento;
use function core\is_true;


/**
 * Clase que adapta la tabla i_documentos_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/3/2025
 */
class PgDocumentoRepository extends ClaseRepository implements DocumentoRepositoryInterface
{
    public function __construct()
    {
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('i_documentos_dl');
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Documento
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|FALSE Una colección de objetos de tipo Documento
	
	 */
	public function getDocumentos(array $aWhere=[], array $aOperators=[]): array|FALSE
	{
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$DocumentoSet = new Set();
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
			$sClaveError = 'PgDocumentoRepository.listar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
			return FALSE;
		}
		if (($oDblSt->execute($aWhere)) === FALSE) {
			$sClaveError = 'PgDocumentoRepository.listar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
			return FALSE;
		}
		
		$filas = $oDblSt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
			// para las fechas del postgres (texto iso)
			$aDatos['f_recibido'] = (new ConverterDate('date', $aDatos['f_recibido']))->fromPg();
			$aDatos['f_asignado'] = (new ConverterDate('date', $aDatos['f_asignado']))->fromPg();
			$aDatos['f_ult_comprobacion'] = (new ConverterDate('date', $aDatos['f_ult_comprobacion']))->fromPg();
			$aDatos['f_perdido'] = (new ConverterDate('date', $aDatos['f_perdido']))->fromPg();
			$aDatos['f_eliminado'] = (new ConverterDate('date', $aDatos['f_eliminado']))->fromPg();
            $Documento = new Documento();
            $Documento->setAllAttributes($aDatos);
			$DocumentoSet->add($Documento);
		}
		return $DocumentoSet->getTot();
	}

/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Documento $Documento): bool
    {
        $id_doc = $Documento->getId_doc();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_doc = $id_doc")) === FALSE) {
            $sClaveError = 'PgDocumentoRepository.eliminar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
        return TRUE;
    }

	
	/**
	 * Si no existe el registro, hace un insert, si existe, se hace el update.
	
	 */
	public function Guardar(Documento $Documento): bool
    {
        $id_doc = $Documento->getId_doc();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_doc);

		$aDatos = [];
		$aDatos['id_tipo_doc'] = $Documento->getId_tipo_doc();
		$aDatos['id_ubi'] = $Documento->getId_ubi();
		$aDatos['id_lugar'] = $Documento->getId_lugar();
		$aDatos['observ'] = $Documento->getObserv();
        $aDatos['observ_ctr'] = $Documento->getObservCtr();
		$aDatos['en_busqueda'] = $Documento->isEn_busqueda();
		$aDatos['perdido'] = $Documento->isPerdido();
		$aDatos['eliminado'] = $Documento->isEliminado();
		$aDatos['num_reg'] = $Documento->getNum_reg();
		$aDatos['num_ini'] = $Documento->getNum_ini();
		$aDatos['num_fin'] = $Documento->getNum_fin();
		$aDatos['identificador'] = $Documento->getIdentificador();
		$aDatos['num_ejemplares'] = $Documento->getNum_ejemplares();
		// para las fechas
		$aDatos['f_recibido'] = (new ConverterDate('date', $Documento->getF_recibido()))->toPg();
		$aDatos['f_asignado'] = (new ConverterDate('date', $Documento->getF_asignado()))->toPg();
		$aDatos['f_ult_comprobacion'] = (new ConverterDate('date', $Documento->getF_ult_comprobacion()))->toPg();
		$aDatos['f_perdido'] = (new ConverterDate('date', $Documento->getF_perdido()))->toPg();
		$aDatos['f_eliminado'] = (new ConverterDate('date', $Documento->getF_eliminado()))->toPg();
		array_walk($aDatos, 'core\poner_null');
		//para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if ( is_true($aDatos['en_busqueda']) ) { $aDatos['en_busqueda']='true'; } else { $aDatos['en_busqueda']='false'; }
		if ( is_true($aDatos['perdido']) ) { $aDatos['perdido']='true'; } else { $aDatos['perdido']='false'; }
		if ( is_true($aDatos['eliminado']) ) { $aDatos['eliminado']='true'; } else { $aDatos['eliminado']='false'; }

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					id_tipo_doc              = :id_tipo_doc,
					id_ubi                   = :id_ubi,
					id_lugar                 = :id_lugar,
					f_recibido               = :f_recibido,
					f_asignado               = :f_asignado,
					observ                   = :observ,
					observ_ctr               = :observ_ctr,
					f_ult_comprobacion       = :f_ult_comprobacion,
					en_busqueda              = :en_busqueda,
					perdido                  = :perdido,
					f_perdido                = :f_perdido,
					eliminado                = :eliminado,
					f_eliminado              = :f_eliminado,
					num_reg                  = :num_reg,
					num_ini                  = :num_ini,
					num_fin                  = :num_fin,
					identificador            = :identificador,
					num_ejemplares           = :num_ejemplares";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_doc = $id_doc")) === FALSE) {
				$sClaveError = 'PgDocumentoRepository.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
				return FALSE;
			}
				
            try {
                $oDblSt->execute($aDatos);
            } catch ( PDOException $e) {
                $err_txt=$e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgDocumentoRepository.update.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return FALSE;
            }
		} else {
			// INSERT
			$aDatos['id_doc'] = $Documento->getId_doc();
			$campos="(id_doc,id_tipo_doc,id_ubi,id_lugar,f_recibido,f_asignado,observ,observ_ctr,f_ult_comprobacion,en_busqueda,perdido,f_perdido,eliminado,f_eliminado,num_reg,num_ini,num_fin,identificador,num_ejemplares)";
			$valores="(:id_doc,:id_tipo_doc,:id_ubi,:id_lugar,:f_recibido,:f_asignado,:observ,:observ_ctr,:f_ult_comprobacion,:en_busqueda,:perdido,:f_perdido,:eliminado,:f_eliminado,:num_reg,:num_ini,:num_fin,:identificador,:num_ejemplares)";
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClaveError = 'PgDocumentoRepository.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
				return FALSE;
			}
            try {
                $oDblSt->execute($aDatos);
            } catch ( PDOException $e) {
                $err_txt=$e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgDocumentoRepository.insertar.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return FALSE;
			}
		}
		return TRUE;
	}
	
    private function isNew(int $id_doc): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_doc = $id_doc")) === FALSE) {
			$sClaveError = 'PgDocumentoRepository.isNew';
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
     * @param int $id_doc
     * @return array|bool
	
     */
    public function datosById(int $id_doc): array|bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_doc = $id_doc")) === FALSE) {
			$sClaveError = 'PgDocumentoRepository.getDatosById';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return FALSE;
        }
		$aDatos = $oDblSt->fetch(PDO::FETCH_ASSOC);
		// para las fechas del postgres (texto iso)
		if ($aDatos !== FALSE) {
			$aDatos['f_recibido'] = (new ConverterDate('date', $aDatos['f_recibido']))->fromPg();
			$aDatos['f_asignado'] = (new ConverterDate('date', $aDatos['f_asignado']))->fromPg();
			$aDatos['f_ult_comprobacion'] = (new ConverterDate('date', $aDatos['f_ult_comprobacion']))->fromPg();
			$aDatos['f_perdido'] = (new ConverterDate('date', $aDatos['f_perdido']))->fromPg();
			$aDatos['f_eliminado'] = (new ConverterDate('date', $aDatos['f_eliminado']))->fromPg();
		}
        return $aDatos;
    }
    
	
    /**
     * Busca la clase con id_doc en la base de datos .
	
     */
    public function findById(int $id_doc): ?Documento
    {
        $aDatos = $this->datosById($id_doc);
        if (empty($aDatos)) {
            return null;
        }
        return (new Documento())->setAllAttributes($aDatos);
    }
	
    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('i_documentos_dl_id_doc_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}