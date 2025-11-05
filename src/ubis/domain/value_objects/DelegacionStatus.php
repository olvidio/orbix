<?php

namespace src\ubis\domain\value_objects;

final class DelegacionStatus
{
    private bool $value;

    public function __construct(bool $value)
    {
        $this->value = $value;
    }

    public static function fromScalar(mixed $value): self
    {
        if (is_string($value)) {
            $v = strtolower(trim($value));
            if (in_array($v, ['t','true','1'], true)) { return new self(true); }
            if (in_array($v, ['f','false','0',''], true)) { return new self(false); }
        }
        return new self((bool)$value);
    }

    public function value(): bool
    {
        return $this->value;
    }

    public function isActive(): bool
    {
        return $this->value === true;
    }

    public function __toString(): string
    {
        return $this->value ? 'true' : 'false';
    }

    public function equals(DelegacionStatus $other): bool
    {
        return $this->value === $other->value();
    }
}
