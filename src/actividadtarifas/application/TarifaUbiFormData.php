<?php

namespace src\actividadtarifas\application;

use src\shared\config\ConfigGlobal;
use src\actividadtarifas\application\services\TipoTarifaDropdown;
use src\actividadtarifas\domain\value_objects\SerieId;
use src\ubis\domain\contracts\TarifaUbiRepositoryInterface;

/**
 * Data builder para el formulario de `TarifaUbi` (alta o edicion de
 * una tarifa de una casa para un año).
 *
 * Sucesor de la rama `form_tarifa_ubi` del dispatcher legacy
 * `apps/actividadtarifas/controller/tarifa_ajax.php`.
 */
final class TarifaUbiFormData
{
    /**
     * @return array{
     *   es_nuevo: bool,
     *   id_item: string,
     *   id_ubi: int,
     *   year: int,
     *   letra: string,
     *   cantidad: string,
     *   opciones_tarifa: array<int,string>,
     *   opciones_serie: array<int,string>,
     *   id_serie_sel: int
     * }
     */
    public static function execute(array $input): array
    {
        $id_item = (string)($input['id_item'] ?? '');
        $id_ubi = (int)($input['id_ubi'] ?? 0);
        $year = (int)($input['year'] ?? 0);
        $letra = (string)($input['letra'] ?? '');

        $es_nuevo = $id_item === '';
        $cantidad = '';

        if (!$es_nuevo) {
            $repo = $GLOBALS['container']->get(TarifaUbiRepositoryInterface::class);
            $oTarifaUbi = $repo->findById((int)$id_item);
            if ($oTarifaUbi !== null) {
                $cantidad = (string)$oTarifaUbi->getCantidad();
            }
        }

        if ($letra === '') {
            $letra = (string)_("nueva");
        }

        $opciones_tarifa = [];
        if ($es_nuevo) {
            $miSfsv = ConfigGlobal::mi_sfsv();
            $opciones_tarifa = TipoTarifaDropdown::opciones($miSfsv);
        }

        return [
            'es_nuevo' => $es_nuevo,
            'id_item' => $id_item,
            'id_ubi' => $id_ubi,
            'year' => $year,
            'letra' => $letra,
            'cantidad' => $cantidad,
            'opciones_tarifa' => $opciones_tarifa,
            'opciones_serie' => SerieId::getArraySerie(),
            'id_serie_sel' => SerieId::GENERAL,
        ];
    }
}
