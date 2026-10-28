<?php

declare(strict_types=1);

namespace src\shared\infrastructure\persistence\postgresql;

use InvalidArgumentException;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\CriteriaField;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\FilterExpression;
use src\shared\domain\criteria\NullsPosition;
use src\shared\domain\criteria\Operator;
use src\shared\domain\criteria\Order;
use src\shared\domain\criteria\OrderDirection;

/**
 * Transición: traduce el par `$aWhere` / `$aOperators` (con nombres de columna) a un
 * {@see Criteria}, para que `getXxx(array, array)` pueda ser un envoltorio de `matching()`.
 *
 * - Columna → campo: con el mapa campo => columna del repositorio (invertido) o, si el campo
 *   no está remapeado, por `Enum::tryFrom($columna)`. Sin campo → {@see LegacyColumnFilter}.
 * - Parsea los formatos antiguos de valor: `"a,b"` (`OR`, `BETWEEN`), `{1,2}` (`ANY`),
 *   cadenas ya formateadas y mapas id => etiqueta (`IN`).
 * - `TXT`, `~INV` y `LIKE`/`ILIKE` → {@see LegacyRawFilter}. Trozos de `_ordre` que no son
 *   `columna [ASC|DESC]` → {@see LegacyRawOrder}.
 * - Un operador que compara con valor `null` daba SQL que nunca coincide (`c = NULL`);
 *   se conserva ese resultado con `FALSE`.
 *
 * @internal Desaparece al terminar la migración (paso 5.2).
 */
final class LegacyWhereAdapter
{
    private const OPERADORES_LIKE = ['LIKE', 'ILIKE', 'NOT LIKE', 'NOT ILIKE'];

