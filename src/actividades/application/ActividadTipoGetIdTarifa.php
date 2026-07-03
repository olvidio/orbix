<?php

namespace src\actividades\application;

use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;
use src\shared\domain\helpers\FuncTablasSupport;

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
        $id_tipo_activ = FuncTablasSupport::inputString($input, 'entrada');
        $aWhere = [
            'id_tipo_activ' => $id_tipo_activ,
            '_ordre' => 'id_serie',
        ];

        $RelacionTarifaTipoActividadRepository = $this->relacionTarifaTipoActividadRepository;
        $cActiTipoTarifa = $RelacionTarifaTipoActividadRepository->getTipoActivTarifas($aWhere);

        if ($cActiTipoTarifa !== []) {
            return (string) $cActiTipoTarifa[0]->getId_tarifa();
        }

        return '';
    }
}
