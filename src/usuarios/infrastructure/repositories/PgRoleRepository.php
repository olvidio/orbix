<?php

namespace src\usuarios\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use PDOException;

use src\usuarios\domain\entity\Role;
use src\usuarios\domain\contracts\RoleRepositoryInterface;

use function core\is_true;
/**
 * Clase que adapta la tabla aux_roles a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/4/2025
 */
class PgRoleRepository extends ClaseRepository implements RoleRepositoryInterface
{
    public function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl); 
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl_select($oDbl_Select); 
        $this->setNomTabla('aux_roles');
    }

    public function getArrayRoles(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_role, role FROM $nom_tabla ORDER BY role";
        try {
            $aOpciones = [];
            foreach ($oDbl->query($sQuery) as $aClave) {
                $clave = $aClave[0];
                $val = $aClave[1];
                $aOpciones[$clave] = $val;
            }
        } catch (PDOException $e) {
            $sClauError = 'Role.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
        }
        return $aOpciones;
    }

    public function getArrayRolesPau(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_role, pau
				FROM $nom_tabla WHERE pau IS NOT NULL
				ORDER BY id_role";
        try {
            $aOpciones = [];
            foreach ($oDbl->query($sQuery) as $aClave) {
                $val = strtolower($aClave['pau']?? '');
                $clave = strtolower($aClave['id_role']);
                $aOpciones[$clave] = $val;
            }
        } catch (PDOException $e) {
            $sClauError = 'Role.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
        }
        return $aOpciones;
    }

    public function getArrayRolesCondicion(string $sWhere = ''): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_role, role
				FROM $nom_tabla $sWhere
				ORDER BY role";
        try {
            $aOpciones = [];
            foreach ($oDbl->query($sQuery) as $aClave) {
                $val = strtolower($aClave['role']?? '');
                $clave = strtolower($aClave['id_role']);
                $aOpciones[$clave] = $val;
            }
        } catch (PDOException $e) {
            $sClauError = 'Role.lista';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
        }
        return $aOpciones;
    }

/* -------------------- GESTOR BASE ---------------------------------------- */

	/**
	 * devuelve una colección (array) de objetos de tipo Role
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|false Una colección de objetos de tipo Role
	
	 */
	public function getRoles(array $aWhere=[], array $aOperators=[]): array|false
	{
        $oDbl = $this->getoDbl_Select();
		$nom_tabla = $this->getNomTabla();
		$RoleSet = new Set();
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
			$sClaveError = 'PgRoleRepository.listar.prepare';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
			return false;
		}
		if (($oDblSt->execute($aWhere)) === false) {
			$sClaveError = 'PgRoleRepository.listar.execute';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
			return false;
		}
		
		$filas =$stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            $Role = new Role();
            $Role->setAllAttributes($aDatos);
			$RoleSet->add($Role);
		}
		return $RoleSet->getTot();
	}

/* -------------------- ENTIDAD --------------------------------------------- */

	public function Eliminar(Role $Role): bool
    {
        $id_role = $Role->getId_role();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_role = $id_role";
 return $this->pdoExec( $oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

	
	/**
	 * Si no existe el registro, hace un insert, si existe, se hace el update.
	
	 */
	public function Guardar(Role $Role): bool
    {
        $id_role = $Role->getId_role();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_role);

		$aDatos = [];
		$aDatos['role'] = $Role->getRoleAsString();
		$aDatos['sf'] = $Role->isSf();
		$aDatos['sv'] = $Role->isSv();
		$aDatos['pau'] = $Role->getPauAsString();
		$aDatos['dmz'] = $Role->isDmz();
		array_walk($aDatos, 'core\poner_null');
		//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if ( is_true($aDatos['sf']) ) { $aDatos['sf']='true'; } else { $aDatos['sf']='false'; }
		if ( is_true($aDatos['sv']) ) { $aDatos['sv']='true'; } else { $aDatos['sv']='false'; }
		if ( is_true($aDatos['dmz']) ) { $aDatos['dmz']='true'; } else { $aDatos['dmz']='false'; }

		if ($bInsert === false) {
			//UPDATE
			$update="
					role                     = :role,
					sf                       = :sf,
					sv                       = :sv,
					pau                      = :pau,
					dmz                      = :dmz";
			$sql = "UPDATE $nom_tabla SET $update WHERE id_role = $id_role";
			$stmt = $this->pdoPrepare( $oDbl, $sql, __METHOD__, __FILE__, __LINE__);
				
            try {
                $oDblSt->execute($aDatos);
            } catch ( PDOException $e) {
                $err_txt=$e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgRoleRepository.update.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return false;
            }
		} else {
			// INSERT
			$aDatos['id_role'] = $Role->getId_role();
			$campos="(id_role,role,sf,sv,pau,dmz)";
			$valores="(:id_role,:role,:sf,:sv,:pau,:dmz)";		
			$sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
			$stmt = $this->pdoPrepare( $oDbl, $sql, __METHOD__, __FILE__, __LINE__);
            try {
                $oDblSt->execute($aDatos);
            } catch ( PDOException $e) {
                $err_txt=$e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgRoleRepository.insertar.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return false;
			}
		}
		return TRUE;
	}
	
    private function isNew(int $id_role): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_role = $id_role";
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
     * @param int $id_role
     * @return array|bool
	
     */
    public function datosById(int $id_role): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_role = $id_role";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
		return $stmt->fetch(PDO::FETCH_ASSOC);

    }
    
	
    /**
     * Busca la clase con id_role en la base de datos .
	
     */
    public function findById(int $id_role): ?Role
    {
        $aDatos = $this->datosById($id_role);
        if (empty($aDatos)) {
            return null;
        }
        return (new Role())->setAllAttributes($aDatos);
    }
	
    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('aux_roles_id_role_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}