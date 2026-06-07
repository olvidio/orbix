<?php

namespace src\encargossacd\application;

use src\encargossacd\domain\EncargoConstants;
use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdObservRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\encargossacd\domain\entity\EncargoHorario;
use src\encargossacd\domain\entity\EncargoSacdHorario;
use src\encargossacd\domain\services\EncargoDominioService;
use src\permisos\domain\XPermisos;

/**
 * Datos para la ficha de encargos de un SACD
 * (`sacd_ficha_ajax?que=ficha`).
 */
final class SacdFichaData
{

    public function __construct(
        private EncargoDominioService $dominioService,
        private EncargoHorarioRepositoryInterface $encargoHorarioRepository,
        private EncargoRepositoryInterface $encargoRepository,
        private EncargoSacdHorarioRepositoryInterface $encargoSacdHorarioRepository,
        private EncargoSacdObservRepositoryInterface $encargoSacdObservRepository,
        private EncargoSacdRepositoryInterface $encargoSacdRepository,
        private EncargoTipoRepositoryInterface $encargoTipoRepository
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(int $id_nom): array
    {
        $oDominio = $this->dominioService;
        $hoy = date('Y-m-d');

        $permiso = 0;
        $oPerm = $_SESSION['oPerm'] ?? null;
        if ($oPerm instanceof XPermisos
            && ($oPerm->have_perm_oficina('des') || $oPerm->have_perm_oficina('vcsd'))
        ) {
            $permiso = 1;
        }

        $observ_sacd = '';
        $cEncargoSacdObserv = $this->encargoSacdObservRepository->getEncargosSacdObservs(['id_nom' => $id_nom]);
        foreach ($cEncargoSacdObserv as $oEncargoSacdObserv) {
            $observ_sacd = (string) $oEncargoSacdObserv->getObserv();
        }

        $aWhereES = [
            'id_nom' => $id_nom,
            'f_fin' => 'x',
            '_ordre' => 'modo, f_ini DESC',
        ];
        $aOperadorES = ['f_fin' => 'IS NULL'];
        $cEncargosSacd1 = $this->encargoSacdRepository->getEncargosSacd($aWhereES, $aOperadorES);

        $aWhereES['f_fin'] = "'$hoy'";
        $aOperadorES['f_fin'] = '>';
        $cEncargosSacd2 = $this->encargoSacdRepository->getEncargosSacd($aWhereES, $aOperadorES);

        $cEncargosSacd = array_merge($cEncargosSacd1, $cEncargosSacd2);

        $encargos = [];
        $avisos = [];
        foreach ($cEncargosSacd as $oEncargoSacd) {
            $id_enc = (int) $oEncargoSacd->getId_enc();
            $modo = (int) $oEncargoSacd->getModo();

            $oEncargo = $this->encargoRepository->findById($id_enc);
            if ($oEncargo === null) {
                continue;
            }

            $id_tipo_enc = $oEncargo->getId_tipo_enc();
            $prefijo = (int) substr((string) $id_tipo_enc, 0, 1);
            if ($prefijo === 7 || $prefijo === 4) {
                continue;
            }

            $sf_sv = $oEncargo->getGrupo_encargo();
            $id_ubi = (int) ($oEncargo->getId_ubi() ?? 0);
            $desc_enc = (string) ($oEncargo->getDesc_enc() ?? '');

            $oEncargoTipo = $this->encargoTipoRepository->findById((int) $id_tipo_enc);
            $mod_horario = $oEncargoTipo !== null ? (int) $oEncargoTipo->getMod_horario() : 0;

            $desc_enc_vis = $desc_enc;
            if ($permiso !== 1 && $sf_sv === 2) {
                $desc_enc_vis = (string) preg_replace('/\(.+\)/', '', $desc_enc);
            }

            $cEncargoHorarios = $this->cargarHorariosCtr($this->encargoHorarioRepository, $id_enc, $hoy);

            $dedic_ctr = '';
            $dedic_ctr_m = '';
            $dedic_ctr_t = '';
            $dedic_ctr_v = '';
            if ($mod_horario === 3) {
                $h = 0;
                foreach ($cEncargoHorarios as $oH) {
                    $h++;
                    $texto = $oDominio->texto_horario(
                        (string) ($oH->getMas_menos() ?? ''),
                        (string) ($oH->getDia_ref() ?? ''),
                        (string) ($oH->getDia_inc() ?? ''),
                        (string) ($oH->getDia_num() ?? ''),
                        $this->timeToString($oH->getH_ini()),
                        $this->timeToString($oH->getH_fin()),
                        (string) ($oH->getN_sacd() ?? ''),
                    );
                    if ($h > 1) {
                        $dedic_ctr .= ' y ';
                    }
                    $dedic_ctr .= $texto;
                }
            } else {
                foreach ($cEncargoHorarios as $oH) {
                    switch ((string) ($oH->getDia_ref() ?? '')) {
                        case 'm':
                            $dedic_ctr_m = (string) ($oH->getDia_inc() ?? '');
                            break;
                        case 't':
                            $dedic_ctr_t = (string) ($oH->getDia_inc() ?? '');
                            break;
                        case 'v':
                            $dedic_ctr_v = (string) ($oH->getDia_inc() ?? '');
                            break;
                    }
                }
            }

            $cHorariosSacd = $this->cargarHorariosSacd(
                $this->encargoSacdHorarioRepository,
                $id_enc,
                $id_nom,
                $hoy,
            );

            $dedic_sacd = '';
            $dedic_m = '';
            $dedic_t = '';
            $dedic_v = '';
            if ($mod_horario === 3) {
                $h = 0;
                foreach ($cHorariosSacd as $oH) {
                    $h++;
                    $texto = $oDominio->texto_horario(
                        (string) ($oH->getMas_menos() ?? ''),
                        (string) ($oH->getDia_ref() ?? ''),
                        (string) ($oH->getDia_inc() ?? ''),
                        (string) ($oH->getDia_num() ?? ''),
                        $this->timeToString($oH->getH_ini()),
                        $this->timeToString($oH->getH_fin()),
                        '',
                    );
                    if ($h > 1) {
                        $dedic_sacd .= ' y ';
                    }
                    $dedic_sacd .= $texto;
                }
                if ($dedic_sacd === '') {
                    $dedic_sacd = _('horario del ctr') . ': ' . $dedic_ctr;
                }
            } else {
                foreach ($cHorariosSacd as $oH) {
                    switch ((string) ($oH->getDia_ref() ?? '')) {
                        case 'm':
                            $dedic_m = (string) ($oH->getDia_inc() ?? '');
                            break;
                        case 't':
                            $dedic_t = (string) ($oH->getDia_inc() ?? '');
                            break;
                        case 'v':
                            $dedic_v = (string) ($oH->getDia_inc() ?? '');
                            break;
                        default:
                            $avisos[] = sprintf(
                                _('Se debería haber borrado el encargo "%s" porque no tenía definido el dia de ref.'),
                                $desc_enc,
                            );
                    }
                }
            }

            $encargos[] = [
                'id_enc' => $id_enc,
                'id_tipo_enc' => (int) $id_tipo_enc,
                'mod_horario' => $mod_horario,
                'modo' => $modo,
                'sf_sv' => $sf_sv,
                'id_ubi' => $id_ubi,
                'desc_enc' => $desc_enc_vis,
                'dedic_ctr' => $dedic_ctr,
                'dedic_ctr_m' => $dedic_ctr_m,
                'dedic_ctr_t' => $dedic_ctr_t,
                'dedic_ctr_v' => $dedic_ctr_v,
                'dedic_sacd' => $dedic_sacd,
                'dedic_m' => $dedic_m,
                'dedic_t' => $dedic_t,
                'dedic_v' => $dedic_v,
            ];
        }

        $EncargoConstants = new EncargoConstants();
        $opciones_mas_raw = $EncargoConstants->getOpcionesEncargos();
        $opciones_mas = [];
        foreach ($opciones_mas_raw as $k => $v) {
            $opciones_mas[(string) $k] = (string) $v;
        }

        return [
            'permiso' => $permiso,
            'observ_sacd' => $observ_sacd,
            'encargos' => $encargos,
            'opciones_mas' => $opciones_mas,
            'avisos' => $avisos,
        ];
    }

    /**
     * @return list<EncargoHorario>
     */
    private function cargarHorariosCtr(
        EncargoHorarioRepositoryInterface $repo,
        int $id_enc,
        string $hoy,
    ): array {
        $aWhere = [
            'id_enc' => $id_enc,
            'f_fin' => 'x',
            '_ordre' => 'f_ini DESC',
        ];
        $aOperador = ['f_fin' => 'IS NULL'];
        $c0 = $repo->getEncargoHorarios($aWhere, $aOperador);

        $aWhere['f_fin'] = "'$hoy'";
        $aOperador['f_fin'] = '>';
        $c1 = $repo->getEncargoHorarios($aWhere, $aOperador);

        return array_merge($c0, $c1);
    }

    /**
     * @return list<EncargoSacdHorario>
     */
    private function cargarHorariosSacd(
        EncargoSacdHorarioRepositoryInterface $repo,
        int $id_enc,
        int $id_nom,
        string $hoy,
    ): array {
        $aWhere = [
            'id_enc' => $id_enc,
            'id_nom' => $id_nom,
            'f_fin' => "'$hoy'",
        ];
        $aOperador = ['f_fin' => '>'];
        $c1 = $repo->getEncargoSacdHorarios($aWhere, $aOperador);

        $aWhere['f_fin'] = 'x';
        $aOperador['f_fin'] = 'IS NULL';
        $c2 = $repo->getEncargoSacdHorarios($aWhere, $aOperador);

        return array_merge($c1, $c2);
    }

    private function timeToString(mixed $time): string
    {
        if ($time === null) {
            return '';
        }
        if (is_object($time) && method_exists($time, 'value')) {
            return (string) $time->value();
        }

        return is_scalar($time) ? (string) $time : '';
    }
}
