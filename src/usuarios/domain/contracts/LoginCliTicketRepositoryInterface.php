<?php

declare(strict_types=1);

namespace src\usuarios\domain\contracts;

/**
 * Tickets de un solo uso para que un proceso CLI lanzado desde la web (p. ej. el generador de
 * avisos) inicie sesión como el usuario que lo lanzó, sin pasar su contraseña.
 */
interface LoginCliTicketRepositoryInterface
{
    /** Crea un ticket para el usuario y esquema de la sesión actual y lo devuelve (texto opaco). */
    public function emitir(string $username, string $esquema): string;

    /**
     * Consume el ticket: la segunda vez, caducado o desconocido, devuelve null.
     *
     * @return array{username: string, esquema: string}|null
     */
    public function consumir(string $ticket): ?array;
}
