<?php

declare(strict_types=1);

namespace frontend\configuracion\helpers;

use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\security\HashFront;

/**
 * Completa el JSON de {@see \src\configuracion\application\ModulosFormData} para la vista.
 */
final class ModulosFormRender
{
    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public static function enrich(array $payload): array
    {
        $hm = isset($payload['hash_main']) && is_array($payload['hash_main']) ? $payload['hash_main'] : [];
        $oHash = new HashFront();
        $oHash->setCamposForm(PayloadCoercion::string($hm['campos_form'] ?? ''));
        $cn = PayloadCoercion::string($hm['campos_no'] ?? '');
        if ($cn !== '') {
            $oHash->setCamposNo($cn);
        }
        $oHash->setArrayCamposHidden(ConfiguracionPayload::hashCamposHidden($hm['campos_hidden'] ?? null));
        $payload['hash_form_html'] = $oHash->getCamposHtml();

        $ha = isset($payload['hash_actualizar']) && is_array($payload['hash_actualizar']) ? $payload['hash_actualizar'] : [];
        $oHashA = new HashFront();
        $cna = PayloadCoercion::string($ha['campos_no'] ?? '');
        if ($cna !== '') {
            $oHashA->setCamposNo($cna);
        }
        $oHashA->setArrayCamposHidden(ConfiguracionPayload::hashCamposHidden($ha['campos_hidden'] ?? null));
        $payload['hash_actualizar_html'] = $oHashA->getCamposHtml();

        unset($payload['hash_main'], $payload['hash_actualizar']);

        return $payload;
    }
}
