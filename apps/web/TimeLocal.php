<?php

namespace web;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;

class TimeLocal
{
    private DateTimeImmutable $time;

    /**
     * @param string $timeString Una cadena en formato HH:MM:SS (o similar).
     */
    public static function fromString(string $timeString): self
    {
        // Intentamos primero con segundos
        $dateTime = DateTimeImmutable::createFromFormat('H:i:s', $timeString, new DateTimeZone('UTC'));

        // Si falla, intentamos sin segundos (H:i)
        if ($dateTime === false) {
            $dateTime = DateTimeImmutable::createFromFormat('H:i', $timeString, new DateTimeZone('UTC'));
        }

        // Si sigue fallando, lanzamos error
        if ($dateTime === false) {
            throw new InvalidArgumentException("Formato de hora inválido: $timeString");
        }

        return new self($dateTime);
    }

    private function __construct(DateTimeImmutable $time)
    {
        $this->time = $time;
    }

    // Método para obtener el valor para la base de datos (PostgreSQL)
    public function toDatabaseString(): string
    {
        return $this->time->format('H:i:s');
    }

    // Método para mostrar la hora
    public function format(string $format): string
    {
        return $this->time->format($format);
    }

    // Métodos de negocio (ejemplo)
    public function isBefore(TimeLocal $other): bool
    {
        return $this->time < $other->time;
    }
}