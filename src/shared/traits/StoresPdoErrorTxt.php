<?php

namespace src\shared\traits;

/**
 * Almacén mínimo de texto de error para clases que usan HandlesPdoErrors
 * sin extender ClaseRepository.
 */
trait StoresPdoErrorTxt
{
    protected string $sErrorTxt = '';

    /** @return $this */
    protected function setErrorTxt(string $sErrorTxt): static
    {
        $this->sErrorTxt = $sErrorTxt;

        return $this;
    }
}
