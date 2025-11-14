<?php

namespace src\ubis\domain\value_objects;

final class TipoCasaCode
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
            throw new \InvalidArgumentException('TipoCasaCode cannot be empty');
        }
        // By UI config, argument length is 6 (see DatosCampo->setArgument(8))
        if (mb_strlen($value) > 8) {
            throw new \InvalidArgumentException('TipoCasaCode must be at most 6 characters');
        }
        // Allow letters, numbers, underscore and hyphen
        if (!preg_match('/^[\p{L}0-9_.\- ]+$/u', $value)) {
            throw new \InvalidArgumentException('TipoCasaCode has invalid characters');
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

    public function equals(TipoCasaCode $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
