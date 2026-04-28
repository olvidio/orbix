<?php

namespace src\casas\application;

use src\shared\config\ConfigGlobal;
use src\shared\domain\value_objects\DateTimeLocal;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\casas\domain\contracts\IngresoRepositoryInterface;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;

/**
 * Data builder: listado de actividades con plazas previstas editables.
 *
 * Sucesor de la rama de listado de `apps/casas/controller/prevision_asistentes.php`.
 * Devuelve filas y cabeceras listas para alimentar una `TablaEditable`;
 * el renderizado HTML lo hace el frontend.
 */
final class PrevisionAsistentesData
{
    /**
     * @return array{
     *   a_cabeceras: array<int,array<string,string|int>>,
     *   a_valores: array<int,array<string,mixed>>,
     *   inicio_iso: string,
     *   fin_iso: string,
     *   inicio_local: string,
     *   fin_local: string,
     *   mi_of: string,
     *   mi_sfsv: int,
     *   permitido: bool
     * }
     */
    public static function execute(array $input): array
    {
        $mi_of_in = (string)($input['mi_of'] ?? '');
        $inicio_iso = (string)($input['inicio_iso'] ?? '');
        $fin_iso = (string)($input['fin_iso'] ?? '');
        $periodo = (string)($input['periodo'] ?? '');

        $mi_of = $mi_of_in === '' ? ConfigGlobal::mi_oficina() : $mi_of_in;
        $mi_sfsv = ConfigGlobal::mi_sfsv();

        $a_cabeceras = [
            ['name' => (string)_("id_activ"), 'field' => 'id', 'visible' => 'no'],
            ['name' => (string)_("actividad"), 'field' => 'actividad', 'width' => 180],
            ['name' => (string)_("plazas"), 'title' => (string)_("plazas de la casa"), 'field' => 'plazas', 'width' => 20],
            ['name' => (string)_("mínimas"), 'title' => (string)_("plazas mínimas"), 'field' => 'plazas_min', 'width' => 20],
            [
                'name' => (string)_("previstas"),
                'title' => (string)_("plazas previstas"),
                'field' => 'previstas',
                'width' => 15,
                'editor' => 'Slick.Editors.Integer',
                'formatter' => 'cssFormatter',
            ],
        ];

        $permitido = true;
        $aWhere = ['_ordre' => 'f_ini'];
        $aOperador = ['id_tipo_activ' => '~'];

        if ($inicio_iso !== '' && $fin_iso !== '') {
            if ($periodo === 'desdeHoy') {
                $aWhere['f_fin'] = "'$inicio_iso','$fin_iso'";
                $aOperador['f_fin'] = 'BETWEEN';
            } else {
                $aWhere['f_ini'] = "'$inicio_iso','$fin_iso'";
                $aOperador['f_ini'] = 'BETWEEN';
            }
        }

        switch ($mi_of) {
            case 'sm':
                $aWhere['id_tipo_activ'] = '^' . $mi_sfsv . '1';
                break;
            case 'nax':
                $aWhere['id_tipo_activ'] = '^' . $mi_sfsv . '2';
                break;
            case 'agd':
                $aWhere['id_tipo_activ'] = '^' . $mi_sfsv . '3';
                break;
            case 'sg':
                $aWhere['id_tipo_activ'] = '^' . $mi_sfsv . '[45]';
                break;
            case 'des':
                // Condición legacy que no se mapea al operador ~: se mantiene
                // el comportamiento delegando al filtro por defecto.
                $aWhere['id_tipo_activ'] = '^(16|1141|1125|1341)';
                break;
            case 'sr':
                $aWhere['id_tipo_activ'] = '^' . $mi_sfsv . '7';
                break;
            default:
                if ($_SESSION['oConfig']->getGestionCalendario() === 'central') {
                    $aWhere['id_tipo_activ'] = '^' . $mi_sfsv;
                } else {
                    $permitido = false;
                }
        }

        $a_valores = [];
        if ($permitido) {
            $actividadRepo = $GLOBALS['container']->get(ActividadDlRepositoryInterface::class);
            $casaRepo = $GLOBALS['container']->get(CasaDlRepositoryInterface::class);
            $ingresoRepo = $GLOBALS['container']->get(IngresoRepositoryInterface::class);

            $cActividades = $actividadRepo->getActividades($aWhere, $aOperador);
            $i = 0;
            if (is_array($cActividades)) {
                foreach ($cActividades as $oActividad) {
                    $i++;
                    $id_activ = $oActividad->getId_activ();
                    $nom_activ = $oActividad->getNom_activ();
                    $id_ubi = $oActividad->getId_ubi();

                    $oIngreso = $ingresoRepo->findById($id_activ);
                    $num_asistentes_previstos = $oIngreso !== null ? $oIngreso->getNum_asistentes_previstos() : 0;

                    $oUbi = $casaRepo->findById($id_ubi);
                    $plazas = $oUbi !== null ? $oUbi->getPlazas() : 0;
                    $plazas_min = $oUbi !== null ? $oUbi->getPlazas_min() : 0;

                    $a_valores[$i] = [
                        'clase' => 'tono2',
                        'id' => $id_activ,
                        'actividad' => ['editable' => 'false', 'valor' => $nom_activ],
                        'plazas' => ['editable' => 'false', 'valor' => $plazas],
                        'plazas_min' => ['editable' => 'false', 'valor' => $plazas_min],
                        'previstas' => ['editable' => 'true', 'valor' => $num_asistentes_previstos],
                    ];
                }
            }
        }

        $inicio_local = '';
        $fin_local = '';
        if ($inicio_iso !== '' && $fin_iso !== '') {
            $inicio_local = (new DateTimeLocal($inicio_iso))->getFromLocal();
            $fin_local = (new DateTimeLocal($fin_iso))->getFromLocal();
        }

        return [
            'a_cabeceras' => $a_cabeceras,
            'a_valores' => $a_valores,
            'inicio_iso' => $inicio_iso,
            'fin_iso' => $fin_iso,
            'inicio_local' => $inicio_local,
            'fin_local' => $fin_local,
            'mi_of' => $mi_of,
            'mi_sfsv' => $mi_sfsv,
            'permitido' => $permitido,
        ];
    }
}
