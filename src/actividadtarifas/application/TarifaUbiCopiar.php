<?php

namespace src\actividadtarifas\application;


/**
 * Accion: copiar las tarifas de una casa del año anterior al año actual.
 *
 * La funcionalidad legacy estaba rota (metodo `copiar()` inexistente).
 * Se mantiene el endpoint por paridad de rutas.
 */
final class TarifaUbiCopiar
{
    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $id_ubi = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'id_ubi');
        $year = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'year');
        if ($id_ubi === 0 || $year === 0) {
            return (string) _("no sé qué casa/año tengo que copiar");
        }

        return (string) _("función de copiar tarifas pendiente de reimplementar");
    }
}
