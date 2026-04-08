<?php

namespace Tests\factories\ubiscamas;

use src\ubiscamas\domain\entity\Habitacion;
use src\ubiscamas\domain\value_objects\HabitacionId;
use src\ubiscamas\domain\value_objects\HabitacionNombre;
use src\ubiscamas\domain\value_objects\HabitacionOrden;
use src\ubiscamas\domain\value_objects\NumeroCamas;
use src\ubiscamas\domain\value_objects\PlantaText;
use src\ubiscamas\domain\value_objects\TipoLavabo;

/**
 * Factory para crear instancias de Habitacion para tests
 */
class HabitacionFactory
{
    /**
     * Crea una instancia simple de Habitacion con datos mínimos.
     * El id_habitacion es un UUID generado aleatoriamente.
     */
    public function createSimple(?string $id = null): Habitacion
    {
        $id = $id ?? sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );

        $oHabitacion = new Habitacion();
        $oHabitacion->setIdUbiVo(100113);
        $oHabitacion->setIdHabitacionVo(new HabitacionId($id));
        $oHabitacion->setOrdenVo(new HabitacionOrden(1));
        $oHabitacion->setNombreVo(new HabitacionNombre('test_habitacion'));

        return $oHabitacion;
    }

    /**
     * Crea una instancia completa de Habitacion con datos de prueba.
     */
    public function create(?string $id = null): Habitacion
    {
        $id = $id ?? sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );

        $oHabitacion = new Habitacion();
        $oHabitacion->setIdUbiVo(100113);
        $oHabitacion->setIdHabitacionVo(new HabitacionId($id));
        $oHabitacion->setOrdenVo(new HabitacionOrden(rand(1, 20)));
        $oHabitacion->setNombreVo(new HabitacionNombre('test_habitacion_' . rand(1, 999)));
        $oHabitacion->setNumeroCamasVo(new NumeroCamas(rand(1, 4)));
        $oHabitacion->setNumeroCamasVipVo(new NumeroCamas(rand(0, 2)));
        $oHabitacion->setPlantaVo(new PlantaText('Baja'));
        $oHabitacion->setSillon(false);
        $oHabitacion->setAdaptada(false);
        $oHabitacion->setFumador(false);
        $oHabitacion->setTipoLavaboVo(new TipoLavabo(1));
        $oHabitacion->setDespacho(false);

        return $oHabitacion;
    }
}
