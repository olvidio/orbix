<?php

namespace src\asignaturas\domain\value_objects;

final class AsignaturaTipoName
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
            throw new \InvalidArgumentException('AsignaturaTipoName cannot be empty');
        }
        // Máx. longitud estimada 20
        if (mb_strlen($value) > 20) {
            throw new \InvalidArgumentException('AsignaturaTipoName must be at most 20 characters');
        }
        if (!preg_match("/^[\p{L}0-9 .,'’_\-()\+]+$/u", $value)) {
            throw new \InvalidArgumentException('AsignaturaTipoName has invalid characters');
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

    public function equals(AsignaturaTipoName $other): bool
    {
        return $this->value === $other->value();
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
