<?php

namespace src\configuracion\domain\value_objects;

final class ConfigParametroCode
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
            throw new \InvalidArgumentException('ConfigParametroCode cannot be empty');
        }
        // Typical parameter keys are short (e.g., curso_crt, idioma_default)
        if (mb_strlen($value) > 64) {
            throw new \InvalidArgumentException('ConfigParametroCode must be at most 64 characters');
        }
        // Allow letters, numbers, underscore and hyphen
        if (!preg_match('/^[A-Za-z0-9_-]+$/u', $value)) {
            throw new \InvalidArgumentException('ConfigParametroCode has invalid characters');
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

    public function equals(ConfigParametroCode $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
