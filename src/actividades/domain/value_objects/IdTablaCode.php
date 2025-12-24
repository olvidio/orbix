<?php

namespace src\actividades\domain\value_objects;

final class IdTablaCode
{
    public const DL = 'dl';
    public const EX = 'ex';

    private string $value;

    public function __construct(string $value)
    {
        $value = strtolower(trim($value));
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(string $value): void
    {
        if (!in_array($value, [self::DL, self::EX], true)) {
            throw new \InvalidArgumentException("IdTablaCode sólo admite 'dl' o 'ex'");
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(IdTablaCode $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
