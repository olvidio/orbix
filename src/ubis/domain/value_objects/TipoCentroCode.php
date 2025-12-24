<?php

namespace src\ubis\domain\value_objects;

final class TipoCentroCode
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
            throw new \InvalidArgumentException('TipoCentroCode cannot be empty');
        }
        // By UI config, argument length is 5 (see DatosCampo->setArgument(5))
        if (mb_strlen($value) > 5) {
            throw new \InvalidArgumentException('TipoCentroCode must be at most 8 characters');
        }
        // Allow letters, numbers, underscore and hyphen, and comma
        if (!preg_match('/^[A-Za-z0-9_-|,]+$/u', $value)) {
            throw new \InvalidArgumentException('TipoCentroCode has invalid characters');
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

    public function equals(TipoCentroCode $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
