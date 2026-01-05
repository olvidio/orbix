<?php

namespace src\shared\infrastructure;

use PDO;
use src\shared\domain\contracts\EventBusInterface;
use src\shared\domain\contracts\UnitOfWorkInterface;
use Exception;

/**
 * Implementación de Unit of Work usando PDO
 *
 * Gestiona transacciones de base de datos y despacho de eventos de dominio
 */
class PdoUnitOfWork implements UnitOfWorkInterface
{
    private array $entities = [];
    private bool $inTransaction = false;

    public function __construct(
        private PDO $pdo,
        private EventBusInterface $eventBus
    ) {
    }

    /**
     * Ejecuta una operación dentro de una transacción
     *
     * @param callable $operation
     * @return mixed
     * @throws Exception
     */
    public function execute(callable $operation): mixed
    {
        $this->beginTransaction();

        try {
            $result = $operation($this);
            $this->commit();
            return $result;
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    /**
     * Registra una entidad para despachar sus eventos
     *
     * @param object $entity
     * @return void
     */
    public function registerEntity(object $entity): void
    {
        // Solo registrar si la entidad tiene el método pullDomainEvents
        if (method_exists($entity, 'pullDomainEvents')) {
            $this->entities[] = $entity;
        }
    }

    /**
     * Inicia una transacción
     *
     * @return void
     */
    public function beginTransaction(): void
    {
        if (!$this->inTransaction) {
            $this->pdo->beginTransaction();
            $this->inTransaction = true;
            $this->entities = [];
        }
    }

    /**
     * Confirma la transacción y despacha todos los eventos
     *
     * @return void
     */
    public function commit(): void
    {
        if ($this->inTransaction) {
            $this->pdo->commit();
            $this->inTransaction = false;

            // Despachar todos los eventos de las entidades registradas
            $this->dispatchAllEvents();
        }
    }

    /**
     * Revierte la transacción sin despachar eventos
     *
     * @return void
     */
    public function rollback(): void
    {
        if ($this->inTransaction) {
            $this->pdo->rollBack();
            $this->inTransaction = false;
            $this->entities = [];
        }
    }

    /**
     * Despacha todos los eventos de dominio de las entidades registradas
     *
     * @return void
     */
    private function dispatchAllEvents(): void
    {
        foreach ($this->entities as $entity) {
            if (method_exists($entity, 'pullDomainEvents')) {
                foreach ($entity->pullDomainEvents() as $event) {
                    $this->eventBus->dispatch($event);
                }
            }
        }

        $this->entities = [];
    }
}
