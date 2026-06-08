<?php

namespace src\shared\domain\criteria;

/**
 * Converteix el parell d'arrays on solien anar criteri + operador (p. ex. aWhere / aOperador) en un {@see AndCriteria}.
 * Les claus especials _ordre, _limit s'ignoren (són metadada de la consulta, no predicats).
 */
final class LegacyArrayCriteriaFactory
{
    /**
     * @param array<string, mixed> $where
     * @param array<string, string> $operators
     */
    public static function fromWhereAndOperatorArrays(
        array $where,
        array $operators = [],
    ): AndCriteria {
        $children = [];
        foreach ($where as $field => $value) {
            if ($field === '_ordre' || $field === '_limit') {
                continue;
            }
            $op = (string) ($operators[$field] ?? '=');
            if ($op === '') {
                $op = '=';
            }
            if (in_array($op, ['IS NULL', 'IS NOT NULL'], true)) {
                $children[] = new FieldFilter((string) $field, $op, null);
            } else {
                $children[] = new FieldFilter((string) $field, $op, $value);
            }
        }
        return new AndCriteria($children);
    }
}
