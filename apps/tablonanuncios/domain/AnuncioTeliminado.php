<?php

namespace tablonanuncios\domain;


use DateTimeZone;
use web\DateTimeLocal;

final class AnuncioTeliminado extends DateTimeLocal
{
    public function __construct(string $dia_iso, string $time, ?DateTimeZone $timezone = null)
    {
        // TODO check...
        $datetime = $dia_iso . 'T' . $time;
        parent::__construct($datetime, $timezone);
    }
}