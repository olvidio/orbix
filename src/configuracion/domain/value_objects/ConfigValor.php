<?php

namespace src\configuracion\domain\value_objects;

final class ConfigValor
{
    private string $value;

    public function __construct(string $value)
    {
        // Do not trim to preserve significant spaces; allow any UTF-8 text
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(string $value): void
    {
        // Allow any string (including JSON, CSV, numbers); enforce a generous max length to avoid abuse
        if (mb_strlen($value) > 4096) {
            throw new \InvalidArgumentException('ConfigValor must be at most 4096 characters');
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

    public function equals(ConfigValor $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
