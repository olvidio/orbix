<?php

declare(strict_types=1);

namespace frontend\ubiscamas\helpers;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\security\HashFront;

require_once __DIR__ . '/ubiscamas_support.php';

/**
 * Construye campos HTML de `HashFront` y parámetros `linkSinValParams` para formularios ubiscamas.
 * Los datos planos vienen de {@see \src\ubiscamas\application\HabitacionFormData} y {@see \src\ubiscamas\application\CamaFormData}.
 */
final class UbiscamasFormHashCompose
{
    /**
     * @param array<string, mixed> $data respuesta de HabitacionFormData::build
     * @return array{
     *   hash_form_html: string,
     *   hash_actualizar_html: string,
     *   url_cama_form: string,
     *   h_cama_form_params: string,
     *   url_cama_delete: string,
     *   h_cama_delete_params: string
     * }
     */
    public static function habitacionForm(array $data): array
    {
        $form = isset($data['hash_form']) && is_array($data['hash_form']) ? $data['hash_form'] : [];
        $oHash = new HashFront();
        $oHash->setCamposForm(tessera_imprimir_string($form['campos_form'] ?? ''));
        $oHash->setCamposChk(tessera_imprimir_string($form['campos_chk'] ?? ''));
        $oHash->setCamposNo(tessera_imprimir_string($form['campos_no'] ?? ''));
        $oHash->setArrayCamposHidden(ubiscamas_hash_campos_hidden($form['campos_hidden'] ?? []));

        $act = isset($data['hash_actualizar']) && is_array($data['hash_actualizar']) ? $data['hash_actualizar'] : [];
        $oAct = new HashFront();
        $oAct->setCamposForm('');
        $oAct->setCamposNo(tessera_imprimir_string($act['campos_no'] ?? ''));
        $oAct->setArrayCamposHidden(ubiscamas_hash_campos_hidden($act['campos_hidden'] ?? []));

        $cf = isset($data['cama_form_hash']) && is_array($data['cama_form_hash']) ? $data['cama_form_hash'] : [];
        $oCamaForm = new HashFront();
        $oCamaForm->setUrl(tessera_imprimir_string($cf['url'] ?? ''));
        $oCamaForm->setCamposForm(tessera_imprimir_string($cf['campos_form'] ?? ''));

        $cd = isset($data['cama_delete_hash']) && is_array($data['cama_delete_hash']) ? $data['cama_delete_hash'] : [];
        $oCamaDel = new HashFront();
        $oCamaDel->setUrl(tessera_imprimir_string($cd['url'] ?? ''));
        $oCamaDel->setCamposForm(tessera_imprimir_string($cd['campos_form'] ?? ''));

        return [
            'hash_form_html' => $oHash->getCamposHtml(),
            'hash_actualizar_html' => $oAct->getCamposHtml(),
            'url_cama_form' => tessera_imprimir_string($cf['url'] ?? ''),
            'h_cama_form_params' => $oCamaForm->linkSinValParams(),
            'url_cama_delete' => tessera_imprimir_string($cd['url'] ?? ''),
            'h_cama_delete_params' => $oCamaDel->linkSinValParams(),
        ];
    }

    /**
     * @param array<string, mixed> $data respuesta de CamaFormData::build
     * @return array{hash_form_html: string, cama_update_url: string}
     */
    public static function camaForm(array $data): array
    {
        $form = isset($data['hash_form']) && is_array($data['hash_form']) ? $data['hash_form'] : [];
        $oHash = new HashFront();
        $oHash->setCamposForm(tessera_imprimir_string($form['campos_form'] ?? ''));
        $oHash->setCamposChk(tessera_imprimir_string($form['campos_chk'] ?? ''));
        $oHash->setArrayCamposHidden(ubiscamas_hash_campos_hidden($form['campos_hidden'] ?? []));

        $base = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/');

        return [
            'hash_form_html' => $oHash->getCamposHtml(),
            'cama_update_url' => $base . '/src/ubiscamas/cama_update',
        ];
    }
}
