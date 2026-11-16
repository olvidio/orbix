<?php

declare(strict_types=1);

namespace frontend\shared\helpers;

use frontend\shared\web\Lista;

/**
 * Monta una {@see Lista} con los datos neutros que devuelve un endpoint de `src/`
 * (`cabeceras`, `datos` y, si vienen, `grupos`, `pie`, `botones` e `id_tabla`).
 */
final class ListaDesdeDatos
{
    /**
     * @param array<string, mixed> $data
     */
    public static function lista(array $data): Lista
    {
        $oLista = new Lista();
        if (is_string($data['id_tabla'] ?? null) && $data['id_tabla'] !== '') {
            $oLista->setId_tabla($data['id_tabla']);
        }
        if (is_array($data['grupos'] ?? null)) {
            $oLista->setGrupos($data['grupos']);
        }
        $oLista->setCabeceras(is_array($data['cabeceras'] ?? null) ? $data['cabeceras'] : []);
        if (is_array($data['botones'] ?? null)) {
            $oLista->setBotones($data['botones']);
        }
        $oLista->setDatos(is_array($data['datos'] ?? null) ? $data['datos'] : []);
        if (is_string($data['pie'] ?? null) && $data['pie'] !== '') {
            $oLista->setPie($data['pie']);
        }

        return $oLista;
    }
}
