<?php

namespace src\shared\domain\contracts;

/**
 * Interface para el Event Bus
 * Permite despachar eventos de dominio a sus respectivos listeners
 */
interface EventBusInterface
{
    /**
     * Despacha un evento a todos sus listeners registrados
     *
     * @param object $event El evento de dominio a despachar
     * @return void
     */
    public function dispatch(object $event): void;

    /**
     * Registra un listener para un tipo específico de evento
     *
     * @param string $eventClass El nombre de la clase del evento
     * @param callable $listener El listener que procesará el evento
     * @return void
     */
    public function subscribe(string $eventClass, callable $listener): void;
}
