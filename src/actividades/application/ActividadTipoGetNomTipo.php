<?php

namespace src\actividades\application;

use src\actividades\domain\entity\TiposActividades;
use src\shared\domain\helpers\OpcionesDesplegable;
use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Devuelve el payload (id, opciones, selected, blanco, val_blanco, action) del
 * desplegable de nombres de tipo de actividad. El frontend construye el
 * `<select>`.
 */
class ActividadTipoGetNomTipo
{
    /**
     * @param array<string, mixed> $input
     * @return array{id: string, opciones: list<array{0: string, 1: string}>, selected: string, blanco: bool, val_blanco: string, action: string}
     */
    public function execute(array $input = []): array
    {
        $Qentrada = FuncTablasSupport::inputString($input, 'entrada');
        $Qextendida = FuncTablasSupport::inputString($input, 'extendida');
        $Qmodo = FuncTablasSupport::inputString($input, 'modo', 'buscar');
        $extendida = FuncTablasSupport::isTrue($Qextendida) === true;

        if ($extendida) {
            $aux = $Qentrada . '..';
            $oTipoActiv = new TiposActividades($aux, $extendida);
            $a_nom_tipo_posibles = $oTipoActiv->getNom_tipoPosibles2Digitos();
            $opcion_blanco = '..';
        } else {
            $aux = $Qentrada . '...';
            $oTipoActiv = new TiposActividades($aux, $extendida);
            $a_nom_tipo_posibles = $oTipoActiv->getNom_tipoPosibles3Digitos();
            $opcion_blanco = '...';
        }

        $action = $Qmodo === 'buscar' ? 'fnjs_id_activ()' : 'fnjs_act_id_activ()';

        return [
            'id' => 'inom_tipo_val',
            'opciones' => OpcionesDesplegable::enOrden($a_nom_tipo_posibles),
            'selected' => $opcion_blanco,
            'blanco' => true,
            'val_blanco' => $opcion_blanco,
            'action' => $action,
        ];
    }
}
