<?php

namespace web;

use DateInterval;
use DateTime;
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
class TimeLocal extends DateTimeLocal
{
    private $oData;


    public function __construct(string $datetime = 'now', ?DateTimeZone $timezone = null)
    {
        parent::__construct($datetime, $timezone);
    }

    public function setTime(int $hora=0, int $minuto=0, int $segundo=0, int $microsegundo=0): TimeLocal
    {
        return parent::setTime($hora,$minuto,$segundo);
    }


    public function getTime()
    {
        return parent::format('H:i:s');
    }


    /**
     * @param string $format
     * @return string|array
     */
    public function format($format='')
    {
        $english = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $local = array(_("lunes"), _("martes"), _("miércoles"), _("jueves"), _("viernes"), _("sábado"), _("domingo"));
        return str_replace($english, $local, parent::format($format));
    }

}
