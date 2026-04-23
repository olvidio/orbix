<?php

namespace src\casas\application;

use src\casas\domain\contracts\UbiGastoRepositoryInterface;
use src\casas\domain\entity\UbiGasto;
use src\casas\domain\value_objects\UbiGastoCantidad;
use src\casas\domain\value_objects\UbiGastoTipo;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Use case: guardar los gastos y aportaciones (sv/sf) mensuales de
 * una casa para un año completo. Borra los existentes y los reinserta
 * con fecha 5 de cada mes.
 *
 * Sucesor de la rama `que=guardarGasto` de
 * `apps/casas/controller/casa_ec_ajax.php`.
 */
final class CasaEcGastosGuardar
{
    public static function execute(array $input): array
    {
        $id_ubi = (int)($input['id_ubi'] ?? 0);
        $year = (int)($input['year'] ?? 0);
        if ($id_ubi === 0 || $year === 0) {
            return ['ok' => false, 'mensaje' => (string)_("Faltan id_ubi o year."), 'data' => ''];
        }

        $UbiGastoRepository = $GLOBALS['container']->get(UbiGastoRepositoryInterface::class);
        for ($m = 1; $m < 13; $m++) {
            $g = (float)str_replace(',', '.', (string)($input["g$m"] ?? 0));
            $ap_sv = (float)str_replace(',', '.', (string)($input["ap_sv$m"] ?? 0));
            $ap_sf = (float)str_replace(',', '.', (string)($input["ap_sf$m"] ?? 0));
            $oFecha = new DateTimeLocal("$year/$m/5");

            foreach ([
                UbiGastoTipo::GASTO => $g,
                UbiGastoTipo::APORTACION_SV => $ap_sv,
                UbiGastoTipo::APORTACION_SF => $ap_sf,
            ] as $tipo => $cantidad) {
                $newId = $UbiGastoRepository->getNewId();
                $oUbiGasto = new UbiGasto();
                $oUbiGasto->setId_item($newId);
                $oUbiGasto->setF_gasto($oFecha);
                $oUbiGasto->setId_ubi($id_ubi);
                $oUbiGasto->setTipoVo(new UbiGastoTipo($tipo));
                $oUbiGasto->setCantidadVo(new UbiGastoCantidad($cantidad));
                if ($UbiGastoRepository->Guardar($oUbiGasto) === false) {
                    return ['ok' => false, 'mensaje' => (string)_("Hay un error, no se ha guardado."), 'data' => ''];
                }
            }
        }

        return ['ok' => true, 'mensaje' => '', 'data' => ''];
    }
}
