<?php

namespace src\actividades\application;

use frontend\shared\web\Lista;
use src\actividades\domain\entity\TiposActividades;

/**
 * Devuelve la tabla HTML de nombres de tipo de actividad (id, nombre). Portado
 * del case `nom_tipo_tabla` del dispatcher legacy.
 */
class ActividadTipoGetNomTipoTabla
{
    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input = []): string
    {
        $Qentrada = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'entrada');

        $aux = $Qentrada . '..';
        $oTipoActiv = new TiposActividades($aux, true);
        $a_nom_tipo_posibles = $oTipoActiv->getNom_tipoPosibles2Digitos();

        $a_cabeceras = [_("id"), _("nombre")];
        $a_valores = [];
        $i = 0;
        foreach ($a_nom_tipo_posibles as $id => $nom) {
            $i++;
            $a_valores[$i][1] = $id;
            $a_valores[$i][2] = $nom;
        }

        $oTabla = new Lista();
        $oTabla->setBotones([]);
        $oTabla->setCabeceras($a_cabeceras);
        $oTabla->setDatos($a_valores);
        $oTabla->setFormatoTabla('html');

        return $oTabla->mostrar_tabla();
    }
}
