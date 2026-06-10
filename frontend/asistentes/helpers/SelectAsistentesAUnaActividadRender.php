<?php

declare(strict_types=1);

namespace frontend\asistentes\helpers;

require_once __DIR__ . '/asistentes_support.php';

use function frontend\shared\helpers\payload_string;

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
        $path = payload_string($meta, 'path');
        $campos = payload_string($meta, 'campos_form');
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

        $relForm = payload_string($wrapper, 'url_form_relative');
        $urlForm = $abs($relForm);
        $urlFormCargos = $abs(payload_string($wrapper, 'url_form_cargos_relative'));
        $urlEliminar = $abs(payload_string($wrapper, 'url_eliminar_path'));
        $urlCargoEliminar = $abs(payload_string($wrapper, 'url_cargo_eliminar_path'));

        $moverPath = payload_string($wrapper, 'url_mover_path');
        $urlMover = $abs($moverPath);
        $urlPlazaAsignar = $abs(payload_string($wrapper, 'url_plaza_asignar_path'));

        $ajaxMover = isset($seg['ajax_hash_mover']) && is_array($seg['ajax_hash_mover']) ? $seg['ajax_hash_mover'] : [];
        $ajaxPlaza = isset($seg['ajax_hash_plaza']) && is_array($seg['ajax_hash_plaza']) ? $seg['ajax_hash_plaza'] : [];
        $h3 = self::linkSinValParamsFromPath([
            'path' => $moverPath !== '' ? $moverPath : payload_string($ajaxMover, 'path'),
            'campos_form' => payload_string($ajaxMover, 'campos_form', 'id_pau!id_activ'),
        ]);
        $plazaPath = payload_string($wrapper, 'url_plaza_asignar_path');
        $h4 = self::linkSinValParamsFromPath([
            'path' => $plazaPath !== '' ? $plazaPath : payload_string($ajaxPlaza, 'path'),
            'campos_form' => payload_string($ajaxPlaza, 'campos_form', 'plaza!lista_json!id_activ'),
        ]);

        $hashMain = isset($seg['hash_main']) && is_array($seg['hash_main']) ? $seg['hash_main'] : [];
        $oHash = new HashFront();
        $oHash->setCamposForm(payload_string($hashMain, 'campos_form'));
        $oHash->setCamposNo(payload_string($hashMain, 'campos_no'));
        $hidden = asistentes_hash_campos_hidden($hashMain['campos_hidden'] ?? []);
        $oHash->setArrayCamposHidden($hidden);

        $hashMat = isset($seg['hash_matriculas']) && is_array($seg['hash_matriculas']) ? $seg['hash_matriculas'] : [];
        $oHash1 = new HashFront();
        $oHash1->setCamposForm(payload_string($hashMat, 'campos_form'));
        $oHash1->setCamposNo(payload_string($hashMat, 'campos_no'));
        $hiddenM = asistentes_hash_campos_hidden($hashMat['campos_hidden'] ?? []);
        $oHash1->setArrayCamposHidden($hiddenM);

        $tabla = isset($seg['tabla']) && is_array($seg['tabla']) ? $seg['tabla'] : [];
        $oTabla = new Lista();
        $oTabla->setId_tabla(payload_string($tabla, 'id_tabla', 'select_asistentes_a_una_actividad'));
        $oTabla->setCabeceras(actividades_lista_cabeceras($tabla['cabeceras'] ?? []));
        $oTabla->setBotones(actividades_lista_botones($tabla['botones'] ?? []));
        $oTabla->setDatos(actividades_lista_datos($tabla['valores'] ?? []));

        $aLinks_dl = asistentes_sign_link_map($seg['links_dl_specs'] ?? []);

        $oView = new ViewNewPhtml('frontend\asistentes\view');

        return $oView->renderizar('select_asistentes_a_una_actividad.phtml', [
            'oTabla' => $oTabla,
            'oHash' => $oHash,
            'oHash1' => $oHash1,
            'id_sel_value' => payload_string($seg, 'id_sel_value'),
            'id_pau' => tessera_imprimir_int($seg['id_pau'] ?? 0),
            'h3' => $h3,
            'h4' => $h4,
            'plazas_txt' => payload_string($seg, 'plazas_txt'),
            'resumen_plazas' => payload_string($seg, 'resumen_plazas'),
            'resumen_plazas2' => payload_string($seg, 'resumen_plazas2'),
            'leyenda_html' => payload_string($seg, 'leyenda_html'),
            'aLinks_dl' => $aLinks_dl,
            'msg_err' => payload_string($seg, 'msg_err'),
            'txt_eliminar' => payload_string($wrapper, 'txt_eliminar'),
            'bloque' => payload_string($wrapper, 'bloque'),
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
