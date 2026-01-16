<?php

namespace src\misas\domain\value_objects;

use DateTimeZone;
use web\DateTimeLocal;

final class EncargoDiaTend extends DateTimeLocal
{
    public function __construct(string $dia_iso, string $time, ?DateTimeZone $timezone = null)
    {
        // TODO check...
        $datetime = $dia_iso . 'T' . $time;
        parent::__construct($datetime, $timezone);
    }
}