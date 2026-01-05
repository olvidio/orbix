<?php

namespace src\configuracion\domain\entity;

use src\configuracion\domain\value_objects\ConfigParametroCode;
use src\configuracion\domain\value_objects\ConfigValor;
use src\shared\domain\traits\Hydratable;

class ConfigSchema
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private string $parametro;

    private string|null $valor = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/


    public function getParametro(): string
    {
        return $this->parametro;
    }


    public function setParametro(string $parametro): void
    {
        $this->parametro = $parametro;
    }

    // Value Object API for parametro
    public function getParametroVo(): ConfigParametroCode
    {
        return new ConfigParametroCode($this->parametro);
    }

    public function setParametroVo(ConfigParametroCode $code): void
    {
        $this->parametro = $code->value();
    }


    public function getValor(): ?string
    {
        return $this->valor;
    }


    public function setValor(?string $valor = null): void
    {
        $this->valor = $valor;
    }

    // Value Object API for valor
    public function getValorVo(): ?ConfigValor
    {
        return $this->valor !== null ? new ConfigValor($this->valor) : null;
    }

    public function setValorVo(?ConfigValor $valor = null): void
    {
        $this->valor = $valor?->value();
    }
}