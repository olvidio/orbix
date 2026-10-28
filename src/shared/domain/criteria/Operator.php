<?php

declare(strict_types=1);

namespace src\shared\domain\criteria;

/**
 * Operadores de filtro permitidos. Son nombres de dominio: el SQL
 * correspondiente lo decide el conversor de infraestructura.
 */
enum Operator: string
{
    case Eq = 'eq';
    case Neq = 'neq';
    case Lt = 'lt';
    case Lte = 'lte';
    case Gt = 'gt';
    case Gte = 'gte';
    case IsNull = 'is_null';
    case IsNotNull = 'is_not_null';
    case In = 'in';
    case NotIn = 'not_in';
    case Between = 'between';
    case ArrayContains = 'array_contains';
    case Matches = 'matches';
    case MatchesInsensitive = 'matches_insensitive';
    case NotMatches = 'not_matches';
    case NotMatchesInsensitive = 'not_matches_insensitive';
    case MatchesUnaccented = 'matches_unaccented';
    case HasBits = 'has_bits';

    /** Operadores que no llevan valor. */
    public function takesValue(): bool
    {
        return $this !== self::IsNull && $this !== self::IsNotNull;
    }

    /** Operadores cuyo valor es una lista. */
    public function takesList(): bool
    {
        return $this === self::In || $this === self::NotIn || $this === self::Between;
    }
}
