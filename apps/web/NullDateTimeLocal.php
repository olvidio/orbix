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

    static private function getFormat()
    {
        $idioma = $_SESSION['session_auth']['idioma'];
        # Si no hemos encontrado ningún idioma que nos convenga, mostramos la web en el idioma por defecto
        if (!isset($idioma)) {
            $idioma = $_SESSION['oConfig']->getIdioma_default();
        }
        $a_idioma = explode('.', $idioma);
        $code_lng = $a_idioma[0];
        //$code_char = $a_idioma[1];
        switch ($code_lng) {
            case 'en_US':
                $format = 'm/j/Y';
                break;
            default:
                $format = 'j/m/Y';
        }
        return $format;
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

    static public function createFromFormat($format, $datetime, \DateTimeZone $timezone = NULL): \DateTime|false
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
