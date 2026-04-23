<?php

namespace src\actividadtarifas\application;

use core\ConfigGlobal;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\domain\entity\TipoTarifa;

/**
 * Mutacion: crea o actualiza un `TipoTarifa`.
 *
 * Sucesor de la rama `tar_update` del dispatcher legacy
 * `apps/actividadtarifas/controller/tarifa_ajax.php`.
 */
final class TipoTarifaUpdate
{
    /**
     * Devuelve texto de error vacio si ha ido bien. El controlador
     * HTTP lo envuelve con `ContestarJson::enviar(...)`.
     */
    public static function execute(array $input): string
    {
        $id_tarifa = (string)($input['id_tarifa'] ?? '');
        $letra = (string)($input['letra'] ?? '');
        $modo = (string)($input['modo'] ?? '');
        $observ = (string)($input['observ'] ?? '');

        $repo = $GLOBALS['container']->get(TipoTarifaRepositoryInterface::class);

        if ($id_tarifa === 'nuevo' || $id_tarifa === '') {
            $newId = $repo->getNewId();
            $oTipoTarifa = new TipoTarifa();
            $oTipoTarifa->setId_tarifa($newId);
            $oTipoTarifa->setSfsv(ConfigGlobal::mi_sfsv());
        } else {
            $oTipoTarifa = $repo->findById((int)$id_tarifa);
            if ($oTipoTarifa === null) {
                return (string)_("no se encuentra la tarifa");
            }
        }

        if ($letra !== '') {
            $oTipoTarifa->setLetra($letra);
        }
        if ($modo !== '') {
            $oTipoTarifa->setModo((int)$modo);
        }
        if ($observ !== '') {
            $oTipoTarifa->setObserv($observ);
        }

        if ($repo->Guardar($oTipoTarifa) === false) {
            return (string)_("hay un error, no se ha guardado")
                . "\n" . $repo->getErrorTxt();
        }

        return '';
    }
}
