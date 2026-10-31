<?php

declare(strict_types=1);

namespace src\shared\infrastructure\persistence\postgresql;

use BackedEnum;
use DateTimeInterface;
use InvalidArgumentException;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\CriteriaField;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\FilterExpression;
use src\shared\domain\criteria\FilterGroup;
use src\shared\domain\criteria\LogicalOperator;
use src\shared\domain\criteria\NullsPosition;
use src\shared\domain\criteria\Operator;
use src\shared\domain\criteria\Order;
use src\shared\domain\criteria\OrderExpression;
use src\shared\domain\criteria\OrderDirection;

/**
 * Traduce un {@see Criteria} a SQL de PostgreSQL con todos los valores enlazados.
 *
 * Cada repositorio crea el conversor con la clase del enum de campos que acepta y,
 * solo para las excepciones, el mapa campo => columna (por defecto la columna es el
 * valor del enum). Un campo de otro enum lanza InvalidArgumentException.
 *
 * El resultado es `['sql' => string, 'params' => array<string, mixed>]`. Las claves de
 * `params` no llevan los dos puntos (`p0`, `p1`…). Los enteros (LIMIT/OFFSET incluidos)
 * se devuelven como int: al ejecutar, enlazarlos como PDO::PARAM_INT.
 */
final class PgCriteriaConverter
{
    private int $seq = 0;

    /** @var array<string, mixed> */
    private array $params = [];

    /**
     * @param class-string<CriteriaField> $fieldClass
     * @param array<string, string> $columns campo (valor del enum) => columna
     */
    public function __construct(
        private readonly string $fieldClass,
        private readonly array $columns = [],
    ) {
        if (!is_subclass_of($fieldClass, CriteriaField::class)) {
            throw new InvalidArgumentException($fieldClass . ' no implementa CriteriaField');
        }
        foreach ($columns as $columna) {
            self::validarColumna($columna);
        }
    }

    /**
     * Solo el WHERE (con la palabra WHERE, o '' si no hay filtros). Útil para count().
     *
     * @return array{sql: string, params: array<string, mixed>}
     */
    public function convertWhere(Criteria $criteria): array
    {
        $this->reset();
        $sql = $this->where($criteria);
        return ['sql' => $sql, 'params' => $this->params];
    }

    /**
     * WHERE + ORDER BY + LIMIT + OFFSET. Cada parte va precedida de un espacio si existe.
     *
     * @return array{sql: string, params: array<string, mixed>}
     */
    public function convert(Criteria $criteria): array
    {
        $this->reset();
        $sql = $this->where($criteria);

        $orders = array_map(fn (OrderExpression $o): string => $this->order($o), $criteria->orders());
        if ($orders !== []) {
            $sql .= ' ORDER BY ' . implode(', ', $orders);
        }
        if ($criteria->getLimit() !== null) {
            $sql .= ' LIMIT ' . $this->bind($criteria->getLimit());
        }
        if ($criteria->getOffset() !== null) {
            $sql .= ' OFFSET ' . $this->bind($criteria->getOffset());
        }
        return ['sql' => $sql, 'params' => $this->params];
    }

    private function reset(): void
    {
        $this->seq = 0;
        $this->params = [];
    }

    private function where(Criteria $criteria): string
    {
        $partes = array_map(fn (FilterExpression $f): string => $this->expression($f), $criteria->filters());
        return $partes === [] ? '' : ' WHERE ' . implode(' AND ', $partes);
    }

    private function expression(FilterExpression $e): string
    {
        if ($e instanceof FilterGroup) {
            $glue = $e->operator === LogicalOperator::Or ? ' OR ' : ' AND ';
            $partes = array_map(fn (FilterExpression $c): string => $this->expression($c), $e->children);
            return '(' . implode($glue, $partes) . ')';
        }
        if ($e instanceof Filter) {
            return $this->operatorSql($this->column($e->field), $e->operator, $e->value);
        }
        throw new InvalidArgumentException('Expresión de filtro no soportada: ' . get_debug_type($e));
    }

