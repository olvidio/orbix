<?php

declare(strict_types=1);

namespace src\inventario\application\support;

use src\shared\domain\helpers\FuncTablasSupport;

/**
 * Clave de selección de fila en tablas tablaDB (base64 URL-safe de la PK JSON).
 */
final class DocumentoTablaSelKey
{
    /**
     * @return positive-int|null
     */
    public static function idDocFromUrlsafeKey(string $urlsafeKey): ?int
    {
        if ($urlsafeKey === '') {
            return null;
        }

        $decoded = FuncTablasSupport::urlsafeB64decode($urlsafeKey);
        if ($decoded === '') {
            return null;
        }

        try {
            $pkey = json_decode($decoded, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }

        if (is_int($pkey) || (is_string($pkey) && is_numeric($pkey))) {
            $rawId = $pkey;
        } elseif (is_array($pkey)) {
            $rawId = $pkey['id_doc'] ?? $pkey[0] ?? null;
        } else {
            return null;
        }
        if (!is_numeric($rawId)) {
            return null;
        }

        $idDoc = (int) $rawId;

        return $idDoc > 0 ? $idDoc : null;
    }
}
