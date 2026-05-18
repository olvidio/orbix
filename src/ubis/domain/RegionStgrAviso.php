<?php

namespace src\ubis\domain;

/**
 * Avisos de configuración cuando una dl no tiene región del stgr o falta en xu_dl.
 */
final class RegionStgrAviso
{
    public const TIPO_DL_NO_ENCONTRADA = 'dl_no_encontrada';
    public const TIPO_REGION_STGR_FALTA = 'region_stgr_falta';
    public const TIPO_ESQUEMA_NO_ENCONTRADO = 'esquema_no_encontrado';

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
            || str_contains($msg, _('Delegaciones no dadas de alta'))
            || str_contains($msg, 'Delegaciones no dadas de alta');
    }

    /**
     * @param array<string, array<string, string>> $problemas tipo => [dele => dele]
     */
    public static function registrar(array &$problemas, \Throwable $e): void
    {
        if ($e instanceof RegionStgrConfigException) {
            $problemas[$e->getTipo()][$e->getDele()] = $e->getDele();

            return;
        }
        if (!self::esDlSinRegion($e)) {
            return;
        }
        $dele = self::extraerDeleLegacy($e->getMessage());
        if ($dele === '') {
            return;
        }
        $problemas[self::inferirTipoLegacy($e->getMessage())][$dele] = $dele;
    }

    /**
     * @param array<string, array<string, string>> $problemas
     */
    public static function formatear(array $problemas): string
    {
        $bloques = [];

        if (!empty($problemas[self::TIPO_DL_NO_ENCONTRADA])) {
            $bloques[] = self::bloqueDlNoEncontrada(array_values($problemas[self::TIPO_DL_NO_ENCONTRADA]));
        }
        if (!empty($problemas[self::TIPO_REGION_STGR_FALTA])) {
            $bloques[] = self::bloqueRegionStgrFalta(array_values($problemas[self::TIPO_REGION_STGR_FALTA]));
        }
        if (!empty($problemas[self::TIPO_ESQUEMA_NO_ENCONTRADO])) {
            $bloques[] = self::bloqueEsquemaNoEncontrado(array_values($problemas[self::TIPO_ESQUEMA_NO_ENCONTRADO]));
        }

        return implode('<br><br>', $bloques);
    }

    /**
     * @param list<string> $dls
     */
    private static function bloqueDlNoEncontrada(array $dls): string
    {
        sort($dls);

        return sprintf(
            _('Delegaciones no dadas de alta en xu_dl: %s.'),
            self::listaDls($dls)
        ) . '<br>' . _(
            'Se consultan región y región del stgr para obtener el id_schema de cada persona de paso.'
        ) . '<br>' . _(
            'Consecuencias: las personas se listan, pero no se enlazarán notas, certificados ni traslados entre regiones.'
        ) . '<br>' . _(
            'Cómo corregirlo: en Ubis, dé de alta cada dl con región y región del stgr, y compruebe que el esquema exista en db_idschema.'
        );
    }

    /**
     * @param list<string> $dls
     */
    private static function bloqueRegionStgrFalta(array $dls): string
    {
        sort($dls);

        return sprintf(
            _('Delegaciones sin región del stgr (region_stgr en xu_dl): %s.'),
            self::listaDls($dls)
        ) . '<br>' . _(
            'Sin ese dato no se puede calcular el esquema de base de datos (id_schema).'
        ) . '<br>' . _(
            'Consecuencias: operaciones entre regiones del stgr (notas ajenas, certificados, traslados) fallarán para esas personas.'
        ) . '<br>' . _(
            'Cómo corregirlo: en Ubis, edite cada dl y asigne la región del stgr correspondiente.'
        );
    }

    /**
     * @param list<string> $dls
     */
    private static function bloqueEsquemaNoEncontrado(array $dls): string
    {
        sort($dls);

        return sprintf(
            _('Delegaciones cuyo esquema no está en db_idschema: %s.'),
            self::listaDls($dls)
        ) . '<br>' . _(
            'Se necesita ese registro para localizar el id_schema usado en notas y certificados entre regiones.'
        ) . '<br>' . _(
            'Consecuencias: las personas aparecen en listados, pero las acciones que crucen regiones del stgr no funcionarán.'
        ) . '<br>' . _(
            'Cómo corregirlo: verifique en Ubis región y región del stgr de cada dl, y que el esquema esté dado de alta en db_idschema (sufijo v o f según el ámbito de sesión).'
        );
    }

    /**
     * @param list<string> $dls
     */
    private static function listaDls(array $dls): string
    {
        return implode(', ', array_map(static fn (string $dl): string => '«' . $dl . '»', $dls));
    }

    private static function extraerDeleLegacy(string $msg): string
    {
        if (preg_match('/dl:\s*([^\s<»]+)/u', $msg, $m)) {
            return trim($m[1], '«»');
        }
        if (preg_match('/«([^»]+)»/u', $msg, $m)) {
            return $m[1];
        }

        return '';
    }

    private static function inferirTipoLegacy(string $msg): string
    {
        if (str_contains($msg, _('No se encuentra el id del esquema:')) || str_contains($msg, 'No se encuentra el id del esquema')) {
            return self::TIPO_ESQUEMA_NO_ENCONTRADO;
        }
        if (str_contains($msg, _('falta indicar a que región del stgr pertenece la dl:')) || str_contains($msg, 'región del stgr pertenece la dl')) {
            return self::TIPO_REGION_STGR_FALTA;
        }

        return self::TIPO_DL_NO_ENCONTRADA;
    }
}
