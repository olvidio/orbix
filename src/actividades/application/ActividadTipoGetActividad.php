<?php

namespace src\actividades\application;

use src\actividades\domain\entity\TiposActividades;
use src\shared\domain\helpers\OpcionesDesplegable;

/**
 * Devuelve el payload (id, opciones, selected, blanco, val_blanco, action) del
 * desplegable de actividades posibles. El frontend construye el `<select>`.
 */
class ActividadTipoGetActividad
{
    /**
     * @param array<string, mixed> $input
     * @return array{id: string, opciones: list<array{0: string, 1: string}>, selected: string, blanco: bool, val_blanco: string, action: string}
     */
    public function execute(array $input = []): array
    {
        $Qentrada = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'entrada');
        $Qextendida = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'extendida');
        $extendida = \src\shared\domain\helpers\FuncTablasSupport::isTrue($Qextendida) === true;

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
            'opciones' => OpcionesDesplegable::enOrden($a_actividades_posibles),
            'selected' => $opcion_blanco,
            'blanco' => true,
            'val_blanco' => $opcion_blanco,
            'action' => 'fnjs_nom_tipo()',
        ];
    }
}
