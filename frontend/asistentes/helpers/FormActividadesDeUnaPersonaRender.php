<?php

declare(strict_types=1);

namespace frontend\asistentes\helpers;

use function frontend\shared\helpers\payload_string;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\security\HashFront;
use frontend\shared\web\Desplegable;

/**
 * Completa el payload JSON de {@see \src\asistentes\application\FormActividadesDeUnaPersonaData}
 * con HTML de HashFront y Desplegable.
 */
final class FormActividadesDeUnaPersonaRender
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

        $url_guardar = $base . '/' . ltrim(payload_string($paths, 'asistente_guardar'), '/');
        $url_self = $base . '/' . ltrim(payload_string($paths, 'form_self'), '/');

        $plazas_installed = !empty($payload['plazas_installed']);
        $url_ajax = '';
        $h1 = '';
        if ($plazas_installed) {
            $ajaxPath = payload_string($paths, 'posibles_propietarios_data');
            if ($ajaxPath !== '') {
                $url_ajax = $base . '/' . ltrim($ajaxPath, '/');
                $ajaxMeta = isset($payload['ajax_propietarios']) && is_array($payload['ajax_propietarios'])
                    ? $payload['ajax_propietarios']
                    : [];
                $oHash1 = new HashFront();
                $oHash1->setUrl($url_ajax);
                $oHash1->setCamposForm(payload_string($ajaxMeta, 'campos_form', 'id_activ!id_nom'));
                $h1 = $oHash1->linkSinValParams();
            }
        }

        $hashMain = isset($payload['hash_main']) && is_array($payload['hash_main']) ? $payload['hash_main'] : [];
        $oHash = new HashFront();
        $oHash->setCamposForm(payload_string($hashMain, 'campos_form'));
        $oHash->setCamposNo(payload_string($hashMain, 'campos_no'));
        $hidden = $hashMain['campos_hidden'] ?? [];
        $oHash->setArrayCamposHidden(is_array($hidden) ? $hidden : []);

        $despl_actividades_html = '';
        if (isset($payload['actividades_opciones']) && is_array($payload['actividades_opciones'])) {
            $oDesplActividades = new Desplegable();
            $oDesplActividades->setNombre('id_activ');
            $oDesplActividades->setOpciones($payload['actividades_opciones']);
            $onChange = $payload['actividades_onchange'] ?? null;
            if (is_string($onChange) && $onChange !== '') {
                $oDesplActividades->setAction($onChange);
            }
            $despl_actividades_html = $oDesplActividades->desplegable();
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
            $oPlaza->setOpcion_sel(payload_string($payload, 'plaza_selected'));
            $desplegable_plaza_html = $oPlaza->desplegable();

            $propOpciones = isset($payload['propietario_opciones']) && is_array($payload['propietario_opciones'])
                ? $payload['propietario_opciones']
                : [];
            $oProp = new Desplegable();
            $oProp->setNombre('propietario');
            $oProp->setOpciones($propOpciones);
            $oProp->setOpcion_sel(payload_string($payload, 'propietario_selected'));
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
            'despl_actividades_html' => $despl_actividades_html,
            'desplegable_plaza_html' => $desplegable_plaza_html,
            'desplegable_propietarios_html' => $desplegable_propietarios_html,
        ]);
    }
}
