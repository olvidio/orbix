<?php

namespace src\notas\domain\value_objects;

use InvalidArgumentException;

final class NotaSituacion
{
    // tipo constantes.
    public const DESCONOCIDO = 0;
    public const SUPERADA = 1;
    public const CURSADA = 2;
    public const MAGNA = 3;
    public const SUMMA = 4;
    public const CONVALIDADA = 5;
    public const PREVISTA_CA = 6;
    public const PREVISTA_INV = 7;
    public const NO_HECHA_CA = 8;
    public const NO_HECHA_INV = 9;
    public const NUMERICA = 10;
    public const EXENTO = 11;
    public const EXAMINADO = 12;
    public const FALTA_CERTIFICADO = 13;

    public static function getArraySituacionTxt(): array
    {
        return [
            self::DESCONOCIDO => _("desconocido"),
            self::SUPERADA => _("superada"),
            self::CURSADA => _("cursada"),
            self::MAGNA => _("Magna cum laude"),
            self::SUMMA => _("Summa cum laude"),
            self::CONVALIDADA => _("convalidada"),
            self::PREVISTA_CA => _("prevista ca"),
            self::PREVISTA_INV => _("prevista inv"),
            self::NO_HECHA_CA => _("no hecha ca"),
            self::NO_HECHA_INV => _("no hecha inv"),
            self::NUMERICA => _("nota numérica"),
            self::EXENTO => _("Exento"),
            self::EXAMINADO => _("examinado"),
            self::FALTA_CERTIFICADO => _("falta certificado"),
        ];
    }

    private int $value;

    public function __construct(int $value)
    {
        if (!array_key_exists($value, self::getArraySituacionTxt())) {
            throw new InvalidArgumentException(sprintf('<%s> no es un valor válido para NotaSituacion', $value));
        }
        $this->value = $value;
    }

    public function value(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }

    public static function fromNullable(?int $value): ?self
    {
        if ($value === null) {
            return null;
        }
        return new self($value);
    }
}
