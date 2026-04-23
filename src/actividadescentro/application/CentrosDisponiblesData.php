<?php

namespace src\actividadescentro\application;

use src\actividadescentro\domain\contracts\CentroEncargadoRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;

/**
 * Devuelve la lista de centros disponibles (candidatos) para asignar como
 * encargados de una actividad segun el tipo: sg / sr / nagd / sssc / sfsg /
 * sfsr / sfnagd.
 *
 * Para `tipo=sg` la respuesta incluye tambien, por centro, el numero de
 * actividades en el periodo `[inicio, fin]` y la diferencia de dias con la
 * proxima/anterior actividad del centro respecto a `f_ini_act`.
 *
 * Sucesor de las ramas `nuevo_*` del dispatcher legacy `activ_ctr_ajax.php`.
 * Por la excepcion tolerable de `refactor.md` (dispatcher de lectura con
 * ramas que comparten contrato JSON) se agrupan en un unico use case con
 * parametro `tipo`.
 */
final class CentrosDisponiblesData
{
    public const TIPOS_VALIDOS = ['sg', 'sr', 'nagd', 'sssc', 'sfsg', 'sfsr', 'sfnagd'];

    public static function execute(array $input): array
    {
        $tipo = (string)($input['tipo'] ?? '');
        $id_activ = (int)($input['id_activ'] ?? 0);
        if (!in_array($tipo, self::TIPOS_VALIDOS, true)) {
            return [
                'tipo' => $tipo,
                'id_activ' => $id_activ,
                'centros' => [],
                'error' => _("tipo no valido"),
            ];
        }

        [$aWhere, $aOperador, $usarCentroEllas] = self::filtros($tipo);
        $repo = $usarCentroEllas
            ? $GLOBALS['container']->get(CentroEllasRepositoryInterface::class)
            : $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        $cCentros = $repo->getCentros($aWhere, $aOperador);
        if (!is_array($cCentros)) {
            $cCentros = [];
        }

        $centros = [];
        // Info adicional solo para sg.
        if ($tipo === 'sg') {
            $inicio = (string)($input['inicio'] ?? '');
            $fin = (string)($input['fin'] ?? '');
            $f_ini_act = (string)($input['f_ini_act'] ?? '');
            $f_ini_act_iso = '';
            if (!empty($f_ini_act)) {
                $oDate = DateTimeLocal::createFromLocal($f_ini_act);
                $f_ini_act_iso = $oDate->getIso();
            }
            $periodo = (!empty($inicio) && !empty($fin))
                ? "f_ini BETWEEN '" . $inicio . "' AND '" . $fin . "'"
                : '';

            $repoEncargado = $GLOBALS['container']->get(CentroEncargadoRepositoryInterface::class);
            foreach ($cCentros as $oCentro) {
                $id_ubi = (int)$oCentro->getId_ubi();
                $num_activ = 0;
                if ($periodo !== '') {
                    $cActivs = $repoEncargado->getActividadesDeCentros($id_ubi, $periodo);
                    $num_activ = is_array($cActivs) ? count($cActivs) : 0;
                }
                $dif = '';
                if ($f_ini_act_iso !== '') {
                    $dif = $repoEncargado->getProximasActividadesDeCentro($id_ubi, $f_ini_act_iso);
                }
                $centros[] = [
                    'id_ubi' => $id_ubi,
                    'nombre_ubi' => (string)$oCentro->getNombre_ubi(),
                    'num_actividades_periodo' => $num_activ,
                    'dif_dias' => $dif,
                ];
            }
        } else {
            foreach ($cCentros as $oCentro) {
                $centros[] = [
                    'id_ubi' => (int)$oCentro->getId_ubi(),
                    'nombre_ubi' => (string)$oCentro->getNombre_ubi(),
                ];
            }
        }

        return [
            'tipo' => $tipo,
            'id_activ' => $id_activ,
            'centros' => $centros,
        ];
    }

    /**
     * @return array{0: array, 1: array, 2: bool}  [aWhere, aOperador, usarCentroEllas]
     */
    private static function filtros(string $tipo): array
    {
        $aWhere = ['active' => 't', '_ordre' => 'nombre_ubi'];
        $aOperador = [];
        $usarCentroEllas = false;

        switch ($tipo) {
            case 'sg':
                $aWhere['tipo_ctr'] = '^s[^s]*';
                $aOperador['tipo_ctr'] = '~';
                break;
            case 'sr':
                $aWhere['tipo_labor'] = '512';
                $aOperador['tipo_labor'] = '&';
                break;
            case 'nagd':
                $aWhere['tipo_ctr'] = '^[na]';
                $aOperador['tipo_ctr'] = '~';
                break;
            case 'sssc':
                // Misma regla que `ListCtrData` (`ctr_sssc`): centros SSS+ = `ss` o `sss`.
                // `^sss` dejaba fuera `ss` y no coincide con el criterio de ubis.
                $aWhere['tipo_ctr'] = '^(ss|sss)$';
                $aOperador['tipo_ctr'] = '~';
                break;
            case 'sfsg':
                $aWhere['tipo_labor'] = '64';
                $aOperador['tipo_labor'] = '&';
                $usarCentroEllas = true;
                break;
            case 'sfsr':
                $aWhere['tipo_labor'] = '512';
                $aOperador['tipo_labor'] = '&';
                $usarCentroEllas = true;
                break;
            case 'sfnagd':
                $aWhere['tipo_ctr'] = '^[na]';
                $aOperador['tipo_ctr'] = '~';
                $usarCentroEllas = true;
                break;
        }
        return [$aWhere, $aOperador, $usarCentroEllas];
    }
}
