<?php

namespace src\planning\application;

use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\planning\application\ActividadesDePersonaService;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;

/**
 * Personas + actividades agrupadas por centro para `planning_ctr_select`.
 */
final class PlanningCtrSelectData
{
    /**
     * @param array<string, mixed> $post
     * @return array{msg_txt: string, cabecera_title: string, a_actividades2: array}
     */
    public static function execute(array $post, DateTimeLocal $oIniPlanning, string $inicio_local, string $fin_iso, string $inicio_iso): array
    {
        $Qsacd = (string)($post['sacd'] ?? '');
        $Qctr = (string)($post['ctr'] ?? '');
        $Qtodos_n = (string)($post['todos_n'] ?? '');
        $Qtodos_agd = (string)($post['todos_agd'] ?? '');
        $Qtodos_s = (string)($post['todos_s'] ?? '');

        $aWhereP = ['situacion' => 'A'];
        if ($Qsacd === '') {
            $aWhereP['sacd'] = 'f';
        }

        $msg_txt = '';
        $cabecera_title = '';
        $PersonaDlRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);
        $cPersonas = [];
        if ($Qctr !== '') {
            $nom_ubi = str_replace('+', '\\+', $Qctr);
            $aWhere = ['nombre_ubi' => '^' . $nom_ubi];
            $aOperador = ['nombre_ubi' => 'sin_acentos'];
            $GesCentros = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
            $cCentros = $GesCentros->getCentros($aWhere, $aOperador);
            if (!empty($cCentros)) {
                foreach ($cCentros as $oCentro) {
                    $id_ubi = $oCentro->getId_ubi();
                    $nombre_ubi = $oCentro->getNombre_ubi();
                    $cabecera_title = ucfirst(sprintf(_("personas de: %s"), $nombre_ubi));
                    $aWhereP['id_ctr'] = $id_ubi;
                    $aWhereP['_ordre'] = 'apellido1';
                    $cPersonas2 = $PersonaDlRepository->getPersonas($aWhereP);
                    if (is_array($cPersonas2) && count($cPersonas2) >= 1) {
                        $cPersonas = array_merge($cPersonas, $cPersonas2);
                    } else {
                        $msg_txt .= sprintf(_("No encuentro personas para %s"), $nombre_ubi);
                        $msg_txt .= '<br>';
                    }
                }
            } else {
                $msg_txt = _("No encuentro este ctr");
            }
        } else {
            $cabecera_title = ucfirst(_("centros"));
            $aWhereP['id_tabla'] = 'n';
            if ($Qtodos_n !== '') {
                $aWhereP['id_tabla'] = 'n';
            }
            if ($Qtodos_agd !== '') {
                $aWhereP['id_tabla'] = 'a';
            }
            if ($Qtodos_s !== '') {
                $aWhereP['id_tabla'] = 's';
            }
            $aWhereP['_ordre'] = 'id_ctr, apellido1';
            $cPersonas = $PersonaDlRepository->getPersonas($aWhereP);
        }

        $a_actividades2 = ActividadesDePersonaService::actividadesPorPersona(
            $cPersonas,
            $fin_iso,
            $inicio_iso,
            $oIniPlanning,
            $inicio_local,
            agruparPorCentro: true
        );

        return [
            'msg_txt' => $msg_txt,
            'cabecera_title' => $cabecera_title,
            'a_actividades2' => $a_actividades2,
        ];
    }
}
