<?php

namespace src\encargossacd\application;

use core\ConfigGlobal;
use src\encargossacd\application\services\EncargoAplicacionService;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;

/**
 * Genera el listado de atencion SACD "b" (cr 9/05, Anexo2, 9.4 b).
 * Sustituye la logica de `frontend/encargossacd/controller/listas_b.php`.
 */
final class ListasBData
{
    /**
     * @return array{
     *     cabecera_left: string,
     *     cabecera_right: string,
     *     cabecera_right_2: string,
     *     Html: string
     * }
     */
    public static function execute(int $sf): array
    {
        $oService = new EncargoAplicacionService();

        $any = $_SESSION['oConfig']->any_final_curs('crt');
        $inicurs = \core\curso_est('inicio', $any, 'crt')->getFromLocal();
        $fincurs = \core\curso_est('fin', $any, 'crt')->getFromLocal();

        $cabecera_left = sprintf(_('Curso:  %s - %s'), $inicurs, $fincurs);
        $cabecera_right = ConfigGlobal::mi_delef();
        $cabecera_right_2 = _('ref. cr 1/14, 10, b)');

        $poblacion = $oService->getLugar_dl();
        $oDateLocal = new DateTimeLocal();
        $hoy_local = $oDateLocal->getFromLocal('.');
        $lugar_fecha = "$poblacion, $hoy_local";

        $tipos_de_ctr = ['igl', 'cgioc', '^cgi$'];

        $Html = '';
        $EncargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);
        $EncargoSacdRepository = $GLOBALS['container']->get(EncargoSacdRepositoryInterface::class);
        $PersonaDlRepository = $GLOBALS['container']->get(PersonaDlRepositoryInterface::class);

        foreach ($tipos_de_ctr as $tipo_ctr_que) {
            switch ($tipo_ctr_que) {
                case 'igl':
                    $txt_tipo_ctr = $sf === 0 ? ('1. ' . _('Iglesias')) : '';
                    $Html .= "<div><table><tr><td class=grupo colspan=2>$txt_tipo_ctr</td></tr>";
                    break;
                case 'cgioc':
                    if ($sf !== 0) {
                        $Html .= '<div><table><tr><td class=grupo colspan=2>1. ' . _('oc') . '</td></tr>';
                    } else {
                        $Html .= '<div class=salta_pag ><table><tr><td class=grupo colspan=2>2. ' . _('oc') . '</td></tr>';
                    }
                    break;
                case '^cgi$':
                    $prefijo = $sf !== 0 ? '2. ' : '3. ';
                    $Html .= '<div><table><tr><td class=grupo colspan=2>' . $prefijo . _('lp') . '</td></tr>';
                    break;
            }

            $aWhere = [
                'active' => 't',
                'tipo_ctr' => "^$tipo_ctr_que",
                '_ordre' => 'nombre_ubi',
            ];
            $aOperador = ['tipo_ctr' => '~'];

            if ($sf === 1) {
                $GesCentros = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
            } else {
                $GesCentros = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
            }
            $cCentros = $GesCentros->getCentros($aWhere, $aOperador) ?: [];

            foreach ($cCentros as $oCentro) {
                $id_ubi = $oCentro->getId_ubi();
                $nombre_ubi = $oCentro->getNombre_ubi();
                $tipo_ctr = $oCentro->getTipo_ctr();
                $cargos = [];
                $cEncargos = $EncargoRepository->getEncargos(['id_ubi' => $id_ubi]) ?: [];
                $sacds = [];
                foreach ($cEncargos as $oEncargo) {
                    $id_enc = $oEncargo->getId_enc();
                    $cEncargosSacd = $EncargoSacdRepository->getEncargosSacd(
                        ['id_enc' => $id_enc, 'f_fin' => 'null', '_ordre' => 'modo'],
                        ['f_fin' => 'IS NULL'],
                    ) ?: [];
                    $s = 0;
                    foreach ($cEncargosSacd as $oEncargoSacd) {
                        $s++;
                        $modo = (int)$oEncargoSacd->getModo();
                        $id_nom = $oEncargoSacd->getId_nom();
                        $oPersona = $PersonaDlRepository->findById($id_nom);
                        if ($oPersona === null) {
                            continue;
                        }
                        $nom_ap = $oPersona->getNombreApellidosCrSin();
                        $dedicacion_txt = $oService->dedicacion($id_nom, $id_enc);

                        if (!empty($cargos[$id_nom])) {
                            $orden_cargo = strtok((string)$cargos[$id_nom], '#');
                            $cargo = strtok('#');
                            if ($cargo === 'sacd') {
                                $cargo .= ' cl';
                            }
                            $dedicacion_txt = $dedicacion_txt !== ''
                                ? $cargo . ' ' . $dedicacion_txt
                                : $cargo;
                            $orden_2 = (int)$orden_cargo;
                        } else {
                            $orden_2 = 1000 + $s;
                        }

                        switch ($modo) {
                            case 2:
                            case 3:
                                $parentesis = match ($tipo_ctr) {
                                    'igloc' => ucfirst(_('rector')),
                                    'igl' => ucfirst(_('encargado')),
                                    default => _('capellán'),
                                };
                                $sacd_titular = $sf === 1
                                    ? $nom_ap
                                    : sprintf('%s (%s)', $nom_ap, $parentesis);
                                $sacds[$orden_2] = $sacd_titular . '#' . $dedicacion_txt;
                                break;
                            case 4:
                                $sacds[$orden_2] = $nom_ap;
                                break;
                            case 5:
                                $sacds[$orden_2] = $nom_ap . '#' . $dedicacion_txt;
                                break;
                        }
                    }
                }
                $Html .= "<tr><td class=centro>$nombre_ubi </td></tr>";
                ksort($sacds);
                foreach ($sacds as $txt) {
                    $sacd = strtok((string)$txt, '#');
                    $dedicacion = strtok('#');
                    $Html .= "<tr><td>$sacd</td><td>$dedicacion</td></tr>";
                }
            }
            $Html .= '</table></div>';
        }

        $Html .= '<table><col width=50%>';
        $Html .= "<tr><td class=izquierda></td><td class=derecha>$lugar_fecha</td></tr>";
        $Html .= '</table>';

        return [
            'cabecera_left' => $cabecera_left,
            'cabecera_right' => (string)$cabecera_right,
            'cabecera_right_2' => $cabecera_right_2,
            'Html' => $Html,
        ];
    }
}
