<?php

namespace src\personas\domain\value_objects;

final class TrasladoTipoCmbCode
{
    private string $value;

    public function __construct(string $value)
    {
        $value = trim($value);
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(string $value): void
    {
        if ($value === '') {
            throw new \InvalidArgumentException('TrasladoTipoCmbCode cannot be empty');
        }
        if (mb_strlen($value) > 2) {
            throw new \InvalidArgumentException('TrasladoTipoCmbCode must be at most 2 characters');
        }
    }

    public function value(): string { return $this->value; }
    public function __toString(): string { return $this->value; }

    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
