<?php

namespace src\actividadtarifas\application;

use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\domain\value_objects\TarifaModoId;

/**
 * Data builder para el formulario modificar/nuevo de `TipoTarifa`.
 *
 * Sucesor de la rama `tar_form` del dispatcher legacy
 * `apps/actividadtarifas/controller/tarifa_ajax.php`.
 */
final class TipoTarifaFormData
{
    /**
     * @return array{
     *   id_tarifa: string,
     *   es_nuevo: bool,
     *   letra: string,
     *   modo: int,
     *   observ: string,
     *   opciones_modo: array<int,string>
     * }
     */
    public static function execute(array $input): array
    {
        $id_tarifa = (string)($input['id_tarifa'] ?? '');
        $es_nuevo = $id_tarifa === '' || $id_tarifa === 'nuevo';

        $letra = '';
        $modo = 0;
        $observ = '';

        if (!$es_nuevo) {
            $repo = $GLOBALS['container']->get(TipoTarifaRepositoryInterface::class);
            $oTipoTarifa = $repo->findById((int)$id_tarifa);
            if ($oTipoTarifa !== null) {
                $letra = (string)$oTipoTarifa->getLetra();
                $modo = (int)$oTipoTarifa->getModo();
                $observ = (string)$oTipoTarifa->getObserv();
            }
        }

        return [
            'id_tarifa' => $es_nuevo ? 'nuevo' : $id_tarifa,
            'es_nuevo' => $es_nuevo,
            'letra' => $letra,
            'modo' => $modo,
            'observ' => $observ,
            'opciones_modo' => TarifaModoId::getArrayModo(),
        ];
    }
}
