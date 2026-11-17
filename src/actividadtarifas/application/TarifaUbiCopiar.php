<?php

namespace src\actividadtarifas\application;

use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\ubis\domain\contracts\TarifaUbiRepositoryInterface;
use src\ubis\domain\criteria\TarifaUbiField;
use src\ubis\domain\entity\TarifaUbi;

/**
 * Accion: copiar las tarifas de una casa del año anterior al año elegido.
 *
 * Borra antes las tarifas que la casa ya tenga en ese año (la clave es casa + tarifa + año + serie). Si el año
 * anterior no tiene tarifas no se borra nada. Si falla el borrado de alguna tarifa actual no se copia nada.
 */
final class TarifaUbiCopiar
{
    public function __construct(
        private TarifaUbiRepositoryInterface $tarifaUbiRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return string vacío si se ha copiado; si no, el aviso o error
     */
    public function execute(array $input): string
    {
        $id_ubi = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_ubi');
        $year = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'year');
        if ($id_ubi === 0 || $year === 0) {
            return (string) _("no sé qué casa/año tengo que copiar");
        }
        $any_anterior = $year - 1;

        $cAnteriores = $this->tarifasDe($id_ubi, $any_anterior);
        if ($cAnteriores === []) {
            return sprintf((string) _("No hay tarifas del año %d para copiar."), $any_anterior);
        }

        foreach ($this->tarifasDe($id_ubi, $year) as $oActual) {
            if ($this->tarifaUbiRepository->Eliminar($oActual) === false) {
                return (string) _("No se han podido borrar las tarifas actuales. No se ha copiado nada.")
                    . "\n" . $this->tarifaUbiRepository->getErrorTxt();
            }
        }

        $error_txt = '';
        foreach ($cAnteriores as $oAnterior) {
            $oNueva = new TarifaUbi();
            $oNueva->setId_item($this->tarifaUbiRepository->getNewId());
            $oNueva->setId_ubi($id_ubi);
            $oNueva->setYear($year);
            $oNueva->setId_tarifa($oAnterior->getId_tarifa());
            $oNueva->setId_serie($oAnterior->getId_serie());
            $cantidad = $oAnterior->getCantidad();
            if ($cantidad !== null) {
                $oNueva->setCantidad($cantidad);
            }
            $oNueva->setObserv($oAnterior->getObserv());
            if ($this->tarifaUbiRepository->Guardar($oNueva) === false) {
                $error_txt .= (string) _("hay un error, no se ha guardado")
                    . "\n" . $this->tarifaUbiRepository->getErrorTxt() . "\n";
            }
        }

        return $error_txt;
    }

    /**
     * @return list<TarifaUbi>
     */
    private function tarifasDe(int $id_ubi, int $year): array
    {
        return array_values($this->tarifaUbiRepository->matching(
            Criteria::create()
                ->where(Filter::eq(TarifaUbiField::IdUbi, $id_ubi))
                ->andWhere(Filter::eq(TarifaUbiField::Year, $year))
        ));
    }
}
