<?php

namespace src\actividadcargos\domain\value_objects;

final class TipoCargoCode
{
    public const EMPTY = '';
    public const D = 'd';
    public const SD = 'sd';
    public const SCL = 'scl';
    public const SACD = 'sacd';
    public const COORDINA = 'coordina';
    public const D_EST = 'd.est.';

    public const VALID_VALUES = [
        self::EMPTY,
        self::D,
        self::SD,
        self::SCL,
        self::SACD,
        self::COORDINA,
        self::D_EST,
    ];

    private const TRANSLATIONS = [
        self::D => 'Director',
        self::SD => 'Subdirector',
        self::SCL => 'Secretario',
        self::SACD => 'Sacerdote',
        self::COORDINA => 'Coordinador',
        self::D_EST => 'Director de estudios',
    ];

    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(string $value): void
    {
        if (!in_array($value, self::VALID_VALUES, true)) {
            throw new \InvalidArgumentException(
                sprintf('TipoCargoCode must be one of: %s', implode(', ', self::VALID_VALUES))
            );
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(TipoCargoCode $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public static function fromNullableString(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }
        $value_trimmed = trim($value);
        if ($value_trimmed === '') {
            return null;
        }
        return new self($value_trimmed);
    }
}
