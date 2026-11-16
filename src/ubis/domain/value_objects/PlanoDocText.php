<?php

namespace src\ubis\domain\value_objects;

/**
 * Contenido del plano de una dirección: el fichero que se sube (una foto, un jpg, un pdf…).
 *
 * Es binario y de cualquier tamaño: se guarda byte a byte, sin recortar espacios, saltos de línea ni
 * bytes nulos de los extremos y sin límite de longitud (con el límite de 255 caracteres que tenía,
 * una dirección con un plano de verdad no se podía ni leer).
 */
final class PlanoDocText
{
    private string $value;

    public function __construct(string $value)
    {
        if ($value === '') {
            throw new \InvalidArgumentException('PlanoDocText cannot be empty');
        }
        $this->value = $value;
    }

    public function value(): string
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
