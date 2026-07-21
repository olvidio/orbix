<?php

namespace src\notas\domain;

/**
 * Criterio de «entidad externa» para el perímetro de certificados
 * (docs/dev/notas_modelo_acta.md §2).
 *
 * No incluye otra región STGR Orbix: ahí el expediente se resuelve por agregación.
 */
final class DestinoNotaExterno
{
    public static function esExternoPorIdNom(int $id_nom): bool
    {
        return $id_nom < 0;
    }

    public static function esExternoPorEsquema(?string $nombre_schema): bool
    {
        return $nombre_schema === 'restov' || $nombre_schema === 'restof';
    }

    public static function esExterno(int $id_nom, string|null $nombre_schema = null): bool
    {
        return self::esExternoPorIdNom($id_nom) || self::esExternoPorEsquema($nombre_schema);
    }
}
