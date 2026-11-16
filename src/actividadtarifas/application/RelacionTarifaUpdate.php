<?php

namespace src\actividadtarifas\application;

use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;
use src\actividadtarifas\domain\criteria\RelacionTarifaTipoActividadField as F;
use src\actividadtarifas\domain\entity\RelacionTarifaTipoActividad;
use src\actividadtarifas\domain\value_objects\SerieId;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;

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
        $id_item = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'id_item');
        $id_tarifa = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_tarifa');
        $id_tipo_activ = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_tipo_activ');

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

        // (id_tipo_activ, id_serie) es UNIQUE en la tabla: sin esta comprobación el INSERT lanzaba una
        // excepción y el endpoint no devolvía JSON («error de comunicación con el servidor»).
        $cMismoTipo = $this->relacionTarifaRepository->matching(
            Criteria::create()
                ->where(Filter::eq(F::IdTipoActiv, $id_tipo_activ))
                ->andWhere(Filter::eq(F::IdSerie, SerieId::GENERAL))
        );
        foreach ($cMismoTipo as $oOtra) {
            if ($oOtra->getId_item() !== $oRelacion->getId_item()) {
                return (string) _("este tipo de actividad ya tiene una tarifa");
            }
        }

        $oRelacion->setId_tarifa($id_tarifa);
        $oRelacion->setId_serie(SerieId::GENERAL);
        $oRelacion->setId_tipo_activ($id_tipo_activ);

        try {
            $guardado = $this->relacionTarifaRepository->Guardar($oRelacion);
            $errorTxt = $guardado ? '' : $this->relacionTarifaRepository->getErrorTxt();
        } catch (\RuntimeException $e) {
            $guardado = false;
            $errorTxt = $e->getMessage();
        }
        if ($guardado === false) {
            return (string) _("hay un error, no se ha guardado") . "\n" . $errorTxt;
        }

        return '';
    }
}
