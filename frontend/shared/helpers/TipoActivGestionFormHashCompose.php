<?php

declare(strict_types=1);

namespace frontend\shared\helpers;

use frontend\shared\security\HashF;

/**
 * Campos ocultos firmados (`HashF::getCamposHtml`) para los formularios de gestión
 * de tipos de actividad ({@see \src\actividades\application\TipoActivFormNuevo},
 * {@see \src\actividades\application\TipoActivFormModificar}).
 */
final class TipoActivGestionFormHashCompose
{
    public static function nuevoHiddenHtml(): string
    {
        $oHash = new HashF();
        $oHash->setCamposForm('iactividad_val!iasistentes_val!id_nom_tipo_activ!isfsv_val!nom_tipo_activ');

        return $oHash->getCamposHtml();
    }

    public static function modificarHiddenHtml(int $idTipoActiv): string
    {
        $oHash = new HashF();
        $oHash->setCamposForm('nom_tipo_activ');
        $oHash->setArrayCamposHidden([
            'id_tipo_activ' => $idTipoActiv,
        ]);

        return $oHash->getCamposHtml();
    }
}
