<?php

namespace src\actividades\application;

use src\actividades\domain\entity\TiposActividades;
use src\permisos\domain\XPermisos;
use src\shared\domain\helpers\OpcionesDesplegable;

use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\is_true;

/**
 * Devuelve el payload (id, opciones, selected, blanco, val_blanco, action) del
 * desplegable de asistentes posibles. El frontend construye el `<select>`.
 */
class ActividadTipoGetAsistentes
{
    /**
     * @param array<string, mixed> $input
     * @return array{id: string, opciones: list<array{0: string, 1: string}>, selected: string, blanco: bool, val_blanco: string, action: string}
     */
    public function execute(array $input = []): array
    {
        $Qentrada = input_string($input, 'entrada');
        $Qextendida = input_string($input, 'extendida');
        $extendida = is_true($Qextendida) === true;

        $aux = $Qentrada . '.....';
        $oTipoActiv = new TiposActividades($aux);
        $a_asistentes_posibles = $oTipoActiv->getAsistentesPosibles();

        $blanco = false;
        $oPerm = $_SESSION['oPerm'] ?? null;
        if ($oPerm instanceof XPermisos
            && ($oPerm->have_perm_oficina('des') || $oPerm->have_perm_oficina('calendario'))
        ) {
            $blanco = true;
        }

        return [
            'id' => 'iasistentes_val',
            'opciones' => OpcionesDesplegable::enOrden($a_asistentes_posibles),
            'selected' => '.',
            'blanco' => $blanco,
            'val_blanco' => '.',
            'action' => 'fnjs_actividad(' . ($extendida ? 'true' : 'false') . ')',
        ];
    }
}
