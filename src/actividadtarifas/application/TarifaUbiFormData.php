<?php

namespace src\actividadtarifas\application;

use src\actividadtarifas\application\services\TipoTarifaDropdown;
use src\actividadtarifas\domain\value_objects\SerieId;
use src\shared\config\ConfigGlobal;
use src\shared\security\HashB;
use src\ubis\domain\contracts\TarifaUbiRepositoryInterface;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

/**
 * Data builder para el formulario de `TarifaUbi` (alta o edicion de
 * una tarifa de una casa para un año).
 */
final class TarifaUbiFormData
{
    public function __construct(
        private TarifaUbiRepositoryInterface $tarifaUbiRepository,
        private TipoTarifaDropdown $tipoTarifaDropdown,
    ) {
    }

    /**
     * @param array<string, mixed> $input
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
    public function execute(array $input): array
    {
        $id_item = input_string($input, 'id_item');
        $id_ubi = input_int($input, 'id_ubi');
        $year = input_int($input, 'year');
        $letra = input_string($input, 'letra');

        $es_nuevo = $id_item === '';
        $cantidad = '';

        if (!$es_nuevo) {
            $oTarifaUbi = $this->tarifaUbiRepository->findById((int) $id_item);
            if ($oTarifaUbi !== null) {
                $cantidad = (string) $oTarifaUbi->getCantidad();
            }
        }

        if ($letra === '') {
            $letra = (string) _("nueva");
        }

        $opciones_tarifa = [];
        if ($es_nuevo) {
            $miSfsv = ConfigGlobal::mi_sfsv();
            $opciones_tarifa = $this->tipoTarifaDropdown->opciones($miSfsv);
        }

        $ctxUpdate = $es_nuevo
            ? ['id_ubi' => $id_ubi, 'year' => $year]
            : ['id_item' => (int) $id_item, 'id_ubi' => $id_ubi, 'year' => $year];
        $token_update = HashB::sign('tarifa_ubi_update', $ctxUpdate);

        $token_eliminar = '';
        if (!$es_nuevo) {
            $token_eliminar = HashB::sign('tarifa_ubi_eliminar', ['id_item' => (int) $id_item]);
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
