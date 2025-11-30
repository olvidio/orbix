<?php

namespace src\profesores\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use PDOException;

use src\profesores\domain\entity\ProfesorPublicacion;
use src\profesores\domain\contracts\ProfesorPublicacionRepositoryInterface;


use web\DateTimeLocal;
use web\NullDateTimeLocal;
use core\ConverterDate;
use function core\is_true;
/**
 * Clase que adapta la tabla d_publicaciones a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 29/11/2025
 */
class PgProfesorPublicacionRepository extends ClaseRepository implements ProfesorPublicacionRepositoryInterface
{
    public function __construct()
    {
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl); 
        $this->setNomTabla('d_publicaciones');
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo ProfesorPublicacion
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|false Una colección de objetos de tipo ProfesorPublicacion
	
	 */
	public function getProfesorPublicaciones(array $aWhere=[], array $aOperators=[]): array|false
	{
        $oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$PublicacionSet = new Set();
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
			$sClaveError = 'PgProfesorPublicacionRepository.listar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
			return false;
		}
		if (($oDblSt->execute($aWhere)) === false) {
			$sClaveError = 'PgProfesorPublicacionRepository.listar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
			return false;
		}
		
		$filas = $oDblSt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
			// para las fechas del postgres (texto iso)
			$aDatos['f_publicacion'] = (new ConverterDate('date', $aDatos['f_publicacion']))->fromPg();
            $Publicacion = new ProfesorPublicacion();
            $Publicacion->setAllAttributes($aDatos);
			$PublicacionSet->add($Publicacion);
		}
		return $PublicacionSet->getTot();
	}

/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(ProfesorPublicacion $Publicacion): bool
    {
        $id_item = $Publicacion->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item = $id_item")) === false) {
            $sClaveError = 'PgProfesorPublicacionRepository.eliminar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return false;
        }
        return TRUE;
    }

	
	/**
	 * Si no existe el registro, hace un insert, si existe, se hace el update.
	
	 */
	public function Guardar(ProfesorPublicacion $Publicacion): bool
    {
        $id_item = $Publicacion->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item);

		$aDatos = [];
		$aDatos['id_nom'] = $Publicacion->getId_nom();
		$aDatos['tipo_publicacion'] = $Publicacion->getTipo_publicacion();
		$aDatos['titulo'] = $Publicacion->getTitulo();
		$aDatos['editorial'] = $Publicacion->getEditorial();
		$aDatos['coleccion'] = $Publicacion->getColeccion();
		$aDatos['pendiente'] = $Publicacion->isPendiente();
		$aDatos['referencia'] = $Publicacion->getReferencia();
		$aDatos['lugar'] = $Publicacion->getLugar();
		$aDatos['observ'] = $Publicacion->getObserv();
		// para las fechas
		$aDatos['f_publicacion'] = (new ConverterDate('date', $Publicacion->getF_publicacion()))->toPg();
		array_walk($aDatos, 'core\poner_null');
		//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if ( is_true($aDatos['pendiente']) ) { $aDatos['pendiente']='true'; } else { $aDatos['pendiente']='false'; }

		if ($bInsert === false) {
			//UPDATE
			$update="
					id_nom                   = :id_nom,
					tipo_publicacion         = :tipo_publicacion,
					titulo                   = :titulo,
					editorial                = :editorial,
					coleccion                = :coleccion,
					f_publicacion            = :f_publicacion,
					pendiente                = :pendiente,
					referencia               = :referencia,
					lugar                    = :lugar,
					observ                   = :observ";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item = $id_item")) === false) {
				$sClaveError = 'PgProfesorPublicacionRepository.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
				return false;
			}
				
            try {
                $oDblSt->execute($aDatos);
            } catch ( PDOException $e) {
                $err_txt=$e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgProfesorPublicacionRepository.update.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return false;
            }
		} else {
			// INSERT
			$aDatos['id_item'] = $Publicacion->getId_item();
			$campos="(id_item,id_nom,tipo_publicacion,titulo,editorial,coleccion,f_publicacion,pendiente,referencia,lugar,observ)";
			$valores="(:id_item,:id_nom,:tipo_publicacion,:titulo,:editorial,:coleccion,:f_publicacion,:pendiente,:referencia,:lugar,:observ)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClaveError = 'PgProfesorPublicacionRepository.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
				return false;
			}
            try {
                $oDblSt->execute($aDatos);
            } catch ( PDOException $e) {
                $err_txt=$e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgProfesorPublicacionRepository.insertar.execute';
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
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item = $id_item")) === false) {
			$sClaveError = 'PgProfesorPublicacionRepository.isNew';
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
     * @param int $id_item
     * @return array|bool
	
     */
    public function datosById(int $id_item): array|bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item = $id_item")) === false) {
			$sClaveError = 'PgProfesorPublicacionRepository.getDatosById';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            return false;
        }
		$aDatos = $oDblSt->fetch(PDO::FETCH_ASSOC);
		// para las fechas del postgres (texto iso)
		if ($aDatos !== false) {
			$aDatos['f_publicacion'] = (new ConverterDate('date', $aDatos['f_publicacion']))->fromPg();
		}
        return $aDatos;
    }
    
	
    /**
     * Busca la clase con id_item en la base de datos .
	
     */
    public function findById(int $id_item): ?ProfesorPublicacion
    {
        $aDatos = $this->datosById($id_item);
        if (empty($aDatos)) {
            return null;
        }
        return (new ProfesorPublicacion())->setAllAttributes($aDatos);
    }
	
    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('d_publicaciones_id_item_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}