    private function operatorSql(string $c, Operator $operator, mixed $v): string
    {
        switch ($operator) {
            case Operator::Eq:
                return "$c = " . $this->bind($v);
            case Operator::Neq:
                return "$c <> " . $this->bind($v);
            case Operator::Lt:
                return "$c < " . $this->bind($v);
            case Operator::Lte:
                return "$c <= " . $this->bind($v);
            case Operator::Gt:
                return "$c > " . $this->bind($v);
            case Operator::Gte:
                return "$c >= " . $this->bind($v);
            case Operator::IsNull:
                return "$c IS NULL";
            case Operator::IsNotNull:
                return "$c IS NOT NULL";
            case Operator::In:
            case Operator::NotIn:
                $lista = is_array($v) ? $v : [];
                if ($lista === []) {
                    // IN () no existe en SQL: nada coincide / todo coincide.
                    return $operator === Operator::In ? 'FALSE' : 'TRUE';
                }
                $marcas = array_map(fn (mixed $x): string => $this->bind($x), $lista);
                $kw = $operator === Operator::In ? 'IN' : 'NOT IN';
                return "$c $kw (" . implode(', ', $marcas) . ')';
            case Operator::Between:
                $par = is_array($v) ? array_values($v) : [];
                return "$c BETWEEN " . $this->bind($par[0] ?? null) . ' AND ' . $this->bind($par[1] ?? null);
            case Operator::ArrayContains:
                return $this->bind($v) . " = ANY($c)";
            case Operator::Matches:
                return "$c::text ~ " . $this->bind($v);
            case Operator::MatchesInsensitive:
                return "$c::text ~* " . $this->bind($v);
            case Operator::NotMatches:
                return "$c::text !~ " . $this->bind($v);
            case Operator::NotMatchesInsensitive:
                return "$c::text !~* " . $this->bind($v);
            case Operator::MatchesUnaccented:
                return "public.sin_acentos($c::text) ~* public.sin_acentos(" . $this->bind($v) . '::text)';
            case Operator::HasBits:
                // Dos parámetros con el mismo valor: no repetir un placeholder con nombre.
                return "($c & " . $this->bind($v) . ') = ' . $this->bind($v);
        }
    }

    private function order(OrderExpression $o): string
    {
        if (!$o instanceof Order) {
            throw new InvalidArgumentException('Ordenación no soportada: ' . get_debug_type($o));
        }
        $sql = $this->column($o->field) . ($o->direction === OrderDirection::Desc ? ' DESC' : ' ASC');
        if ($o->nulls !== null) {
            $sql .= $o->nulls === NullsPosition::First ? ' NULLS FIRST' : ' NULLS LAST';
        }
        return $sql;
    }

    private function column(CriteriaField $field): string
    {
        if (!$field instanceof $this->fieldClass) {
            throw new InvalidArgumentException(
                'El campo ' . $field::class . '::' . $field->name . ' no pertenece a ' . $this->fieldClass
            );
        }
        $columna = $this->columns[(string) $field->value] ?? (string) $field->value;
        self::validarColumna($columna);
        return $columna;
    }

    private static function validarColumna(string $columna): void
    {
        if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*(\.[A-Za-z_][A-Za-z0-9_]*)?$/', $columna) !== 1) {
            throw new InvalidArgumentException('Nombre de columna no válido: ' . $columna);
        }
    }

    /** Registra el valor como parámetro y devuelve su placeholder (`:p0`). */
    private function bind(mixed $value): string
    {
        $name = 'p' . $this->seq++;
        $this->params[$name] = self::normalizar($value);
        return ':' . $name;
    }

    private static function normalizar(mixed $v): mixed
    {
        if ($v instanceof DateTimeInterface) {
            return $v->format('Y-m-d H:i:s.uP');
        }
        if ($v instanceof BackedEnum) {
            return $v->value;
        }
        if (is_bool($v)) {
            return $v ? 't' : 'f';
        }
        if (is_object($v) && method_exists($v, 'value')) {
            return self::normalizar($v->value());
        }
        return $v;
    }
}
