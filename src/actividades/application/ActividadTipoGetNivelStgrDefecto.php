<?php

namespace src\actividades\application;

/**
 * Nivel STGR sugerido por defecto para un `id_tipo_activ` (misma regla que el formulario ver actividad).
 */
final class ActividadTipoGetNivelStgrDefecto
{
    public function execute(array $input = []): string
    {
        $id_tipo_activ = (string)($input['entrada'] ?? '');

        return (string)ActividadVerDatos::nivelStgrPorDefectoParaIdTipoActividad($id_tipo_activ);
    }
}
