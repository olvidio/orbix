<?php

declare(strict_types=1);

namespace frontend\actividadestudios\helpers;

use frontend\dossiers\helpers\DossierTipoFormLinkSpecsSigning;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;

/**
 * Bloque dossier 3005 en frontend: HashFront, Lista, URLs firmadas.
 *
 * @see \src\actividadestudios\application\Select_asignaturas_de_una_actividad::getSegmentData()
 */
final class SelectAsignaturasDeUnaActividadRender
{
    /**
     * @param array<string, mixed> $seg payload de ficha_segmentos (campos tipo/id se pueden ignorar)
     */
    public static function render(array $seg): string
    {
        $hash = isset($seg['hash']) && is_array($seg['hash']) ? $seg['hash'] : [];
        $oHashSelect = new HashFront();
        $oHashSelect->setCamposForm((string)($hash['campos_form'] ?? ''));
        $oHashSelect->setCamposNo((string)($hash['campos_no'] ?? ''));
        $hidden = $hash['campos_hidden'] ?? [];
        $oHashSelect->setArrayCamposHidden(is_array($hidden) ? $hidden : []);

        $tabla = isset($seg['tabla']) && is_array($seg['tabla']) ? $seg['tabla'] : [];
        $oTabla = new Lista();
        $oTabla->setId_tabla((string)($tabla['id_tabla'] ?? 'select3005'));
        $cabeceras = $tabla['cabeceras'] ?? [];
        $botones = $tabla['botones'] ?? [];
        $valores = $tabla['valores'] ?? [];
        $oTabla->setCabeceras(is_array($cabeceras) ? $cabeceras : []);
        $oTabla->setBotones(is_array($botones) ? $botones : []);
        $oTabla->setDatos(is_array($valores) ? $valores : []);

        $spec = $seg['link_insert_spec'] ?? null;
        $linkInsert = is_array($spec) ? DossierTipoFormLinkSpecsSigning::fromSpec($spec) : '';

        $base = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/');
        $urlFormRel = (string)($seg['url_form_relative'] ?? '');
        $urlForm = $urlFormRel !== '' ? $base . '/' . ltrim($urlFormRel, '/') : '';
        $elimPath = (string)($seg['url_actividad_asignatura_eliminar_path'] ?? '');
        $urlEliminar = $elimPath !== '' ? $base . '/' . ltrim($elimPath, '/') : '';

        $oView = new ViewNewPhtml('frontend\actividadestudios\view');

        return $oView->renderizar('select_asignaturas_de_una_actividad.phtml', [
            'oHashSelect' => $oHashSelect,
            'oTabla' => $oTabla,
            'link_insert' => $linkInsert,
            'txt_eliminar' => (string)($seg['txt_eliminar'] ?? ''),
            'txt_no_permiso' => (string)($seg['txt_no_permiso'] ?? ''),
            'bloque' => (string)($seg['bloque'] ?? ''),
            'url_form' => $urlForm,
            'url_actividad_asignatura_eliminar' => $urlEliminar,
        ], false);
    }
}
