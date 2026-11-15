<?php

namespace src\notas\domain\value_objects;

/**
 * Contenido binario del PDF firmado de un acta.
 *
 * Se guarda byte a byte: no se recortan los espacios ni los saltos de línea de los extremos (un PDF
 * acaba normalmente en «%%EOF» + salto de línea), porque el fichero guardado tiene que ser el que se
 * subió.
 */
final class Pdf
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function fromNullableString(?string $value): ?self
    {
        if ($value === null || $value === '') {
            return null;
        }

        return new self($value);
    }
}
