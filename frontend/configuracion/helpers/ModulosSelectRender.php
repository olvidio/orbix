<?php

declare(strict_types=1);

namespace frontend\configuracion\helpers;

use frontend\shared\security\HashFront;

/**
 * Completa el JSON de {@see \src\configuracion\application\ModulosSelectData} para la vista.
 */
final class ModulosSelectRender
{
    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public static function enrich(array $payload): array
    {
        $hm = isset($payload['hash_lista']) && is_array($payload['hash_lista']) ? $payload['hash_lista'] : [];
        $oHash = new HashFront();
        $oHash->setCamposForm((string)($hm['campos_form'] ?? ''));
        $cn = (string)($hm['campos_no'] ?? '');
        if ($cn !== '') {
            $oHash->setCamposNo($cn);
        }
        $hidden = $hm['campos_hidden'] ?? [];
        if (is_array($hidden) && $hidden !== []) {
            $oHash->setArrayCamposHidden($hidden);
        }
        $payload['hash_lista_html'] = $oHash->getCamposHtml();
        unset($payload['hash_lista']);

        return $payload;
    }
}
