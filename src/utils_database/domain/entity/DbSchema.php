<?php

namespace src\utils_database\domain\entity;

use src\utils_database\domain\value_objects\DbSchemaCode;
use src\utils_database\domain\value_objects\DbSchemaId;

/**
 * Clase que implementa la entidad db_idschema
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 13/11/2025
 */
class DbSchema
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Schema de DbSchema
     *
     * @var string
     */
    private string $sschema;
    /**
     * Id de DbSchema
     *
     * @var int
     */
    private int $iid;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return DbSchema
     */
    public function setAllAttributes(array $aDatos): DbSchema
    {
        if (array_key_exists('schema', $aDatos)) {
            $this->setSchemaVo(DbSchemaCode::fromString($aDatos['schema'] ?? null));
        }
        if (array_key_exists('id', $aDatos)) {
            $this->setIdVo(isset($aDatos['id']) ? new DbSchemaId((int)$aDatos['id']) : null);
        }
        return $this;
    }

    /**
     * LEGACY
     * @return string $sschema
     */
    public function getSchema(): string
    {
        return $this->sschema;
    }

    /**
     * LEGACY
     * @param string $sschema
     */
    public function setSchema(string $sschema): void
    {
        $this->sschema = $sschema;
    }

    /**
     * Value Object API for schema code
     */
    public function getSchemaVo(): DbSchemaCode
    {
        return new DbSchemaCode($this->sschema);
    }

    public function setSchemaVo(DbSchemaCode $code): void
    {
        $this->sschema = $code->value();
    }

    /**
     * LEGACY
     * @return int $iid
     */
    public function getId(): int
    {
        return $this->iid;
    }

    /**
     * LEGACY
     * @param int $iid
     */
    public function setId(int $iid): void
    {
        $this->iid = $iid;
    }

    /**
     * Value Object API for id
     */
    public function getIdVo(): DbSchemaId
    {
        return new DbSchemaId($this->iid);
    }

    public function setIdVo(DbSchemaId $id): void
    {
        $this->iid = $id->value();
    }
}