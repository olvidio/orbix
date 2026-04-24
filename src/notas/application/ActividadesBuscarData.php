<?php

namespace src\notas\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\application\services\DelegacionDropdown;

/**
 * Datos necesarios para pintar el dialogo "buscar actividad" que se
 * abre desde `form_notas_de_una_persona.phtml` al modificar una nota asociada a una
 * actividad (cursos, convivencias, ...).
 *
 * Devuelve:
 * - `delegaciones`: `[cod => etiqueta]` con la delegacion preseleccionada.
 * - `actividades`: `[id_activ => nom_activ]` en +/- 3 meses (o 10 meses
 *   hacia atras y 1 hacia delante si no hay fecha de referencia).
 * - `dl_org_sel`, `id_activ_sel`: valores preseleccionados.
 */
final class ActividadesBuscarData
{
    public static function execute(array $input): array
    {
        $dl_org = (string)($input['dl_org'] ?? '');
        $f_acta_iso = (string)($input['f_acta_iso'] ?? '');
        $id_activ_sel = (int)($input['id_activ'] ?? 0);

        if (empty($dl_org)) {
            $dl_org = ConfigGlobal::mi_delef();
        }

        if (!empty($f_acta_iso)) {
            $oF_acta = new \DateTime($f_acta_iso);
            $oFIni = clone $oF_acta;
            $oF_acta->add(new \DateInterval('P3M'));
            $f_fin_iso = $oF_acta->format('Y-m-d');
            $oFIni->sub(new \DateInterval('P3M'));
            $f_ini_iso = $oFIni->format('Y-m-d');
        } else {
            $oHoy = new DateTimeLocal();
            $oHoyIni = clone $oHoy;
            $oHoy->add(new \DateInterval('P1M'));
            $f_fin_iso = $oHoy->format('Y-m-d');
            $oHoyIni->sub(new \DateInterval('P10M'));
            $f_ini_iso = $oHoyIni->format('Y-m-d');
        }

        $ActividadRepository = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
        $cActividades = $ActividadRepository->getActividades(
            [
                'f_ini' => "'$f_ini_iso','$f_fin_iso'",
                'id_tipo_activ' => '^1(12|33)',
                '_ordre' => 'f_ini',
                'dl_org' => $dl_org,
            ],
            [
                'f_ini' => 'BETWEEN',
                'id_tipo_activ' => '~',
            ]
        );

        $aActividades = [];
        foreach ($cActividades as $oActividad) {
            $aActividades[$oActividad->getId_activ()] = $oActividad->getNom_activ();
        }

        return [
            'delegaciones' => DelegacionDropdown::delegacionesURegiones(),
            'actividades' => $aActividades,
            'dl_org_sel' => $dl_org,
            'id_activ_sel' => $id_activ_sel,
            'f_ini_iso' => $f_ini_iso,
            'f_fin_iso' => $f_fin_iso,
        ];
    }
}
