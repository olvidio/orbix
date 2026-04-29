<?php

declare(strict_types=1);

namespace frontend\asistentes\helpers;

use frontend\dossiers\helpers\DossierTipoFormLinkSpecsSigning;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;

/**
 * Bloque dossier 3101 en frontend.
 *
 * @see \src\asistentes\application\Select_asistentes_a_una_actividad::getSegmentData()
 */
final class SelectAsistentesAUnaActividadRender
{
    /**
     * @param array{path: string, campos_form: string} $meta
     */
    private static function linkSinValParamsFromPath(array $meta): string
    {
        $path = (string)($meta['path'] ?? '');
        $campos = (string)($meta['campos_form'] ?? '');
        if ($path === '') {
            return '';
        }
        $url = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/') . '/' . ltrim($path, '/');
        $oHash = new HashFront();
        $oHash->setUrl($url);
        $oHash->setCamposForm($campos);

        return $oHash->linkSinValParams();
    }

    /**
     * @param array<string, mixed> $seg
     */
    public static function render(array $seg): string
    {
        $wrapper = isset($seg['wrapper']) && is_array($seg['wrapper']) ? $seg['wrapper'] : [];
        $base = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/');
        $abs = static function (string $rel) use ($base): string {
            return $rel !== '' ? $base . '/' . ltrim($rel, '/') : '';
        };

        $relForm = (string)($wrapper['url_form_relative'] ?? '');
        $urlForm = $abs($relForm);
        $urlFormCargos = $abs((string)($wrapper['url_form_cargos_relative'] ?? ''));
        $urlEliminar = $abs((string)($wrapper['url_eliminar_path'] ?? ''));
        $urlCargoEliminar = $abs((string)($wrapper['url_cargo_eliminar_path'] ?? ''));

        $moverPath = (string)($wrapper['url_mover_path'] ?? '');
        $urlMover = $abs($moverPath);
        $urlPlazaAsignar = $abs((string)($wrapper['url_plaza_asignar_path'] ?? ''));

        $ajaxMover = isset($seg['ajax_hash_mover']) && is_array($seg['ajax_hash_mover']) ? $seg['ajax_hash_mover'] : [];
        $ajaxPlaza = isset($seg['ajax_hash_plaza']) && is_array($seg['ajax_hash_plaza']) ? $seg['ajax_hash_plaza'] : [];
        $h3 = self::linkSinValParamsFromPath([
            'path' => $moverPath !== '' ? $moverPath : (string)($ajaxMover['path'] ?? ''),
            'campos_form' => (string)($ajaxMover['campos_form'] ?? 'id_pau!id_activ'),
        ]);
        $plazaPath = (string)($wrapper['url_plaza_asignar_path'] ?? '');
        $h4 = self::linkSinValParamsFromPath([
            'path' => $plazaPath !== '' ? $plazaPath : (string)($ajaxPlaza['path'] ?? ''),
            'campos_form' => (string)($ajaxPlaza['campos_form'] ?? 'plaza!lista_json!id_activ'),
        ]);

        $hashMain = isset($seg['hash_main']) && is_array($seg['hash_main']) ? $seg['hash_main'] : [];
        $oHash = new HashFront();
        $oHash->setCamposForm((string)($hashMain['campos_form'] ?? ''));
        $oHash->setCamposNo((string)($hashMain['campos_no'] ?? ''));
        $hidden = $hashMain['campos_hidden'] ?? [];
        $oHash->setArrayCamposHidden(is_array($hidden) ? $hidden : []);

        $hashMat = isset($seg['hash_matriculas']) && is_array($seg['hash_matriculas']) ? $seg['hash_matriculas'] : [];
        $oHash1 = new HashFront();
        $oHash1->setCamposForm((string)($hashMat['campos_form'] ?? ''));
        $oHash1->setCamposNo((string)($hashMat['campos_no'] ?? ''));
        $hiddenM = $hashMat['campos_hidden'] ?? [];
        $oHash1->setArrayCamposHidden(is_array($hiddenM) ? $hiddenM : []);

        $tabla = isset($seg['tabla']) && is_array($seg['tabla']) ? $seg['tabla'] : [];
        $oTabla = new Lista();
        $oTabla->setId_tabla((string)($tabla['id_tabla'] ?? 'select_asistentes_a_una_actividad'));
        $oTabla->setCabeceras(is_array($tabla['cabeceras'] ?? null) ? $tabla['cabeceras'] : []);
        $oTabla->setBotones(is_array($tabla['botones'] ?? null) ? $tabla['botones'] : []);
        $oTabla->setDatos(is_array($tabla['valores'] ?? null) ? $tabla['valores'] : []);

        $dlSpecs = $seg['links_dl_specs'] ?? [];
        $aLinks_dl = is_array($dlSpecs) ? DossierTipoFormLinkSpecsSigning::signLinkMap($dlSpecs) : [];

        $oView = new ViewNewPhtml('frontend\asistentes\view');

        return $oView->renderizar('select_asistentes_a_una_actividad.phtml', [
            'oTabla' => $oTabla,
            'oHash' => $oHash,
            'oHash1' => $oHash1,
            'id_pau' => $seg['id_pau'] ?? 0,
            'h3' => $h3,
            'h4' => $h4,
            'plazas_txt' => (string)($seg['plazas_txt'] ?? ''),
            'resumen_plazas' => (string)($seg['resumen_plazas'] ?? ''),
            'resumen_plazas2' => (string)($seg['resumen_plazas2'] ?? ''),
            'leyenda_html' => (string)($seg['leyenda_html'] ?? ''),
            'aLinks_dl' => $aLinks_dl,
            'msg_err' => (string)($seg['msg_err'] ?? ''),
            'txt_eliminar' => (string)($wrapper['txt_eliminar'] ?? ''),
            'bloque' => (string)($wrapper['bloque'] ?? ''),
            'url_form' => $urlForm,
            'url_mover' => $urlMover,
            'url_plaza_asignar' => $urlPlazaAsignar,
            'url_eliminar' => $urlEliminar,
            'url_form_cargos_actividad' => $urlFormCargos,
            'url_cargo_eliminar' => $urlCargoEliminar,
            'plazas_installed' => !empty($seg['plazas_installed']),
        ], false);
    }
}
