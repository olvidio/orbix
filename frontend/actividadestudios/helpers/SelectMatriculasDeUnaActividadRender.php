<?php

declare(strict_types=1);

namespace frontend\actividadestudios\helpers;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;

/**
 * Bloque dossier 3103 en frontend: HashFront, Lista agrupada, URLs.
 *
 * @see \src\actividadestudios\application\Select_matriculas_de_una_actividad::getSegmentData()
 */
final class SelectMatriculasDeUnaActividadRender
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
        $elimPath = (string)($wrapper['url_matricula_eliminar_path'] ?? '');
        $urlEliminar = $elimPath !== '' ? $base . '/' . ltrim($elimPath, '/') : '';

        $hash = isset($seg['hash']) && is_array($seg['hash']) ? $seg['hash'] : [];
        $oHashSelect = new HashFront();
        $oHashSelect->setCamposForm((string)($hash['campos_form'] ?? ''));
        $oHashSelect->setCamposNo((string)($hash['campos_no'] ?? ''));
        $hidden = $hash['campos_hidden'] ?? [];
        $oHashSelect->setArrayCamposHidden(is_array($hidden) ? $hidden : []);

        $tabla = isset($seg['tabla']) && is_array($seg['tabla']) ? $seg['tabla'] : [];
        $oTabla = new Lista();
        $grupos = $tabla['grupos'] ?? [];
        $oTabla->setGrupos(is_array($grupos) ? $grupos : []);
        $oTabla->setCabeceras(is_array($tabla['cabeceras'] ?? null) ? $tabla['cabeceras'] : []);
        $oTabla->setBotones(is_array($tabla['botones'] ?? null) ? $tabla['botones'] : []);
        $oTabla->setDatos(is_array($tabla['valores'] ?? null) ? $tabla['valores'] : []);

        $sinMsg = (string)($seg['sin_asignaturas_mensaje'] ?? '');
        $err = (string)($seg['msg_err'] ?? '');

        $html = '';
        if ($sinMsg !== '') {
            $html .= '<p>' . $sinMsg . '</p>';
        }
        $html .= $err;

        $oView = new ViewNewPhtml('frontend\actividadestudios\view');
        $html .= $oView->renderizar('select_matriculas_de_una_actividad.phtml', [
            'oHashSelect' => $oHashSelect,
            'oTabla' => $oTabla,
            'txt_eliminar' => (string)($wrapper['txt_eliminar'] ?? ''),
            'nom_activ' => (string)($wrapper['nom_activ'] ?? ''),
            'url_form' => $urlForm,
            'url_matricula_eliminar' => $urlEliminar,
        ], false);

        return $html;
    }
}
