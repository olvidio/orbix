<?php

namespace src\actividades\application;

use src\actividades\domain\entity\TiposActividades;

/**
 * Códigos (dos cifras) y nombres de tipo de actividad posibles para un prefijo (`entrada`: sfsv + asistentes +
 * actividad). Salida `nom_tipo_posibles` de `actividad_tipo_get`; la tabla la monta
 * `frontend/actividades/controller/actividad_tipo_nom_tipo_tabla.php`.
 */
final class ActividadTipoGetNomTipoPosibles
{
    /**
     * @param array<string, mixed> $input
     * @return array{nom_tipos: list<array{0: string, 1: string}>}
     */
    public function execute(array $input = []): array
    {
        $entrada = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'entrada');
        $oTipoActiv = new TiposActividades($entrada . '..', true);

        $nom_tipos = [];
        foreach ($oTipoActiv->getNom_tipoPosibles2Digitos() as $id => $nom) {
            $nom_tipos[] = [(string) $id, (string) $nom];
        }

        return ['nom_tipos' => $nom_tipos];
    }
}
