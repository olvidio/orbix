<?php

namespace src\shared\domain\criteria;

/**
 * Tots els fills es combinen amb AND.
 */
final class AndCriteria implements Criteria
{
    /** @param list<Criteria> $children */
    public function __construct(
        public readonly array $children = [],
    ) {
    }
}
