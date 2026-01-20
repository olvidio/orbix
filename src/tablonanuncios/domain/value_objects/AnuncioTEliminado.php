<?php

namespace src\tablonanuncios\domain\value_objects;


use DateTimeZone;
use src\shared\domain\value_objects\DateTimeLocal;

final class AnuncioTEliminado extends DateTimeLocal
{
    public function __construct(string $dia_iso, string $time, ?DateTimeZone $timezone = null)
    {
        // TODO check...
        $datetime = $dia_iso . 'T' . $time;
        parent::__construct($datetime, $timezone);
    }
}