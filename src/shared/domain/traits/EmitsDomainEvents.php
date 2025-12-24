<?php

namespace src\shared\domain\traits;

/**
 * Trait para entidades que emiten eventos de dominio
 * Proporciona funcionalidad básica para gestionar eventos
 */
trait EmitsDomainEvents
{
    /**
     * @var array Lista de eventos de dominio pendientes
     */
    private array $domainEvents = [];

    /**
     * Registra un evento de dominio
     *
     * @param object $event El evento a registrar
     * @return void
     */
    protected function recordEvent(object $event): void
    {
        $this->domainEvents[] = $event;
    }

    /**
     * Obtiene y limpia todos los eventos de dominio pendientes
     *
     * @return array Lista de eventos de dominio
     */
    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }

    /**
     * Limpia todos los eventos de dominio sin retornarlos
     *
     * @return void
     */
    public function clearDomainEvents(): void
    {
        $this->domainEvents = [];
    }
}
