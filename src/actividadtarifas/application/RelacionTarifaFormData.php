<?php

namespace src\actividadtarifas\application;

use src\actividadtarifas\application\services\TipoTarifaDropdown;
use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;
use src\actividades\domain\entity\TiposActividades;
use src\shared\config\ConfigGlobal;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Data builder para el formulario modificar/nuevo de
 * `RelacionTarifaTipoActividad`.
 */
final class RelacionTarifaFormData
{
    public function __construct(
        private RelacionTarifaTipoActividadRepositoryInterface $relacionTarifaRepository,
        private TipoTarifaDropdown $tipoTarifaDropdown,
    ) {
    }

    /**
     * @param array<string, mixed> $input
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
    public function execute(array $input): array
    {
        $id_item = FuncTablasSupport::inputString($input, 'id_item');
        $es_nuevo = $id_item === '' || $id_item === 'nuevo';

        $id_tipo_activ = 0;
        $nom_tipo_activ = '';
        $isfsv = 0;
        $id_tarifa_sel = 0;

        if (!$es_nuevo) {
            $oRelacion = $this->relacionTarifaRepository->findById((int) $id_item);
            if ($oRelacion !== null) {
                $id_tipo_activ = $oRelacion->getId_tipo_activ();
                $id_tarifa_sel = $oRelacion->getId_tarifa();
                $oTipoActiv = new TiposActividades($id_tipo_activ);
                $nom_tipo_activ = $oTipoActiv->getNom();
                $isfsv = (int) $oTipoActiv->getSfsvId();
            }
        } else {
            $isfsv = ConfigGlobal::mi_sfsv();
        }

        $opciones_tarifa = $this->tipoTarifaDropdown->opciones($isfsv);

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
