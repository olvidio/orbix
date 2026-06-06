<?php

namespace src\shared\domain\helpers;

use src\shared\domain\value_objects\DateTimeLocal;
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

/**
 * para convertir los arrays de php para introducir en el postgresql.
 *
 * @param array
 * @return string pg_array
 */
function array_php2pg($phpArray = [])
{
    if (!empty($phpArray) && is_array($phpArray)) {
        $phpArray_filtered = array_filter($phpArray);
    }
    // el join no va si el array esta vacío
    if (empty($phpArray_filtered)) {
        return "{}";
    } else {
        return "{" . implode(",", $phpArray_filtered) . "}";
    }
}

/**
 * para convertir los arrays que provienen del postgresql a php.
 * En este caso, el array es integer[]
 *
 * @param string $postgresArray
 * @return array
 */
function array_pgInteger2php($postgresArray): array
{
    if (empty($postgresArray)) {
        return [];
    }
    $str_csv = trim($postgresArray, "{}");
    if (empty($str_csv)) {
        $phpArray = [];
    } else {
        $phpArrayString = explode(',', $str_csv);
        $phpArray = array_map('intval', $phpArrayString);
    }
    return $phpArray;
}

function urlsafe_b64encode($string)
{
    $data = base64_encode($string);
    $data = str_replace(array('+', '/', '='), array('-', '_', '.'), $data);
    return $data;
}

function urlsafe_b64decode($string)
{
    $data = str_replace(array('-', '_', '.'), array('+', '/', '='), $string);
    return base64_decode($data);
}

/**
 * Para unificar los valores true ('t', 'true', 1, 'on...)
 *
 *
 * @author    Daniel Serrabou
 * @since        23/3/2020.
 *
 */
function is_true($val)
{
    if (is_string($val)) {
        $val = ($val === 't') ? 'true' : $val;
        $boolval = filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    } else {
        $boolval = $val;
    }

    return $boolval;
}

function is_true_txt($val)
{
    return is_true($val) ? _("si") : _("no");
}

/**
 * Para poner null en los valores vacíos de un array
 *
 *
 * @author    Daniel Serrabou
 * @since        28/10/09.
 *
 */
function poner_null(&$valor)
{
    if (!$valor && $valor !== 0 && $valor !== '0') { //admito que sea 0 o '0'.
        $valor = NULL;
    }
}

/**
 * Para poner string empty en los valores null de un array,
 * necesario para la función http_build_query, que no pone
 * los parámetros con valor null
 *
 * @author    Daniel Serrabou
 * @since        26/10/18.
 *
 */
function poner_empty_on_null(&$valor)
{
    if ($valor === NULL) {
        $valor = '';
    }
}

//-----------------------------------------------------------------------------------

/**
 * Función para corregir la del php strnatcasecmp. Compara sin tener en cuenta los acentos. La uso para ordenar arrays.
 *
 */
function strsinacentocmp($str1, $str2): int
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
 * Ordena filas de profesor por apellido1, apellido2 y nombre (sin acentos).
 *
 * @param list<array{ap1: string, ap2?: string, nom: string}> $filas
 */
function usort_profesores_por_apellidos(array &$filas): void
{
    usort($filas, static function (array $a, array $b): int {
        $c = strsinacentocmp($a['ap1'], $b['ap1']);
        if ($c !== 0) {
            return $c;
        }
        $c = strsinacentocmp($a['ap2'] ?? '', $b['ap2'] ?? '');
        if ($c !== 0) {
            return $c;
        }

        return strsinacentocmp($a['nom'], $b['nom']);
    });
}

/**
 * Función para corregir la del php strtoupper. No pone en mayúsculas las vocales acentuadas
 * 18-8-2022 corregido con la función mb_strtoupper. Ignoro porque no estaba así?¿
 *
 */
function strtoupper_dlb($texto)
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
function curso_est($que, $any, $tipo = 'est', ?array $calendario = null)
{
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
        switch ($tipo) {
            case "est":
                $ini_d = $_SESSION['oConfig']->getDiaIniStgr();
                $ini_m = $_SESSION['oConfig']->getMesIniStgr();
                $fin_d = $_SESSION['oConfig']->getDiaFinStgr();
                $fin_m = $_SESSION['oConfig']->getMesFinStgr();
                break;
            case "crt":
                $ini_d = $_SESSION['oConfig']->getDiaIniCrt();
                $ini_m = $_SESSION['oConfig']->getMesIniCrt();
                $fin_d = $_SESSION['oConfig']->getDiaFinCrt();
                $fin_m = $_SESSION['oConfig']->getMesFinCrt();
                break;
            default:
                $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
                exit ($err_switch);
        }
    }
    if (empty($any)) {
        if ($calendario !== null) {
            $any = $tipo === 'crt'
                ? (int)($calendario['any_final_crt'] ?? 0)
                : (int)($calendario['any_final_est'] ?? 0);
        } else {
            $any = $_SESSION['oConfig']->any_final_curs($tipo === 'crt' ? 'crt' : 'est');
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
 * @param array<string, mixed> $input
 */
function input_string(array $input, string $key, string $default = ''): string
{
    if (!array_key_exists($key, $input)) {
        return $default;
    }
    $value = $input[$key];
    if (is_string($value)) {
        return $value;
    }
    if (is_int($value) || is_float($value) || is_bool($value)) {
        return (string)$value;
    }

    return $default;
}

/**
 * @param array<string, mixed> $input
 */
function input_int(array $input, string $key, int $default = 0): int
{
    if (!array_key_exists($key, $input)) {
        return $default;
    }
    $value = $input[$key];
    if (is_int($value)) {
        return $value;
    }
    if (is_string($value) && is_numeric($value)) {
        return (int)$value;
    }
    if (is_float($value)) {
        return (int)$value;
    }

    return $default;
}

/**
 * @param array<string, mixed> $input
 * @return list<string>
 */
function input_string_list(array $input, string $key): array
{
    if (!isset($input[$key]) || !is_array($input[$key])) {
        return [];
    }
    $result = [];
    foreach ($input[$key] as $item) {
        if (is_string($item)) {
            $result[] = $item;
        } elseif (is_int($item) || is_float($item)) {
            $result[] = (string) $item;
        }
    }

    return $result;
}