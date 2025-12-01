<?php

namespace shared\infrastructure;

use core\ClaseRepository;
use core\Condicion;
use core\ConverterDate;
use core\Set;
use PDO;
use PDOException;
use shared\domain\entity\ColaMail;
use shared\domain\repositories\ColaMailRepositoryInterface;
use web\NullDateTimeLocal;

/**
 * Clase que adapta la tabla cola_mails a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 1/3/2024
 */
class PgColaMailRepository extends ClaseRepository implements ColaMailRepositoryInterface
{
    public function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('cola_mails');
    }

    /* -------------------- GESTOR BASE ---------------------------------------- */

    /**
     * devuelve una colección (array) de objetos de tipo ColaMail
     *
     * @param array $aWhere asociativo con los valores para cada campo de la BD.
     * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return array|false Una colección de objetos de tipo ColaMail
     */
    public function getColaMails(array $aWhere = [], array $aOperators = []): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $ColaMailSet = new Set();
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
            // para las fechas del postgres (texto iso)
            $aDatos['sended'] = (new ConverterDate('timestamp', $aDatos['sended']))->fromPg();
            $ColaMail = new ColaMail();
            $ColaMail->setAllAttributes($aDatos);
            $ColaMailSet->add($ColaMail);
        }
        return $ColaMailSet->getTot();
    }

    public function deleteColaMails($date_iso): void
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $sQry = "DELETE FROM $nom_tabla WHERE sended < '$date_iso'";

        if (($oDblSt = $oDbl->query($sQry)) === false) {
            $sClaveError = 'PgColaMailRepository.listar.prepare';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClaveError, __LINE__, __FILE__);
            //return false;
        }
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ColaMail $ColaMail): bool
    {
        $uuid_item = $ColaMail->getUuid_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE uuid_item = '$uuid_item'";
 return $this->pdoExec( $oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * Si no existe el registro, hace un insert, si existe, se hace el update.
     */
    public function Guardar(ColaMail $ColaMail): bool
    {
        $uuid_item = $ColaMail->getUuid_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($uuid_item);

        $aDatos = [];
        $aDatos['mail_to'] = $ColaMail->getMail_to();
        $aDatos['message'] = $ColaMail->getMessage();
        $aDatos['subject'] = $ColaMail->getSubject();
        $aDatos['headers'] = $ColaMail->getHeaders();
        $aDatos['writed_by'] = $ColaMail->getWrited_by();
        // para las fechas
        if (is_a($ColaMail->getSended(), NullDateTimeLocal::class)){
            $aDatos['sended'] = NULL;
        } else {
            $aDatos['sended'] = (new ConverterDate('timestamp', $ColaMail->getSended()))->toPg();
        }
        //array_walk($aDatos, 'core\poner_null');

        if ($bInsert === false) {
            //UPDATE
            $update = "
					mail_to                  = :mail_to,
					message                  = :message,
					subject                  = :subject,
					headers                  = :headers,
					writed_by                 = :writed_by,
					sended                   = :sended";
            $sql = "UPDATE $nom_tabla SET $update WHERE uuid_item = '$uuid_item'";
            $stmt = $this->pdoPrepare( $oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        } else {
         //INSERT
            $aDatos['uuid_item'] = $ColaMail->getUuid_item();
            $campos = "(uuid_item,mail_to,message,subject,headers,writed_by,sended)";
            $valores = "(:uuid_item,:mail_to,:message,:subject,:headers,:writed_by,:sended)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare( $oDbl, $sql, __METHOD__, __FILE__, __LINE__);
		}
		return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew($uuid_item): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE uuid_item = '$uuid_item'";
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
     * @param  $uuid_item
     * @return array|bool
     */
    public function datosById($uuid_item): array|bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE uuid_item = '$uuid_item'";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        $aDatos = $oDblSt->fetch(PDO::FETCH_ASSOC);
        // para las fechas del postgres (texto iso)
        if ($aDatos !== false) {
            $aDatos['sended'] = (new ConverterDate('timestamp', $aDatos['sended']))->fromPg();
        }
        return $aDatos;
    }

    /**
     * Busca la clase con uuid_item en la base de datos .
     */
    public function findById($uuid_item): ?ColaMail
    {
        $aDatos = $this->datosById($uuid_item);
        if (empty($aDatos)) {
            return null;
        }
        return (new ColaMail())->setAllAttributes($aDatos);
    }
}