<?php

namespace src\actividades\application;

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Nivel STGR sugerido por defecto para un `id_tipo_activ` (misma regla que el formulario ver actividad).
 */
final class ActividadTipoGetNivelStgrDefecto
{
    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input = []): string
    {
        $id_tipo_activ = FuncTablasSupport::inputString($input, 'entrada');

        return (string) ActividadVerDatos::nivelStgrPorDefectoParaIdTipoActividad($id_tipo_activ);
    }
}
