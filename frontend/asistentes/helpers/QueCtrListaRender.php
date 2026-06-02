<?php

declare(strict_types=1);

namespace frontend\asistentes\helpers;

use function frontend\shared\helpers\payload_string;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\security\HashFront;
use frontend\shared\web\PeriodoQue;

/**
 * Completa el JSON de {@see \src\asistentes\application\QueCtrListaData} para la vista.
 */
final class QueCtrListaRender
{
    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public static function enrich(array $payload): array
    {
        $hashMain = isset($payload['hash_main']) && is_array($payload['hash_main']) ? $payload['hash_main'] : [];
        $oHash = new HashFront();
        $oHash->setCamposForm(payload_string($hashMain, 'campos_form'));
        $cn = payload_string($hashMain, 'campos_no');
        if ($cn !== '') {
            $oHash->setCamposNo($cn);
        }
        $hidden = $hashMain['campos_hidden'] ?? [];
        $oHash->setArrayCamposHidden(is_array($hidden) ? $hidden : []);
        $payload['hash_form_html'] = $oHash->getCamposHtml();
        unset($payload['hash_main']);

        $pf = $payload['periodo_form'] ?? null;
        if (is_array($pf)) {
            $oFormP = new PeriodoQue();
            $oFormP->setFormName(payload_string($pf, 'form_name', 'modifica'));
            $oFormP->setTitulo(payload_string($pf, 'titulo'));
            $oFormP->setPosiblesPeriodos((array)($pf['opciones_periodos'] ?? []));
            $oFormP->setDesplPeriodosOpcion_sel($pf['periodo_sel'] ?? 'tot_any');
            $oFormP->setDesplAnysOpcion_sel($pf['year_sel'] ?? (int)date('Y'));
            $payload['periodo_form_html'] = $oFormP->getHtml();
        } else {
            $payload['periodo_form_html'] = '';
        }
        unset($payload['periodo_form']);

        $action = payload_string($payload, 'action');
        if ($action !== '' && !str_starts_with($action, 'http://') && !str_starts_with($action, 'https://')) {
            $payload['action'] = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/') . '/' . ltrim($action, '/');
        }

        return $payload;
    }
}
