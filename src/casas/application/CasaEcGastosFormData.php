<?php

namespace src\casas\application;

use src\casas\domain\contracts\UbiGastoRepositoryInterface;
use src\casas\domain\value_objects\UbiGastoTipo;
use src\ubis\domain\contracts\CasaDlRepositoryInterface;

/**
 * Data builder: formulario anual con gastos y aportaciones (sv/sf)
 * por mes de una casa. Sucesor de la rama `que=getGastos` de
 * `apps/casas/controller/casa_ec_ajax.php`.
 */
final class CasaEcGastosFormData
{
    public static function execute(array $input): array
    {
        $year = (int)($input['year'] ?? (int)date('Y'));
        /** @var array $ids_ubi */
        $ids_ubi = (array)($input['id_cdc'] ?? []);

        $aGrupos = [];
        $CasaDl = $GLOBALS['container']->get(CasaDlRepositoryInterface::class);
        foreach ($ids_ubi as $id_ubi) {
            $id_ubi = (int)$id_ubi;
            if ($id_ubi === 0) { continue; }
            $oCasa = $CasaDl->findById($id_ubi);
            if ($oCasa === null) { continue; }
            $aGrupos[$id_ubi] = $oCasa->getNombreUbiVo()?->value() ?? '';
        }
        if ($aGrupos === []) {
            return [
                'ok' => false,
                'error' => (string)_("Debe seleccionar una casa."),
                'casas' => [],
                'year' => $year,
            ];
        }

        $UbiGastoRepository = $GLOBALS['container']->get(UbiGastoRepositoryInterface::class);
        $casas = [];
        foreach ($aGrupos as $id_ubi => $titulo) {
            $id_ubi = (int)$id_ubi;
            $aWhere = [
                'id_ubi' => $id_ubi,
                'f_gasto' => "'$year/1/1','$year/12/31'",
                '_ordre' => 'f_gasto',
            ];
            $aOperador = ['f_gasto' => 'BETWEEN'];
            $cGastos = $UbiGastoRepository->getUbisGastos($aWhere, $aOperador);
            $aGastos = [];
            if (is_array($cGastos)) {
                foreach ($cGastos as $oUbiGasto) {
                    $oFecha = $oUbiGasto->getF_gasto();
                    $mes = (int)$oFecha->format('n');
                    $tipo = $oUbiGasto->getTipoVo()?->value() ?? 0;
                    $cantidad = $oUbiGasto->getCantidadVo()?->value() ?? 0.0;
                    $aGastos[$mes][$tipo] = $cantidad;
                }
            }

            $meses = [];
            $suma_g = 0.0; $suma_sv = 0.0; $suma_sf = 0.0;
            for ($m = 1; $m < 13; $m++) {
                $g = (float)($aGastos[$m][UbiGastoTipo::GASTO] ?? 0);
                $ap_sv = (float)($aGastos[$m][UbiGastoTipo::APORTACION_SV] ?? 0);
                $ap_sf = (float)($aGastos[$m][UbiGastoTipo::APORTACION_SF] ?? 0);
                $suma_g += $g;
                $suma_sv += $ap_sv;
                $suma_sf += $ap_sf;
                $meses[] = [
                    'mes' => $m,
                    'g' => $g,
                    'ap_sv' => $ap_sv,
                    'ap_sf' => $ap_sf,
                ];
            }
            $casas[] = [
                'id_ubi' => $id_ubi,
                'titulo' => $titulo,
                'meses' => $meses,
                'suma_g' => $suma_g,
                'suma_sv' => $suma_sv,
                'suma_sf' => $suma_sf,
            ];
        }

        return [
            'ok' => true,
            'error' => '',
            'casas' => $casas,
            'year' => $year,
        ];
    }
}
