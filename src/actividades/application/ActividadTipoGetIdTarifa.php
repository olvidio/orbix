<?php

namespace src\actividades\application;

use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;
use src\actividadtarifas\domain\criteria\RelacionTarifaTipoActividadField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;

/**
 * Devuelve el id_tarifa asociado a un id_tipo_activ. Portado del case
 * `id_tarifa` del dispatcher legacy.
 *
 * Nota: en el codigo legacy el caso hacia `return` en medio del switch, que a
 * nivel de fichero equivale a salir sin emitir salida. Aqui devolvemos el
 * valor como string para que el controlador HTTP lo haga visible via echo,
 * arreglando ese comportamiento silencioso.
 */
class ActividadTipoGetIdTarifa
{
    public function __construct(
        private RelacionTarifaTipoActividadRepositoryInterface $relacionTarifaTipoActividadRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input = []): string
    {
        $id_tipo_activ = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'entrada');
        $RelacionTarifaTipoActividadRepository = $this->relacionTarifaTipoActividadRepository;
        $cActiTipoTarifa = $RelacionTarifaTipoActividadRepository->matching(
            Criteria::create()
                ->where(Filter::eq(RelacionTarifaTipoActividadField::IdTipoActiv, $id_tipo_activ))
                ->orderBy(RelacionTarifaTipoActividadField::IdSerie)
        );

        if ($cActiTipoTarifa !== []) {
            return (string) $cActiTipoTarifa[0]->getId_tarifa();
        }

        return '';
    }
}
