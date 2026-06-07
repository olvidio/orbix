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
use src\zonassacd\domain\contracts\ZonaGrupoRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

use function src\shared\domain\helpers\strtoupper_dlb;

/**
 * Genera el listado de atencion SACD "c" (cr 9/05, Anexo2, 9.4 c).
 * Sustituye la logica de `frontend/encargossacd/controller/listas_c.php`.
 */
final class ListasCData
{

    public function __construct(
        private EncargoAplicacionService $aplicacionService,
        private CentroDlRepositoryInterface $centroDlRepository,
        private CentroEllasRepositoryInterface $centroEllasRepository,
        private EncargoRepositoryInterface $encargoRepository,
        private EncargoSacdRepositoryInterface $encargoSacdRepository,
        private PersonaDlRepositoryInterface $personaDlRepository,
        private ZonaGrupoRepositoryInterface $zonaGrupoRepository,
        private ZonaRepositoryInterface $zonaRepository
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
    public function execute(): array
    {
        $oService = $this->aplicacionService;

        /** @var ConfigSnapshot $oConfig */


        $oConfig = $_SESSION['oConfig'];


        $any = $oConfig->any_final_curs('crt');
        $inicurs = \src\shared\domain\helpers\curso_est('inicio', $any, 'crt')->getFromLocal();
        $fincurs = \src\shared\domain\helpers\curso_est('fin', $any, 'crt')->getFromLocal();

        $cabecera_left = sprintf(_('Curso:  %s - %s'), $inicurs, $fincurs);
        $cabecera_right = ConfigGlobal::mi_delef();
        $cabecera_right_2 = _('ref. cr 1/14, 10, d)');

        $permiso_sf = '';
        $oPerm = $_SESSION['oPerm'] ?? null;
        if (is_object($oPerm) && method_exists($oPerm, 'have_perm_oficina')
            && ($oPerm->have_perm_oficina('vcsd') || $oPerm->have_perm_oficina('des'))
        ) {
            $permiso_sf = 'si';
        }

        $cZonasGrupos = $this->zonaGrupoRepository->getZonasGrupo(['_ordre' => 'orden']) ?: [];
        $array_grupos = [];
        foreach ($cZonasGrupos as $oZonaGrupo) {
            $array_grupos[$oZonaGrupo->getId_grupo()] = $oZonaGrupo->getNombre_grupo();
        }

        $Html_all = '<div class=salta_pag><table>';

        foreach ($cZonasGrupos as $oZonaGrupo) {
            $id_grupo = $oZonaGrupo->getId_grupo();
            $cZonas = $this->zonaRepository->getZonas(['id_grupo' => $id_grupo]) ?: [];
            $a_sacd = [];

            foreach ($cZonas as $oZona) {
                $id_zona = $oZona->getId_zona();
                $cCentrosDl = $this->centroDlRepository->getCentros(['id_zona' => $id_zona]) ?: [];
                foreach ($cCentrosDl as $oCentroDl) {
                    $id_ubi = $oCentroDl->getId_ubi();
                    $cPersonas = $this->personaDlRepository->getPersonas([
                        'id_ctr' => $id_ubi,
                        'situacion' => 'A',
                        'sacd' => 't',
                        '_ordre' => 'apellido1,apellido2,nom',
                    ]) ?: [];
                    foreach ($cPersonas as $oPersonaNAgd) {
                        $id_nom = $oPersonaNAgd->getId_nom();
                        $nom_ap = $oPersonaNAgd->getNombreApellidosCrSin();
                        $nom_orden = $oPersonaNAgd->getPrefApellidosNombre();
                        $sv_txt = '';
                        $sf_txt = '';
                        $sssc_txt = '';
                        $sf_ctr = [];
                        $sf_cgi = [];

                        $aWhereT = [
                            'id_nom' => $id_nom,
                            'f_fin' => 'null',
                            '_ordre' => 'modo',
                        ];
                        $aOperadorT = ['f_fin' => 'IS NULL'];
                        $cTareasSacd = $this->encargoSacdRepository->getEncargosSacd($aWhereT, $aOperadorT) ?: [];
                        foreach ($cTareasSacd as $oTareaSacd) {
                            $modo = (int)$oTareaSacd->getModo();
                            $id_enc = $oTareaSacd->getId_enc();
                            $oEncargo = $this->encargoRepository->findById($id_enc);
                            if ($oEncargo === null) {
                                continue;
                            }
                            $id_tipo_enc = (int)$oEncargo->getId_tipo_enc();
                            $id_ubi_enc = $oEncargo->getId_ubi();
                            if (empty($id_ubi_enc)) {
                                $nombre_ubi = '';
                            } else {
                                $iid = (string)$id_ubi_enc;
                                if ((int)$iid[0] === 2) {
                                    $oCentroEnc = $this->centroEllasRepository->findById($id_ubi_enc);
                                } else {
                                    $oCentroEnc = $this->centroDlRepository->findById($id_ubi_enc);
                                }
                                $nombre_ubi = $oCentroEnc?->getNombre_ubi() ?? '';
                            }
                            $dedicacion_txt = $oService->dedicacion($id_nom, $id_enc);
                            $modo_txt = match ($modo) {
                                1 => 'coordinador de',
                                2 => 'cl de',
                                3 => 'atención de',
                                4 => 'suplente de',
                                5 => 'colaborador de',
                                default => '',
                            };
                            if ($modo === 4) {
                                $dedicacion_txt = '';
                            }
                            switch ($id_tipo_enc) {
                                case 1100:
                                    $sv_txt .= trim(", $modo_txt: $nombre_ubi $dedicacion_txt");
                                    break;
                                case 1200:
                                    if ($permiso_sf === 'si') {
                                        $sf_txt .= trim(", $modo_txt: $nombre_ubi $dedicacion_txt");
                                    } else {
                                        if ($modo === 3) {
                                            $sf_ctr[3] = ($sf_ctr[3] ?? 0) + 1;
                                        } elseif ($modo === 4 || $modo === 5) {
                                            $sf_ctr[4] = ($sf_ctr[4] ?? 0) + 1;
                                        }
                                    }
                                    break;
                                case 1300:
                                    $sssc_txt .= trim(", $modo_txt: $nombre_ubi $dedicacion_txt");
                                    break;
                                case 2100:
                                    $sv_txt .= trim(", $modo_txt: $nombre_ubi $dedicacion_txt");
                                    break;
                                case 2200:
                                    if ($permiso_sf === 'si') {
                                        $sf_txt .= trim(", $modo_txt: $nombre_ubi $dedicacion_txt");
                                    } else {
                                        if ($modo === 3) {
                                            $sf_cgi[3] = ($sf_cgi[3] ?? 0) + 1;
                                        } elseif ($modo === 4 || $modo === 5) {
                                            $sf_cgi[4] = ($sf_cgi[4] ?? 0) + 1;
                                        }
                                    }
                                    break;
                                case 3000:
                                    $sv_txt .= trim(", $modo_txt: $nombre_ubi $dedicacion_txt");
                                    break;
                                case 5020:
                                    $sv_txt .= trim(", estudio: $nombre_ubi $dedicacion_txt");
                                    break;
                                case 5030:
                                    $sv_txt .= trim(", descanso: $nombre_ubi $dedicacion_txt");
                                    break;
                            }
                        }
                        $sv_txt = substr($sv_txt, 2);
                        $sssc_txt = substr($sssc_txt, 2);

                        if ($permiso_sf === 'si') {
                            $sf_txt = substr($sf_txt, 2);
                        } else {
                            $n3 = (int)($sf_ctr[3] ?? 0);
                            $n4 = (int)($sf_ctr[4] ?? 0);
                            $c3 = (int)($sf_cgi[3] ?? 0);
                            $c4 = (int)($sf_cgi[4] ?? 0);
                            if ($n3 === 1) {
                                $sf_txt .= ', ' . sprintf(_('%s centro sf'), $n3);
                            } elseif ($n3 > 1) {
                                $sf_txt .= ', ' . sprintf(_('%s centros sf'), $n3);
                            }
                            if ($n4 === 1) {
                                $sf_txt .= ', ' . sprintf(_('suplente de %s centro sf'), $n4);
                            } elseif ($n4 > 1) {
                                $sf_txt .= ', ' . sprintf(_('suplente de %s centros sf'), $n4);
                            }
                            if ($c3 === 1) {
                                $sf_txt .= ', ' . sprintf(_('atiende %s colegio sf'), $c3);
                            } elseif ($c3 > 1) {
                                $sf_txt .= ', ' . sprintf(_('atiende %s colegios sf'), $c3);
                            }
                            if ($c4 === 1) {
                                $sf_txt .= ', ' . sprintf(_('colabora con %s colegio sf'), $c4);
                            } elseif ($c4 > 1) {
                                $sf_txt .= ', ' . sprintf(_('colabora con %s colegios sf'), $c4);
                            }
                            $sf_txt = substr($sf_txt, 2);
                        }
                        $a_sacd[$nom_orden] = "<tr><td class=centro>$nom_ap</td></tr><tr><td>$sv_txt<br>$sssc_txt</td><td class=sf>$sf_txt</td></tr>";
                    }
                }
            }
            uksort($a_sacd, 'src\shared\domain\helpers\strsinacentocmp');

            $poblacion = !empty($id_grupo) ? ($array_grupos[$id_grupo] ?? _('otros')) : _('otros');
            $titulo_2 = strtoupper_dlb($poblacion);
            if ($titulo_2 !== '') {
                $Html_all .= "<tr><td class=poblacion colspan=2>$titulo_2</td></tr>";
            }
            foreach ($a_sacd as $html) {
                $Html_all .= $html;
            }
        }

        $Html_all .= '</table></div>';

        $poblacion = $oService->getLugar_dl();
        $oDateLocal = new DateTimeLocal();
        $hoy_local = $oDateLocal->getFromLocal('.');
        $lugar_fecha = "$poblacion, $hoy_local";

        $Html_all .= '<table><col width=50%>';
        $Html_all .= "<tr><td class=izquierda></td><td class=derecha>$lugar_fecha</td></tr>";
        $Html_all .= '</table>';

        return [
            'cabecera_left' => $cabecera_left,
            'cabecera_right' => (string)$cabecera_right,
            'cabecera_right_2' => $cabecera_right_2,
            'Html' => $Html_all,
        ];
    }
}
