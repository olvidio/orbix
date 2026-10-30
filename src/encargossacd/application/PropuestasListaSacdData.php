<?php

namespace src\encargossacd\application;

use src\encargossacd\application\services\EncargoAplicacionService;
use src\encargossacd\domain\contracts\EncargoRepositoryInterface;
use src\encargossacd\domain\contracts\EncargoSacdObservRepositoryInterface;
use src\encargossacd\domain\contracts\PropuestaEncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\contracts\PropuestaEncargoSacdRepositoryInterface;
use src\encargossacd\domain\criteria\EncargoSacdObservField;
use src\encargossacd\domain\criteria\PropuestaEncargoSacdField;
use src\encargossacd\domain\criteria\PropuestaEncargoSacdHorarioField;
use src\personas\domain\contracts\PersonaDlRepositoryInterface;
use src\personas\domain\criteria\PersonaField;
use src\shared\config\ConfigGlobal;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;

/**
 * Listado de propuestas por SACD (tablas staging `propuesta_*`).
 * Sustituye `frontend/encargossacd/controller/propuestas_lista_sacd.php`.
 */
final class PropuestasListaSacdData
{
    public function __construct(
        private EncargoAplicacionService $aplicacionService,
        private CentroDlRepositoryInterface $centroDlRepository,
        private CentroEllasRepositoryInterface $centroEllasRepository,
        private EncargoRepositoryInterface $encargoRepository,
        private PropuestaEncargoSacdHorarioRepositoryInterface $propuestaEncargoSacdHorarioRepository,
        private EncargoSacdObservRepositoryInterface $encargoSacdObservRepository,
        private PropuestaEncargoSacdRepositoryInterface $propuestaEncargoSacdRepository,
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
        $patronTabla = match ($sel) {
            'nagd' => '^n|^a',
            'sssc' => '^sss',
            default => null,
        };
        if ($patronTabla !== null) {
            $cPersonas = $this->personaDlRepository->matching(
                Criteria::create()
                    ->where(Filter::matches(PersonaField::IdTabla, $patronTabla))
                    ->andWhere(Filter::eq(PersonaField::Situacion, 'A'))
                    ->andWhere(Filter::eq(PersonaField::Sacd, true))
                    ->andWhere(Filter::eq(PersonaField::Dl, ConfigGlobal::mi_delef()))
                    ->orderBy(PersonaField::Apellido1)
                    ->addOrderBy(PersonaField::Apellido2)
                    ->addOrderBy(PersonaField::Nom)
            ) ?: [];
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

            $cEncargoSacdObserv = $this->encargoSacdObservRepository->matching(
                Criteria::create()->where(Filter::eq(EncargoSacdObservField::IdNom, $id_nom))
            );
            $observ = '';
            if (count($cEncargoSacdObserv) > 0) {
                $observ = (string)$cEncargoSacdObserv[0]->getObserv();
            }

            $criterioPropuesta = Criteria::create()
                ->where(Filter::eq(PropuestaEncargoSacdField::IdNomNew, $id_nom))
                ->orderBy(PropuestaEncargoSacdField::Modo);
            $cEncargosSacd1 = $this->propuestaEncargoSacdRepository->matching(
                $criterioPropuesta->andWhere(Filter::isNull(PropuestaEncargoSacdField::FFin))
            );
            $cEncargosSacd2 = $this->propuestaEncargoSacdRepository->matching(
                $criterioPropuesta->andWhere(Filter::gt(PropuestaEncargoSacdField::FFin, $hoy_iso))
            );
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
                    $criterioPar = Criteria::create()
                        ->where(Filter::eq(PropuestaEncargoSacdField::IdEnc, $id_enc))
                        ->andWhere(Filter::eq(PropuestaEncargoSacdField::Modo, 4));
                    $cPar = $this->propuestaEncargoSacdRepository->matching(
                        $criterioPar->andWhere(Filter::isNull(PropuestaEncargoSacdField::FFin))
                    );
                    if (count($cPar) === 0) {
                        $cPar = $this->propuestaEncargoSacdRepository->matching(
                            $criterioPar->andWhere(Filter::gt(PropuestaEncargoSacdField::FFin, $hoy_iso))
                        );
                    }
                    if (count($cPar) === 1) {
                        $oSup = $this->personaDlRepository->findById((int) ($cPar[0]->getId_nom_new() ?? 0));
                        $sup_tit = $oSup?->getNombreApellidos() ?? '';
                    }
                } elseif ($modo === 4) {
                    $criterioPar = Criteria::create()
                        ->where(Filter::eq(PropuestaEncargoSacdField::IdEnc, $id_enc))
                        ->andWhere(Filter::matches(PropuestaEncargoSacdField::Modo, '[23]'));
                    $cPar = $this->propuestaEncargoSacdRepository->matching(
                        $criterioPar->andWhere(Filter::isNull(PropuestaEncargoSacdField::FFin))
                    );
                    if (count($cPar) === 0) {
                        $cPar = $this->propuestaEncargoSacdRepository->matching(
                            $criterioPar->andWhere(Filter::gt(PropuestaEncargoSacdField::FFin, $hoy_iso))
                        );
                    }
                    if (count($cPar) === 1) {
                        $oTit = $this->personaDlRepository->findById((int) ($cPar[0]->getId_nom_new() ?? 0));
                        $sup_tit = $oTit?->getNombreApellidos() ?? '';
                    }
                }

                $criterioH = Criteria::create()
                    ->where(Filter::eq(PropuestaEncargoSacdHorarioField::IdEnc, $id_enc))
                    ->andWhere(Filter::eq(PropuestaEncargoSacdHorarioField::IdNom, $id_nom));
                $cHorarios1 = $this->propuestaEncargoSacdHorarioRepository->matching(
                    $criterioH->andWhere(Filter::gt(PropuestaEncargoSacdHorarioField::FFin, $hoy_iso))
                );
                $cHorarios2 = $this->propuestaEncargoSacdHorarioRepository->matching(
                    $criterioH->andWhere(Filter::isNull(PropuestaEncargoSacdHorarioField::FFin))
                );
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
