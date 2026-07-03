<?php

declare(strict_types=1);

namespace frontend\actividadessacd\helpers;

use frontend\notas\helpers\NotasFormSupport;
use frontend\shared\helpers\PayloadCoercion;
use src\configuracion\domain\value_objects\ConfigSnapshot;

final class ActividadessacdSession
{
    public static function oConfig(): ?ConfigSnapshot
    {
        $oConfig = $_SESSION['oConfig'] ?? null;

        return $oConfig instanceof ConfigSnapshot ? $oConfig : null;
    }

    public static function anyFinalCurs(): int
    {
        $oConfig = self::oConfig();
        if ($oConfig === null) {
            return (int) date('Y');
        }

        return $oConfig->any_final_curs();
    }

    public static function sessionIdioma(): string
    {
        $auth = $_SESSION['session_auth'] ?? null;
        if (is_array($auth) && isset($auth['idioma']) && is_string($auth['idioma'])) {
            return $auth['idioma'];
        }
        $oConfig = self::oConfig();
        if ($oConfig === null) {
            return '';
        }

        return PayloadCoercion::string($oConfig->getIdioma_default());
    }
}

final class ActividadessacdPayload
{
    /**
     * @param array<int|string, mixed> $payload
     * @return array<int|string, string>
     */
    public static function localesFromPayload(array $payload): array
    {
        return NotasFormSupport::desplegableOpciones($payload['a_locales'] ?? []);
    }

    /**
     * @param array<int|string, mixed> $payload
     */
    public static function textoFromPayload(array $payload): string
    {
        return PayloadCoercion::string($payload['texto'] ?? '');
    }
}
