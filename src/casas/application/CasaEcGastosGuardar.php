<?php

namespace src\casas\application;

use src\casas\domain\contracts\UbiGastoRepositoryInterface;
use src\casas\domain\criteria\UbiGastoField;
use src\casas\domain\entity\UbiGasto;
use src\casas\domain\value_objects\UbiGastoCantidad;
use src\casas\domain\value_objects\UbiGastoTipo;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\value_objects\DateTimeLocal;

/**
 * Use case: guardar los gastos y aportaciones (sv/sf) mensuales de
 * una casa para un año completo, con fecha 5 de cada mes: un apunte por
 * mes y tipo. Si ya hay apunte de ese mes y tipo se actualiza; los que
 * estén repetidos (antes cada guardado añadía 36 nuevos) se borran
 * después de guardar todos los meses.
 *
 * Sucesor de la rama `que=guardarGasto` de
 * `apps/casas/controller/casa_ec_ajax.php`.
 */
final class CasaEcGastosGuardar
{
    public function __construct(
        private UbiGastoRepositoryInterface $ubiGastoRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{ok: bool, mensaje: string, data: string}
     */
    public function execute(array $input): array
    {
        $id_ubi = isset($input['id_ubi']) && is_numeric($input['id_ubi']) ? (int) $input['id_ubi'] : 0;
        $year = isset($input['year']) && is_numeric($input['year']) ? (int) $input['year'] : 0;
        if ($id_ubi === 0 || $year === 0) {
            return ['ok' => false, 'mensaje' => (string)_("Faltan id_ubi o year."), 'data' => ''];
        }

        // Apuntes que ya hay en el año, agrupados por mes y tipo.
        /** @var array<int, array<int, list<UbiGasto>>> $aExistentes */
        $aExistentes = [];
        $cGastos = $this->ubiGastoRepository->matching(
            Criteria::create()
                ->where(Filter::eq(UbiGastoField::IdUbi, $id_ubi))
                ->andWhere(Filter::between(UbiGastoField::FGasto, "$year-01-01", "$year-12-31"))
                ->orderBy(UbiGastoField::IdItem)
        );
        foreach ($cGastos as $oExistente) {
            $oF = $oExistente->getF_gasto();
            $tipoExistente = $oExistente->getTipoVo()?->value();
            if ($oF === null || $tipoExistente === null) {
                continue;
            }
            $aExistentes[(int)$oF->format('n')][$tipoExistente][] = $oExistente;
        }

        /** @var list<UbiGasto> $aRepetidos */
        $aRepetidos = [];
        for ($m = 1; $m < 13; $m++) {
            $g = self::parseDecimal($input["g$m"] ?? 0);
            $ap_sv = self::parseDecimal($input["ap_sv$m"] ?? 0);
            $ap_sf = self::parseDecimal($input["ap_sf$m"] ?? 0);
            $oFecha = new DateTimeLocal("$year/$m/5");

            foreach ([
                UbiGastoTipo::GASTO => $g,
                UbiGastoTipo::APORTACION_SV => $ap_sv,
                UbiGastoTipo::APORTACION_SF => $ap_sf,
            ] as $tipo => $cantidad) {
                $aMismoMesYTipo = $aExistentes[$m][$tipo] ?? [];
                $oUbiGasto = array_shift($aMismoMesYTipo);
                if ($oUbiGasto === null) {
                    $oUbiGasto = new UbiGasto();
                    $oUbiGasto->setId_item($this->ubiGastoRepository->getNewId());
                }
                array_push($aRepetidos, ...$aMismoMesYTipo);
                $oUbiGasto->setF_gasto($oFecha);
                $oUbiGasto->setId_ubi($id_ubi);
                $oUbiGasto->setTipoVo(new UbiGastoTipo($tipo));
                $oUbiGasto->setCantidadVo(new UbiGastoCantidad($cantidad));
                if ($this->ubiGastoRepository->Guardar($oUbiGasto) === false) {
                    return ['ok' => false, 'mensaje' => (string)_("Hay un error, no se ha guardado."), 'data' => ''];
                }
            }
        }

        foreach ($aRepetidos as $oRepetido) {
            if ($this->ubiGastoRepository->Eliminar($oRepetido) === false) {
                return ['ok' => false, 'mensaje' => (string)_("Los gastos se han guardado, pero no se han podido borrar los apuntes repetidos."), 'data' => ''];
            }
        }

        return ['ok' => true, 'mensaje' => '', 'data' => ''];
    }

    private static function parseDecimal(mixed $raw): float
    {
        if (is_int($raw) || is_float($raw)) {
            return (float) $raw;
        }
        if (is_string($raw)) {
            return (float) str_replace(',', '.', $raw);
        }

        return 0.0;
    }
}
