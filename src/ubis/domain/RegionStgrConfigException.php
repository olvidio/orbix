<?php

namespace src\ubis\domain;

/**
 * Configuración incompleta de delegación / región del stgr (no es un fallo de aplicación).
 */
class RegionStgrConfigException extends \RuntimeException
{
    public function __construct(
        private readonly string $tipo,
        private readonly string $dele,
        private readonly string $esquema = '',
    ) {
        parent::__construct($dele);
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function getDele(): string
    {
        return $this->dele;
    }

    public function getEsquema(): string
    {
        return $this->esquema;
    }
}
