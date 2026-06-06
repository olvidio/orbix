<?php

namespace src\casas\application;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\casas\domain\contracts\IngresoRepositoryInterface;
use src\casas\domain\entity\Ingreso;
use src\casas\domain\value_objects\IngresoImporte;
use src\casas\domain\value_objects\IngresoNumAsistentes;
use src\casas\domain\value_objects\IngresoObserv;

/**
 * Use case: crear/actualizar el Ingreso asociado a una actividad y, de
 * paso, los campos `tarifa` y `precio` de la propia actividad.
 *
 * Sucesor de la rama `que=guardar` de
 * `apps/casas/controller/casa_ajax.php`.
 */
final class CasaIngresoUpdate
{
    public function __construct(
        private ActividadAllRepositoryInterface $actividadAllRepository,
        private IngresoRepositoryInterface $ingresoRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{ok: bool, mensaje: string, data: string}
     */
    public function execute(array $input): array
    {
        $id_activ = isset($input['id_activ']) && is_numeric($input['id_activ']) ? (int) $input['id_activ'] : 0;
        if ($id_activ === 0) {
            return ['ok' => false, 'mensaje' => (string)_("Falta id_activ"), 'data' => ''];
        }

        $id_tarifa = null;
        if (isset($input['id_tarifa']) && is_numeric($input['id_tarifa'])) {
            $id_tarifa = (int) $input['id_tarifa'];
        }
        $precio_raw = $input['precio'] ?? null;

        $oActividad = $this->actividadAllRepository->findById($id_activ);
        if ($oActividad === null) {
            return ['ok' => false, 'mensaje' => (string)_("Actividad no encontrada"), 'data' => ''];
        }
        if ($id_tarifa !== null) {
            $oActividad->setTarifa($id_tarifa);
        }
        if ($precio_raw !== null && $precio_raw !== '') {
            $precio = self::parseDecimal($precio_raw);
            $oActividad->setPrecio($precio);
        }
        if ($this->actividadAllRepository->Guardar($oActividad) === false) {
            return ['ok' => false, 'mensaje' => (string)_("Hay un error, no se ha guardado la actividad."), 'data' => ''];
        }

        $oIngreso = $this->ingresoRepository->findById($id_activ);
        if ($oIngreso === null) {
            $oIngreso = new Ingreso();
            $oIngreso->setId_activ($id_activ);
        }
        $ingresos = self::parseDecimal($input['ingresos'] ?? 0);
        $num_asistentes = isset($input['num_asistentes']) && is_numeric($input['num_asistentes'])
            ? (int) $input['num_asistentes']
            : 0;
        $observ = isset($input['observ']) && is_string($input['observ']) ? $input['observ'] : '';
        $oIngreso->setIngresosVo(new IngresoImporte($ingresos));
        $oIngreso->setNumAsistentesVo(new IngresoNumAsistentes($num_asistentes));
        $oIngreso->setObservVo(new IngresoObserv($observ));

        if ($this->ingresoRepository->Guardar($oIngreso) === false) {
            return ['ok' => false, 'mensaje' => (string)_("Hay un error, no se ha guardado."), 'data' => ''];
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
