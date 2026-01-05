<?php

namespace src\shared\domain\contracts;

/**
 * Marker interface para identificar raíces de agregado en DDD
 *
 * Solo las entidades que implementen esta interfaz tendrán eventos de dominio
 * despachados automáticamente por el Unit of Work.
 *
 * @package orbix
 * @subpackage shared\domain\contracts
 */
interface AggregateRoot
{
    /**
     * Obtiene y limpia todos los eventos de dominio pendientes
     *
     * @return array Lista de eventos de dominio
     */
    public function pullDomainEvents(): array;
}
