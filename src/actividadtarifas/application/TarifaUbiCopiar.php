<?php

namespace src\actividadtarifas\application;

/**
 * Accion: copiar las tarifas de una casa del año anterior al año
 * actual.
 *
 * **Nota**: en el legacy
 * `apps/actividadtarifas/controller/tarifa_ajax.php` la rama `copiar`
 * invocaba `$TarifaUbiRepository->copiar($year, $id_ubi)`, metodo
 * inexistente en la interfaz — por tanto la funcionalidad ya estaba
 * rota. Se mantiene el endpoint por paridad de rutas con el legacy;
 * el JS solo recibe un mensaje de error tipado. La reimplementacion
 * real se trata como deuda separada (fase 2).
 */
final class TarifaUbiCopiar
{
    public static function execute(array $input): string
    {
        $id_ubi = (int)($input['id_ubi'] ?? 0);
        $year = (int)($input['year'] ?? 0);
        if ($id_ubi === 0 || $year === 0) {
            return (string)_("no sé qué casa/año tengo que copiar");
        }

        return (string)_("función de copiar tarifas pendiente de reimplementar");
    }
}
