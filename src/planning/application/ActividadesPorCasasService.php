<?php

namespace src\planning\application;

use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CentroEllasRepositoryInterface;
use src\ubis\domain\entity\Ubi;

/**
 * Devuelve las actividades de cada casa en un periodo dado, agrupadas
 * por `nombre_ubi`. Para `$Qcdc_sel >= 10` se buscan las actividades
 * directamente y se agrupan por `id_ubi`.
 *
 * Migrado desde `apps/planning/domain/ActividadesPorCasas.php`
 * (slice 2 de la migracion del modulo planning).
 */
class ActividadesPorCasasService
{
    /**
     * @param array<int,int|string>|null $aIdCdc ids de casa cuando
     *        `$Qcdc_sel === 9` (seleccion multiple). Si es `null` se
     *        busca en `$_POST['id_cdc']`.
     * @return array{0: string, 1: array<string, array<int, array<string, array>>>}
     */
    public static function actividadesPorCasas(
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
        $ActividadRepository = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
        $sCdc = '';
        $cdc = [];
        $a_actividades = [];

        if ($Qcdc_sel < 10) {
            $aWhere = [];
            $aOperador = [];
            $cCentrosSf = [];
            switch ($Qcdc_sel) {
                case 1:
                    $aWhere['sv'] = 't';
                    $aWhere['sf'] = 't';
                    break;
                case 2:
                    $aWhere['sv'] = 'f';
                    $aWhere['sf'] = 't';
                    break;
                case 3:
                    $aWhere['sv'] = 't';
                    $aWhere['sf'] = 't';
                    $aWhere['tipo_ubi'] = 'cdcdl';
                    $aWhere['tipo_casa'] = 'cdc|cdr';
                    $aOperador['tipo_casa'] = '~';
                    break;
                case 4:
                    $aWhere['sv'] = 't';
                    break;
                case 5:
                    $aWhere['sf'] = 't';
                    break;
                case 6:
                    $aWhere['sf'] = 't';
                    $GesCentrosSf = $GLOBALS['container']->get(CentroEllasRepositoryInterface::class);
                    $cCentrosSf = $GesCentrosSf->getCentros(['cdc' => 't', '_ordre' => 'nombre_ubi']);
                    break;
                case 9:
                    $a_id_cdc = $aIdCdc ?? ((array)filter_input(INPUT_POST, 'id_cdc', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY));
                    $a_id_cdc = array_filter($a_id_cdc, static fn($v) => $v !== null && $v !== '');
                    if (!empty($a_id_cdc)) {
                        $sCdc = implode(',', $a_id_cdc);
                        $aWhere['id_ubi'] = $sCdc;
                        $aOperador['id_ubi'] = 'IN';
                    }
                    break;
            }
            $aWhere['_ordre'] = 'nombre_ubi';
            $GesCasaDl = $GLOBALS['container']->get(CasaDlRepositoryInterface::class);
            $cCasasDl = $GesCasaDl->getCasas($aWhere, $aOperador);

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

                $a_cdc = $ActividadRepository->actividadesDeUnaCasa($id_ubi, $oIniPlanning, $oFinPlanning, $Qcdc_sel);
                if ($a_cdc !== false) {
                    $a_actividades[$nombre_ubi] = [$cdc[$p] => $a_cdc];
                    $p++;
                } elseif ($sin_activ === 1) {
                    $a_actividades[$nombre_ubi] = [$cdc[$p] => []];
                    $p++;
                }
            }
            ksort($a_actividades);
            // Linea en blanco al final para que se vean las 3 divisiones aun con todas las casas ocupadas.
            $cdc[$p + 1] = "##";
            $a_actividades[] = [$cdc[$p + 1] => []];
        } else {
            $aWhere = [];
            $aOperador = [];
            switch ($Qcdc_sel) {
                case 11:
                    $aWhere['id_tipo_activ'] = '^1';
                    $aOperador['id_tipo_activ'] = '~';
                    break;
                case 12:
                    $aWhere['id_tipo_activ'] = '^2';
                    $aOperador['id_tipo_activ'] = '~';
                    break;
            }
            $aWhere['f_ini'] = "'$fin_iso'";
            $aOperador['f_ini'] = '<=';
            $aWhere['f_fin'] = "'$inicio_iso'";
            $aOperador['f_fin'] = '>=';
            $aWhere['status'] = 4;
            $aOperador['status'] = '<';
            $aWhere['_ordre'] = 'id_ubi';

            $aUbis = $ActividadRepository->getUbis($aWhere, $aOperador);
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
                    $id_ubi = $oCasa->getId_ubi();
                    $nombre_ubi = $oCasa->getNombre_ubi();
                    $cdc[$p] = "u#$id_ubi#$nombre_ubi";
                }
                $a_cdc = $ActividadRepository->actividadesDeUnaCasa($id_ubi, $oIniPlanning, $oFinPlanning, $Qcdc_sel);
                if ($a_cdc !== false) {
                    $a_actividades[$nombre_ubi] = [$cdc[$p] => $a_cdc];
                    $p++;
                } elseif ($sin_activ === 1) {
                    $a_actividades[$nombre_ubi] = [$cdc[$p] => []];
                    $p++;
                }
            }
            ksort($a_actividades);
            $cdc[$p + 1] = "##";
            $a_actividades[] = [$cdc[$p + 1] => []];
        }

        return [$sCdc, $a_actividades];
    }
}
