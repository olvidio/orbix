<?php

namespace src\actividadtarifas\application;

use src\shared\config\ConfigGlobal;
use src\actividadtarifas\application\services\TipoTarifaDropdown;
use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;
use web\TiposActividades;

/**
 * Data builder para el formulario modificar/nuevo de
 * `RelacionTarifaTipoActividad`.
 *
 * Sucesor del controlador legacy
 * `apps/actividadtarifas/controller/tarifa_tipo_actividad_form.php`.
 */
final class RelacionTarifaFormData
{
    /**
     * @return array{
     *   es_nuevo: bool,
     *   id_item: string,
     *   id_tipo_activ: int,
     *   nom_tipo_activ: string,
     *   isfsv: int,
     *   id_tarifa_sel: int,
     *   opciones_tarifa: array<int,string>
     * }
     */
    public static function execute(array $input): array
    {
        $id_item = (string)($input['id_item'] ?? '');
        $es_nuevo = $id_item === '' || $id_item === 'nuevo';

        $id_tipo_activ = 0;
        $nom_tipo_activ = '';
        $isfsv = 0;
        $id_tarifa_sel = 0;

        if (!$es_nuevo) {
            $repoRel = $GLOBALS['container']->get(RelacionTarifaTipoActividadRepositoryInterface::class);
            $oRelacion = $repoRel->findById((int)$id_item);
            if ($oRelacion !== null) {
                $id_tipo_activ = $oRelacion->getId_tipo_activ();
                $id_tarifa_sel = $oRelacion->getId_tarifa();
                $oTipoActiv = new TiposActividades($id_tipo_activ);
                $nom_tipo_activ = $oTipoActiv->getNom();
                $isfsv = (int)$oTipoActiv->getSfsvId();
            }
        } else {
            $isfsv = ConfigGlobal::mi_sfsv();
        }

        $opciones_tarifa = TipoTarifaDropdown::opciones($isfsv);

        return [
            'es_nuevo' => $es_nuevo,
            'id_item' => $es_nuevo ? 'nuevo' : $id_item,
            'id_tipo_activ' => $id_tipo_activ,
            'nom_tipo_activ' => $nom_tipo_activ,
            'isfsv' => $isfsv,
            'id_tarifa_sel' => $id_tarifa_sel,
            'opciones_tarifa' => $opciones_tarifa,
        ];
    }
}
