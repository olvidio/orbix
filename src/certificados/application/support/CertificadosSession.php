<?php

declare(strict_types=1);

namespace src\certificados\application\support;

final class CertificadosSession
{
    public static function esquemaRegionStgr(): string
    {
        $auth = $_SESSION['session_auth'] ?? null;
        if (!is_array($auth)) {
            return '';
        }
        $esquema = $auth['esquema'] ?? null;

        return is_string($esquema) ? $esquema : '';
    }
}
