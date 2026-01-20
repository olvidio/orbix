<?php

namespace src\cambios\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\ConverterDate;
use core\Set;
use PDO;
use src\cambios\domain\contracts\CambioUsuarioRepositoryInterface;
use src\cambios\domain\entity\CambioUsuario;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\traits\HandlesPdoErrors;


/**
 * Clase que adapta la tabla av_cambios_usuario a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 20/12/2025
 */
class PgCambioUsuarioRepository extends ClaseRepository implements CambioUsuarioRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('av_cambios_usuario');
    }

    /**
     * para eliminar avisos masivamente, anteriores a una fecha.
     *
     * @param DateTimeLocal|string df_fin.
     */
    public function eliminarHastaFecha($df_fin)
    {
        if (empty($df_fin)) return FALSE;
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $nom_tabla_cambios = 'public.av_cambios';

        $oConverter = new ConverterDate('date', $df_fin);
        $sf_fin = $oConverter->toPg();

        $sql = "DELETE FROM $nom_tabla u USING $nom_tabla_cambios c 
                WHERE u.id_schema_cambio=c.id_schema AND u.id_item_cambio=c.id_item_cambio 
                    AND c.timestamp_cambio < '$sf_fin'
                ";

        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo CambioUsuario
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo CambioUsuario
     */
    public function getCambiosUsuario(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $CambioUsuarioSet = new Set();
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
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $aWhere, __METHOD__, __FILE__, __LINE__);

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            $CambioUsuario =  CambioUsuario::fromArray($aDatos);
            $CambioUsuarioSet->add($CambioUsuario);
        }
        return $CambioUsuarioSet->getTot();
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(CambioUsuario $CambioUsuario): bool
    {
        $id_item = $CambioUsuario->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item = $id_item";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }


    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(CambioUsuario $CambioUsuario): bool
    {
        $id_item = $CambioUsuario->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_item);

        $aDatos = $CambioUsuario->toArrayForDatabase();
        if ($bInsert === false) {
            //UPDATE
            unset($aDatos['id_item']);
            $update = "
					id_schema_cambio         = :id_schema_cambio,
					id_item_cambio           = :id_item_cambio,
					id_usuario               = :id_usuario,
					sfsv                     = :sfsv,
					aviso_tipo               = :aviso_tipo,
					avisado                  = :avisado";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item = $id_item";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            // INSERT
            $campos = "(id_item,id_schema_cambio,id_item_cambio,id_usuario,sfsv,aviso_tipo,avisado)";
            $valores = "(:id_item,:id_schema_cambio,:id_item_cambio,:id_usuario,:sfsv,:aviso_tipo,:avisado)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);    }
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
    public function findById(int $id_item): ?CambioUsuario
    {
        $aDatos = $this->datosById($id_item);
        if (empty($aDatos)) {
            return null;
        }
        return CambioUsuario::fromArray($aDatos);
    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('av_cambios_usuario_id_item_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}