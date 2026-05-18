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
    public const TIPO_PERSONA_SIN_ID_SCHEMA = 'persona_sin_id_schema';

    public static function esMensajeSuave(string $mensaje): bool
    {
        $mensaje = self::stripHtml($mensaje);

        return self::esTextoConfiguracionDl($mensaje)
            || str_contains($mensaje, _('persona no válida'))
            || str_contains($mensaje, 'persona no válida')
            || str_contains($mensaje, _('persona no encontrada'))
            || str_contains($mensaje, 'persona no encontrada')
            || str_contains($mensaje, _('Personas del listado sin id_schema válido'))
            || str_contains($mensaje, 'Personas del listado sin id_schema válido');
    }

    public static function mensajePersonaNoValida(): string
    {
        return _('persona no válida') . '<br>' . _(
            'No se ha podido identificar la persona seleccionada (falta id_nom o la dl no tiene esquema válido).'
        ) . '<br>' . _(
            'Consecuencias: no se pueden abrir tessera, certificados ni otras operaciones entre regiones para esa fila.'
        ) . '<br>' . _(
            'Cómo corregirlo: seleccione de nuevo la persona en el listado y, si la dl aparece en el aviso de configuración, corríjala en Ubis.'
        );
    }

    public static function combinarAvisos(string ...$avisos): string
    {
        $partes = [];
        foreach ($avisos as $aviso) {
            $aviso = trim($aviso);
            if ($aviso === '') {
                continue;
            }
            if ($partes !== [] && str_contains(implode(' ', $partes), $aviso)) {
                continue;
            }
            $partes[] = $aviso;
        }

        return implode('<br><br>', $partes);
    }

    /**
     * @param array<string, array<string, string>> $problemas
     */
    public static function registrarPersonaSinSchema(
        array &$problemas,
        int $idNom,
        string $nombre,
        string $dl = '',
    ): void {
        if ($idNom <= 0) {
            return;
        }
        $etiqueta = $nombre . ' (id ' . $idNom . ')';
        if ($dl !== '') {
            $etiqueta .= ', dl ' . $dl;
        }
        $problemas[self::TIPO_PERSONA_SIN_ID_SCHEMA][(string)$idNom] = $etiqueta;
    }

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

        $texto = implode('<br><br>', $bloques);
        if (!empty($problemas[self::TIPO_PERSONA_SIN_ID_SCHEMA])) {
            $personas = array_values($problemas[self::TIPO_PERSONA_SIN_ID_SCHEMA]);
            sort($personas);
            $lineaPersonas = sprintf(
                _('Personas del listado sin id_schema válido: %s.'),
                self::listaEtiquetas($personas)
            );
            if ($texto === '') {
                $texto = self::bloquePersonaSinSchema($personas);
            } else {
                $texto .= '<br>' . $lineaPersonas;
            }
        }

        return $texto;
    }

    /**
     * @param list<string> $personas
     */
    private static function bloquePersonaSinSchema(array $personas): string
    {
        sort($personas);

        return sprintf(
            _('Personas del listado sin id_schema válido: %s.'),
            self::listaEtiquetas($personas)
        ) . '<br>' . _(
            'Sin id_schema no se pueden enlazar notas, certificados ni traslados entre regiones del stgr.'
        ) . '<br>' . _(
            'Consecuencias: la fila aparece en el listado, pero tessera, certificados y traslados fallarán para esas personas.'
        ) . '<br>' . _(
            'Cómo corregirlo: corrija la configuración de la dl de cada persona en Ubis (véase el aviso de delegaciones si también aparece).'
        );
    }

    private static function esTextoConfiguracionDl(string $mensaje): bool
    {
        return str_contains($mensaje, _('Delegaciones no dadas de alta'))
            || str_contains($mensaje, 'Delegaciones no dadas de alta')
            || str_contains($mensaje, _('Delegaciones sin región del stgr'))
            || str_contains($mensaje, 'Delegaciones sin región del stgr')
            || str_contains($mensaje, _('Delegaciones cuyo esquema no está'))
            || str_contains($mensaje, 'Delegaciones cuyo esquema no está');
    }

    private static function stripHtml(string $mensaje): string
    {
        return trim(strip_tags($mensaje));
    }

    /**
     * @param list<string> $etiquetas
     */
    private static function listaEtiquetas(array $etiquetas): string
    {
        return implode(', ', array_map(static fn (string $e): string => '«' . $e . '»', $etiquetas));
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
