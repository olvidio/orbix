<?php

namespace src\actividadplazas\domain\value_objects;

final class DelegacionTablaCode
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
            throw new \InvalidArgumentException('DelegacionTablaCode no puede estar vacío');
        }
        if (mb_strlen($value) > 10) {
            throw new \InvalidArgumentException('DelegacionTablaCode no puede tener más de 10 caracteres');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
