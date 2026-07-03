<?php

namespace src\actividadtarifas\application;

use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;
use src\actividadtarifas\domain\entity\RelacionTarifaTipoActividad;
use src\actividadtarifas\domain\value_objects\SerieId;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Mutacion: crea o actualiza una `RelacionTarifaTipoActividad`.
 */
final class RelacionTarifaUpdate
{
    public function __construct(
        private RelacionTarifaTipoActividadRepositoryInterface $relacionTarifaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $id_item = FuncTablasSupport::inputString($input, 'id_item');
        $id_tarifa = FuncTablasSupport::inputInt($input, 'id_tarifa');
        $id_tipo_activ = FuncTablasSupport::inputInt($input, 'id_tipo_activ');

        if ($id_tarifa === 0) {
            return (string) _("debe indicar la tarifa");
        }
        if ($id_tipo_activ === 0) {
            return (string) _("debe indicar el tipo de actividad");
        }

        if ($id_item === 'nuevo' || $id_item === '') {
            $newId = $this->relacionTarifaRepository->getNewId();
            $oRelacion = new RelacionTarifaTipoActividad();
            $oRelacion->setId_item($newId);
        } else {
            $oRelacion = $this->relacionTarifaRepository->findById((int) $id_item);
            if ($oRelacion === null) {
                return (string) _("no se encuentra la relación");
            }
        }

        $oRelacion->setId_tarifa($id_tarifa);
        $oRelacion->setId_serie(SerieId::GENERAL);
        $oRelacion->setId_tipo_activ($id_tipo_activ);

        if ($this->relacionTarifaRepository->Guardar($oRelacion) === false) {
            return (string) _("hay un error, no se ha guardado")
                . "\n" . $this->relacionTarifaRepository->getErrorTxt();
        }

        return '';
    }
}
