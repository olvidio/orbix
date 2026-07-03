<?php

declare(strict_types=1);

namespace frontend\asistentes\helpers;

use frontend\shared\helpers\FuncTablasSupport;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\security\HashFront;
use frontend\shared\web\Lista;
use frontend\actividades\helpers\ActividadesListaSupport;

/**
 * Completa el JSON de {@see \src\asistentes\application\ActivPendientesSelectData} para la vista.
 */
final class ActivPendientesSelectRender
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
        $base = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/');
        $formRel = \frontend\shared\helpers\FuncTablasSupport::payloadString($paths, 'form_action');
        $payload['form_action'] = $formRel !== '' ? $base . '/' . ltrim($formRel, '/') : '';

        $hashMain = isset($payload['hash_main']) && is_array($payload['hash_main']) ? $payload['hash_main'] : [];
        $oHashForm = new HashFront();
        $oHashForm->setCamposForm(\frontend\shared\helpers\FuncTablasSupport::payloadString($hashMain, 'campos_form', 'tipo_personas!sactividad!any'));
        $cn = \frontend\shared\helpers\FuncTablasSupport::payloadString($hashMain, 'campos_no');
        if ($cn !== '') {
            $oHashForm->setCamposNo($cn);
        }
        $hidden = AsistentesRenderSupport::hashCamposHidden($hashMain['campos_hidden'] ?? []);
        if ($hidden !== []) {
            $oHashForm->setArrayCamposHidden($hidden);
        }
        $payload['hash_campos_html'] = $oHashForm->getCamposHtml();

        $a_cabeceras = ActividadesListaSupport::cabeceras($payload['a_cabeceras_activ_pendientes'] ?? []);
        $a_valores_dl = ActividadesListaSupport::signValoresFromPayload($payload['a_valores_activ_pendientes_dl'] ?? []);
        $a_valores_otras = ActividadesListaSupport::signValoresFromPayload($payload['a_valores_activ_pendientes_otras'] ?? []);

        $oTablaDl = new Lista();
        $oTablaDl->setId_tabla('activ_pendientes_select');
        $oTablaDl->setCabeceras($a_cabeceras);
        $oTablaDl->setDatos($a_valores_dl);
        $payload['tabla_dl_html'] = $oTablaDl->mostrar_tabla();

        $oTablaOtrasDl = new Lista();
        $oTablaOtrasDl->setId_tabla('activ_pendientes_select_otras');
        $oTablaOtrasDl->setCabeceras($a_cabeceras);
        $oTablaOtrasDl->setDatos($a_valores_otras);
        $payload['tabla_otras_html'] = $oTablaOtrasDl->mostrar_tabla();

        unset(
            $payload['paths'],
            $payload['hash_main'],
            $payload['a_cabeceras_activ_pendientes'],
            $payload['a_valores_activ_pendientes_dl'],
            $payload['a_valores_activ_pendientes_otras'],
        );

        return $payload;
    }
}
