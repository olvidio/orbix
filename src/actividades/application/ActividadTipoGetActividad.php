<?php

namespace src\actividades\application;

use web\TiposActividades;

use function core\is_true;

/**
 * Devuelve el payload (id, opciones, selected, blanco, val_blanco, action) del
 * desplegable de actividades posibles. El frontend construye el `<select>`.
 */
class ActividadTipoGetActividad
{
    /**
     * @param array $input
     * @return array{id: string, opciones: array<int|string,string>, selected: string, blanco: bool, val_blanco: string, action: string}
     */
    public function execute(array $input = []): array
    {
        $Qentrada = (string)($input['entrada'] ?? '');
        $Qextendida = (string)($input['extendida'] ?? '');
        $extendida = (bool)is_true($Qextendida);

        $aux = $Qentrada . '....';
        $oTipoActiv = new TiposActividades($aux);

        if ($extendida) {
            $a_actividades_posibles = $oTipoActiv->getActividadesPosibles2Digitos();
            $opcion_blanco = '..';
        } else {
            $a_actividades_posibles = $oTipoActiv->getActividadesPosibles1Digito();
            $opcion_blanco = '.';
        }

        return [
            'id' => 'iactividad_val',
            'opciones' => $a_actividades_posibles,
            'selected' => $opcion_blanco,
            'blanco' => true,
            'val_blanco' => $opcion_blanco,
            'action' => 'fnjs_nom_tipo()',
        ];
    }
}
