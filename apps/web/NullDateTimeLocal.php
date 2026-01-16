<?php

namespace web;

/**
 * Classe per les dates. Afageix a la clase del php la vista amn num. romans.
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 26/11/2010
 */
class NullDateTimeLocal extends \DateTime
{
    private $oData;

    public static function Meses()
    {
        $aMeses = array('1' => _("enero"),
            '2' => _("febrero"),
            '3' => _("marzo"),
            '4' => _("abril"),
            '5' => _("mayo"),
            '6' => _("junio"),
            '7' => _("julio"),
            '8' => _("agosto"),
            '9' => _("septiembre"),
            '10' => _("octubre"),
            '11' => _("noviembre"),
            '12' => _("diciembre")
        );
        return $aMeses;
    }

    public function getFechaLatin()
    {
        return '';
    }

    public static function createFromLocal($data = ''): string
    {
        return '';
    }

    public function getFromLocal()
    {
        return '';
    }

    static public function createFromFormat($format, $datetime, ?\DateTimeZone $timezone = NULL): \DateTime|false
    {
        return '';
    }

    public function format(string $format): string
    {
        return '';
    }

    public function formatRoman()
    {
        return '';
    }

    public function duracion($oDateDiff)
    {
        return '';
    }

    public function duracionAjustada($oDateDiff)
    {
        return '';
    }
}
