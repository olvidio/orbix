<?php

namespace src\cambios\domain\value_objects;

final class AvisoTipoId
{
    public const TIPO_LISTA = 1; // Anotar en lista.
    public const TIPO_MAIL = 2; // por mail.

    public static function getArrayAvisoTipo(): array
    {
        return [
            self::TIPO_LISTA => _("lista"),
            self::TIPO_MAIL => _("email"),
        ];
    }

    // ---------------------------------------------------------------------------
    private int $value;

    public function __construct(int $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(int $value): void
    {
        if (!in_array($value, [self::TIPO_LISTA, self::TIPO_MAIL], true)) {
            throw new \InvalidArgumentException('AvisoTipoId solo puede ser 1 o 2');
        }
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(AvisoTipoId $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromNullable(?int $value): ?self
    {
        if ($value === null) {
            return null;
        }
        return new self($value);
    }
}
