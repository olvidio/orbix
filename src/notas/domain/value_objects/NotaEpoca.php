<?php

namespace src\notas\domain\value_objects;

final class NotaEpoca
{

    public const EPOCA_CA = 1; // ca verano.
    public const EPOCA_INVIERNO = 2; // semestre invierno.
    public const EPOCA_OTRO = 3; // sin especificar.

    /**
     * Textos que ve el usuario (los mismos que el formulario de la nota).
     *
     * @return array<int, string>
     */
    public static function getArrayEpocaTxt(): array
    {
        return [
            self::EPOCA_CA => _("ca"),
            self::EPOCA_INVIERNO => _("semestre invierno"),
            self::EPOCA_OTRO => _("sin especificar"),
        ];
    }

    private int $value;

    public function __construct(int $value)
    {
        if (!array_key_exists($value, self::getArrayEpocaTxt())) {
            throw new \InvalidArgumentException(sprintf('<%s> no es un valor válido para NotaEpoca', $value));
        }
        $this->value = $value;
    }

    public function value(): int
    {
        return $this->value;
    }

    public function getDescripcion(): string
    {
        return self::getArrayEpocaTxt()[$this->value];
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    public static function fromNullableInt(?int $value): ?self
    {
        if ($value === null) {
            return null;
        }
        return new self($value);
    }
}
