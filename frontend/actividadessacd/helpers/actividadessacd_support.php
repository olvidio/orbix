<?php

/**
 * Helpers compartidos del módulo frontend/actividadessacd.
 */

require_once __DIR__ . '/../../actividades/helpers/actividades_support.php';

use src\configuracion\domain\value_objects\ConfigSnapshot;

function actividadessacd_o_config(): ?ConfigSnapshot
{
    $oConfig = $_SESSION['oConfig'] ?? null;

    return $oConfig instanceof ConfigSnapshot ? $oConfig : null;
}

function actividadessacd_any_final_curs(): int
{
    $oConfig = actividadessacd_o_config();
    if ($oConfig === null) {
        return (int) date('Y');
    }

    return $oConfig->any_final_curs();
}

function actividadessacd_session_idioma(): string
{
    $auth = $_SESSION['session_auth'] ?? null;
    if (is_array($auth) && isset($auth['idioma']) && is_string($auth['idioma'])) {
        return $auth['idioma'];
    }
    $oConfig = actividadessacd_o_config();
    if ($oConfig === null) {
        return '';
    }

    return tessera_imprimir_string($oConfig->getIdioma_default());
}

/**
 * @param array<int|string, mixed> $payload
 * @return array<int|string, string>
 */
function actividadessacd_locales_from_payload(array $payload): array
{
    return notas_desplegable_opciones($payload['a_locales'] ?? []);
}

/**
 * @param array<int|string, mixed> $payload
 */
function actividadessacd_texto_from_payload(array $payload): string
{
    return tessera_imprimir_string($payload['texto'] ?? '');
}
