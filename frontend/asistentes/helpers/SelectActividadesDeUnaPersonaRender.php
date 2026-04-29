<?php

declare(strict_types=1);

namespace frontend\asistentes\helpers;

use frontend\dossiers\helpers\DossierTipoFormLinkSpecsSigning;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\BotonesCurso;
use frontend\shared\web\Lista;

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
        $base = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/');
        $relForm = (string)($wrapper['url_form_relative'] ?? '');
        $urlForm = $relForm !== '' ? $base . '/' . ltrim($relForm, '/') : '';
        $elimPath = (string)($wrapper['url_eliminar_path'] ?? '');
        $urlEliminar = $elimPath !== '' ? $base . '/' . ltrim($elimPath, '/') : '';

        $hash = isset($seg['hash']) && is_array($seg['hash']) ? $seg['hash'] : [];
        $oHashSelect = new HashFront();
        $oHashSelect->setCamposForm((string)($hash['campos_form'] ?? ''));
        $oHashSelect->setCamposNo((string)($hash['campos_no'] ?? ''));
        $hidden = $hash['campos_hidden'] ?? [];
        $oHashSelect->setArrayCamposHidden(is_array($hidden) ? $hidden : []);

        $tabla = isset($seg['tabla']) && is_array($seg['tabla']) ? $seg['tabla'] : [];
        $oTabla = new Lista();
        $oTabla->setId_tabla((string)($tabla['id_tabla'] ?? 'select_actividades_de_una_persona'));
        $oTabla->setCabeceras(is_array($tabla['cabeceras'] ?? null) ? $tabla['cabeceras'] : []);
        $oTabla->setBotones(is_array($tabla['botones'] ?? null) ? $tabla['botones'] : []);
        $oTabla->setDatos(is_array($tabla['valores'] ?? null) ? $tabla['valores'] : []);

        $modoCurso = (int)($seg['modo_curso'] ?? 1);
        $oBotonesCurso = new BotonesCurso($modoCurso);

        $dlSpecs = $seg['links_dl_specs'] ?? [];
        $otrosSpecs = $seg['links_otros_specs'] ?? [];
        $aLinks_dl = is_array($dlSpecs) ? DossierTipoFormLinkSpecsSigning::signLinkMap($dlSpecs) : [];
        $aLinks_otros = is_array($otrosSpecs) ? DossierTipoFormLinkSpecsSigning::signLinkMap($otrosSpecs) : [];

        $msgErr = (string)($seg['msg_err'] ?? '');

        $oView = new ViewNewPhtml('frontend\asistentes\view');

        return $msgErr . $oView->renderizar('select_actividades_de_una_persona.phtml', [
            'oTabla' => $oTabla,
            'oBotonesCurso' => $oBotonesCurso,
            'oHashSelect' => $oHashSelect,
            'aLinks_dl' => $aLinks_dl,
            'aLinks_otros' => $aLinks_otros,
            'txt_eliminar' => (string)($wrapper['txt_eliminar'] ?? ''),
            'bloque' => (string)($wrapper['bloque'] ?? ''),
            'url_form' => $urlForm,
            'url_eliminar' => $urlEliminar,
        ], false);
    }
}
