<?php

declare(strict_types=1);

namespace frontend\actividades\helpers;

use frontend\notas\helpers\NotasFormSupport;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\web\Desplegable;

final class ActividadesRenderSupport
{
/** @param array<string, mixed>|null $raw */
public static function desplegableHtml(?array $raw): string
{
    if ($raw === null || $raw === []) {
        return '';
    }
    $id = \frontend\shared\helpers\PayloadCoercion::string($raw['id'] ?? '');
    if ($id === '') {
        return '';
    }
    $opciones = NotasFormSupport::desplegableOpciones($raw['opciones'] ?? []);
    $blanco = !array_key_exists('blanco', $raw) || (bool) $raw['blanco'];
    $d = Desplegable::desdeOpciones($opciones, $id, $blanco);
    $selected = \frontend\shared\helpers\PayloadCoercion::string($raw['selected'] ?? '');
    if ($selected !== '') {
        $d->setOpcion_sel($selected);
    }
    $action = \frontend\shared\helpers\PayloadCoercion::string($raw['action'] ?? '');
    if ($action !== '') {
        $d->setAction($action);
    }
    if (array_key_exists('val_blanco', $raw)) {
        $d->setBlanco(true);
        $d->setValBlanco(\frontend\shared\helpers\PayloadCoercion::string($raw['val_blanco']));
    }
    $opcionNo = $raw['opcion_no'] ?? null;
    if (is_array($opcionNo) && $opcionNo !== []) {
        $normalized = [];
        foreach ($opcionNo as $item) {
            $normalized[] = \frontend\shared\helpers\PayloadCoercion::string($item);
        }
        $d->setOpcion_no($normalized);
    }

    return $d->desplegable();
}
}
