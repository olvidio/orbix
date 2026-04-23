<?php

namespace src\actividadtarifas\application;

use src\ubis\domain\contracts\TarifaUbiRepositoryInterface;

/**
 * Mutacion: actualiza en lote las cantidades (`cantidad`) de varias
 * `TarifaUbi` desde el estudio economico de casa.
 *
 * Input: `inc_cantidad` es un `array<string,string>` donde la clave
 * es `"<id_tarifa>#<id_item>"` y el valor la cantidad a guardar.
 *
 * Sucesor de la rama `update_inc` del dispatcher legacy
 * `apps/actividadtarifas/controller/tarifa_ajax.php`. Consumido por
 * `apps/casas/controller/calendario_ubi_resumen_ajax.php`.
 */
final class TarifaUbiUpdateInc
{
    public static function execute(array $input): string
    {
        $inc_cantidad = $input['inc_cantidad'] ?? null;
        if (!is_array($inc_cantidad) || empty($inc_cantidad)) {
            return '';
        }

        $repo = $GLOBALS['container']->get(TarifaUbiRepositoryInterface::class);
        $errores = [];

        foreach ($inc_cantidad as $key => $cantidad) {
            $tarifa = strtok((string)$key, '#');
            $id_item = (int)strtok('#');
            $cantidadNum = (int)round((float)$cantidad);
            unset($tarifa); // token auxiliar

            if ($id_item === 0 && $cantidadNum === 0) {
                continue;
            }
            if ($id_item === 0) {
                continue;
            }

            $oTarifaUbi = $repo->findById($id_item);
            if ($oTarifaUbi === null) {
                continue;
            }
            $oTarifaUbi->setCantidad($cantidadNum);

            if ($repo->Guardar($oTarifaUbi) === false) {
                $errores[] = (string)_("hay un error, no se ha guardado")
                    . "\n" . $repo->getErrorTxt();
            }
        }

        return implode("\n", $errores);
    }
}
