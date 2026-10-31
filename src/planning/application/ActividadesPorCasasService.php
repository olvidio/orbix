<?php

namespace src\planning\application;

use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\criteria\ActividadAllField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\criteria\CasaField;
use src\ubis\domain\criteria\CentroEllasField;
use src\ubis\domain\entity\Ubi;

/**
 * Devuelve las actividades de cada casa en un periodo dado, agrupadas
 * por `nombre_ubi`.
 */
class ActividadesPorCasasService
{
    public function __construct(
        private ActividadRepositoryInterface $actividadRepository,
        private CasaDlRepositoryInterface $casaDlRepository,
        private CentroEllasRepositoryInterface $centroEllasRepository,
    ) {
    }

    /**
     * @param array<int,int|string>|null $aIdCdc ids de casa cuando `$Qcdc_sel === 9`
     * @return array{0: string, 1: array<int|string, array<string, list<array<string, mixed>>>>}
     */
    public function actividadesPorCasas(
        int $Qcdc_sel,
        DateTimeLocal $oIniPlanning,
        DateTimeLocal $oFinPlanning,
        int $sin_activ,
        DateTimeLocal $oFin,
        DateTimeLocal $oInicio,
        ?array $aIdCdc = null
    ): array {
        $fin_iso = $oFin->format('Y-m-d');
        $inicio_iso = $oInicio->format('Y-m-d');
        $sCdc = '';
        $cdc = [];
        $a_actividades = [];

        if ($Qcdc_sel < 10) {
            $criterio = Criteria::create();
            $cCentrosSf = [];
            switch ($Qcdc_sel) {
                case 1:
                    $criterio = $criterio->where(Filter::eq(CasaField::Sv, true))
                        ->andWhere(Filter::eq(CasaField::Sf, true));
                    break;
                case 2:
                    $criterio = $criterio->where(Filter::eq(CasaField::Sv, false))
                        ->andWhere(Filter::eq(CasaField::Sf, true));
                    break;
                case 3:
                    $criterio = $criterio->where(Filter::eq(CasaField::Sv, true))
                        ->andWhere(Filter::eq(CasaField::Sf, true))
                        ->andWhere(Filter::eq(CasaField::TipoUbi, 'cdcdl'))
                        ->andWhere(Filter::matches(CasaField::TipoCasa, 'cdc|cdr'));
                    break;
                case 4:
                    $criterio = $criterio->where(Filter::eq(CasaField::Sv, true));
                    break;
                case 5:
                    $criterio = $criterio->where(Filter::eq(CasaField::Sf, true));
                    break;
                case 6:
                    $criterio = $criterio->where(Filter::eq(CasaField::Sf, true));
                    $cCentrosSf = $this->centroEllasRepository->matching(
                        Criteria::create()
                            ->where(Filter::eq(CentroEllasField::Cdc, true))
                            ->orderBy(CentroEllasField::NombreUbi)
                    );
                    break;
                case 9:
                    $a_id_cdc = $aIdCdc ?? [];
                    $a_id_cdc = array_filter($a_id_cdc, static fn ($v) => $v !== '');
                    if (!empty($a_id_cdc)) {
                        $sCdc = implode(',', $a_id_cdc);
                        $criterio = $criterio->where(
                            Filter::in(CasaField::IdUbi, array_map('intval', array_values($a_id_cdc)))
                        );
                    }
                    break;
            }
            $cCasasDl = $this->casaDlRepository->matching($criterio->orderBy(CasaField::NombreUbi));

            if ($Qcdc_sel === 6) {
                foreach ($cCentrosSf as $oCentroSf) {
                    $cCasasDl[] = $oCentroSf;
                }
            }

            $p = 0;
            foreach ($cCasasDl as $oCasaDl) {
                $id_ubi = $oCasaDl->getId_ubi();
                $nombre_ubi = $oCasaDl->getNombre_ubi();
                $cdc[$p] = "u#$id_ubi#$nombre_ubi";

                $a_cdc = $this->actividadRepository->actividadesDeUnaCasa((int)$id_ubi, $oIniPlanning, $oFinPlanning, $Qcdc_sel);
                $a_actividades[$nombre_ubi] = [$cdc[$p] => $a_cdc];
                $p++;
            }
            ksort($a_actividades);
            $cdc[$p + 1] = "##";
            $a_actividades[] = [$cdc[$p + 1] => []];
        } else {
            $criteriaUbis = Criteria::create();
            switch ($Qcdc_sel) {
                case 11:
                    $criteriaUbis = $criteriaUbis->andWhere(Filter::matches(ActividadAllField::IdTipoActiv, '^1'));
                    break;
                case 12:
                    $criteriaUbis = $criteriaUbis->andWhere(Filter::matches(ActividadAllField::IdTipoActiv, '^2'));
                    break;
            }
            $criteriaUbis = $criteriaUbis
                ->andWhere(Filter::lte(ActividadAllField::FIni, $fin_iso))
                ->andWhere(Filter::gte(ActividadAllField::FFin, $inicio_iso))
                ->andWhere(Filter::lt(ActividadAllField::Status, 4));

            $aUbis = $this->actividadRepository->getUbis($criteriaUbis);
            $p = 0;
            foreach ($aUbis as $id_ubi) {
                if (empty($id_ubi)) {
                    $nombre_ubi = _("por determinar");
                    $cdc[$p] = "u#2#$nombre_ubi";
                } elseif ($id_ubi == 1) {
                    $nombre_ubi = _("otros lugares");
                    $cdc[$p] = "u#$id_ubi#$nombre_ubi";
                } else {
                    $oCasa = Ubi::NewUbi($id_ubi);
                    if ($oCasa === null) {
                        continue;
                    }
                    $id_ubi = $oCasa->getId_ubi();
                    $nombre_ubi = $oCasa->getNombre_ubi();
                    $cdc[$p] = "u#$id_ubi#$nombre_ubi";
                }
                $a_cdc = $this->actividadRepository->actividadesDeUnaCasa((int)$id_ubi, $oIniPlanning, $oFinPlanning, $Qcdc_sel);
                $a_actividades[$nombre_ubi] = [$cdc[$p] => $a_cdc];
                $p++;
            }
            ksort($a_actividades);
            $cdc[$p + 1] = "##";
            $a_actividades[] = [$cdc[$p + 1] => []];
        }

        return [$sCdc, $a_actividades];
    }
}
