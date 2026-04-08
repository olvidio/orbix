<?php

namespace Tests\factories\ubiscamas;

use src\ubiscamas\domain\entity\Cama;
use src\ubiscamas\domain\value_objects\CamaDescripcion;
use src\ubiscamas\domain\value_objects\CamaId;
use src\ubiscamas\domain\value_objects\HabitacionId;

/**
 * Factory para crear instancias de Cama para tests
 */
class CamaFactory
{
    private static function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * Crea una instancia simple de Cama con datos mínimos.
     * @param string|null $id UUID de la cama (generado si null)
     * @param string|null $idHabitacion UUID de la habitación (generado si null)
     */
    public function createSimple(?string $id = null, ?string $idHabitacion = null): Cama
    {
        $id = $id ?? self::generateUuid();
        $idHabitacion = $idHabitacion ?? self::generateUuid();

        $oCama = new Cama();
        $oCama->setIdCamaVo(new CamaId($id));
        $oCama->setIdHabitacionVo(new HabitacionId($idHabitacion));
        $oCama->setDescripcionVo(new CamaDescripcion('test_cama'));

        return $oCama;
    }

    /**
     * Crea una instancia completa de Cama con datos de prueba.
     */
    public function create(?string $id = null, ?string $idHabitacion = null): Cama
    {
        $id = $id ?? self::generateUuid();
        $idHabitacion = $idHabitacion ?? self::generateUuid();

        $oCama = new Cama();
        $oCama->setIdCamaVo(new CamaId($id));
        $oCama->setIdHabitacionVo(new HabitacionId($idHabitacion));
        $oCama->setDescripcionVo(new CamaDescripcion('test_cama_' . rand(1, 999)));
        $oCama->setLarga(false);
        $oCama->setVip(false);

        return $oCama;
    }
}
