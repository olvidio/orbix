<?php

namespace src\actividades\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use PDOException;
use src\actividades\domain\contracts\NivelStgrRepositoryInterface;
use src\actividades\domain\entity\NivelStgr;
use src\actividades\domain\value_objects\NivelStgrId;

/**
 * Clase que adapta la tabla xa_nivel_stgr a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 18/11/2025
 */
class PgNivelStgrRepository extends ClaseRepository implements NivelStgrRepositoryInterface
{
    public function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('xa_nivel_stgr');
    }

    public function getArrayNivelesStgr(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();

        $sQuery = "SELECT nivel_stgr,desc_breve || '(' || desc_nivel || ')' FROM $nom_tabla ORDER BY orden";
        if (($oDbl->query($sQuery)) === false) {
            $sClauError = 'GestorNivelStgr.lista';
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
     * devuelve una colección (array) de objetos de tipo NivelStgr
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo NivelStgr
     */
    public function getNivelesStgr(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $NivelStgrSet = new Set();
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
            $NivelStgr = new NivelStgr();
            $NivelStgr->setAllAttributes($aDatos);
            $NivelStgrSet->add($NivelStgr);
        }
        return $NivelStgrSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(NivelStgr $NivelStgr): bool
    {
        $nivel_stgr = $NivelStgr->getNivel_stgr();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE nivel_stgr = $nivel_stgr";
 return $this->pdoExec( $oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(NivelStgr $NivelStgr): bool
    {
        $nivel_stgr = $NivelStgr->getNivel_stgr();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($nivel_stgr);

        $aDatos = [];
        $aDatos['desc_nivel'] = $NivelStgr->getDesc_nivel();
        $aDatos['desc_breve'] = $NivelStgr->getDesc_breve();
        $aDatos['orden'] = $NivelStgr->getOrden();
        array_walk($aDatos, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					desc_nivel               = :desc_nivel,
					desc_breve               = :desc_breve,
					orden                    = :orden";
            $sql = "UPDATE $nom_tabla SET $update WHERE nivel_stgr = $nivel_stgr";
            $stmt = $this->pdoPrepare( $oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        } else {
         //INSERT
            $aDatos['nivel_stgr'] = $NivelStgr->getNivel_stgr();
            $campos = "(nivel_stgr,desc_nivel,desc_breve,orden)";
            $valores = "(:nivel_stgr,:desc_nivel,:desc_breve,:orden)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare( $oDbl, $sql, __METHOD__, __FILE__, __LINE__);
		}
		return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $nivel_stgr): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE nivel_stgr = $nivel_stgr";
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
     * @param int $nivel_stgr
     * @return array|bool
     */
    public function datosById(int $nivel_stgr): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE nivel_stgr = $nivel_stgr";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    public function datosByIdVO(NivelStgrId $id): array|bool
    {
        return $this->datosById($id->value());
    }

    /**
     * Busca la clase con nivel_stgr en la base de datos .
     */
    public function findById(int $nivel_stgr): ?NivelStgr
    {
        $aDatos = $this->datosById($nivel_stgr);
        if (empty($aDatos)) {
            return null;
        }
        return (new NivelStgr())->setAllAttributes($aDatos);
    }

    public function findByIdVO(NivelStgrId $id): ?NivelStgr
    {
        return $this->findById($id->value());
    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('xa_nivel_stgr_nivel_stgr_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }

    public function getNewIdVO(): NivelStgrId
    {
        return new NivelStgrId((int)$this->getNewId());
    }
}