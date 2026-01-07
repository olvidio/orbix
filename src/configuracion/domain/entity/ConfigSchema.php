<?php

namespace src\configuracion\domain\entity;

use src\configuracion\domain\value_objects\ConfigParametroCode;
use src\configuracion\domain\value_objects\ConfigValor;
use src\shared\domain\traits\Hydratable;

class ConfigSchema
{
    use Hydratable;

    /* ATRIBUTOS ----------------------------------------------------------------- */


    private ConfigParametroCode $parametro;

    private ?ConfigValor $valor = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/


    public function getParametro(): string
    {
        return $this->parametro->value();
    }


    public function setParametro(string $parametro): void
    {
        $this->parametro = ConfigParametroCode::fromNullableString($parametro);
    }

    // Value Object API for parametro
    public function getParametroVo(): ConfigParametroCode
    {
        return $this->parametro;
    }

    public function setParametroVo(ConfigParametroCode|string $code): void
    {
        $this->parametro = $code instanceof ConfigParametroCode
            ? $code
            : ConfigParametroCode::fromNullableString($code);
    }


    public function getValor(): ?string
    {
        return $this->valor?->value();
    }


    public function setValor(?string $valor = null): void
    {
        $this->valor = ConfigValor::fromNullableString($valor);
    }

    // Value Object API for valor
    public function getValorVo(): ?ConfigValor
    {
        return $this->valor;
    }

    public function setValorVo(ConfigValor|string|null $valor = null): void
    {
        $this->valor = $valor instanceof ConfigValor
            ? $valor
            : ConfigValor::fromNullableString($valor);
    }
}