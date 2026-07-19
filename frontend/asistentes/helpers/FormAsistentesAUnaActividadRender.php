<?php

declare(strict_types=1);

namespace frontend\asistentes\helpers;

use frontend\shared\helpers\FuncTablasSupport;
use frontend\notas\helpers\NotasFormSupport;
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

        $paths = isset($payload['paths']) && is_array($payload['paths']) ? $payload['paths'] : [];

        $url_guardar = AppUrlConfig::browserUrlFromAppRelative(
            \frontend\shared\helpers\FuncTablasSupport::payloadString($paths, 'asistente_guardar')
        );
        $url_self = AppUrlConfig::browserUrlFromAppRelative(
            \frontend\shared\helpers\FuncTablasSupport::payloadString($paths, 'form_self')
        );

        $plazas_installed = !empty($payload['plazas_installed']);
        $url_ajax = '';
        $h1 = '';
        if ($plazas_installed) {
            $ajaxPath = \frontend\shared\helpers\FuncTablasSupport::payloadString($paths, 'posibles_propietarios_data');
            if ($ajaxPath !== '') {
                $url_ajax = AppUrlConfig::browserUrlFromAppRelative($ajaxPath);
                $ajaxMeta = isset($payload['ajax_propietarios']) && is_array($payload['ajax_propietarios'])
                    ? $payload['ajax_propietarios']
                    : [];
                $oHash1 = new HashFront();
                $oHash1->setUrl($url_ajax);
                $oHash1->setCamposForm(\frontend\shared\helpers\FuncTablasSupport::payloadString($ajaxMeta, 'campos_form', 'id_activ!id_nom'));
                $h1 = $oHash1->linkSinValParams();
            }
        }

        $hashMain = isset($payload['hash_main']) && is_array($payload['hash_main']) ? $payload['hash_main'] : [];
        $oHash = new HashFront();
        $oHash->setCamposForm(\frontend\shared\helpers\FuncTablasSupport::payloadString($hashMain, 'campos_form'));
        $oHash->setCamposNo(\frontend\shared\helpers\FuncTablasSupport::payloadString($hashMain, 'campos_no'));
        $hidden = AsistentesRenderSupport::hashCamposHidden($hashMain['campos_hidden'] ?? []);
        $oHash->setArrayCamposHidden($hidden);

        $desplegable_personas_html = '';
        $personasOpciones = NotasFormSupport::desplegableOpciones($payload['personas_opciones'] ?? []);
        if ($personasOpciones !== []) {
            $oDespl = new Desplegable();
            $oDespl->setNombre('id_nom');
            $oDespl->setOpciones($personasOpciones);
            $onChange = $payload['personas_onchange'] ?? null;
            if (is_string($onChange) && $onChange !== '') {
                $oDespl->setAction($onChange);
            }
            $desplegable_personas_html = $oDespl->desplegable();
        }

        $desplegable_plaza_html = '';
        $desplegable_propietarios_html = '';
        if ($plazas_installed) {
            $plazaOpciones = NotasFormSupport::desplegableOpciones($payload['plaza_opciones'] ?? []);
            $oPlaza = new Desplegable();
            $oPlaza->setNombre('plaza');
            $oPlaza->setOpciones($plazaOpciones);
            $oPlaza->setOpcion_sel(\frontend\shared\helpers\FuncTablasSupport::payloadString($payload, 'plaza_selected'));
            $desplegable_plaza_html = $oPlaza->desplegable();

            $propOpciones = NotasFormSupport::desplegableOpciones($payload['propietario_opciones'] ?? []);
            $oProp = new Desplegable();
            $oProp->setNombre('propietario');
            $oProp->setOpciones($propOpciones);
            $oProp->setOpcion_sel(\frontend\shared\helpers\FuncTablasSupport::payloadString($payload, 'propietario_selected'));
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
