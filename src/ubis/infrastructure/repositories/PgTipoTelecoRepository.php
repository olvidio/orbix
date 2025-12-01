<?php

namespace src\ubis\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use PDOException;
use src\ubis\domain\contracts\TipoTelecoRepositoryInterface;
use src\ubis\domain\entity\TipoTeleco;
use function core\is_true;

/**
 * Clase que adapta la tabla xd_tipo_teleco a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 17/11/2025
 */
class PgTipoTelecoRepository extends ClaseRepository implements TipoTelecoRepositoryInterface
{
    public function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('xd_tipo_teleco');
    }

    public function getArrayTiposTelecoPersona(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oTipoCentroSet = new Set();
        $sQuery = "SELECT id, nombre_teleco
				FROM $nom_tabla
				WHERE persona='t'
				ORDER BY nombre_teleco";

       if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorTipoTeleco.lista';
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

    public function getArrayTiposTelecoUbi(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oTipoCentroSet = new Set();
        $sQuery = "SELECT id, nombre_teleco
				FROM $nom_tabla
				WHERE ubi='t'
				ORDER BY nombre_teleco";

        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorTipoTeleco.lista';
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

    public function getArrayTiposTeleco(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $oTipoCentroSet = new Set();
        $sQuery = "SELECT id, nombre_teleco
				FROM $nom_tabla
				ORDER BY nombre_teleco";

        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorTipoTeleco.lista';
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
     * devuelve una colección (array) de objetos de tipo TipoTeleco
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo TipoTeleco
     */
    public function getTiposTeleco(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $TipoTelecoSet = new Set();
        $oCondicion = new Condicion();
        $aCondicion = [];
        foreach ($aWhere as $camp => $val) {
            if ($camp === '_ordre') {
                continue;
            }
            if ($camp === '_limit') {
                continue;
            }
            $sOperador = $aOperators[$camp] ?? '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) {
                $aCondicion[] = $a;
            }
            // operadores que no requieren valores
            if ($sOperador === 'BETWEEN' || $sOperador === 'IS NULL' || $sOperador === 'IS NOT NULL' || $sOperador === 'OR') {
                unset($aWhere[$camp]);
            }
            if ($sOperador === 'IN' || $sOperador === 'NOT IN') {
                unset($aWhere[$camp]);
            }
            if ($sOperador === 'TXT') {
                unset($aWhere[$camp]);
            }
        }
        $sCondicion = implode(' AND ', $aCondicion);
        if ($sCondicion !== '') {
            $sCondicion = " WHERE " . $sCondicion;
        }
        $sOrdre = '';
        $sLimit = '';
        if (isset($aWhere['_ordre']) && $aWhere['_ordre'] !== '') {
            $sOrdre = ' ORDER BY ' . $aWhere['_ordre'];
        }
        if (isset($aWhere['_ordre'])) {
            unset($aWhere['_ordre']);
        }
        if (isset($aWhere['_limit']) && $aWhere['_limit'] !== '') {
            $sLimit = ' LIMIT ' . $aWhere['_limit'];
        }
        if (isset($aWhere['_limit'])) {
            unset($aWhere['_limit']);
        }
       $sQry = "SELECT * FROM $nom_tabla " . $sCondicion . $sOrdre . $sLimit;
       $stmt = $this->prepareAndExecute( $oDbl, $sQry, $aWhere,__METHOD__, __FILE__, __LINE__);

        $filas =$stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            $TipoTeleco = new TipoTeleco();
            $TipoTeleco->setAllAttributes($aDatos);
            $TipoTelecoSet->add($TipoTeleco);
        }
        return $TipoTelecoSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(TipoTeleco $TipoTeleco): bool
    {
        $id = $TipoTeleco->getId();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id = $id";
 return $this->pdoExec( $oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(TipoTeleco $TipoTeleco): bool
    {
        $id = $TipoTeleco->getId();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id);

        $aDatos = [];
        $aDatos['tipo_teleco'] = $TipoTeleco->getTipoTelecoVo()?->value() ?? '';
        $aDatos['nombre_teleco'] = $TipoTeleco->getNombreTelecoVo()?->value() ?? '';
        $aDatos['ubi'] = $TipoTeleco->isUbi();
        $aDatos['persona'] = $TipoTeleco->isPersona();
        array_walk($aDatos, 'core\poner_null');
        //para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
        if (is_true($aDatos['ubi'])) {
            $aDatos['ubi'] = 'true';
        } else {
            $aDatos['ubi'] = 'false';
        }
        if (is_true($aDatos['persona'])) {
            $aDatos['persona'] = 'true';
        } else {
            $aDatos['persona'] = 'false';
        }

        if ($bInsert === false) {
            //UPDATE
            $update = "
					tipo_teleco              = :tipo_teleco,
					nombre_teleco            = :nombre_teleco,
					ubi                      = :ubi,
					persona                  = :persona";
            $sql = "UPDATE $nom_tabla SET $update WHERE id = $id";
            $stmt = $this->pdoPrepare( $oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        } else {
         //INSERT
            $aDatos['id'] = $TipoTeleco->getId();
            $campos = "(tipo_teleco,nombre_teleco,ubi,persona,id)";
            $valores = "(:tipo_teleco,:nombre_teleco,:ubi,:persona,:id)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare( $oDbl, $sql, __METHOD__, __FILE__, __LINE__);
		}
		return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id = $id";
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
     * @param int $id
     * @return array|bool
     */
    public function datosById(int $id): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id = $id";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    /**
     * Busca la clase con id en la base de datos .
     */
    public function findById(int $id): ?TipoTeleco
    {
        $aDatos = $this->datosById($id);
        if (empty($aDatos)) {
            return null;
        }
        return (new TipoTeleco())->setAllAttributes($aDatos);
    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('xd_tipo_teleco_id_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}