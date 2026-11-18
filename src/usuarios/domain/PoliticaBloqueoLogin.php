<?php

declare(strict_types=1);

namespace src\usuarios\domain;

/**
 * Bloqueo temporal del login: tras MAX_FALLOS fallos seguidos (contraseña o código 2FA) se rechaza
 * durante MINUTOS_BLOQUEO aunque la contraseña sea correcta. Un acierto pone el contador a cero.
 */
final class PoliticaBloqueoLogin
{
    public const MAX_FALLOS = 5;

    public const MINUTOS_BLOQUEO = 15;
}
