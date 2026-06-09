<?php

declare(strict_types=1);

namespace frontend\actividadcargos\helpers;

require_once __DIR__ . '/actividadcargos_support.php';

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
        $cfg = actividadcargos_hash_form_config($data['hash_form_config'] ?? null);
        unset($data['hash_form_config']);
        if ($cfg === null) {
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
        $oHash->setCamposNo($cfg['campos_no']);
        $hidden = $cfg['campos_hidden'] ?? [];
        if ($hidden !== []) {
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
        $personas = actividadcargos_desplegable_select($data['personas_select'] ?? null);
        unset($data['personas_select']);
        if ($personas !== null) {
            $d = Desplegable::desdeOpciones($personas['opciones'], 'id_nom', true);
            if ($personas['opcion_sel'] !== '') {
                $d->setOpcion_sel($personas['opcion_sel']);
            }
            $data['desplegable_personas_html'] = $d->desplegable();
        } else {
            $data['desplegable_personas_html'] = '';
        }

        $cargos = actividadcargos_desplegable_select($data['cargos_select'] ?? null);
        unset($data['cargos_select']);
        if ($cargos !== null) {
            $d = Desplegable::desdeOpciones($cargos['opciones'], 'id_cargo', true);
            if ($cargos['opcion_sel'] !== '') {
                $d->setOpcion_sel($cargos['opcion_sel']);
            }
            $data['desplegable_cargos_html'] = $d->desplegable();
        } else {
            $data['desplegable_cargos_html'] = '';
        }

        return $data;
    }
}