    /** @var array<string, string> columna => valor del enum */
    private array $campoDeColumna = [];

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
        foreach ($columns as $campo => $columna) {
            $this->campoDeColumna[$columna] = (string) $campo;
        }
    }

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     */
    public function toCriteria(array $aWhere, array $aOperators = []): Criteria
    {
        $criteria = Criteria::create();
        $primero = true;
        foreach ($aWhere as $columna => $valor) {
            $columna = (string) $columna;
            if ($columna === '_ordre' || $columna === '_limit') {
                continue;
            }
            $filtro = $this->filtro($columna, (string) ($aOperators[$columna] ?? ''), $valor);
            $criteria = $primero ? $criteria->where($filtro) : $criteria->andWhere($filtro);
            $primero = false;
        }

        $ordre = $aWhere['_ordre'] ?? null;
        if (is_string($ordre) && trim($ordre) !== '') {
            foreach (self::partirPorComas($ordre) as $trozo) {
                $criteria = $criteria->addOrder($this->orden($trozo));
            }
        }

        $limit = $aWhere['_limit'] ?? null;
        if ($limit !== null && trim((string) $limit) !== '') {
            if (preg_match('/^\s*(\d+)(?:\s+OFFSET\s+(\d+))?\s*$/i', (string) $limit, $m) !== 1) {
                throw new InvalidArgumentException('_limit no válido: ' . (string) $limit);
            }
            $criteria = $criteria->limit((int) $m[1]);
            if (isset($m[2])) {
                $criteria = $criteria->offset((int) $m[2]);
            }
        }
        return $criteria;
    }

    private function campo(string $columna): ?CriteriaField
    {
        $nombre = $this->campoDeColumna[$columna] ?? null;
        if ($nombre === null) {
            // Identidad, salvo que ese campo esté remapeado a otra columna.
            if (array_key_exists($columna, $this->columns)) {
                return null;
            }
            $nombre = $columna;
        }
        /** @var CriteriaField|null $campo */
        $campo = ($this->fieldClass)::tryFrom($nombre);
        return $campo;
    }

    private function filtro(string $columna, string $op, mixed $valor): FilterExpression
    {
        $op = trim($op);

        if ($op === 'TXT') {
            return new LegacyRawFilter(is_scalar($valor) ? (string) $valor : 'TRUE');
        }
        if ($op === '~INV') {
            self::validarColumna($columna);
            return $valor === null ? new LegacyRawFilter('FALSE')
                : new LegacyRawFilter('?::text ~ ' . $columna, [$valor]);
        }
        if (in_array(strtoupper($op), self::OPERADORES_LIKE, true)) {
            self::validarColumna($columna);
            return $valor === null ? new LegacyRawFilter('FALSE')
                : new LegacyRawFilter($columna . ' ' . strtoupper($op) . ' ?', [$valor]);
        }

        [$operator, $v] = $this->operador($op, $valor);
        if ($operator->takesValue() && $v === null) {
            return new LegacyRawFilter('FALSE');
        }
        $campo = $this->campo($columna);
        return $campo !== null
            ? Filter::of($campo, $operator, $v)
            : new LegacyColumnFilter($columna, $operator, $v);
    }

    /** @return array{0: Operator, 1: mixed} */
    private function operador(string $op, mixed $valor): array
    {
        switch ($op) {
            case '':
            case '=':
                return [Operator::Eq, $valor];
            case '!=':
            case '<>':
                return [Operator::Neq, $valor];
            case '<':
                return [Operator::Lt, $valor];
            case '<=':
                return [Operator::Lte, $valor];
            case '>':
                return [Operator::Gt, $valor];
            case '>=':
                return [Operator::Gte, $valor];
            case 'IS NULL':
                return [Operator::IsNull, null];
            case 'IS NOT NULL':
                return [Operator::IsNotNull, null];
            case '~':
                return [Operator::Matches, self::texto($valor)];
            case '~*':
                return [Operator::MatchesInsensitive, self::texto($valor)];
            case '!~':
                return [Operator::NotMatches, self::texto($valor)];
            case '!~*':
                return [Operator::NotMatchesInsensitive, self::texto($valor)];
            case 'sin_acentos':
                return [Operator::MatchesUnaccented, self::texto($valor)];
            case '&':
                return [Operator::HasBits, is_numeric($valor) ? (int) $valor : null];
            case 'IN_ARRAY':
                return [Operator::ArrayContains, $valor];
            case 'OR':
            case 'ANY':
                return [Operator::In, self::lista($valor)];
            case 'IN':
                return [Operator::In, self::lista($valor, true)];
            case 'NOT IN':
                return [Operator::NotIn, self::lista($valor, true)];
            case 'BETWEEN':
                $par = self::lista($valor);
                if (count($par) !== 2) {
                    throw new InvalidArgumentException('BETWEEN requiere dos valores');
                }
                return [Operator::Between, $par];
        }
        throw new InvalidArgumentException('Operador no soportado: ' . $op);
    }

    private function orden(string $trozo): \src\shared\domain\criteria\OrderExpression
    {
        $trozo = trim($trozo);
        $re = '/^([A-Za-z_][A-Za-z0-9_]*(?:\.[A-Za-z_][A-Za-z0-9_]*)?)(?:\s+(ASC|DESC))?(?:\s+NULLS\s+(FIRST|LAST))?$/i';
        if (preg_match($re, $trozo, $m) === 1) {
            $campo = $this->campo($m[1]);
            if ($campo !== null) {
                $dir = isset($m[2]) && strtoupper($m[2]) === 'DESC' ? OrderDirection::Desc : OrderDirection::Asc;
                $nulls = isset($m[3]) ? (strtoupper($m[3]) === 'FIRST' ? NullsPosition::First : NullsPosition::Last) : null;
                return new Order($campo, $dir, $nulls);
            }
        }
        return new LegacyRawOrder($trozo);
    }

    private static function validarColumna(string $columna): void
    {
        if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*(\.[A-Za-z_][A-Za-z0-9_]*)?$/', $columna) !== 1) {
            throw new InvalidArgumentException('Nombre de columna no válido: ' . $columna);
        }
    }

    private static function texto(mixed $v): ?string
    {
        return is_scalar($v) ? (string) $v : null;
    }

    /**
     * Lista de valores desde los formatos antiguos.
     *
     * @return list<mixed>|null null si el valor es null
     */
    private static function lista(mixed $valor, bool $mapaIdEtiqueta = false): ?array
    {
        if ($valor === null) {
            return null;
        }
        if (is_array($valor)) {
            if ($mapaIdEtiqueta && $valor !== [] && array_keys($valor) !== range(0, count($valor) - 1)) {
                $claves = array_keys($valor);
                if ($claves === array_values(array_filter($claves, 'is_int'))) {
                    return $claves;
                }
            }
            return array_values($valor);
        }
        if (!is_scalar($valor)) {
            return null;
        }
        $texto = trim((string) $valor);
        if (str_starts_with($texto, '{') && str_ends_with($texto, '}')) {
            $texto = substr($texto, 1, -1);
        }
        return self::tokens($texto);
    }

    /**
     * Separa por comas respetando comillas simples o dobles. Lo entrecomillado es texto;
     * lo demás es entero si lo parece.
     *
     * @return list<int|string>
     */
    private static function tokens(string $texto): array
    {
        if (trim($texto) === '') {
            return [];
        }
        $out = [];
        $actual = '';
        $comilla = null;
        $entrecomillado = false;
        $n = strlen($texto);
        for ($i = 0; $i < $n; $i++) {
            $ch = $texto[$i];
            if ($comilla !== null) {
                if ($ch === $comilla && ($texto[$i + 1] ?? '') === $comilla) {
                    $actual .= $ch;
                    $i++;
                } elseif ($ch === $comilla) {
                    $comilla = null;
                } else {
                    $actual .= $ch;
                }
                continue;
            }
            if ($ch === "'" || $ch === '"') {
                $comilla = $ch;
                $entrecomillado = true;
            } elseif ($ch === ',') {
                $out[] = self::token($actual, $entrecomillado);
                $actual = '';
                $entrecomillado = false;
            } else {
                $actual .= $ch;
            }
        }
        $out[] = self::token($actual, $entrecomillado);
        return $out;
    }

    private static function token(string $t, bool $entrecomillado): int|string
    {
        if ($entrecomillado) {
            return $t;
        }
        $t = trim($t);
        return preg_match('/^-?\d+$/', $t) === 1 ? (int) $t : $t;
    }

    /** @return list<string> */
    private static function partirPorComas(string $texto): array
    {
        $out = [];
        $actual = '';
        $prof = 0;
        foreach (str_split($texto) as $ch) {
            if ($ch === '(') {
                $prof++;
            } elseif ($ch === ')') {
                $prof--;
            }
            if ($ch === ',' && $prof === 0) {
                $out[] = $actual;
                $actual = '';
                continue;
            }
            $actual .= $ch;
        }
        $out[] = $actual;
        return array_values(array_filter($out, static fn (string $s): bool => trim($s) !== ''));
    }
}
