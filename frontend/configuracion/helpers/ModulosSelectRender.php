<?php

declare(strict_types=1);

namespace frontend\configuracion\helpers;

use frontend\shared\security\HashFront;

require_once __DIR__ . '/configuracion_support.php';

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
        $oHash->setCamposForm(tessera_imprimir_string($hm['campos_form'] ?? ''));
        $cn = tessera_imprimir_string($hm['campos_no'] ?? '');
        if ($cn !== '') {
            $oHash->setCamposNo($cn);
        }
        $hidden = configuracion_hash_campos_hidden($hm['campos_hidden'] ?? null);
        if ($hidden !== []) {
            $oHash->setArrayCamposHidden($hidden);
        }
        $payload['hash_lista_html'] = $oHash->getCamposHtml();
        unset($payload['hash_lista']);

        return $payload;
    }
}
