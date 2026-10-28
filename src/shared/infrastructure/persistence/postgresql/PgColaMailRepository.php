<?php

namespace src\shared\infrastructure\persistence\postgresql;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\ConverterDate;
use PDO;
use src\shared\infrastructure\GlobalPdo;
use src\shared\domain\contracts\ColaMailRepositoryInterface;
use src\shared\domain\criteria\ColaMailField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\entity\ColaMail;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\RunsCriteriaQueries;

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
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBPC');
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl);
        $this->setNomTabla('cola_mails');
    }

    /* --------------------  BASiC SEARCH ---------------------------------------- */

    /**
     * Mails de la cola que cumplen el criterio.
     *
     * @return list<ColaMail>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(ColaMailField::class, self::COLUMNAS);
        $result = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            // para las fechas del postgres (texto iso)
            $aDatos['sended'] = (new ConverterDate('timestamp', $aDatos['sended']))->fromPg();
            $result[] = ColaMail::fromArray($aDatos);
        }

        return $result;
    }

    /**
     * devuelve una colección (array) de objetos de tipo ColaMail
     *
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere asociativo con los valores para cada campo de la BD.
     * @param array<string, string> $aOperators asociativo con los operadores que hay que aplicar a cada campo
     * @return list<ColaMail>
     */
    public function getColaMails(array $aWhere = [], array $aOperators = []): array
    {
        $adapter = new LegacyWhereAdapter(ColaMailField::class, self::COLUMNAS);
        return $this->matching($adapter->toCriteria($aWhere, $aOperators));
    }

    public function deleteColaMails(string $date_iso): void
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $sQry = "DELETE FROM $nom_tabla WHERE sended < '$date_iso'";
        $this->pdoExec($oDbl, $sQry, __METHOD__, __FILE__, __LINE__);
    }

    /* -------------------- ENTIDAD --------------------------------------------- */

    public function Eliminar(ColaMail $ColaMail): bool
    {
        $uuid_item = $ColaMail->getUuid_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE uuid_item = '$uuid_item'";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
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

        $aDatos = $ColaMail->toArrayForDatabase([
            'sended' => fn($v) => (new ConverterDate('timestamp', $v))->toPg(),
        ]);

        if ($bInsert === false) {
            unset($aDatos['uuid_item']);
            //UPDATE
            $update = "
					mail_to                  = :mail_to,
					message                  = :message,
					subject                  = :subject,
					headers                  = :headers,
					writed_by                 = :writed_by,
					sended                   = :sended";
            $sql = "UPDATE $nom_tabla SET $update WHERE uuid_item = '$uuid_item'";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            //INSERT
            $campos = "(uuid_item,mail_to,message,subject,headers,writed_by,sended)";
            $valores = "(:uuid_item,:mail_to,:message,:subject,:headers,:writed_by,:sended)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }

        if ($stmt === false) {
            return false;
        }

        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(string $uuid_item): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE uuid_item = '$uuid_item'";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return true;
        }
        if (!$stmt->rowCount()) {
            return TRUE;
        }
        return false;
    }

    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     *
     * @return array<string, mixed>|false
     */
    public function datosById(string $uuid_item): array|false
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE uuid_item = '$uuid_item'";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }

        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return false;
        }

        // para las fechas del postgres (texto iso)
        $aDatos['sended'] = (new ConverterDate('timestamp', $aDatos['sended']))->fromPg();

        $row = [];
        foreach ($aDatos as $key => $value) {
            $row[(string) $key] = $value;
        }

        return $row;
    }

    /**
     * Busca la clase con uuid_item en la base de datos .
     */
    public function findById(string $uuid_item): ?ColaMail
    {
        $aDatos = $this->datosById($uuid_item);
        if (!is_array($aDatos)) {
            return null;
        }
        return ColaMail::fromArray($aDatos);
    }
}
