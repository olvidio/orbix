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
    public static function execute(array $input): array
    {
        $id_activ = (int)($input['id_activ'] ?? 0);
        if ($id_activ === 0) {
            return ['ok' => false, 'mensaje' => (string)_("Falta id_activ"), 'data' => ''];
        }

        $ActividadAll = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
        $Ingreso = $GLOBALS['container']->get(IngresoRepositoryInterface::class);

        $id_tarifa = isset($input['id_tarifa']) ? (string)$input['id_tarifa'] : null;
        $precio_raw = $input['precio'] ?? null;

        $oActividad = $ActividadAll->findById($id_activ);
        if ($oActividad === null) {
            return ['ok' => false, 'mensaje' => (string)_("Actividad no encontrada"), 'data' => ''];
        }
        if ($id_tarifa !== null) {
            $oActividad->setTarifa($id_tarifa);
        }
        if ($precio_raw !== null && $precio_raw !== '') {
            $precio = (float)str_replace(',', '.', (string)$precio_raw);
            $oActividad->setPrecio($precio);
        }
        if ($ActividadAll->Guardar($oActividad) === false) {
            return ['ok' => false, 'mensaje' => (string)_("Hay un error, no se ha guardado la actividad."), 'data' => ''];
        }

        $oIngreso = $Ingreso->findById($id_activ);
        if ($oIngreso === null) {
            $oIngreso = new Ingreso();
            $oIngreso->setId_activ($id_activ);
        }
        $ingresos = (float)str_replace(',', '.', (string)($input['ingresos'] ?? 0));
        $num_asistentes = (int)($input['num_asistentes'] ?? 0);
        $observ = (string)($input['observ'] ?? '');
        $oIngreso->setIngresosVo(new IngresoImporte($ingresos));
        $oIngreso->setNumAsistentesVo(new IngresoNumAsistentes($num_asistentes));
        $oIngreso->setObservVo(new IngresoObserv($observ));

        if ($Ingreso->Guardar($oIngreso) === false) {
            return ['ok' => false, 'mensaje' => (string)_("Hay un error, no se ha guardado."), 'data' => ''];
        }

        return ['ok' => true, 'mensaje' => '', 'data' => ''];
    }
}
