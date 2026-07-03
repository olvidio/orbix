<?php

declare(strict_types=1);

namespace frontend\actividades\helpers;

use frontend\notas\helpers\NotasFormSupport;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\web\Desplegable;

final class ActividadesRenderSupport
{
public static function desplegableHtml(?array $raw): string
{
    if ($raw === null || $raw === []) {
        return '';
    }
    $id = PayloadCoercion::string($raw['id'] ?? '');
    if ($id === '') {
        return '';
    }
    $opciones = NotasFormSupport::desplegableOpciones($raw['opciones'] ?? []);
    $blanco = !array_key_exists('blanco', $raw) || (bool) $raw['blanco'];
    $d = Desplegable::desdeOpciones($opciones, $id, $blanco);
    $selected = PayloadCoercion::string($raw['selected'] ?? '');
    if ($selected !== '') {
        $d->setOpcion_sel($selected);
    }
    $action = PayloadCoercion::string($raw['action'] ?? '');
    if ($action !== '') {
        $d->setAction($action);
    }
    if (array_key_exists('val_blanco', $raw)) {
        $d->setBlanco(true);
        $d->setValBlanco(PayloadCoercion::string($raw['val_blanco']));
    }
    $opcionNo = $raw['opcion_no'] ?? null;
    if (is_array($opcionNo) && $opcionNo !== []) {
        $normalized = [];
        foreach ($opcionNo as $item) {
            $normalized[] = PayloadCoercion::string($item);
        }
        $d->setOpcion_no($normalized);
    }

    return $d->desplegable();
}
}
