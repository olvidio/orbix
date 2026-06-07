<?php

namespace src\encargossacd\application;

use src\shared\config\ConfigGlobal;
use src\encargossacd\application\services\EncargoAplicacionService;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdObservRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdRepositoryInterface;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;

/**
 * Datos para la comunicacion a los SACD.
 * Sustituye la logica de
 * `frontend/encargossacd/controller/listas_com_sacd.php`.
 */
final class ListasComSacdData
{

    public function __construct(
        private EncargoAplicacionService $aplicacionService,
        private CentroDlRepositoryInterface $centroDlRepository,
        private CentroEllasRepositoryInterface $centroEllasRepository,
        private EncargoRepositoryInterface $encargoRepository,
        private EncargoSacdHorarioRepositoryInterface $encargoSacdHorarioRepository,
        private EncargoSacdObservRepositoryInterface $encargoSacdObservRepository,
        private EncargoSacdRepositoryInterface $encargoSacdRepository,
        private PersonaDlRepositoryInterface $personaDlRepository
    ) {
    }

    /**
     * @return array{
     *     array_modo: array<int, array<int|string, mixed>>,
     *     lugar_fecha: string
     * }
     */
    public function execute(string $sel): array
    {
        $oService = $this->aplicacionService;

        $hoy_iso = date('Y-m-d');
        $oDateLocal = new DateTimeLocal();
        $hoy_local = $oDateLocal->getFromLocal('.');
        $poblacion = $oService->getLugar_dl();
        $lugar_fecha = "$poblacion, $hoy_local";

        $array_orden = [1 => 1, 2 => 2, 3 => 2, 4 => 4, 5 => 3, 6 => 5];

        $cPersonas = [];
        $aWhere = [];
        $aOperador = [];
        switch ($sel) {
            case 'nagd':
                $aWhere['id_tabla'] = '^n|^a';
                $aWhere['situacion'] = 'A';
                $aWhere['sacd'] = 't';
                $aWhere['dl'] = ConfigGlobal::mi_delef();
                $aWhere['_ordre'] = 'apellido1,apellido2,nom';
                $aOperador['id_tabla'] = '~';
                $cPersonas = $this->personaDlRepository->getPersonas($aWhere, $aOperador) ?: [];
                break;
            case 'sssc':
                $aWhere['id_tabla'] = '^sss';
                $aWhere['situacion'] = 'A';
                $aWhere['sacd'] = 't';
                $aWhere['dl'] = ConfigGlobal::mi_delef();
                $aWhere['_ordre'] = 'apellido1,apellido2,nom';
                $aOperador['id_tabla'] = '~';
                $cPersonas = $this->personaDlRepository->getPersonas($aWhere, $aOperador) ?: [];
                break;
        }

        /** @var array<int, array{nom_ap: string, txt: array<string, string>, grupo: array<string, list<array<string, mixed>>>, observ: list<array{desc_enc: string}>}> $array_modo */
        $array_modo = [];
        $s = 0;
        foreach ($cPersonas as $oPersona) {
            $s++;
            $id_nom = $oPersona->getId_nom();
            $idioma = (string)$oPersona->getIdioma_preferido();
            /** @var array<int|string, list<array<string, mixed>>> $grupos */
            $grupos = [];
            $array_modo[$s] = [
                'nom_ap' => $oPersona->getNombreApellidos(),
                'txt' => [
                    'com_sacd' => $oService->getTraduccion('com_sacd', $idioma),
                    't_secc' => $oService->getTraduccion('t_secc', $idioma),
                    't_mañanas' => $oService->getTraduccion('t_mañanas', $idioma),
                    't_tardes1' => $oService->getTraduccion('t_tardes1', $idioma),
                    't_tardes2' => $oService->getTraduccion('t_tardes2', $idioma),
                    't_titular' => $oService->getTraduccion('t_titular', $idioma),
                    't_suplente' => $oService->getTraduccion('t_suplente', $idioma),
                    't_colaborador' => $oService->getTraduccion('t_colaborador', $idioma),
                    't_otros' => $oService->getTraduccion('t_otros', $idioma),
                ],
                'grupo' => [],
                'observ' => [],
            ];

            $cEncargoSacdObserv = $this->encargoSacdObservRepository->getEncargosSacdObservs(['id_nom' => $id_nom]) ?: [];
            $observ = '';
            if (count($cEncargoSacdObserv) > 0) {
                $observ = (string)$cEncargoSacdObserv[0]->getObserv();
            }

            $cEncargosSacd1 = $this->encargoSacdRepository->getEncargosSacd(
                ['id_nom' => $id_nom, 'f_fin' => 'x', '_ordre' => 'modo'],
                ['f_fin' => 'IS NULL'],
            ) ?: [];
            $cEncargosSacd2 = $this->encargoSacdRepository->getEncargosSacd(
                ['id_nom' => $id_nom, 'f_fin' => $hoy_iso, '_ordre' => 'modo'],
                ['f_fin' => '>'],
            ) ?: [];
            $cEncargosSacd = array_merge($cEncargosSacd1, $cEncargosSacd2);

            foreach ($cEncargosSacd as $oEncargoSacd) {
                $id_enc = $oEncargoSacd->getId_enc();
                $modo = (int)$oEncargoSacd->getModo();
                $oEncargo = $this->encargoRepository->findById($id_enc);
                if ($oEncargo === null) {
                    continue;
                }
                $id_tipo_enc = (int)$oEncargo->getId_tipo_enc();
                $id_tipo_enc_txt = (string)$id_tipo_enc;
                if ((int)$id_tipo_enc_txt[0] === 4
                    || (int)$id_tipo_enc_txt[0] === 7
                    || (int)$id_tipo_enc_txt[0] === 8
                ) {
                    continue;
                }
                $desc_enc = (string)$oEncargo->getDesc_enc();
                $id_ubi = $oEncargo->getId_ubi();
                $desc_lugar = (string)$oEncargo->getDesc_lugar();
                $grupo = $array_orden[$modo] ?? 5;
                $nombre_ubi = '';
                if (!empty($id_ubi)) {
                    if ((int)substr((string)$id_ubi, 0, 1) === 2) {
                        $oUbi = $this->centroEllasRepository->findById($id_ubi);
                    } else {
                        $oUbi = $this->centroDlRepository->findById($id_ubi);
                    }
                    $nombre_ubi = $oUbi?->getNombre_ubi() ?? '';
                }
                $seccion = '';
                if (!empty($id_tipo_enc)) {
                    $seccion = match ((int)($id_tipo_enc_txt[1] ?? 0)) {
                        1 => 'sv',
                        2 => 'sf',
                        3 => 'sss+',
                        default => '',
                    };
                }

                $sup_tit = '';
                if ($modo === 2 || $modo === 3) {
                    $cPar = $this->encargoSacdRepository->getEncargosSacd(
                        ['id_enc' => $id_enc, 'f_fin' => 'x', 'modo' => 4],
                        ['f_fin' => 'IS NULL'],
                    ) ?: [];
                    if (count($cPar) === 0) {
                        $cPar = $this->encargoSacdRepository->getEncargosSacd(
                            ['id_enc' => $id_enc, 'f_fin' => $hoy_iso, 'modo' => 4],
                            ['f_fin' => '>'],
                        ) ?: [];
                    }
                    if (count($cPar) === 1) {
                        $oSup = $this->personaDlRepository->findById($cPar[0]->getId_nom());
                        $sup_tit = $oSup?->getNombreApellidos() ?? '';
                    }
                } elseif ($modo === 4) {
                    $cPar = $this->encargoSacdRepository->getEncargosSacd(
                        ['id_enc' => $id_enc, 'f_fin' => 'x', 'modo' => '[23]'],
                        ['modo' => '~', 'f_fin' => 'IS NULL'],
                    ) ?: [];
                    if (count($cPar) === 0) {
                        $cPar = $this->encargoSacdRepository->getEncargosSacd(
                            ['id_enc' => $id_enc, 'f_fin' => $hoy_iso, 'modo' => '[23]'],
                            ['modo' => '~', 'f_fin' => '>'],
                        ) ?: [];
                    }
                    if (count($cPar) === 1) {
                        $oTit = $this->personaDlRepository->findById($cPar[0]->getId_nom());
                        $sup_tit = $oTit?->getNombreApellidos() ?? '';
                    }
                }

                $aWhereH = [
                    'id_enc' => $id_enc,
                    'id_nom' => $id_nom,
                    'f_fin' => "'$hoy_iso'",
                ];
                $aOperadorH = ['f_fin' => '>'];
                $cHorarios1 = $this->encargoSacdHorarioRepository->getEncargoSacdHorarios($aWhereH, $aOperadorH) ?: [];
                $aOperadorH['f_fin'] = 'IS NULL';
                $cHorarios2 = $this->encargoSacdHorarioRepository->getEncargoSacdHorarios($aWhereH, $aOperadorH) ?: [];
                $cHorarios = array_merge($cHorarios1, $cHorarios2);

                $dedic_m = '';
                $dedic_t = '';
                $dedic_v = '';
                foreach ($cHorarios as $oEncargoSacdHorario) {
                    switch ((string)$oEncargoSacdHorario->getDia_ref()) {
                        case 'm':
                            $dedic_m = (string)$oEncargoSacdHorario->getDia_inc();
                            break;
                        case 't':
                            $dedic_t = (string)$oEncargoSacdHorario->getDia_inc();
                            break;
                        case 'v':
                            $dedic_v = (string)$oEncargoSacdHorario->getDia_inc();
                            break;
                    }
                }

                if ($id_tipo_enc === 5020 || $id_tipo_enc === 5030 || $id_tipo_enc === 6000) {
                    $grupo = 6;
                    $nombre_ubi = $oService->getTraduccion('e_' . $desc_enc, $idioma);
                    $dedicacion = $oService->dedicacion($id_nom, $id_enc, $idioma);
                    $dedic_m = is_string($dedicacion) ? $dedicacion : '';
                }

                if ($id_tipo_enc === 4002 || $id_tipo_enc === 1110 || $id_tipo_enc === 1210) {
                    continue;
                }

                if (!empty($id_enc)) {
                    $nombre_ubi_lugar = $nombre_ubi;
                    $nombre_ubi_lugar .= empty($desc_lugar) ? '' : ' (' . $desc_lugar . ')';
                    $grupoKey = (string) $grupo;
                    $array_modo[$s]['grupo'][$grupoKey][] = [
                        'desc_enc' => $desc_enc,
                        'nombre_ubi' => $nombre_ubi_lugar,
                        'seccion' => $seccion,
                        'dedic_m' => $dedic_m,
                        'dedic_t' => $dedic_t,
                        'dedic_v' => $dedic_v,
                        'sup_tit' => $sup_tit,
                    ];
                }
            }
            if ($observ !== '') {
                $array_modo[$s]['observ'][] = ['desc_enc' => $observ];
            }
        }

        return [
            'array_modo' => $this->normalizeArrayModo($array_modo),
            'lugar_fecha' => $lugar_fecha,
        ];
    }

    /**
     * @param array<int|string, mixed> $array_modo
     * @return array<int, array<string, mixed>>
     */
    private function normalizeArrayModo(array $array_modo): array
    {
        $out = [];
        foreach ($array_modo as $k => $row) {
            if (!is_array($row)) {
                continue;
            }
            $normalized = [];
            foreach ($row as $rk => $rv) {
                $normalized[(string) $rk] = $rv;
            }
            $out[(int) $k] = $normalized;
        }

        return $out;
    }
}
