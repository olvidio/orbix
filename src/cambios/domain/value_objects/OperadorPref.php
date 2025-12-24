<?php

namespace src\cambios\domain\value_objects;

final class OperadorPref
{
    public const IGUAL = '=';
    public const MAYOR = '>';
    public const MENOR = '<';
    public const REGEXP = 'regexp';


    public static function getArrayOperador(): array
    {
        return [
            self::IGUAL => '=',
            self::MAYOR => '>',
            self::MENOR => '<',
            self::REGEXP => 'regexp',
        ];
    }

    // ---------------------------------------------------------------------------
    private string $value;

    public function __construct(string $value)
    {
        $this->validate($value);
        $this->value = $value;
    }

    private function validate(string $value): void
    {
        if (!in_array($value, [self::IGUAL, self::MAYOR, self::MENOR, self::REGEXP], true)) {
            throw new \InvalidArgumentException("OperadorPref solo puede ser '=', '>', '<' o 'regexp'");
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(OperadorPref $other): bool
    {
        return $this->value === $other->value();
    }
}
