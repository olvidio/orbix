<?php

namespace src\shared\domain\value_objects;

use DateTimeImmutable;

class NullTimeLocal
{
    private DateTimeImmutable $time;

    /**
     * @param string $timeString Una cadena en formato HH:MM:SS (o similar).
     */
    public static function fromString(string $timeString): self
    {
        return self;
    }

    private function __construct(DateTimeImmutable $time)
    {
        $this->time = $time;
    }

    // Método para obtener el valor para la base de datos (PostgreSQL)
    public function toDatabaseString(): string
    {
        return '';
    }

    // Método para mostrar la hora
    public function format(string $format): string
    {
        return '';
    }

    // Métodos de negocio (ejemplo)
    public function isBefore(NullTimeLocal $other): bool
    {
        return '';
    }
}