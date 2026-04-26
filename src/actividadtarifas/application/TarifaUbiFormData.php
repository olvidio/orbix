<?php

namespace src\actividadtarifas\application;

use src\shared\config\ConfigGlobal;
use src\shared\security\HashB;
use src\actividadtarifas\application\services\TipoTarifaDropdown;
use src\actividadtarifas\domain\value_objects\SerieId;
use src\ubis\domain\contracts\TarifaUbiRepositoryInterface;

/**
 * Data builder para el formulario de `TarifaUbi` (alta o edicion de
 * una tarifa de una casa para un año).
 *
 * Junto con los datos del form, emite las **cápsulas `HashB`** que el
 * navegador transportará opacamente y que los endpoints de mutación
 * (`tarifa_ubi_update`, `tarifa_ubi_eliminar`) abrirán para recuperar
 * el contexto firmado (`id_item`, `id_ubi`, `year`). Ver
 * `documentacion/hash_arquitectura.md`.
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
     *   id_serie_sel: int,
     *   token_update: string,
     *   token_eliminar: string
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

        // Cápsula que autoriza la mutación posterior (`tarifa_ubi_update`).
        // Firma la identidad del registro; los campos editables (id_tarifa,
        // id_serie, cantidad, observ) viajan aparte en el POST y se validan
        // ahí independientemente.
        $ctxUpdate = $es_nuevo
            ? ['id_ubi' => $id_ubi, 'year' => $year]
            : ['id_item' => (int)$id_item, 'id_ubi' => $id_ubi, 'year' => $year];
        $token_update = HashB::sign('tarifa_ubi_update', $ctxUpdate);

        // Cápsula de eliminación: solo tiene sentido en edición (existe
        // id_item). En alta, el botón "eliminar" ni siquiera se renderiza.
        $token_eliminar = '';
        if (!$es_nuevo) {
            $token_eliminar = HashB::sign('tarifa_ubi_eliminar', ['id_item' => (int)$id_item]);
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
            'token_update' => $token_update,
            'token_eliminar' => $token_eliminar,
        ];
    }
}
