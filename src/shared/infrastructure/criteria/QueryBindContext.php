<?php

namespace src\shared\infrastructure\criteria;

/**
 * Noms de paràmetre únics per a PDO (placeholder :qb0, :qb1, …) en una sola passada.
 *
 * @internal
 */
final class QueryBindContext
{
    private int $seq = 0;

    /** @var array<string, mixed> */
    public array $params = [];

    public function bind(mixed $value): string
    {
        $name = 'qb' . ($this->seq++);
        $this->params[$name] = $value;
        return $name;
    }
}
