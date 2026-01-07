<?php

namespace src\utils_database\domain\entity;

use src\shared\domain\traits\Hydratable;
use src\utils_database\domain\value_objects\DbSchemaCode;
use src\utils_database\domain\value_objects\DbSchemaId;


class DbSchema
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private DbSchemaCode $schema;

    private DbSchemaId $id;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * @deprecated
     */
    public function getSchema(): string
    {
        return $this->schema->value();
    }

    /**
     * @deprecated
     */
    public function setSchema(string $schema): void
    {
        $this->schema = new DbSchemaCode($schema);
    }

    public function getSchemaVo(): DbSchemaCode
    {
        return $this->schema;
    }

    public function setSchemaVo(DbSchemaCode|string $code): void
    {
        $this->schema = $code instanceof DbSchemaCode
            ? $code
            : new DbSchemaCode($code);
    }

    public function getId(): int
    {
        return $this->id->value();
    }

    public function setId(int $id): void
    {
        $this->id = new DbSchemaId($id);
    }

    public function getIdVo(): DbSchemaId
    {
        return $this->id;
    }

    public function setIdVo(DbSchemaId|int  $id): void
    {
        $this->id = $id instanceof DbSchemaId
            ? $id
            : new DbSchemaId($id);
    }
}