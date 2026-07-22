<?php

declare(strict_types=1);

namespace frontend\shared\helpers;

use frontend\shared\domain\value_objects\DateTimeLocal;
use src\configuracion\domain\value_objects\ConfigSnapshot;

/**
 * Utilidades frontend; delega en {@see \src\shared\domain\helpers\FuncTablasSupport}
 * salvo {@see cursoEst()} (VO DateTimeLocal del frontend).
 */
final class FuncTablasSupport
{
    public static function urlsafeB64encode(string $string): string
    {
        return \src\shared\domain\helpers\FuncTablasSupport::urlsafeB64encode($string);
    }

    public static function urlsafeB64decode(string $string): string
    {
        return \src\shared\domain\helpers\FuncTablasSupport::urlsafeB64decode($string);
    }

    public static function isTrue(mixed $val): ?bool
    {
        return \src\shared\domain\helpers\FuncTablasSupport::isTrue($val);
    }

    public static function isTrueTxt(mixed $val): string
    {
        return \src\shared\domain\helpers\FuncTablasSupport::isTrueTxt($val);
    }

    public static function strsinacentocmp(string $str1, string $str2): int
    {
        return \src\shared\domain\helpers\FuncTablasSupport::strsinacentocmp($str1, $str2);
    }

    public static function strtoupperDlb(string $texto): string
    {
        return \src\shared\domain\helpers\FuncTablasSupport::strtoupperDlb($texto);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function payloadString(array $payload, string $key, string $default = ''): string
    {
        return \src\shared\domain\helpers\FuncTablasSupport::inputString($payload, $key, $default);
    }

    public static function ponerEmptyOnNull(mixed &$valor): void
    {
        \src\shared\domain\helpers\FuncTablasSupport::ponerEmptyOnNull($valor);
    }

    /**
     * @param array<string, mixed>|null $calendario
     */
    public static function cursoEst(string $que, int|string $any, string $tipo = 'est', ?array $calendario = null): DateTimeLocal
    {
        $any = PayloadCoercion::int($any);
        $oConfig = null;
        if ($calendario !== null) {
            switch ($tipo) {
                case 'est':
                    $ini_d = PayloadCoercion::int($calendario['dia_ini_stgr'] ?? 0);
                    $ini_m = PayloadCoercion::int($calendario['mes_ini_stgr'] ?? 0);
                    $fin_d = PayloadCoercion::int($calendario['dia_fin_stgr'] ?? 0);
                    $fin_m = PayloadCoercion::int($calendario['mes_fin_stgr'] ?? 0);
                    break;
                case 'crt':
                    $ini_d = PayloadCoercion::int($calendario['dia_ini_crt'] ?? 0);
                    $ini_m = PayloadCoercion::int($calendario['mes_ini_crt'] ?? 0);
                    $fin_d = PayloadCoercion::int($calendario['dia_fin_crt'] ?? 0);
                    $fin_m = PayloadCoercion::int($calendario['mes_fin_crt'] ?? 0);
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
                    ? PayloadCoercion::int($calendario['any_final_crt'] ?? 0)
                    : PayloadCoercion::int($calendario['any_final_est'] ?? 0);
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
}
