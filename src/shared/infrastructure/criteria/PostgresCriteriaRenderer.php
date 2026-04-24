<?php

namespace src\shared\infrastructure\criteria;

use InvalidArgumentException;
use src\shared\domain\criteria\AndCriteria;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\FieldFilter;
use src\shared\domain\criteria\OrCriteria;

/**
 * Tradueix un arbre de {@see Criteria} a un fragment de WHERE per a PostgreSQL + paràmetres pels placeholders de PDO.
 * Els operadors de {@see FieldFilter} segueixen el mateix contracte que `src\shared\infrastructure\persistence\postgresql\Condicion::getCondicion` on escau.
 */
final class PostgresCriteriaRenderer
{
    /**
     * @return array{0: string, 1: array<string, mixed>} Fragment sense "WHERE ", i taula de paràmetres (claus sense ':').
     */
    public function render(Criteria $criteria, ?QueryBindContext $context = null): array
    {
        $context ??= new QueryBindContext();
        $sql = $this->build($criteria, $context);
        return [$sql, $context->params];
    }

    private function build(Criteria $criteria, QueryBindContext $ctx): string
    {
        if ($criteria instanceof FieldFilter) {
            return $this->emitFieldFilter($criteria, $ctx);
        }
        if ($criteria instanceof AndCriteria) {
            if ($criteria->children === []) {
                return '';
            }
            $parts = [];
            foreach ($criteria->children as $child) {
                $p = $this->build($child, $ctx);
                if ($p !== '') {
                    $parts[] = $p;
                }
            }
            if ($parts === []) {
                return '';
            }
            if (count($parts) === 1) {
                return $parts[0];
            }
            return implode(' AND ', $parts);
        }
        if ($criteria instanceof OrCriteria) {
            $n = count($criteria->children);
            if ($n === 0) {
                return '';
            }
            if ($n === 1) {
                return $this->build($criteria->children[0], $ctx);
            }
            $frags = [];
            foreach ($criteria->children as $child) {
                $f = $this->build($child, $ctx);
                if ($f === '') {
                    throw new InvalidArgumentException('Criteri buït dins d’un OR no està permès');
                }
                $frags[] = $f;
            }
            return '(' . implode(' OR ', $frags) . ')';
        }
        throw new InvalidArgumentException('Tipus de criteri no implementat: ' . $criteria::class);
    }

    private function emitFieldFilter(FieldFilter $c, QueryBindContext $ctx): string
    {
        $campo = $c->field;
        $operador = $c->operator;
        $valor = $c->value;
        if ($operador === '') {
            $operador = '=';
        }
        switch ($operador) {
            case '!=':
                $p = $ctx->bind($valor);
                return "$campo != :$p";
            case 'IS NOT NULL':
            case 'IS NULL':
                return "$campo $operador";
            case 'BETWEEN':
                [$a, $b] = $this->parseBetweenValue($valor);
                $p1 = $ctx->bind($a);
                $p2 = $ctx->bind($b);
                return "$campo BETWEEN :$p1 AND :$p2";
            case '!~':
                $p = $ctx->bind($valor);
                return "$campo::text !~ :$p";
            case '!~*':
                $p = $ctx->bind($valor);
                return "$campo::text !~* :$p";
            case '~':
                $p = $ctx->bind($valor);
                return "$campo::text ~ :$p";
            case '~*':
                $p = $ctx->bind($valor);
                return "$campo::text ~* :$p";
            case '~INV':
                $p = $ctx->bind($valor);
                return ":$p::text ~ $campo";
            case 'sin_acentos':
                $p = $ctx->bind($valor);
                return "public.sin_acentos($campo::text) ~* public.sin_acentos(:$p::text)";
            case '&':
                $p1 = $ctx->bind($valor);
                $p2 = $ctx->bind($valor);
                return "($campo & :$p1) = :$p2";
            case 'ANY':
                $p = $ctx->bind($valor);
                return "$campo = ANY (:$p)";
            case 'IN':
            case 'NOT IN':
                if (!is_array($valor) || $valor === []) {
                    throw new InvalidArgumentException("IN/NOT IN requereix un array no buit: $campo");
                }
                $ph = [];
                foreach (array_values($valor) as $v) {
                    $ph[] = ':' . $ctx->bind($v);
                }
                $list = implode(', ', $ph);
                return "$campo $operador ($list)";
            case 'TXT':
                return (string) $valor;
        }
        $p = $ctx->bind($valor);
        return "$campo $operador :$p";
    }

    private function parseBetweenValue(mixed $valor): array
    {
        if (is_array($valor) && count($valor) === 2) {
            $v = array_values($valor);
            return [$v[0], $v[1]];
        }
        if (is_string($valor)) {
            $p1 = strtok($valor, ',');
            $p2 = strtok(',');
            if ($p1 !== false && $p2 !== false) {
                return [$p1, $p2];
            }
        }
        throw new InvalidArgumentException('BETWEEN requereix un array de dos elements o string "a,b"');
    }
}
