<?php

declare(strict_types=1);

namespace src\shared\domain\criteria;

use BackedEnum;
use DateTimeInterface;
use InvalidArgumentException;

/**
 * Condición sobre un campo: campo + operador + valor. Inmutable.
 *
 * El valor es un tipo PHP nativo (int, float, string, bool, DateTimeInterface),
 * un enum, un value object con `value()` o una lista de ellos. Nunca un string
 * con formato (`"a,b"`, `"{1,2}"`).
 */
final class Filter implements FilterExpression
{
    private function __construct(
        public readonly CriteriaField $field,
        public readonly Operator $operator,
        public readonly mixed $value = null,
    ) {
        $this->validar();
    }

    public static function eq(CriteriaField $field, mixed $value): self
    {
        return new self($field, Operator::Eq, $value);
    }

    public static function neq(CriteriaField $field, mixed $value): self
    {
        return new self($field, Operator::Neq, $value);
    }

    public static function lt(CriteriaField $field, mixed $value): self
    {
        return new self($field, Operator::Lt, $value);
    }

    public static function lte(CriteriaField $field, mixed $value): self
    {
        return new self($field, Operator::Lte, $value);
    }

    public static function gt(CriteriaField $field, mixed $value): self
    {
        return new self($field, Operator::Gt, $value);
    }

    public static function gte(CriteriaField $field, mixed $value): self
    {
        return new self($field, Operator::Gte, $value);
    }

    public static function isNull(CriteriaField $field): self
    {
        return new self($field, Operator::IsNull);
    }

    public static function isNotNull(CriteriaField $field): self
    {
        return new self($field, Operator::IsNotNull);
    }

    /** @param list<mixed> $values */
    public static function in(CriteriaField $field, array $values): self
    {
        return new self($field, Operator::In, array_values($values));
    }

    /** @param list<mixed> $values */
    public static function notIn(CriteriaField $field, array $values): self
    {
        return new self($field, Operator::NotIn, array_values($values));
    }

    public static function between(CriteriaField $field, mixed $desde, mixed $hasta): self
    {
        return new self($field, Operator::Between, [$desde, $hasta]);
    }

    /** El valor está contenido en la columna de tipo array. */
    public static function arrayContains(CriteriaField $field, mixed $value): self
    {
        return new self($field, Operator::ArrayContains, $value);
    }

    public static function matches(CriteriaField $field, string $pattern): self
    {
        return new self($field, Operator::Matches, $pattern);
    }

    public static function matchesInsensitive(CriteriaField $field, string $pattern): self
    {
        return new self($field, Operator::MatchesInsensitive, $pattern);
    }

    public static function notMatches(CriteriaField $field, string $pattern): self
    {
        return new self($field, Operator::NotMatches, $pattern);
    }

    public static function notMatchesInsensitive(CriteriaField $field, string $pattern): self
    {
        return new self($field, Operator::NotMatchesInsensitive, $pattern);
    }

    /** Coincidencia por expresión regular ignorando mayúsculas y acentos. */
    public static function matchesUnaccented(CriteriaField $field, string $pattern): self
    {
        return new self($field, Operator::MatchesUnaccented, $pattern);
    }

    /** Todos los bits de la máscara están activos en el campo. */
    public static function hasBits(CriteriaField $field, int $mask): self
    {
        return new self($field, Operator::HasBits, $mask);
    }

    private function validar(): void
    {
        if (!$this->operator->takesValue()) {
            return;
        }
        if ($this->operator->takesList()) {
            if (!is_array($this->value)) {
                throw new InvalidArgumentException('El operador ' . $this->operator->value . ' requiere una lista');
            }
            if ($this->operator === Operator::Between && count($this->value) !== 2) {
                throw new InvalidArgumentException('between requiere exactamente dos valores');
            }
            foreach ($this->value as $v) {
                self::validarEscalar($v);
            }
            return;
        }
        self::validarEscalar($this->value);
    }

    private static function validarEscalar(mixed $v): void
    {
        if ($v === null) {
            throw new InvalidArgumentException('El valor no puede ser null: usar isNull()/isNotNull()');
        }
        if (is_scalar($v) || $v instanceof DateTimeInterface || $v instanceof BackedEnum) {
            return;
        }
        if (is_object($v) && method_exists($v, 'value')) {
            return;
        }
        throw new InvalidArgumentException('Tipo de valor no soportado en Filter: ' . get_debug_type($v));
    }
}
