<?php

declare(strict_types=1);

namespace frontend\asistentes\helpers;

use frontend\shared\helpers\FuncTablasSupport;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\shared\helpers\PayloadCoercion;
use frontend\actividades\helpers\ActividadesListaSupport;

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
        $path = FuncTablasSupport::payloadString($meta, 'path');
        $campos = FuncTablasSupport::payloadString($meta, 'campos_form');
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

        $relForm = FuncTablasSupport::payloadString($wrapper, 'url_form_relative');
        $urlForm = $abs($relForm);
        $urlFormCargos = $abs(FuncTablasSupport::payloadString($wrapper, 'url_form_cargos_relative'));
        $urlEliminar = $abs(FuncTablasSupport::payloadString($wrapper, 'url_eliminar_path'));
        $urlCargoEliminar = $abs(FuncTablasSupport::payloadString($wrapper, 'url_cargo_eliminar_path'));

        $moverPath = FuncTablasSupport::payloadString($wrapper, 'url_mover_path');
        $urlMover = $abs($moverPath);
        $urlPlazaAsignar = $abs(FuncTablasSupport::payloadString($wrapper, 'url_plaza_asignar_path'));

        $ajaxMover = isset($seg['ajax_hash_mover']) && is_array($seg['ajax_hash_mover']) ? $seg['ajax_hash_mover'] : [];
        $ajaxPlaza = isset($seg['ajax_hash_plaza']) && is_array($seg['ajax_hash_plaza']) ? $seg['ajax_hash_plaza'] : [];
        $h3 = self::linkSinValParamsFromPath([
            'path' => $moverPath !== '' ? $moverPath : FuncTablasSupport::payloadString($ajaxMover, 'path'),
            'campos_form' => FuncTablasSupport::payloadString($ajaxMover, 'campos_form', 'id_pau!id_activ'),
        ]);
        $plazaPath = FuncTablasSupport::payloadString($wrapper, 'url_plaza_asignar_path');
        $h4 = self::linkSinValParamsFromPath([
            'path' => $plazaPath !== '' ? $plazaPath : FuncTablasSupport::payloadString($ajaxPlaza, 'path'),
            'campos_form' => FuncTablasSupport::payloadString($ajaxPlaza, 'campos_form', 'plaza!lista_json!id_activ'),
        ]);

        $hashMain = isset($seg['hash_main']) && is_array($seg['hash_main']) ? $seg['hash_main'] : [];
        $oHash = new HashFront();
        $oHash->setCamposForm(FuncTablasSupport::payloadString($hashMain, 'campos_form'));
        $oHash->setCamposNo(FuncTablasSupport::payloadString($hashMain, 'campos_no'));
        $hidden = AsistentesRenderSupport::hashCamposHidden($hashMain['campos_hidden'] ?? []);
        $oHash->setArrayCamposHidden($hidden);

        $hashMat = isset($seg['hash_matriculas']) && is_array($seg['hash_matriculas']) ? $seg['hash_matriculas'] : [];
        $oHash1 = new HashFront();
        $oHash1->setCamposForm(FuncTablasSupport::payloadString($hashMat, 'campos_form'));
        $oHash1->setCamposNo(FuncTablasSupport::payloadString($hashMat, 'campos_no'));
        $hiddenM = AsistentesRenderSupport::hashCamposHidden($hashMat['campos_hidden'] ?? []);
        $oHash1->setArrayCamposHidden($hiddenM);

        $tabla = isset($seg['tabla']) && is_array($seg['tabla']) ? $seg['tabla'] : [];
        $oTabla = new Lista();
        $oTabla->setId_tabla(FuncTablasSupport::payloadString($tabla, 'id_tabla', 'select_asistentes_a_una_actividad'));
        $oTabla->setCabeceras(ActividadesListaSupport::cabeceras($tabla['cabeceras'] ?? []));
        $oTabla->setBotones(ActividadesListaSupport::botones($tabla['botones'] ?? []));
        $oTabla->setDatos(ActividadesListaSupport::datos($tabla['valores'] ?? []));

        $aLinks_dl = AsistentesRenderSupport::signLinkMap($seg['links_dl_specs'] ?? []);

        $oView = new ViewNewPhtml('frontend\asistentes\view');

        return $oView->renderizar('select_asistentes_a_una_actividad.phtml', [
            'oTabla' => $oTabla,
            'oHash' => $oHash,
            'oHash1' => $oHash1,
            'id_sel_value' => FuncTablasSupport::payloadString($seg, 'id_sel_value'),
            'id_pau' => PayloadCoercion::int($seg['id_pau'] ?? 0),
            'h3' => $h3,
            'h4' => $h4,
            'plazas_txt' => FuncTablasSupport::payloadString($seg, 'plazas_txt'),
            'resumen_plazas' => FuncTablasSupport::payloadString($seg, 'resumen_plazas'),
            'resumen_plazas2' => FuncTablasSupport::payloadString($seg, 'resumen_plazas2'),
            'leyenda_html' => FuncTablasSupport::payloadString($seg, 'leyenda_html'),
            'aLinks_dl' => $aLinks_dl,
            'msg_err' => FuncTablasSupport::payloadString($seg, 'msg_err'),
            'txt_eliminar' => FuncTablasSupport::payloadString($wrapper, 'txt_eliminar'),
            'bloque' => FuncTablasSupport::payloadString($wrapper, 'bloque'),
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
