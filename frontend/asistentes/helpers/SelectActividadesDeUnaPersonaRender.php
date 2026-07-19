<?php

declare(strict_types=1);

namespace frontend\asistentes\helpers;

use frontend\shared\helpers\FuncTablasSupport;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\BotonesCurso;
use frontend\shared\web\Lista;
use frontend\shared\helpers\PayloadCoercion;
use frontend\actividades\helpers\ActividadesListaSupport;

/**
 * Bloque dossier 1301 en frontend.
 *
 * @see \src\asistentes\application\Select_actividades_de_una_persona::getSegmentData()
 */
final class SelectActividadesDeUnaPersonaRender
{
    /**
     * @param array<string, mixed> $seg
     */
    public static function render(array $seg): string
    {
        $wrapper = isset($seg['wrapper']) && is_array($seg['wrapper']) ? $seg['wrapper'] : [];
        $urlForm = AppUrlConfig::browserUrlFromAppRelative(
            \frontend\shared\helpers\FuncTablasSupport::payloadString($wrapper, 'url_form_relative')
        );
        $urlEliminar = AppUrlConfig::browserUrlFromAppRelative(
            \frontend\shared\helpers\FuncTablasSupport::payloadString($wrapper, 'url_eliminar_path')
        );

        $hash = isset($seg['hash']) && is_array($seg['hash']) ? $seg['hash'] : [];
        $oHashSelect = new HashFront();
        $oHashSelect->setCamposForm(\frontend\shared\helpers\FuncTablasSupport::payloadString($hash, 'campos_form'));
        $oHashSelect->setCamposNo(\frontend\shared\helpers\FuncTablasSupport::payloadString($hash, 'campos_no'));
        $hidden = AsistentesRenderSupport::hashCamposHidden($hash['campos_hidden'] ?? []);
        $oHashSelect->setArrayCamposHidden($hidden);

        $tabla = isset($seg['tabla']) && is_array($seg['tabla']) ? $seg['tabla'] : [];
        $oTabla = new Lista();
        $oTabla->setId_tabla(\frontend\shared\helpers\FuncTablasSupport::payloadString($tabla, 'id_tabla', 'select_actividades_de_una_persona'));
        $oTabla->setCabeceras(ActividadesListaSupport::cabeceras($tabla['cabeceras'] ?? []));
        $oTabla->setBotones(ActividadesListaSupport::botones($tabla['botones'] ?? []));
        $oTabla->setDatos(ActividadesListaSupport::datos($tabla['valores'] ?? []));

        $modoCurso = \frontend\shared\helpers\PayloadCoercion::int($seg['modo_curso'] ?? 1);
        $oBotonesCurso = new BotonesCurso($modoCurso);

        $aLinks_dl = AsistentesRenderSupport::signLinkMap($seg['links_dl_specs'] ?? []);
        $aLinks_otros = AsistentesRenderSupport::signLinkMap($seg['links_otros_specs'] ?? []);

        $msgErr = \frontend\shared\helpers\FuncTablasSupport::payloadString($seg, 'msg_err');

        $oView = new ViewNewPhtml('frontend\asistentes\view');

        return $msgErr . $oView->renderizar('select_actividades_de_una_persona.phtml', [
            'oTabla' => $oTabla,
            'oBotonesCurso' => $oBotonesCurso,
            'oHashSelect' => $oHashSelect,
            'aLinks_dl' => $aLinks_dl,
            'aLinks_otros' => $aLinks_otros,
            'txt_eliminar' => \frontend\shared\helpers\FuncTablasSupport::payloadString($wrapper, 'txt_eliminar'),
            'bloque' => \frontend\shared\helpers\FuncTablasSupport::payloadString($wrapper, 'bloque'),
            'url_form' => $urlForm,
            'url_eliminar' => $urlEliminar,
        ], false);
    }
}
