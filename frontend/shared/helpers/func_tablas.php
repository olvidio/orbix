<?php

namespace frontend\shared\helpers;

use frontend\shared\domain\value_objects\DateTimeLocal;
use src\configuracion\domain\value_objects\ConfigSnapshot;
use function base64_decode;
use function mb_strtoupper;
use function str_replace;
use function strnatcasecmp;

/**
 * Esta página sólo contiene funciones. Es para incluir en otras.
 *
 *
 * @package    delegacion
 * @subpackage    fichas
 * @author    Daniel Serrabou
 * @since        15/5/02.
 *
 */


function urlsafe_b64encode(string $string): string
{
    $data = base64_encode($string);
    $data = str_replace(array('+', '/', '='), array('-', '_', '.'), $data);
    return $data;
}

function urlsafe_b64decode(string $string): string
{
    $data = str_replace(array('-', '_', '.'), array('+', '/', '='), $string);
    $decoded = base64_decode($data, true);

    return is_string($decoded) ? $decoded : '';
}

/**
 * Para unificar los valores true ('t', 'true', 1, 'on...)
 *
 *
 * @author    Daniel Serrabou
 * @since        23/3/2020.
 *
 */
function is_true(mixed $val): ?bool
{
    if (is_string($val)) {
        $val = ($val === 't') ? 'true' : $val;

        return filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }
    if (is_bool($val)) {
        return $val;
    }

    return null;
}

function is_true_txt(mixed $val): string
{
    return is_true($val) ? _("si") : _("no");
}


//-----------------------------------------------------------------------------------

/**
 * Función para corregir la del php strnatcasecmp. Compara sin tener en cuenta los acentos. La uso para ordenar arrays.
 *
 */
function strsinacentocmp(string $str1, string $str2): int
{
    $acentos = array('Á', 'É', 'Í', 'Ó', 'Ú', 'À', 'È', 'Ì', 'Ò', 'Ù', 'Ä', 'Ë', 'Ï', 'Ö', 'Ü', 'Â', 'Ê', 'Î', 'Ô', 'Û', 'Ñ',
        'á', 'é', 'í', 'ó', 'ú', 'à', 'è', 'ì', 'ò', 'ù', 'ä', 'ë', 'ï', 'ö', 'ü', 'â', 'ê', 'î', 'ô', 'û', 'ñ'
    );
    $sin = array('a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'nz',
        'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u', 'nz'
    );

    $str1 = str_replace($acentos, $sin, $str1);
    $str2 = str_replace($acentos, $sin, $str2);

    return strnatcasecmp($str1, $str2);
}

/**
 * Función para corregir la del php strtoupper. No pone en mayúsculas las vocales acentuadas
 * 18-8-2022 corregido con la función mb_strtoupper. Ignoro porque no estaba así?¿
 *
 */
function strtoupper_dlb(string $texto): string
{
    //$texto=strtoupper($texto);
    $texto = mb_strtoupper($texto, 'UTF-8');
    $minusculas = array("á", "é", "í", "ó", "ú", "à", "è", "ò", "ñ");
    $mayusculas = array("Á", "É", "Í", "Ó", "Ú", "À", "È", "Ò", "Ñ");

    return str_replace($minusculas, $mayusculas, $texto);
}

/**
 * Función para saber la fecha de inicio y fin de curso según el año.
 *
 * @param array<string, int>|null $calendario Payload de `PeriodoCalendarioEscolarData` (o compatible).
 *   Si se pasa, no hace falta `$_SESSION['oConfig']` en este helper.
 */
function curso_est(string $que, int|string $any, string $tipo = 'est', ?array $calendario = null): DateTimeLocal
{
    $any = (int) $any;
    $oConfig = null;
    if ($calendario !== null) {
        switch ($tipo) {
            case 'est':
                $ini_d = (int)($calendario['dia_ini_stgr'] ?? 0);
                $ini_m = (int)($calendario['mes_ini_stgr'] ?? 0);
                $fin_d = (int)($calendario['dia_fin_stgr'] ?? 0);
                $fin_m = (int)($calendario['mes_fin_stgr'] ?? 0);
                break;
            case 'crt':
                $ini_d = (int)($calendario['dia_ini_crt'] ?? 0);
                $ini_m = (int)($calendario['mes_ini_crt'] ?? 0);
                $fin_d = (int)($calendario['dia_fin_crt'] ?? 0);
                $fin_m = (int)($calendario['mes_fin_crt'] ?? 0);
                break;
            default:
                $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
                exit($err_switch);
        }
    } else {
        $oConfig = $_SESSION['oConfig'] ?? null;
        if (!$oConfig instanceof ConfigSnapshot) {
            $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
            exit($err_switch);
        }
        switch ($tipo) {
            case 'est':
                $ini_d = $oConfig->getDiaIniStgr();
                $ini_m = $oConfig->getMesIniStgr();
                $fin_d = $oConfig->getDiaFinStgr();
                $fin_m = $oConfig->getMesFinStgr();
                break;
            case 'crt':
                $ini_d = $oConfig->getDiaIniCrt();
                $ini_m = $oConfig->getMesIniCrt();
                $fin_d = $oConfig->getDiaFinCrt();
                $fin_m = $oConfig->getMesFinCrt();
                break;
            default:
                $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
                exit($err_switch);
        }
    }
    if ($any === 0) {
        if ($calendario !== null) {
            $any = $tipo === 'crt'
                ? (int)($calendario['any_final_crt'] ?? 0)
                : (int)($calendario['any_final_est'] ?? 0);
        } elseif ($oConfig instanceof ConfigSnapshot) {
            $any = $oConfig->any_final_curs($tipo === 'crt' ? 'crt' : 'est');
        }
    }
    $any0 = $any - 1;
    //ConfigGlobal::mes_actual()=date("m");
    //if (ConfigGlobal::mes_actual()>$fin_m) ConfigGlobal::any_final_curs()++; // debe estar antes de llamar a la función.
    $inicurs = new DateTimeLocal("$any0-$ini_m-$ini_d");
    $fincurs = new DateTimeLocal("$any-$fin_m-$fin_d");

    switch ($que) {
        case "inicio":
            return $inicurs;
        case "fin":
            return $fincurs;
        default:
            $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
            exit ($err_switch);
    }

}

/**
 * @param array<string, mixed> $payload
 */
function payload_string(array $payload, string $key, string $default = ''): string
{
    if (!array_key_exists($key, $payload)) {
        return $default;
    }
    $value = $payload[$key];
    if (is_string($value)) {
        return $value;
    }
    if (is_int($value) || is_float($value) || is_bool($value)) {
        return (string)$value;
    }

    return $default;
}