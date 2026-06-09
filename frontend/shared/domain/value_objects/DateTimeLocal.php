<?php

namespace frontend\shared\domain\value_objects;

use DateInterval;
use DateTime;
use DateTimeInterface;
use DateTimeZone;

/**
 * Réplica del VO de dominio para uso en frontend (autoload frontend\*, sin dependencia desde src/shared).
 *
 * Mantener sincronizado con {@see src\shared\domain\value_objects\DateTimeLocal} cuando cambie la lógica.
 *
 * Classe per les dates. Afageix a la clase del php la vista amn num. romans.
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 26/11/2010
 */
class DateTimeLocal extends DateTime
{
    /** @return array<int|string, string> */
    public static function Meses(): array
    {
        $aMeses = [
            '1' => _("enero"),
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
        ];
        return $aMeses;
    }

    /** @return array<int|string, string> */
    public static function Meses_latin(): array
    {
        $aMes_latin = [
            '1' => 'ianuario',
            '2' => 'februario',
            '3' => 'martio',
            '4' => 'aprili',
            '5' => 'maio',
            '6' => 'iunio',
            '7' => 'iulio',
            '8' => 'augusto',
            '9' => 'septembri',
            '10' => 'octobri',
            '11' => 'novembri',
            '12' => 'decembri',
        ];
        return $aMes_latin;
    }

    public function getFechaLatin(): string
    {
        $mes_latin = self::Meses_latin();

        $dia = parent::format('j'); //sin ceros iniciales
        $mes = parent::format('n'); //sin ceros iniciales
        $any = parent::format('Y');

        return "die " . $dia . " mense  " . $mes_latin[$mes] . "  anno  " . $any;
    }

    /**
     * Devuelve el formato de fecha según el idioma del usuario (d/m/y, o m/d/Y)
     *
     * @param string $separador separador entre dia, mes año
     */
    public static function getFormat(string $separador = '/'): string
    {
        $sessionAuth = $_SESSION['session_auth'] ?? null;
        $idioma = is_array($sessionAuth) ? ($sessionAuth['idioma'] ?? null) : null;
        # Si no hemos encontrado ningún idioma que nos convenga, mostramos la web en el idioma por defecto
        if (!isset($idioma)) {
            $config = $_SESSION['oConfig'] ?? null;
            if (is_object($config) && method_exists($config, 'getIdioma_default')) {
                $idioma = $config->getIdioma_default();
            }
        }
        if (!is_string($idioma) || $idioma === '') {
            $idioma = 'es_ES';
        }
        $a_idioma = explode('.', $idioma);
        $code_lng = $a_idioma[0];
        //$code_char = $a_idioma[1];
        switch ($code_lng) {
            case 'en_US':
                $format = 'n' . $separador . 'j' . $separador . 'Y';
                break;
            default:
                $format = 'j' . $separador . 'n' . $separador . 'Y';
        }
        return $format;
    }

    /**
     * @return static|null|false
     */
    public static function createFromLocal(mixed $data): static|null|false
    {
        // para distinguir false de null. En caso de no tener, devuelve null
        if ($data === null || $data === '' || $data === false) {
            return null;
        }
        if (!is_string($data)) {
            return false;
        }
        // Cambiar '-' por '/':
        $data = str_replace('-', '/', $data);
        $format = self::getFormat();

        $className = static::class;
        $extnd_dt = new $className();
        $parent_dt = parent::createFromFormat($format, $data);

        if (!$parent_dt) {
            return false;
        }

        $extnd_dt->setTimestamp($parent_dt->getTimestamp());
        /* corregir en el caso que el año tenga dos dígitos
         * No sirve para el siglo I (0-99) ;-) */
        $yy = $extnd_dt->format('y');
        $yyyy = $extnd_dt->format('Y');
        if (($yyyy - $yy) == 0) {
            $currentY4 = date('Y');
            $currentY2 = date('y');
            $currentMilenium = $currentY4 - $currentY2;

            $extnd_dt->add(new DateInterval('P' . $currentMilenium . 'Y'));
        }

        return $extnd_dt;
    }

    public function getIsoTime(): string
    {
        return parent::format('Y-m-d H:i:s');
    }

    public function getIso(): string
    {
        return parent::format('Y-m-d');
    }

    /**
     * Devuelve la fecha en el formato local (según el idioma del usuario)
     *
     * @param string $separador (.-/)
     */
    public function getFromLocalHora(string $separador = '/'): string
    {
        $format = self::getFormat($separador);
        $format .= ' H:i:s';
        return parent::format($format);
    }

    /**
     * devolver null para las 00:00
     */
    public function getHora(): ?string
    {
        $str_hora = $this->format('H:i');
        if ($str_hora === '00:00') {
            return null;
        }
        return $str_hora;
    }

    /**
     * Devuelve la fecha en el formato local (según el idioma del usuario)
     *
     * @param string $separador (.-/)
     */
    public function getFromLocal(string $separador = '/'): string
    {
        $format = self::getFormat($separador);
        return parent::format($format);
    }

    public static function createFromFormat(string $format = '', string $datetime = '', ?DateTimeZone $timezone = null): DateTimeLocal|false
    {
        $className = static::class;
        $extnd_dt = new $className();
        $parent_dt = parent::createFromFormat($format, $datetime, $timezone);

        if (!$parent_dt) {
            return false;
        }
        $extnd_dt->setTimestamp($parent_dt->getTimestamp());
        return $extnd_dt;
    }

    public function format(string $format = ''): string
    {
        $english = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $local = array(_("lunes"), _("martes"), _("miércoles"), _("jueves"), _("viernes"), _("sábado"), _("domingo"));
        return str_replace($english, $local, parent::format($format));
    }

