<?php

namespace web;

use DateTimeZone;

/**
 * Classe per les dates. Afageix a la clase del php la vista amn num. romans.
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 26/11/2010
 */
class NullTimeLocal extends DateTimeLocal
{
    private $oData;

    public function setTime(int $hora=0, int $minuto=0, int $segundo=0, int $microsegundo=0): TimeLocal|null
    {
        return NULL;
    }


    public function getTime()
    {
        return '';
    }


    /**
     * @param string $format
     * @return string|array
     */
    public function format($format='')
    {
        return '';
    }

}
