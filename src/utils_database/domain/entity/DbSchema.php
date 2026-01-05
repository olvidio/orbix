<?php

namespace src\utils_database\domain\entity;

use src\shared\domain\traits\Hydratable;
use src\utils_database\domain\value_objects\DbSchemaCode;
use src\utils_database\domain\value_objects\DbSchemaId;


class DbSchema
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private string $schema;

    private int $id;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * @deprecated
     */
    public function getSchema(): string
    {
        return $this->schema;
    }

    /**
     * @deprecated
     */
    public function setSchema(string $schema): void
    {
        $this->schema = $schema;
    }

    public function getSchemaVo(): DbSchemaCode
    {
        return new DbSchemaCode($this->schema);
    }

    public function setSchemaVo(DbSchemaCode $code): void
    {
        $this->schema = $code->value();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getIdVo(): DbSchemaId
    {
        return new DbSchemaId($this->id);
    }

    public function setIdVo(DbSchemaId $id): void
    {
        $this->id = $id->value();
    }
}