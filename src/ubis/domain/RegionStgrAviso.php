<?php

namespace src\ubis\domain;

/**
 * Mensajes de configuración cuando una dl no tiene región del stgr asignada o falta en xu_dl.
 */
final class RegionStgrAviso
{
    public static function esDlSinRegion(\Throwable $e): bool
    {
        if ($e instanceof RegionStgrConfigException) {
            return true;
        }
        if (!$e instanceof \RuntimeException) {
            return false;
        }
        $msg = $e->getMessage();

        return str_contains($msg, _('falta indicar a que región del stgr pertenece la dl:'))
            || str_contains($msg, 'región del stgr pertenece la dl')
            || str_contains($msg, _('No se encuentra información de la dl:'))
            || str_contains($msg, 'No se encuentra información de la dl')
            || str_contains($msg, _('No se encuentra el id del esquema:'))
            || str_contains($msg, 'No se encuentra el id del esquema')
            || str_contains($msg, _('La delegación «'))
            || str_contains($msg, 'La delegación «');
    }

    public static function append(string $aviso, string $mensaje): string
    {
        $mensaje = trim($mensaje);
        if ($mensaje === '') {
            return $aviso;
        }
        if ($aviso !== '' && str_contains($aviso, $mensaje)) {
            return $aviso;
        }

        return $aviso === '' ? $mensaje : $aviso . '<br>' . $mensaje;
    }

    public static function mensajeDlNoEncontrada(string $dele): string
    {
        return sprintf(
            _('La delegación «%s» no consta en la tabla de delegaciones (xu_dl).'),
            $dele
        ) . '<br>' . _(
            'Se consultan los campos región y región del stgr para obtener el id_schema de cada persona de paso.'
        ) . '<br>' . _(
            'Consecuencias: la persona puede listarse, pero no se enlazarán notas, certificados ni traslados entre regiones hasta corregirlo.'
        ) . '<br>' . sprintf(
            _('Cómo corregirlo: en el mantenimiento de delegaciones (Ubis), dé de alta la dl «%s» con región y región del stgr, y compruebe que el esquema exista en db_idschema.'),
            $dele
        );
    }

    public static function mensajeRegionStgrFalta(string $dele): string
    {
        return sprintf(
            _('La delegación «%s» existe pero no tiene indicada la región del stgr (campo region_stgr en xu_dl).'),
            $dele
        ) . '<br>' . _(
            'Sin ese dato no se puede calcular el esquema de base de datos (id_schema) de las personas de esa dl.'
        ) . '<br>' . _(
            'Consecuencias: operaciones entre regiones del stgr (notas ajenas, certificados, traslados) fallarán para esas personas.'
        ) . '<br>' . sprintf(
            _('Cómo corregirlo: edite la delegación «%s» en Ubis y asigne la región del stgr correspondiente.'),
            $dele
        );
    }

    public static function mensajeEsquemaNoEncontrado(string $esquema, string $dele = ''): string
    {
        $base = sprintf(
            _('No existe el esquema «%s» en la tabla db_idschema'),
            $esquema
        );
        if ($dele !== '') {
            $base .= sprintf(_(' (derivado de la dl «%s»)'), $dele);
        }
        $base .= '.<br>' . _(
            'Se necesita ese registro para localizar el id_schema usado en notas y certificados entre regiones.'
        ) . '<br>' . _(
            'Consecuencias: la persona puede aparecer en listados, pero las acciones que crucen regiones del stgr no funcionarán.'
        ) . '<br>' . _(
            'Cómo corregirlo: verifique en Ubis que la dl tenga región y región del stgr correctas, y que el esquema esté dado de alta en db_idschema (sufijo v o f según el ámbito de sesión).'
        );

        return $base;
    }
}
