<?php

declare(strict_types=1);

namespace src\shared\domain\helpers;

use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\shared\domain\value_objects\DateTimeLocal;
use function base64_decode;
use function mb_strtoupper;
use function str_replace;
use function strnatcasecmp;

/** Helpers de tablas, arrays PG, coerción de input y utilidades de texto. */
final class FuncTablasSupport
{
    /**
     * @param array<int|string, mixed> $phpArray
     */
    public static function arrayPhp2pg(array $phpArray = []): string
    {
        $phpArray_filtered = [];
        if ($phpArray !== []) {
            $phpArray_filtered = array_filter($phpArray, static fn (mixed $v): bool => $v !== null && $v !== '');
        }
        // el join no va si el array esta vacío
        if ($phpArray_filtered === []) {
            return '{}';
        }
    
        $parts = [];
        foreach ($phpArray_filtered as $value) {
            if (is_scalar($value)) {
                $parts[] = (string) $value;
            }
        }
    
        return '{' . implode(',', $parts) . '}';
    }

    /**
     * @return list<int>
     */
    public static function arrayPgInteger2php(string $postgresArray): array
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

    public static function urlsafeB64encode(string $string): string
    {
        $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('-', '_', '.'), $data);
        return $data;
    }

    public static function urlsafeB64decode(string $string): string
    {
        $data = str_replace(array('-', '_', '.'), array('+', '/', '='), $string);
        $decoded = base64_decode($data, true);
    
        return is_string($decoded) ? $decoded : '';
    }

    public static function isTrue(mixed $val): ?bool
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

    /**
     * Preferencias SlickGrid: busca por field/id estables y, en compatibilidad, por name_idx (texto del header sin espacios).
     *
     * @param array<string, mixed> $prefs
     */
    public static function slickPrefValue(array $prefs, ?string $field, ?string $id, string $nameIdx): mixed
    {
        foreach ([$field, $id, $nameIdx] as $key) {
            if ($key !== null && $key !== '' && array_key_exists($key, $prefs)) {
                return $prefs[$key];
            }
        }

        return null;
    }

    public static function isTrueTxt(mixed $val): string
    {
        return self::isTrue($val) ? _("si") : _("no");
    }

    public static function ponerNull(mixed &$valor): void
    {
        if (!$valor && $valor !== 0 && $valor !== '0') { //admito que sea 0 o '0'.
            $valor = NULL;
        }
    }

    public static function ponerEmptyOnNull(mixed &$valor): void
    {
        if ($valor === NULL) {
            $valor = '';
        }
    }

    public static function strsinacentocmp(string $str1, string $str2): int
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
     * @param array<int, array<string, mixed>> $filas
     */
    public static function usortProfesoresPorApellidos(array &$filas): void
    {
        usort($filas, static function (array $a, array $b): int {
            $ap1a = is_string($a['ap1'] ?? null) ? $a['ap1'] : '';
            $ap1b = is_string($b['ap1'] ?? null) ? $b['ap1'] : '';
            $c = self::strsinacentocmp($ap1a, $ap1b);
            if ($c !== 0) {
                return $c;
            }
            $ap2a = is_string($a['ap2'] ?? null) ? $a['ap2'] : '';
            $ap2b = is_string($b['ap2'] ?? null) ? $b['ap2'] : '';
            $c = self::strsinacentocmp($ap2a, $ap2b);
            if ($c !== 0) {
                return $c;
            }
            $noma = is_string($a['nom'] ?? null) ? $a['nom'] : '';
            $nomb = is_string($b['nom'] ?? null) ? $b['nom'] : '';

            return self::strsinacentocmp($noma, $nomb);
        });
    }

    /**
     * @param array<int, array<string, mixed>> $filas
     * @return array<int, string>
     */
    public static function profesoresOpcionesFromFilas(array $filas): array
    {
        $aOpciones = [];
        foreach ($filas as $fila) {
            $idNom = $fila['id_nom'] ?? null;
            if (is_int($idNom)) {
                $key = $idNom;
            } elseif (is_numeric($idNom)) {
                $key = (int) $idNom;
            } else {
                continue;
            }
            $apNom = $fila['ap_nom'] ?? '';
            $aOpciones[$key] = is_string($apNom) ? $apNom : (is_scalar($apNom) ? (string) $apNom : '');
        }

        return $aOpciones;
    }

    public static function strtoupperDlb(string $texto): string
    {
        //$texto=strtoupper($texto);
        $texto = mb_strtoupper($texto, 'UTF-8');
        $minusculas = array("á", "é", "í", "ó", "ú", "à", "è", "ò", "ñ");
        $mayusculas = array("Á", "É", "Í", "Ó", "Ú", "À", "È", "Ò", "Ñ");
    
        return str_replace($minusculas, $mayusculas, $texto);
    }

    /**
     * @param array<string, int|string|null>|null $calendario
     */
    public static function cursoEst(string $que, int|string $any, string $tipo = 'est', ?array $calendario = null): DateTimeLocal
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
     * @param array<string, mixed> $input
     */
    public static function inputString(array $input, string $key, string $default = ''): string
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
    public static function inputInt(array $input, string $key, int $default = 0): int
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
    public static function inputStringList(array $input, string $key): array
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

}
