<?php

declare(strict_types=1);

namespace frontend\actividadcargos\helpers;

use frontend\shared\security\HashFront;
use frontend\shared\web\Desplegable;

/**
 * Hash `HashFront` para formularios y listas dossier actividadcargos.
 *
 * - {@see \src\actividadcargos\application\FormCargosDeActividadData}
 * - {@see \src\actividadcargos\application\FormCargosPersonasEnActividadData}
 * - {@see \src\actividadcargos\application\Select_cargos_de_actividad}
 * - {@see \src\actividadcargos\application\Select_cargos_personas_en_actividad}
 */
final class FormCargosDeActividadHashCompose
{
    /**
     * Convierte `hash_form_config` en `hash_campos_html` para formularios `.phtml`.
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    public static function withHashCamposHtml(array $data): array
    {
        $cfg = $data['hash_form_config'] ?? null;
        unset($data['hash_form_config']);
        if (!is_array($cfg)) {
            $data['hash_campos_html'] = '';

            return $data;
        }

        $data['hash_campos_html'] = self::hashFrontFormHtml($cfg);

        return $data;
    }

    /**
     * @param array{campos_form?: string, campos_no: string, campos_hidden?: array<string, mixed>} $cfg
     */
    public static function hashFrontFormHtml(array $cfg): string
    {
        $oHash = new HashFront();
        $cf = $cfg['campos_form'] ?? null;
        if (is_string($cf) && $cf !== '') {
            $oHash->setCamposForm($cf);
        }
        $oHash->setCamposNo((string)($cfg['campos_no'] ?? ''));
        $hidden = $cfg['campos_hidden'] ?? [];
        if (is_array($hidden) && $hidden !== []) {
            $oHash->setArrayCamposHidden($hidden);
        }

        return $oHash->getCamposHtml();
    }

    /**
     * Hidden + hash para el `<form>` de los select con {@see Lista} (3102 / 1302).
     *
     * @param array{campos_form?: string, campos_no: string, campos_hidden?: array<string, mixed>} $cfg
     */
    public static function selectListaHiddenHtml(array $cfg): string
    {
        return self::hashFrontFormHtml($cfg);
    }

    /**
     * A partir de `personas_select` / `cargos_select` (datos del API) genera el HTML de los {@see Desplegable}.
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    public static function withDesplegablesHtml(array $data): array
    {
        $personas = $data['personas_select'] ?? null;
        unset($data['personas_select']);
        if (is_array($personas) && isset($personas['opciones']) && is_array($personas['opciones'])) {
            $d = Desplegable::desdeOpciones($personas['opciones'], 'id_nom', true);
            $sel = $personas['opcion_sel'] ?? '';
            if ($sel !== '' && $sel !== null) {
                $d->setOpcion_sel((string)$sel);
            }
            $data['desplegable_personas_html'] = $d->desplegable();
        } else {
            $data['desplegable_personas_html'] = '';
        }

        $cargos = $data['cargos_select'] ?? null;
        unset($data['cargos_select']);
        if (is_array($cargos) && isset($cargos['opciones']) && is_array($cargos['opciones'])) {
            $d = Desplegable::desdeOpciones($cargos['opciones'], 'id_cargo', true);
            $d->setOpcion_sel((string)($cargos['opcion_sel'] ?? ''));
            $data['desplegable_cargos_html'] = $d->desplegable();
        } else {
            $data['desplegable_cargos_html'] = '';
        }

        return $data;
    }
}
