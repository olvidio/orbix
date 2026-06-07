<?php

namespace src\encargossacd\application;

use src\configuracion\domain\value_objects\ConfigSnapshot;

use src\shared\config\ConfigGlobal;
use src\encargossacd\application\services\EncargoAplicacionService;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\services\EncargoDominioService;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaGrupoRepositoryInterface;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;

/**
 * Genera el listado "d" de atencion SACD (cr 9/20, 10).
 * Sustituye la logica de `frontend/encargossacd/controller/listas_d.php`.
 *
 * La vista original devolvia dos tablas HTML sueltas (cabecera + listado);
 * aqui se componen ambas en `Html` para que el frontend solo tenga que
 * volcarlas al cliente.
 */
final class ListasDData
{

    public function __construct(
        private EncargoAplicacionService $aplicacionService,
        private CentroDlRepositoryInterface $centroDlRepository,
        private CentroEllasRepositoryInterface $centroEllasRepository,
        private EncargoDominioService $dominioService,
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
    public function execute(int $sf): array
    {
        $oService = $this->aplicacionService;
        $oDomService = $this->dominioService;

        /** @var ConfigSnapshot $oConfig */


        $oConfig = $_SESSION['oConfig'];


        $any = $oConfig->any_final_curs('crt');
        $inicurs = \src\shared\domain\helpers\curso_est('inicio', $any, 'crt')->getFromLocal();
        $fincurs = \src\shared\domain\helpers\curso_est('fin', $any, 'crt')->getFromLocal();

        $cabecera_left = sprintf(_('Curso:  %s - %s'), $inicurs, $fincurs);
        $cabecera_right = ConfigGlobal::mi_delef();
        $cabecera_right_2 = _('ref. cr 9/20, 10)');

        $permiso_sf = '';
        $oPerm = $_SESSION['oPerm'] ?? null;
        if ($sf === 1
            && is_object($oPerm)
            && method_exists($oPerm, 'have_perm_oficina')
            && ($oPerm->have_perm_oficina('vcsd') || $oPerm->have_perm_oficina('des'))
        ) {
            $permiso_sf = 'si';
        }

        $cZonasGrupos = $this->zonaGrupoRepository->getZonasGrupo(['_ordre' => 'orden']) ?: [];
        $array_grupos = [];
        foreach ($cZonasGrupos as $oZonaGrupo) {
            $array_grupos[$oZonaGrupo->getId_grupo()] = $oZonaGrupo->getNombre_grupo();
        }

        $all = [];
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
                        $poblacion = !empty($id_grupo)
                            ? ($array_grupos[$id_grupo] ?? _('otros'))
                            : _('otros');
                        $a_dedicacion = [];

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
                            $nombre_ubi = '';
                            if (!empty($id_ubi_enc)) {
                                $iid = (string)$id_ubi_enc;
                                if ((int)$iid[0] === 2) {
                                    $oCentroEnc = $this->centroEllasRepository->findById($id_ubi_enc);
                                } else {
                                    $oCentroEnc = $this->centroDlRepository->findById($id_ubi_enc);
                                }
                                if ($oCentroEnc !== null) {
                                    $nombre_ubi = (string)$oCentroEnc->getNombre_ubi();
                                }
                            }
                            $dedicacion_txt = $oDomService->dedicacion_horas($id_nom, $id_enc);
                            if ($modo === 4) {
                                continue;
                            }
                            if ($sf === 0) {
                                match ($id_tipo_enc) {
                                    1100 => $a_dedicacion[3][$id_enc] = ['labor' => $nombre_ubi, 'horas' => $dedicacion_txt],
                                    1300 => $a_dedicacion[5][$id_enc] = ['labor' => $nombre_ubi, 'horas' => $dedicacion_txt],
                                    3000 => $a_dedicacion[8][$id_enc] = ['labor' => $nombre_ubi, 'horas' => $dedicacion_txt],
                                    5020 => $a_dedicacion[1][$id_enc] = ['labor' => 'estudio', 'horas' => $dedicacion_txt],
                                    5030 => $a_dedicacion[2][$id_enc] = ['labor' => 'descanso', 'horas' => $dedicacion_txt],
                                    6000 => $a_dedicacion[9][$id_enc] = ['labor' => 'otros', 'horas' => $dedicacion_txt],
                                    2100 => $a_dedicacion[6][$id_enc] = ['labor' => $nombre_ubi, 'horas' => $dedicacion_txt],
                                    default => null,
                                };
                            } else {
                                if ($permiso_sf === 'si') {
                                    match ($id_tipo_enc) {
                                        1200 => $a_dedicacion[4][$id_enc] = ['labor' => $nombre_ubi, 'horas' => $dedicacion_txt],
                                        2200 => $a_dedicacion[7][$id_enc] = ['labor' => $nombre_ubi, 'horas' => $dedicacion_txt],
                                        default => null,
                                    };
                                }
                            }
                        }
                        ksort($a_dedicacion);
                        if ($sf === 1) {
                            if (!empty($a_dedicacion)) {
                                $a_sacd[$nom_orden] = ['nom' => $nom_ap, 'poblacion' => $poblacion, 'dedicacion' => $a_dedicacion];
                            }
                        } else {
                            $a_sacd[$nom_orden] = ['nom' => $nom_ap, 'poblacion' => $poblacion, 'dedicacion' => $a_dedicacion];
                        }
                    }
                }
            }
            uksort($a_sacd, 'src\shared\domain\helpers\strsinacentocmp');
            $all[$id_grupo] = $a_sacd;
        }

        $cabecera_html = '<table><tr><td class=izquierda>' . $cabecera_left . '</td></tr></table>';
        $html = '<table>';
        foreach ($all as $a_sacd) {
            foreach ($a_sacd as $fila) {
                $html .= '<tr><td>' . $fila['nom'] . '</td><td>' . $fila['poblacion'] . '</td>';
                foreach ($fila['dedicacion'] as $a_dedi) {
                    foreach ($a_dedi as $a_dedic) {
                        $html .= '<td>' . $a_dedic['labor'] . '</td><td>' . $a_dedic['horas'] . '</td>';
                    }
                }
                $html .= '</tr>';
            }
        }
        $html .= '</table>';

        $poblacion = $oService->getLugar_dl();
        $oDateLocal = new DateTimeLocal();
        $hoy_local = $oDateLocal->getFromLocal('.');
        $lugar_fecha = "$poblacion, $hoy_local";
        $html .= '<table><col width=50%>';
        $html .= "<tr><td class=izquierda></td><td class=derecha>$lugar_fecha</td></tr>";
        $html .= '</table>';

        return [
            'cabecera_left' => $cabecera_left,
            'cabecera_right' => (string)$cabecera_right,
            'cabecera_right_2' => $cabecera_right_2,
            'Html' => $cabecera_html . $html,
        ];
    }
}
