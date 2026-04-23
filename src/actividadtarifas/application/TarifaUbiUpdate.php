<?php

namespace src\actividadtarifas\application;

use src\ubis\domain\contracts\TarifaUbiRepositoryInterface;
use src\ubis\domain\entity\TarifaUbi;

/**
 * Mutacion: crea o actualiza una `TarifaUbi`.
 *
 * Sucesor de la rama `update` del dispatcher legacy
 * `apps/actividadtarifas/controller/tarifa_ajax.php`.
 */
final class TarifaUbiUpdate
{
    public static function execute(array $input): string
    {
        $id_item = (int)($input['id_item'] ?? 0);
        $id_ubi = (int)($input['id_ubi'] ?? 0);
        $year = (int)($input['year'] ?? 0);
        $id_tarifa = (int)($input['id_tarifa'] ?? 0);
        $id_serie = (int)($input['id_serie'] ?? 0);
        $cantidad = (string)($input['cantidad'] ?? '');
        $observ = (string)($input['observ'] ?? '');

        $repo = $GLOBALS['container']->get(TarifaUbiRepositoryInterface::class);
        if ($id_item !== 0) {
            $oTarifaUbi = $repo->findById($id_item);
            if ($oTarifaUbi === null) {
                return (string)_("no se encuentra la tarifa");
            }
        } else {
            $newId = $repo->getNewId();
            $oTarifaUbi = new TarifaUbi();
            $oTarifaUbi->setId_item($newId);
        }

        if ($id_ubi !== 0) {
            $oTarifaUbi->setId_ubi($id_ubi);
        }
        if ($year !== 0) {
            $oTarifaUbi->setYear($year);
        }
        if ($id_tarifa !== 0) {
            $oTarifaUbi->setId_tarifa($id_tarifa);
        }
        if ($id_serie !== 0) {
            $oTarifaUbi->setId_serie($id_serie);
        }
        if ($cantidad !== '') {
            $oTarifaUbi->setCantidad((float)$cantidad);
        }
        if ($observ !== '') {
            $oTarifaUbi->setObserv($observ);
        }

        if ($repo->Guardar($oTarifaUbi) === false) {
            return (string)_("hay un error, no se ha guardado")
                . "\n" . $repo->getErrorTxt();
        }

        return '';
    }
}
