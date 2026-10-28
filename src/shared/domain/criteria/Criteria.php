<?php

declare(strict_types=1);

namespace src\shared\domain\criteria;

use InvalidArgumentException;

/**
 * Objeto de consulta: filtros + orden + paginación. Sin SQL; lo traduce
 * el conversor de infraestructura. Inmutable: cada método devuelve una copia.
 *
 * Los filtros de primer nivel se unen con AND.
 */
final class Criteria
{
    /**
     * @param list<FilterExpression> $filters
     * @param list<Order> $orders
     */
    private function __construct(
        private readonly array $filters = [],
        private readonly array $orders = [],
        private readonly ?int $limit = null,
        private readonly ?int $offset = null,
    ) {
    }

    public static function create(): self
    {
        return new self();
    }

    /** Sustituye los filtros existentes por este. */
    public function where(FilterExpression $filter): self
    {
        return new self([$filter], $this->orders, $this->limit, $this->offset);
    }

    public function andWhere(FilterExpression $filter): self
    {
        return new self([...$this->filters, $filter], $this->orders, $this->limit, $this->offset);
    }

    /** Sustituye la ordenación existente. */
    public function orderBy(
        CriteriaField $field,
        OrderDirection $direction = OrderDirection::Asc,
        ?NullsPosition $nulls = null,
    ): self {
        return new self($this->filters, [new Order($field, $direction, $nulls)], $this->limit, $this->offset);
    }

    public function addOrderBy(
        CriteriaField $field,
        OrderDirection $direction = OrderDirection::Asc,
        ?NullsPosition $nulls = null,
    ): self {
        return new self(
            $this->filters,
            [...$this->orders, new Order($field, $direction, $nulls)],
            $this->limit,
            $this->offset,
        );
    }

    public function limit(?int $limit): self
    {
        if ($limit !== null && $limit < 0) {
            throw new InvalidArgumentException('limit no puede ser negativo');
        }
        return new self($this->filters, $this->orders, $limit, $this->offset);
    }

    public function offset(?int $offset): self
    {
        if ($offset !== null && $offset < 0) {
            throw new InvalidArgumentException('offset no puede ser negativo');
        }
        return new self($this->filters, $this->orders, $this->limit, $offset);
    }

    /** @return list<FilterExpression> */
    public function filters(): array
    {
        return $this->filters;
    }

    /** @return list<Order> */
    public function orders(): array
    {
        return $this->orders;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }
}
