<?php

namespace src\encargossacd\application;

use src\shared\config\ConfigGlobal;
use src\encargossacd\application\services\EncargoAplicacionService;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\personas\domain\entity\Persona;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;

/**
 * Genera el listado de atencion SACD "a" (cr 9/05, Anexo2, 9.4 a).
 * Sustituye la logica que habia en `frontend/encargossacd/controller/listas_a.php`.
 *
 * Devuelve el HTML completo junto con los textos de cabecera, listos para
 * inyectarlos en la vista `listas.phtml`.
 */
final class ListasAData
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
        $any = $_SESSION['oConfig']->any_final_curs('crt');
        $inicurs = \core\curso_est('inicio', $any, 'crt')->getFromLocal();
        $fincurs = \core\curso_est('fin', $any, 'crt')->getFromLocal();

        $cabecera_left = sprintf(_('Curso:  %s - %s'), $inicurs, $fincurs);
        $cabecera_right = ConfigGlobal::mi_delef();
        $cabecera_right_2 = 'ref. cr 1/14, 10, a)';

        $oService = new EncargoAplicacionService();
        $poblacion = $oService->getLugar_dl();
        $oDateLocal = new DateTimeLocal();
        $hoy_local = $oDateLocal->getFromLocal('.');
        $lugar_fecha = "$poblacion, $hoy_local";

        $tipos_de_ctr = $sf === 1
            ? ['n', 'a[jm$]', 's[jm]']
            : ['n', 'a[jm$]', 's[jm]', 'ss'];

        $Html = '';
        $EncargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);
        $EncargoSacdRepository = $GLOBALS['container']->get(EncargoSacdRepositoryInterface::class);

        foreach ($tipos_de_ctr as $tipo_ctr_que) {
            $txt_tipo_ctr = match ($tipo_ctr_que) {
                'n' => _('1. ctr de n'),
                'a[jm$]' => _('2. ctr de agd'),
                's[jm]' => _('3. ctr de sg'),
                'ss' => _('4. ctr de sss+'),
                default => '',
            };
            if ($txt_tipo_ctr !== '') {
                $Html .= "<div class=salta_pag><table><tr><td class=grupo colspan=2>$txt_tipo_ctr</td></tr>";
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
                $sacd_titular = '';
                $sacd_suplente = '';
                $sacd_colaborador = '';
                $id_ubi = $oCentro->getId_ubi();
                $nombre_ubi = $oCentro->getNombre_ubi();
                $tipo_ctr = $oCentro->getTipo_ctr();

                $cEncargos = $EncargoRepository->getEncargos(
                    ['id_ubi' => $id_ubi, 'id_tipo_enc' => '1[0123]0.'],
                    ['id_tipo_enc' => '~'],
                ) ?: [];
                foreach ($cEncargos as $oEncargo) {
                    $id_enc = $oEncargo->getId_enc();
                    $id_tipo_enc = (string)$oEncargo->getId_tipo_enc();

                    $cEncargoSacd = $EncargoSacdRepository->getEncargosSacd(
                        ['id_enc' => $id_enc, 'f_fin' => 'null', '_ordre' => 'modo'],
                        ['f_fin' => 'IS NULL'],
                    ) ?: [];
                    foreach ($cEncargoSacd as $oEncargoSacd) {
                        $modo = $oEncargoSacd->getModo();
                        $id_nom = $oEncargoSacd->getId_nom();
                        $oPersona = Persona::findPersonaEnGlobal($id_nom);
                        if ($oPersona === null) {
                            $nom_ap = "<br>No encuentro a nadie con id_nom: $id_nom";
                        } else {
                            $nom_ap = $oPersona->getNombreApellidosCrSin();
                        }
                        if ($id_tipo_enc === '1101') {
                            $sacd_colaborador .= '<br>' . $nom_ap;
                        } else {
                            switch ((int)$modo) {
                                case 2:
                                    if ($tipo_ctr === '^njce') {
                                        $sacd_titular = sprintf('%s (%s)', $nom_ap, _('dre'));
                                    } else {
                                        $sacd_titular = $nom_ap;
                                    }
                                    break;
                                case 3:
                                    $parentesis = $tipo_ctr === '^ss' ? _('confesor') : _('no cl');
                                    if ($sf === 1) {
                                        if ($tipo_ctr === '^njce') {
                                            $sacd_titular = sprintf('%s (%s)', $nom_ap, _('dre'));
                                        } else {
                                            $sacd_titular = $nom_ap;
                                        }
                                    } else {
                                        $sacd_titular = sprintf('%s (%s)', $nom_ap, $parentesis);
                                    }
                                    break;
                                case 4:
                                    $sacd_suplente = $nom_ap;
                                    break;
                                case 5:
                                    if ($sacd_suplente === '' && $sacd_colaborador === '') {
                                        $sacd_colaborador = $nom_ap;
                                    } else {
                                        $sacd_colaborador .= '<br>' . $nom_ap;
                                    }
                                    break;
                            }
                        }
                    }
                }
                $Html .= "<tr><td class=centro>$nombre_ubi</td></tr>\n"
                    . "<tr><td>$sacd_titular</td><td>";
                if ($sacd_suplente !== '') {
                    $Html .= "<span class=suplente>$sacd_suplente</span>";
                }
                if ($sacd_colaborador !== '') {
                    $Html .= $sacd_colaborador;
                }
                $Html .= '</td></tr>';
            }
            $Html .= '</table></div>';
        }

        $Html .= '<table>';
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
