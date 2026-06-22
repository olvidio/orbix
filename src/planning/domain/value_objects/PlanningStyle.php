<?php

namespace src\planning\domain\value_objects;

use src\actividadplazas\domain\value_objects\PlazaId;

/**
 * Calcula la clase CSS de una actividad en el planning segun su tipo
 * (sv/sf/otras), caracter propio/personal, estado de plaza y status.
 *
 * Antes vivia en `apps/planning/domain/PlanningStyle.php` con namespace
 * `planning\domain`. Se ha movido a `src/planning/domain/value_objects/`
 * como parte de la migracion del modulo planning (slice 1).
 */
class PlanningStyle
{
    public static function clase(int|string|null $id_tipo_activ, bool|string|null $propio, int|string|null $plaza, int|string|null $status): string
    {
        $svsf = (int) substr((string) $id_tipo_activ, 0, 1);
        switch ($svsf) {
            case 1:
                $clase = "actsv";
                break;
            case 2:
                $clase = "actsf";
                break;
            default:
                $clase = "actotras";
        }
        if ($propio === TRUE) {
            $clase = 'actpropio';
        }
        if ($propio === "p") {
            $clase = 'actpersonal';
        }
        if (!empty($plaza) && $plaza < PlazaId::ASIGNADA) {
            $clase = 'provisional ' . $clase;
        }
        if (!empty($status) && $status === 1) {
            $clase = 'proyecto ' . $clase;
            if ($svsf === 2) {
                $clase = 'proyectof ' . $clase;
            }
        }
        return $clase;
    }
}