    public function formatRoman(): string
    {
        $a_num_romanos = array('1' => "I", '2' => "II", '3' => "III", '4' => "IV", '5' => "V", '6' => "VI", '7' => "VII", '8' => "VIII", '9' => "IX",
            '10' => "X", '11' => "XI", '12' => "XII");
        $dia = parent::format('j');
        $mes = parent::format('n');
        $any = parent::format('y');
        return "$dia." . $a_num_romanos[$mes] . ".$any";
    }

    /**
     * Calcula la diferencia (expresada en días) con la fecha que se le pasa como parámetro
     * Devuelve un número con dos decimales (p.ej. 2,43)
     */
    public function duracion(DateTimeInterface $oDateOtra): float
    {
        /* Formato de DateInterval:
         * a 	Total number of days as a result of a DateTime::diff() or (unknown) otherwise
         * h 	Hours, numeric 	1, 3, 23
         * i 	Minutes, numeric 	1, 3, 59
         * s 	Seconds, numeric
         */
        $interval = $this->diff($oDateOtra);
        $horas = (int)$interval->format('%a') * 24 + (int)$interval->format('%h') + (int)$interval->format('%i') / 60 + (int)$interval->format('%s') / 3600;
        return round($horas / 24, 2);
    }

    /**
     * Calcula la diferencia (expresada en días) con la fecha que se le pasa como parámetro
     * Devuelve el número con un decimal redondeado a 0 o 0,5 (p.ej. 2,0 ó 2,5)
     */
    public function duracionAjustada(DateTimeInterface $oDateOtra): int
    {
        /* Formato de DateInterval:
         * a 	Total number of days as a result of a DateTime::diff() or (unknown) otherwise
         * h 	Hours, numeric 	1, 3, 23
         * i 	Minutes, numeric 	1, 3, 59
         * s 	Seconds, numeric
         */
        $interval = $this->diff($oDateOtra);
        $horas = (int)$interval->format('%a') * 24 + (int)$interval->format('%h') + (int)$interval->format('%i') / 60 + (int)$interval->format('%s') / 3600;
        $dias_con_decimales = $horas / 24;
        // si existe un decimal, redondea al entero superior.
        return (int) round($dias_con_decimales);
    }

    /**
     * IMPORTANTE: esta función suma medio dia (12h) a la función duracionAjustada . NO SÉ PORQUÉ!!!
     *
     * Calcula la diferencia (expresada en días) con la fecha que se le pasa como parámetro
     * Devuelve el número con un decimal redondeado a 0 o 0,5 (p.ej. 2,0 ó 2,5)
     */
    public function duracionAjustadaAumentada(DateTimeInterface $oDateOtra): float|int
    {
        /* Formato de DateInterval:
         * a 	Total number of days as a result of a DateTime::diff() or (unknown) otherwise
         * h 	Hours, numeric 	1, 3, 23
         * i 	Minutes, numeric 	1, 3, 59
         * s 	Seconds, numeric
         */
        $interval = $this->diff($oDateOtra);
        $horas = (int)$interval->format('%a') * 24 + (int)$interval->format('%h') + (int)$interval->format('%i') / 60 + (int)$interval->format('%s') / 3600;
        $horas = $horas + 12;
        $dias_con_decimales = $horas / 24;
        $dias_enteros = (int) floor($dias_con_decimales);
        $decimales = round(($dias_con_decimales - $dias_enteros), 1);
        if ($decimales > 0.1) {
            $decimal_redondeado = 0.5;
        } else {
            $decimal_redondeado = 0;
        }
        return ($dias_enteros + $decimal_redondeado);
    }

    /**
     * comprueba que no exista solape o vacíos entre periodos.
     * oInicio y oFin deben ser objetos DatetimeLocal.
     *
     * @param array<int, array{inicio: self, fin: self, Descripcion?: string}> $cPeriodos
     */
    public function comprobarSolapes(array $cPeriodos): bool|string
    {
        $i = 0;
        $error_txt = '';
        foreach ($cPeriodos as $aPeriodo) {
            $i++;
            $oF_ini = $aPeriodo['inicio'];
            $oF_fin = $aPeriodo['fin'];

            //Fecha fin periodo debe ser posterior a fecha inicio
            if ($oF_fin === $oF_ini) {
                $fecha = $oF_fin->getFromLocal();
                $error_txt .= empty($error_txt) ? '' : '<br>';
                $error_txt .= sprintf(_("la fecha fin es igual a la fecha inicio en el periodo %s: %s"), $i, $fecha);
            }
            if ($oF_fin < $oF_ini) {
                $fecha = $oF_ini->getFromLocal();
                $error_txt .= empty($error_txt) ? '' : '<br>';
                $error_txt .= sprintf(_("la fecha fin es menor que la fecha inicio en el periodo %s: %s"), $i, $fecha);
            }

            // Siguiente
            if ($aPeriodoNext = next($cPeriodos)) {
                $oF_ini_next = $aPeriodoNext['inicio'];
                $interval = $oF_fin->diff($oF_ini_next);
                if ($interval->format('%r%d') > 1) {
                    $fecha = $oF_fin->getFromLocal();
                    $error_txt .= empty($error_txt) ? '' : '<br>';
                    $error_txt .= sprintf(_("dias libres cerca de %s"), $fecha);
                }
                if ($interval->format('%r%d') < 1) {
                    $fecha = $oF_fin->getFromLocal();
                    $error_txt .= empty($error_txt) ? '' : '<br>';
                    $error_txt .= sprintf(_("hay un solape cerca de %s"), $fecha);
                }
            }
        }
        if (empty($error_txt)) {
            return false;
        }

        return $error_txt;
    }
}
