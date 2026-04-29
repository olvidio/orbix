<?php

declare(strict_types=1);

namespace frontend\asistentes\helpers;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\security\HashFront;
use frontend\shared\web\Desplegable;

/**
 * Completa el payload de {@see \src\asistentes\application\FormAsistentesAUnaActividadData}
 * con HTML de HashFront y Desplegable.
 */
final class FormAsistentesAUnaActividadRender
{
    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public static function enrich(array $payload): array
    {
        if (isset($payload['error'])) {
            return $payload;
        }

        $base = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/');
        $paths = isset($payload['paths']) && is_array($payload['paths']) ? $payload['paths'] : [];

        $url_guardar = $base . '/' . ltrim((string)($paths['asistente_guardar'] ?? ''), '/');
        $url_self = $base . '/' . ltrim((string)($paths['form_self'] ?? ''), '/');

        $plazas_installed = !empty($payload['plazas_installed']);
        $url_ajax = '';
        $h1 = '';
        if ($plazas_installed) {
            $ajaxPath = (string)($paths['posibles_propietarios_data'] ?? '');
            if ($ajaxPath !== '') {
                $url_ajax = $base . '/' . ltrim($ajaxPath, '/');
                $ajaxMeta = isset($payload['ajax_propietarios']) && is_array($payload['ajax_propietarios'])
                    ? $payload['ajax_propietarios']
                    : [];
                $oHash1 = new HashFront();
                $oHash1->setUrl($url_ajax);
                $oHash1->setCamposForm((string)($ajaxMeta['campos_form'] ?? 'id_activ!id_nom'));
                $h1 = $oHash1->linkSinValParams();
            }
        }

        $hashMain = isset($payload['hash_main']) && is_array($payload['hash_main']) ? $payload['hash_main'] : [];
        $oHash = new HashFront();
        $oHash->setCamposForm((string)($hashMain['campos_form'] ?? ''));
        $oHash->setCamposNo((string)($hashMain['campos_no'] ?? ''));
        $hidden = $hashMain['campos_hidden'] ?? [];
        $oHash->setArrayCamposHidden(is_array($hidden) ? $hidden : []);

        $desplegable_personas_html = '';
        if (isset($payload['personas_opciones']) && is_array($payload['personas_opciones'])) {
            $oDespl = new Desplegable();
            $oDespl->setNombre('id_nom');
            $oDespl->setOpciones($payload['personas_opciones']);
            $onChange = $payload['personas_onchange'] ?? null;
            if (is_string($onChange) && $onChange !== '') {
                $oDespl->setAction($onChange);
            }
            $desplegable_personas_html = $oDespl->desplegable();
        }

        $desplegable_plaza_html = '';
        $desplegable_propietarios_html = '';
        if ($plazas_installed) {
            $plazaOpciones = isset($payload['plaza_opciones']) && is_array($payload['plaza_opciones'])
                ? $payload['plaza_opciones']
                : [];
            $oPlaza = new Desplegable();
            $oPlaza->setNombre('plaza');
            $oPlaza->setOpciones($plazaOpciones);
            $oPlaza->setOpcion_sel((string)($payload['plaza_selected'] ?? ''));
            $desplegable_plaza_html = $oPlaza->desplegable();

            $propOpciones = isset($payload['propietario_opciones']) && is_array($payload['propietario_opciones'])
                ? $payload['propietario_opciones']
                : [];
            $oProp = new Desplegable();
            $oProp->setNombre('propietario');
            $oProp->setOpciones($propOpciones);
            $oProp->setOpcion_sel((string)($payload['propietario_selected'] ?? ''));
            if (!empty($payload['propietario_select_blanco'])) {
                $oProp->setBlanco(true);
            }
            $desplegable_propietarios_html = $oProp->desplegable();
        }

        return array_merge($payload, [
            'h1' => $h1,
            'url_ajax' => $url_ajax,
            'url_guardar' => $url_guardar,
            'url_self' => $url_self,
            'hash_campos_html' => $oHash->getCamposHtml(),
            'desplegable_personas_html' => $desplegable_personas_html,
            'desplegable_plaza_html' => $desplegable_plaza_html,
            'desplegable_propietarios_html' => $desplegable_propietarios_html,
        ]);
    }
}
