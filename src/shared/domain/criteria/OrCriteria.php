<?php

namespace src\shared\domain\criteria;

use InvalidArgumentException;

/**
 * Els fills es combinen amb OR. Si n'hi ha un, es redueix a aquest criteri.
 *
 * @phpstan-impure
 */
final class OrCriteria implements Criteria
{
    /** @param list<Criteria> $children */
    public function __construct(
        public readonly array $children,
    ) {
        if ($children === []) {
            throw new InvalidArgumentException('OrCriteria requereix almenys un fill');
        }
    }
}
