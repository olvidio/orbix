<?php

namespace src\shared\infrastructure;

use src\shared\domain\contracts\EventBusInterface;

/**
 * Implementación en memoria del Event Bus
 * Gestiona el registro y despacho de eventos de dominio
 */
class InMemoryEventBus implements EventBusInterface
{
    /**
     * @var array<string, array<callable>> Mapa de eventos a sus listeners
     */
    private array $listeners = [];

    /**
     * Registra un listener para un tipo específico de evento
     *
     * @param string $eventClass El nombre de la clase del evento
     * @param callable $listener El listener que procesará el evento
     * @return void
     */
    public function subscribe(string $eventClass, callable $listener): void
    {
        if (!isset($this->listeners[$eventClass])) {
            $this->listeners[$eventClass] = [];
        }

        $this->listeners[$eventClass][] = $listener;
    }

    /**
     * Despacha un evento a todos sus listeners registrados
     *
     * @param object $event El evento de dominio a despachar
     * @return void
     */
    public function dispatch(object $event): void
    {
        $eventClass = get_class($event);

        if (!isset($this->listeners[$eventClass])) {
            return; // No hay listeners para este evento
        }

        foreach ($this->listeners[$eventClass] as $listener) {
            $listener($event);
        }
    }
}
