<?php

namespace src\actividadtarifas\application;

use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;
use src\actividadtarifas\domain\entity\RelacionTarifaTipoActividad;
use src\actividadtarifas\domain\value_objects\SerieId;

/**
 * Mutacion: crea o actualiza una `RelacionTarifaTipoActividad`.
 *
 * Sucesor de la rama `update` del dispatcher legacy
 * `apps/actividadtarifas/controller/tarifa_tipo_actividad_ajax.php`.
 */
final class RelacionTarifaUpdate
{
    public static function execute(array $input): string
    {
        $id_item = (string)($input['id_item'] ?? '');
        $id_tarifa = (int)($input['id_tarifa'] ?? 0);
        $id_tipo_activ = (int)($input['id_tipo_activ'] ?? 0);

        if ($id_tarifa === 0) {
            return (string)_("debe indicar la tarifa");
        }
        if ($id_tipo_activ === 0) {
            return (string)_("debe indicar el tipo de actividad");
        }

        $repo = $GLOBALS['container']->get(RelacionTarifaTipoActividadRepositoryInterface::class);
        if ($id_item === 'nuevo' || $id_item === '') {
            $newId = $repo->getNewId();
            $oRelacion = new RelacionTarifaTipoActividad();
            $oRelacion->setId_item($newId);
        } else {
            $oRelacion = $repo->findById((int)$id_item);
            if ($oRelacion === null) {
                return (string)_("no se encuentra la relación");
            }
        }

        $oRelacion->setId_tarifa($id_tarifa);
        $oRelacion->setId_serie(SerieId::GENERAL);
        $oRelacion->setId_tipo_activ($id_tipo_activ);

        if ($repo->Guardar($oRelacion) === false) {
            return (string)_("hay un error, no se ha guardado")
                . "\n" . $repo->getErrorTxt();
        }

        return '';
    }
}
