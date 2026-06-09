<?php

declare(strict_types=1);

namespace frontend\certificados\helpers;

use frontend\shared\security\HashFrontSignedLink;

/**
 * Firma la URL del botón "adjuntar nuevo certificado" para {@see SelectCertificadosDeUnaPersonaRender}.
 */
final class SelectCertificadosDeUnaPersonaUrlSigning
{
    /**
     * @param array{url_nuevo_spec?: mixed} $in
     * @return array{url_nuevo: string}
     */
    public static function sign(array $in): array
    {
        return [
            'url_nuevo' => HashFrontSignedLink::tryFromSpec($in['url_nuevo_spec'] ?? null),
        ];
    }
}
