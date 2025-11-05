<?php

namespace src\menus\domain\value_objects;

final class TemplateMenuName
{
    private string $value;

    public function __construct(string $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(string $value): void
    {
        if ($value === '') {
            throw new \InvalidArgumentException('TemplateMenuName cannot be empty');
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

    public function equals(TemplateMenuName $other): bool
    {
        return $this->value === $other->value;
    }
}
