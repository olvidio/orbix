<?php

declare(strict_types=1);

namespace src\devel_db_admin\domain\entity;

use src\devel_db_admin\domain\value_objects\MigracionDatabase;
use src\devel_db_admin\domain\value_objects\MigracionTipo;
use src\shared\domain\traits\Hydratable;

final class MigracionAplicada
{
    use Hydratable;

    private ?int $id = null;
    private string $prefijo = '';
    private string $descripcion = '';
    private MigracionDatabase $database;
    private MigracionTipo $tipo;
    private string $sha1 = '';
    private ?string $aplicada_en = null;
    private ?string $usuario = null;
    private bool $ok = true;
    private ?string $mensaje = null;

    public function __construct()
    {
        $this->database = new MigracionDatabase(MigracionDatabase::COMUN);
        $this->tipo = new MigracionTipo(MigracionTipo::ESTRUCTURA);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getPrefijo(): string
    {
        return $this->prefijo;
    }

    public function setPrefijo(string $prefijo): void
    {
        $this->prefijo = $prefijo;
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): void
    {
        $this->descripcion = $descripcion;
    }

    public function getDatabase(): string
    {
        return $this->database->value();
    }

    public function setDatabase(string $database): void
    {
        $this->database = new MigracionDatabase($database);
    }

    public function getDatabaseVo(): MigracionDatabase
    {
        return $this->database;
    }

    public function setDatabaseVo(MigracionDatabase|string $database): void
    {
        $this->database = $database instanceof MigracionDatabase
            ? $database
            : new MigracionDatabase($database);
    }

    public function getTipo(): string
    {
        return $this->tipo->value();
    }

    public function setTipo(string $tipo): void
    {
        $this->tipo = new MigracionTipo($tipo);
    }

    public function getTipoVo(): MigracionTipo
    {
        return $this->tipo;
    }

    public function setTipoVo(MigracionTipo|string $tipo): void
    {
        $this->tipo = $tipo instanceof MigracionTipo
            ? $tipo
            : new MigracionTipo($tipo);
    }

    public function getSha1(): string
    {
        return $this->sha1;
    }

    public function setSha1(string $sha1): void
    {
        $this->sha1 = $sha1;
    }

    public function getAplicada_en(): ?string
    {
        return $this->aplicada_en;
    }

    public function setAplicada_en(?string $aplicada_en): void
    {
        $this->aplicada_en = $aplicada_en;
    }

    public function getUsuario(): ?string
    {
        return $this->usuario;
    }

    public function setUsuario(?string $usuario): void
    {
        $this->usuario = $usuario;
    }

    public function isOk(): bool
    {
        return $this->ok;
    }

    public function setOk(bool|string|int $ok): void
    {
        if (is_string($ok)) {
            $this->ok = in_array(strtolower($ok), ['1', 'true', 't', 'yes', 'si'], true);
            return;
        }
        $this->ok = (bool) $ok;
    }

    public function getMensaje(): ?string
    {
        return $this->mensaje;
    }

    public function setMensaje(?string $mensaje): void
    {
        $this->mensaje = $mensaje;
    }
}
