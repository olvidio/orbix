<?php

namespace src\actividades\application;

use web\TiposActividades;

use function core\is_true;

/**
 * Devuelve el payload (id, opciones, selected, blanco, val_blanco, action) del
 * desplegable de asistentes posibles. El frontend construye el `<select>`.
 */
class ActividadTipoGetAsistentes
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

        $aux = $Qentrada . '.....';
        $oTipoActiv = new TiposActividades($aux);
        $a_asistentes_posibles = $oTipoActiv->getAsistentesPosibles();

        // La opcion en blanco solo es valida para des o calendario.
        $blanco = false;
        if (isset($_SESSION['oPerm'])
            && ($_SESSION['oPerm']->have_perm_oficina('des')
                || $_SESSION['oPerm']->have_perm_oficina('calendario'))
        ) {
            $blanco = true;
        }

        return [
            'id' => 'iasistentes_val',
            'opciones' => $a_asistentes_posibles,
            'selected' => '.',
            'blanco' => $blanco,
            'val_blanco' => '.',
            'action' => 'fnjs_actividad(' . ($extendida ? 'true' : 'false') . ')',
        ];
    }
}
