<?php

namespace src\shared\traits;

use src\shared\domain\contracts\UnitOfWorkInterface;

/**
 * Trait para repositorios que necesitan despachar eventos de dominio
 *
 * Simplifica el despacho de eventos encapsulando la lógica común
 */
trait DispatchesDomainEvents
{
    protected UnitOfWorkInterface $unitOfWork;

    /**
     * Marca una entidad como nueva y registra sus eventos
     */
    protected function markAsNew(object $entity, array $datosActuales = []): void
    {
        if (method_exists($entity, 'marcarComoNueva')) {
            $entity->marcarComoNueva($datosActuales);
            $this->unitOfWork->registerEntity($entity);
        }
    }

    /**
     * Marca una entidad como modificada y registra sus eventos
     */
    protected function markAsModified(object $entity, array $datosActuales = []): void
    {
        if (method_exists($entity, 'marcarComoModificada')) {
            $entity->marcarComoModificada($datosActuales);
            $this->unitOfWork->registerEntity($entity);
        }
    }

    /**
     * Marca una entidad como eliminada y registra sus eventos
     */
    protected function markAsDeleted(object $entity, array $datosActuales): void
    {
        if (method_exists($entity, 'marcarComoEliminada')) {
            $entity->marcarComoEliminada($datosActuales);
            $this->unitOfWork->registerEntity($entity);
        }
    }
}
