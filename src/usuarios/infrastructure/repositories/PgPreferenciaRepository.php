<?php

namespace src\usuarios\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use PDOException;
use src\usuarios\domain\contracts\PreferenciaRepositoryInterface;
use src\usuarios\domain\entity\Preferencia;

/**
 * Clase que adapta la tabla web_preferencias a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 24/4/2025
 */
class PgPreferenciaRepository extends ClaseRepository implements PreferenciaRepositoryInterface
{
    public function __construct()
    {
        $oDbl = $GLOBALS['oDBE'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBE_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('web_preferencias');
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo Preferencia
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo Preferencia
     */
    public function getPreferencias(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $PreferenciaSet = new Set();
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
            $Preferencia = new Preferencia();
            $Preferencia->setAllAttributes($aDatos);
            $PreferenciaSet->add($Preferencia);
        }
        return $PreferenciaSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(Preferencia $Preferencia): bool
    {
        $id_usuario = $Preferencia->getId_usuario();
        $tipo = $Preferencia->getTipo();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_usuario = $id_usuario AND tipo = '$tipo'";
 return $this->pdoExec( $oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(Preferencia $Preferencia): bool
    {
        $id_usuario = $Preferencia->getId_usuario();
        $tipo = $Preferencia->getTipo();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_usuario, $tipo);

        $aDatos = [];
        $aDatos['preferencia'] = $Preferencia->getPreferencia();
        array_walk($aDatos, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					preferencia              = :preferencia
					";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_usuario = $id_usuario AND tipo = '$tipo'";
            $stmt = $this->pdoPrepare( $oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        } else {
         //INSERT
            $aDatos['id_usuario'] = $id_usuario;
            $aDatos['tipo'] = $Preferencia->getTipo();
            $campos = "(tipo,preferencia,id_usuario)";
            $valores = "(:tipo,:preferencia,:id_usuario)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare( $oDbl, $sql, __METHOD__, __FILE__, __LINE__);
            try {
                $oDblSt->execute($aDatos);
            } catch (PDOException $e) {
                $err_txt = $e->errorInfo[2];
                $this->setErrorTxt($err_txt);
                $sClaveError = 'PgPreferenciaRepository.insertar.execute';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClaveError, __LINE__, __FILE__);
                return false;
            }
        }
        return TRUE;
    }

    private function isNew(int $id_usuario, string $tipo): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_usuario = $id_usuario AND tipo = '$tipo'";
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
     * @param string $tipo
     * @return array|bool
     */
    public function datosById(int $id_usuario, string $tipo): array|bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_usuario = $id_usuario AND  tipo = '$tipo'";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    /**
     * Busca la clase con tipo en la base de datos .
     */
    public function findById(int $id_usuario, string $tipo): ?Preferencia
    {
        $aDatos = $this->datosById($id_usuario, $tipo);
        if (empty($aDatos)) {
            return null;
        }
        return (new Preferencia())->setAllAttributes($aDatos);
    }
}