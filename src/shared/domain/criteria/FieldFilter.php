<?php

namespace src\shared\domain\criteria;

use InvalidArgumentException;

/**
 * Condició sobre un sol camp. Els operadors són cadenes compatibles amb
 * {@see \src\shared\infrastructure\persistence\postgresql\Condicion} / {@see PostgresCriteriaRenderer} (p. ex. =, <>, ~, IN, IS NULL, BETWEEN...).
 */
final class FieldFilter implements Criteria
{
    public function __construct(
        public readonly string $field,
        public readonly string $operator,
        public readonly mixed $value = null,
    ) {
        if ($field === '') {
            throw new InvalidArgumentException('El nom de camp no pot ser buit');
        }
    }
}
