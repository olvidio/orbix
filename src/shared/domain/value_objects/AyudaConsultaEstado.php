<?php

declare(strict_types=1);

namespace src\shared\domain\value_objects;

use InvalidArgumentException;

/**
 * Estado de revisión de una consulta hecha a la ayuda.
 */
final class AyudaConsultaEstado
{
    public const PENDIENTE = 1;
    public const RESUELTA = 2;
    public const DESCARTADA = 3;

    /**
     * @return array<int, string>
     */
    public static function getArrayEstado(): array
    {
        return [
            self::PENDIENTE => _("pendiente"),
            self::RESUELTA => _("resuelta"),
            self::DESCARTADA => _("descartada"),
        ];
    }

    private int $value;

    public function __construct(int $value)
    {
        if (!array_key_exists($value, self::getArrayEstado())) {
            throw new InvalidArgumentException(sprintf('Estado de consulta de ayuda no válido: %d', $value));
        }
        $this->value = $value;
    }

    public static function fromNullableInt(?int $value): ?self
    {
        return $value === null ? null : new self($value);
    }

    public function value(): int
    {
        return $this->value;
    }

    public function getDescripcion(): string
    {
        return self::getArrayEstado()[$this->value];
    }

    public function equals(AyudaConsultaEstado $other): bool
    {
        return $this->value === $other->value();
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
