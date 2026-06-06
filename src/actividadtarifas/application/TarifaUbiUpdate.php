<?php

namespace src\actividadtarifas\application;

use src\ubis\domain\contracts\TarifaUbiRepositoryInterface;
use src\ubis\domain\entity\TarifaUbi;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

/**
 * Mutacion: crea o actualiza una `TarifaUbi`.
 */
final class TarifaUbiUpdate
{
    public function __construct(
        private TarifaUbiRepositoryInterface $tarifaUbiRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $id_item = input_int($input, 'id_item');
        $id_ubi = input_int($input, 'id_ubi');
        $year = input_int($input, 'year');
        $id_tarifa = input_int($input, 'id_tarifa');
        $id_serie = input_int($input, 'id_serie');
        $cantidad = input_string($input, 'cantidad');
        $observ = input_string($input, 'observ');

        if ($id_item !== 0) {
            $oTarifaUbi = $this->tarifaUbiRepository->findById($id_item);
            if ($oTarifaUbi === null) {
                return (string) _("no se encuentra la tarifa");
            }
        } else {
            $newId = $this->tarifaUbiRepository->getNewId();
            $oTarifaUbi = new TarifaUbi();
            $oTarifaUbi->setId_item($newId);
        }

        if ($id_ubi !== 0) {
            $oTarifaUbi->setId_ubi($id_ubi);
        }
        if ($year !== 0) {
            $oTarifaUbi->setYear($year);
        }
        if ($id_tarifa !== 0) {
            $oTarifaUbi->setId_tarifa($id_tarifa);
        }
        if ($id_serie !== 0) {
            $oTarifaUbi->setId_serie($id_serie);
        }
        if ($cantidad !== '') {
            $oTarifaUbi->setCantidad((float) $cantidad);
        }
        if ($observ !== '') {
            $oTarifaUbi->setObserv($observ);
        }

        if ($this->tarifaUbiRepository->Guardar($oTarifaUbi) === false) {
            return (string) _("hay un error, no se ha guardado")
                . "\n" . $this->tarifaUbiRepository->getErrorTxt();
        }

        return '';
    }
}
