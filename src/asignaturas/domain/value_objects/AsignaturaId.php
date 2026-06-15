<?php

namespace src\asignaturas\domain\value_objects;

final class AsignaturaId
{
    private int $value;

    public function __construct(int $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    public static function isValidInt(int $value): bool
    {
        $esRangoNormal = ($value >= 1000 && $value <= 3999);
        $esExcepcion = ($value === 9998 || $value === 9999);

        return $esRangoNormal || $esExcepcion;
    }

    private function validate(int $value): void
    {
        if (self::isValidInt($value)) {
            return;
        }

        throw new \InvalidArgumentException(self::invalidIntMessage($value));
    }

    private static function invalidIntMessage(int $value): string
    {
        return sprintf(
            'AsignaturaId must be a 4-digit integer starting with 1, 2 or 3, or be 9998/9999 (got: %d)',
            $value,
        );
    }

    public function value(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    public function equals(AsignaturaId $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromString(string $value): self
    {
        if (!ctype_digit($value)) {
            throw new \InvalidArgumentException(
                sprintf('AsignaturaId string must be digits (got: %s)', $value),
            );
        }

        if (strlen($value) !== 4) {
            throw new \InvalidArgumentException(
                sprintf('AsignaturaId string must be exactly 4 digits (got: %s)', $value),
            );
        }

        return new self((int)$value);
    }

    public static function fromNullableInt(?int $value): ?self
    {
        if ($value === null) {
            return null;
        }

        return new self($value);
    }
}
