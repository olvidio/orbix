<?php

namespace src\configuracion\domain\entity;

use src\configuracion\domain\value_objects\ConfigParametroCode;
use src\configuracion\domain\value_objects\ConfigValor;
/**
 * Clase que implementa la entidad x_config_schema
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/11/2025
 */
class ConfigSchema
{

    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * Parametro de ConfigSchema
     *
     * @var string
     */
    private string $sparametro;
    /**
     * Valor de ConfigSchema
     *
     * @var string|null
     */
    private string|null $svalor = null;

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    /**
     * Establece el valor de todos los atributos
     *
     * @param array $aDatos
     * @return ConfigSchema
     */
    public function setAllAttributes(array $aDatos): ConfigSchema
    {
        if (array_key_exists('parametro', $aDatos)) {
            $this->setParametroVo(ConfigParametroCode::fromString($aDatos['parametro'] ?? ''));
        }
        if (array_key_exists('valor', $aDatos)) {
            // permitir null
            $this->setValorVo($aDatos['valor'] === null ? null : ConfigValor::fromString((string)$aDatos['valor']));
        }
        return $this;
    }

    /**
     * LEGACY
     * @return string $sparametro
     */
    public function getParametro(): string
    {
        return $this->sparametro;
    }

    /**
     * LEGACY
     * @param string $sparametro
     */
    public function setParametro(string $sparametro): void
    {
        $this->sparametro = $sparametro;
    }

    // Value Object API for parametro
    public function getParametroVo(): ConfigParametroCode
    {
        return new ConfigParametroCode($this->sparametro);
    }

    public function setParametroVo(ConfigParametroCode $code): void
    {
        $this->sparametro = $code->value();
    }

    /**
     * LEGACY
     * @return string|null $svalor
     */
    public function getValor(): ?string
    {
        return $this->svalor;
    }

    /**
     * LEGACY
     * @param string|null $svalor
     */
    public function setValor(?string $svalor = null): void
    {
        $this->svalor = $svalor;
    }

    // Value Object API for valor
    public function getValorVo(): ?ConfigValor
    {
        return $this->svalor !== null ? new ConfigValor($this->svalor) : null;
    }

    public function setValorVo(?ConfigValor $valor = null): void
    {
        $this->svalor = $valor?->value();
    }
}