<?php

namespace src\actividades\application;


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
        $id_tipo_activ = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'entrada');

        return (string) ActividadVerDatos::nivelStgrPorDefectoParaIdTipoActividad($id_tipo_activ);
    }
}
