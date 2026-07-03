<?php

namespace src\encargossacd\application;

use src\configuracion\domain\value_objects\ConfigSnapshot;

use src\shared\config\ConfigGlobal;
use src\encargossacd\application\services\EncargoAplicacionService;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;

/**
 * Listado de exigencias SACD por centro/iglesia.
 * Sustituye la logica de
 * `frontend/encargossacd/controller/listas_exigencia_ctr.php`.
 */
final class ListasExigenciaCtrData
{

    public function __construct(
        private EncargoAplicacionService $aplicacionService,
        private CentroDlRepositoryInterface $centroDlRepository,
        private CentroEllasRepositoryInterface $centroEllasRepository,
        private EncargoRepositoryInterface $encargoRepository,
        private EncargoSacdRepositoryInterface $encargoSacdRepository,
        private PersonaDlRepositoryInterface $personaDlRepository
    ) {
    }

    /**
     * @return array{
     *     cabecera_left: string,
     *     cabecera_right: string,
     *     cabecera_right_2: string,
     *     Html: string
     * }
     */
    public function execute(int $sf, string $ctr_igl): array
    {
        $oService = $this->aplicacionService;

        /** @var ConfigSnapshot $oConfig */


        $oConfig = $_SESSION['oConfig'];


        $any = $oConfig->any_final_curs('crt');
        $inicurs = \src\shared\domain\helpers\FuncTablasSupport::cursoEst('inicio', $any, 'crt')->getFromLocal();
        $fincurs = \src\shared\domain\helpers\FuncTablasSupport::cursoEst('fin', $any, 'crt')->getFromLocal();

        $cabecera_left = sprintf(_('Curso:  %s - %s'), $inicurs, $fincurs);
        $cabecera_right = ConfigGlobal::mi_delef();
        $cabecera_right_2 = _('ref. cr 1/14, 10, d)');

        $poblacion = $oService->getLugar_dl();
        $oDateLocal = new DateTimeLocal();
        $hoy_local = $oDateLocal->getFromLocal('.');
        $lugar_fecha = "$poblacion, $hoy_local";

        $tipos_de_ctr = [];
        if ($ctr_igl === 'ctr') {
            $tipos_de_ctr = $sf === 1
                ? ['n', 'a[jm$]', 's[jm]']
                : ['n', 'a[jm$]', 's[jm]', 'ss'];
        }
        if ($ctr_igl === 'igl') {
            $tipos_de_ctr = ['ctr', 'igl', 'cgioc', '^cgi$'];
        }

        $Html = '';
        foreach ($tipos_de_ctr as $tipo_ctr_que) {
            $txt_tipo_ctr = match ($tipo_ctr_que) {
                'n' => _('1. ctr de n'),
                'a[jm$]' => _('2. ctr de agd'),
                's[jm]' => _('3. ctr de sg'),
                'ss' => _('4. ctr de sss+'),
                'igl' => '1. ' . _('Iglesias'),
                'cgioc' => '2. ' . _('oc'),
                '^cgi$' => '3. ' . _('lp'),
                default => '',
            };
            $clase_div = match ($tipo_ctr_que) {
                'n', 'a[jm$]', 's[jm]', 'ss', 'cgioc' => 'salta_pag',
                default => '',
            };
            $div_open = $clase_div !== '' ? "<div class=$clase_div>" : '<div>';
            if ($txt_tipo_ctr !== '') {
                $Html .= "$div_open<table><tr><td class=grupo colspan=2>$txt_tipo_ctr</td></tr>";
            }

            $aWhere = [
                'active' => 't',
                'tipo_ctr' => "^$tipo_ctr_que",
                '_ordre' => 'nombre_ubi',
            ];
            $aOperador = ['tipo_ctr' => '~'];
            $cCentros = [];
            if ($ctr_igl === 'ctr') {
                $gesCentros = $sf === 1 ? $this->centroEllasRepository : $this->centroDlRepository;
                $cCentros = $gesCentros->getCentros($aWhere, $aOperador) ?: [];
            } elseif ($ctr_igl === 'igl') {
                $cCentrosF = $this->centroEllasRepository->getCentros($aWhere, $aOperador) ?: [];
                $cCentrosV = $this->centroDlRepository->getCentros($aWhere, $aOperador) ?: [];
                $cCentros = array_merge($cCentrosV, $cCentrosF);
            }

            foreach ($cCentros as $oCentro) {
                $id_ubi = $oCentro->getId_ubi();
                $nombre_ubi = $oCentro->getNombre_ubi();
                $tipo_ctr = $oCentro->getTipo_ctr();
                /** @var array<int, string> $cargos */
                $cargos = [];
                $cEncargos = $this->encargoRepository->getEncargos(['id_ubi' => $id_ubi]) ?: [];
                $sacds = [];
                $dedicacion_ctr = '';
                foreach ($cEncargos as $oEncargo) {
                    $id_enc = $oEncargo->getId_enc();
                    $dedicacion_ctr = $oService->dedicacion_ctr($id_ubi, $id_enc);

                    $cTareasSacd = $this->encargoSacdRepository->getEncargosSacd(
                        ['id_enc' => $id_enc, 'f_fin' => 'null', '_ordre' => 'modo'],
                        ['f_fin' => 'IS NULL'],
                    ) ?: [];
                    $s = 0;
                    foreach ($cTareasSacd as $oTareaSacd) {
                        $s++;
                        $modo = (int)$oTareaSacd->getModo();
                        $id_nom = $oTareaSacd->getId_nom();
                        $oPersona = $this->personaDlRepository->findById($id_nom);
                        if ($oPersona === null) {
                            continue;
                        }
                        $nom_ap = $oPersona->getNombreApellidosCrSin();
                        $dedicacion_txt = $oService->dedicacion($id_nom, $id_enc);
                        if (array_key_exists($id_nom, $cargos) && $cargos[$id_nom] !== '') {
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

                $Html .= "<tr><td class=titulo>$nombre_ubi</td><td>$dedicacion_ctr</td></tr>";
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
