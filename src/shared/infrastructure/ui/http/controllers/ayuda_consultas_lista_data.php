<?php

/**
 * Endpoint JSON: consultas hechas a la ayuda, agrupadas por pregunta, para revisarlas.
 *
 * Entrada: `estado` (1 pendiente, 2 resuelta, 3 descartada; por defecto 1) y `ver`
 * (`problemas` por defecto | `todas`). Solo administradores: los demás reciben `aviso_txt`.
 */

use src\shared\application\AyudaConsultasListaData;
use src\shared\domain\helpers\FilterPostGet;
use src\shared\infrastructure\DependencyResolver;
use src\shared\infrastructure\ui\http\PermisoRevisarAyudaGuard;
use src\shared\web\ContestarJson;

if (!PermisoRevisarAyudaGuard::permite()) {
    ContestarJson::enviar('', ['aviso_txt' => _("Las consultas a la ayuda las revisa un administrador.")]);
    return;
}

ContestarJson::enviar('', DependencyResolver::get(AyudaConsultasListaData::class)->execute([
    'estado' => FilterPostGet::post('estado'),
    'ver' => FilterPostGet::post('ver'),
]));
